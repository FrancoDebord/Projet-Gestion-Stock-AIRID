<?php

namespace App\Http\Controllers;

use App\Models\ProjectStockBalance;
use App\Models\StockLocation;
use App\Models\Project;
use App\Models\StockItem;
use App\Models\ProductCategory;
use App\Services\ProjectStockService;
use Illuminate\Http\Request;

class ProjectStockController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        // Filtres
        $stockLocationId = $request->get('stock_location_id');
        $projectId = $request->get('project_id');
        $productCategoryId = $request->get('product_category_id');
        $search = $request->get('search');
        $showEmpty = $request->boolean('show_empty', false);

        // Query de base
        $query = ProjectStockBalance::with(['project', 'stockItem.productCategory', 'stockItem.stockLocation'])
            ->whereHas('stockItem', function ($q) use ($stockLocationId, $productCategoryId, $search) {
                if ($stockLocationId) {
                    $q->where('stock_location_id', $stockLocationId);
                }
                if ($productCategoryId) {
                    $q->where('product_category_id', $productCategoryId);
                }
                if ($search) {
                    $q->where(function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%")
                           ->orWhere('sku', 'like', "%{$search}%")
                           ->orWhere('brand', 'like', "%{$search}%");
                    });
                }
            });

        // Filtre par projet
        if ($projectId) {
            if ($projectId === 'global') {
                // Afficher seulement le stock Global
                $globalProject = ProjectStockService::getGlobalProject();
                $query->where('project_id', $globalProject->id);
            } else {
                $query->where('project_id', $projectId);
            }
        }

        // Afficher ou masquer les stocks vides
        if (!$showEmpty) {
            $query->where('balance', '>', 0);
        }

        // Tri et pagination
        $stocks = $query->orderBy('stock_item_id')
                       ->orderBy('project_id')
                       ->paginate(25)
                       ->appends($request->query());

        // Données pour les filtres
        $stockLocations = StockLocation::orderBy('stock_name')->get();
        $projects = Project::orderBy('name')->get();
        $productCategories = ProductCategory::orderBy('name')->get();

        // Statistiques
        $stats = $this->getStats($stockLocationId, $projectId);

        return view('project-stocks.index', compact(
            'stocks',
            'stockLocations',
            'projects',
            'productCategories',
            'stockLocationId',
            'projectId',
            'productCategoryId',
            'search',
            'showEmpty',
            'stats'
        ));
    }

    private function getStats($stockLocationId = null, $projectId = null)
    {
        $stats = [];

        // Nombre total d'articles en stock
        $query = ProjectStockBalance::where('balance', '>', 0);

        if ($stockLocationId) {
            $query->whereHas('stockItem', function ($q) use ($stockLocationId) {
                $q->where('stock_location_id', $stockLocationId);
            });
        }

        if ($projectId) {
            if ($projectId === 'global') {
                $globalProject = ProjectStockService::getGlobalProject();
                $query->where('project_id', $globalProject->id);
            } else {
                $query->where('project_id', $projectId);
            }
        }

        $stats['total_items'] = $query->distinct('stock_item_id')->count('stock_item_id');
        $stats['total_quantity'] = $query->sum('balance');
        $stats['total_projects'] = $query->distinct('project_id')->count('project_id');

        return $stats;
    }

    public function export(Request $request)
    {
        // Cette méthode pourrait être utilisée pour exporter les données
        // Pour l'instant, on redirige vers l'index
        return redirect()->route('project-stocks.index', $request->query());
    }
}
