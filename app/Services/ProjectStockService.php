<?php

namespace App\Services;

use App\Models\ProjectStockBalance;
use App\Models\Project;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class ProjectStockService
{
    /**
     * Obtenir le projet Global
     */
    public static function getGlobalProject()
    {
        return Project::where('name', 'Global')->first();
    }

    /**
     * Obtenir le solde pour un projet et un article spécifique
     */
    public static function getBalance($projectId, $stockItemId)
    {
        return ProjectStockBalance::where('project_id', $projectId)
            ->where('stock_item_id', $stockItemId)
            ->first();
    }

    /**
     * Obtenir tous les soldes pour un article de stock
     */
    public static function getBalancesForStockItem($stockItemId)
    {
        return ProjectStockBalance::with('project')
            ->where('stock_item_id', $stockItemId)
            ->where('balance', '>', 0)
            ->orderBy('balance', 'desc')
            ->get();
    }

    /**
     * Calculer la disponibilité totale pour un article (tous projets confondus)
     */
    public static function getTotalAvailable($stockItemId)
    {
        return ProjectStockBalance::where('stock_item_id', $stockItemId)
            ->sum('balance');
    }

    /**
     * Vérifier la disponibilité en respectant la logique Global + projet
     * Priorité : projet spécifique > Global
     */
    public static function checkAvailability($stockItemId, $projectId = null, $quantity)
    {
        $availability = [
            'can_fulfill' => false,
            'available_quantity' => 0,
            'source_project' => null,
            'source_balances' => collect(),
        ];

        // Si un projet spécifique est demandé, vérifier d'abord ce projet
        if ($projectId) {
            $projectBalance = self::getBalance($projectId, $stockItemId);
            if ($projectBalance && $projectBalance->canFulfill($quantity)) {
                $availability['can_fulfill'] = true;
                $availability['available_quantity'] = $projectBalance->balance;
                $availability['source_project'] = $projectBalance->project;
                $availability['source_balances'] = collect([$projectBalance]);
                return $availability;
            }
        }

        // Vérifier le stock Global
        $globalProject = self::getGlobalProject();
        if ($globalProject) {
            $globalBalance = self::getBalance($globalProject->id, $stockItemId);
            if ($globalBalance && $globalBalance->canFulfill($quantity)) {
                $availability['can_fulfill'] = true;
                $availability['available_quantity'] = $globalBalance->balance;
                $availability['source_project'] = $globalBalance->project;
                $availability['source_balances'] = collect([$globalBalance]);
                return $availability;
            }
        }

        // Si pas assez dans le projet spécifique ou Global, vérifier tous les projets
        $allBalances = self::getBalancesForStockItem($stockItemId);
        $totalAvailable = $allBalances->sum('balance');

        if ($totalAvailable >= $quantity) {
            $availability['can_fulfill'] = true;
            $availability['available_quantity'] = $totalAvailable;
            $availability['source_balances'] = $allBalances;
        }

        return $availability;
    }

    /**
     * Mettre à jour le solde lors d'un mouvement de stock
     */
    public static function updateBalance($projectId, $stockItemId, $quantity, $type)
    {
        DB::transaction(function () use ($projectId, $stockItemId, $quantity, $type) {
            $balance = ProjectStockBalance::firstOrNew([
                'project_id' => $projectId,
                'stock_item_id' => $stockItemId,
            ]);

            $quantityChange = $type === 'in' ? $quantity : -$quantity;
            $balance->balance += $quantityChange;
            $balance->last_movement_at = now();

            if ($balance->balance > 0) {
                $balance->save();
            } elseif ($balance->exists) {
                $balance->delete(); // Supprimer les soldes à zéro
            }
        });
    }

    /**
     * Transférer du stock d'un projet à un autre
     */
    public static function transferStock($fromProjectId, $toProjectId, $stockItemId, $quantity)
    {
        DB::transaction(function () use ($fromProjectId, $toProjectId, $stockItemId, $quantity) {
            // Vérifier que le stock source a suffisamment
            $sourceBalance = self::getBalance($fromProjectId, $stockItemId);
            if (!$sourceBalance || !$sourceBalance->canFulfill($quantity)) {
                throw new \Exception('Stock insuffisant dans le projet source');
            }

            // Débiter le projet source
            self::updateBalance($fromProjectId, $stockItemId, $quantity, 'out');

            // Créditer le projet destination
            self::updateBalance($toProjectId, $stockItemId, $quantity, 'in');
        });
    }

    /**
     * Obtenir les articles en rupture de stock par projet
     */
    public static function getLowStockByProject($projectId = null)
    {
        $query = ProjectStockBalance::with(['stockItem', 'project'])
            ->where('balance', '<=', DB::raw('stock_items.min_quantity'))
            ->join('stock_items', 'project_stock_balances.stock_item_id', '=', 'stock_items.id');

        if ($projectId) {
            $query->where('project_id', $projectId);
        }

        return $query->get();
    }
}
