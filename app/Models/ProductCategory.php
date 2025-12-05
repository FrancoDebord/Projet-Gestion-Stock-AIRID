<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'stock_location_id',
    ];

    public function stockLocation()
    {
        return $this->belongsTo(StockLocation::class, 'stock_location_id');
    }

    public function items()
    {
        return $this->hasMany(StockItem::class, 'product_category_id');
    }

    public function subCategories()
    {
        return $this->hasMany(SubCategory::class, 'product_category_id');
    }
}
