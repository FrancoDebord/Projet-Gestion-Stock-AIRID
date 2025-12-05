<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_item_id',
        'user_id',
        'type',
        'quantity',
        'reason',
        'notes',
        'reference',
        'project_id',
        'purpose',
        'usage_form_path',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function stockItem()
    {
        return $this->belongsTo(StockItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function getQuantityChangeAttribute()
    {
        return $this->type === 'out' ? -$this->quantity : $this->quantity;
    }
}
