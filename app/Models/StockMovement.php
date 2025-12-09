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
        'batch_number',
        'stock_incoming_detail_id',
        'date_mouvement',
        'stock_item_usage_request_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'date_mouvement' => 'datetime',
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

    public function incomingDetail()
    {
        return $this->belongsTo(StockIncomingRecordDetail::class, 'stock_incoming_detail_id');
    }

    public function usageRequest()
    {
        return $this->belongsTo(StockRequest::class, 'stock_item_usage_request_id');
    }

    public function getQuantityChangeAttribute()
    {
        return $this->type === 'out' ? -$this->quantity : $this->quantity;
    }
}
