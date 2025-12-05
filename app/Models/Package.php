<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id', 'code', 'assigned_by', 'assigned_at', 'contents',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
    ];

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }
}
