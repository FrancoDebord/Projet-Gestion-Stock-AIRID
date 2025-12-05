<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'product_category_id',
    ];

    public function productCategory()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function items()
    {
        return $this->hasMany(StockItem::class, 'sub_category_id');
    }
}
