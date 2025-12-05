<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id', 'path', 'original_name', 'uploaded_by',
    ];

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }
}
