<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectStockBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'stock_item_id',
        'balance',
        'last_movement_at',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'last_movement_at' => 'datetime',
    ];

    /**
     * Relation vers le projet
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Relation vers l'article de stock
     */
    public function stockItem()
    {
        return $this->belongsTo(StockItem::class);
    }

    /**
     * Vérifier si le solde est positif
     */
    public function hasStock(): bool
    {
        return $this->balance > 0;
    }

    /**
     * Vérifier si la quantité demandée est disponible
     */
    public function canFulfill(float $quantity): bool
    {
        return $this->balance >= $quantity;
    }

    /**
     * Méthodes utilitaires pour les requêtes
     */
    public function scopeForProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeForStockItem($query, $stockItemId)
    {
        return $query->where('stock_item_id', $stockItemId);
    }

    public function scopeWithStock($query)
    {
        return $query->where('balance', '>', 0);
    }
}
