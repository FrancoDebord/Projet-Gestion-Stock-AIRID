<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockIncomingRecordDetail extends Model
{
    use HasFactory;

    protected $table = 'stock_incoming_record_details';

    protected $fillable = [
        'stock_item_id', 'stock_incoming_record_id', 'code_lot', 'batch_number', 'quantite_lot',
    ];

    public function stockItem()
    {
        return $this->belongsTo(StockItem::class, 'stock_item_id');
    }

    public function stockIncomingRecord()
    {
        return $this->belongsTo(StockIncomingRecord::class, 'stock_incoming_record_id');
    }

    public function stockMovement()
    {
        return $this->belongsTo(StockMovement::class, 'stock_incoming_detail_id');
    }
}
