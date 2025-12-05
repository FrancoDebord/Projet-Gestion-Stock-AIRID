<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'brand',
        'category',
        'product_category_id',
        'sub_category_id',
        'description',
        'initial_quantity',
        'min_quantity',
        'unit',
        'unit_price',
        'stock_location_id',
        'type_usage_product',
        'image',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'type_usage_product' => 'string',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function stockLocation()
    {
        return $this->belongsTo(StockLocation::class, 'stock_location_id');
    }

    public function productCategory()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

    public function movements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function incomingRecordDetails()
    {
        return $this->hasMany(StockIncomingRecordDetail::class, 'stock_item_id');
    }

    public function getTotalValueAttribute()
    {
        return $this->initial_quantity * ($this->unit_price ?? 0);
    }

    public function isLowStock()
    {
        return $this->initial_quantity <= $this->min_quantity;
    }
}
