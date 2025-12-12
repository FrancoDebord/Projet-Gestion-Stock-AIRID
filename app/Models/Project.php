<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'description',
    ];

    public function movements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function stockBalances()
    {
        return $this->hasMany(ProjectStockBalance::class);
    }
}
