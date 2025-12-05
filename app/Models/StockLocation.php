<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_name', 'creation_date', 'creator', 'principal_manager', 'description', 'code_stock',
    ];

    protected $casts = [
        'creation_date' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(StockItem::class, 'stock_location_id');
    }

    public function creatorUser()
    {
        return $this->belongsTo(User::class, 'creator');
    }

    public function principalManager()
    {
        return $this->belongsTo(User::class, 'principal_manager');
    }
}

