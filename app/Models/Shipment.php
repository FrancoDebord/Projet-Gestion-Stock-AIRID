<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_number', 'received_at', 'received_by', 'colis_count', 'sender',
        'to_location_id', 'admin_notes', 'finalized_at', 'finalized_by', 'ack_sent', 'project_id',
    ];

    protected $casts = [
        'received_at' => 'datetime',
        'finalized_at' => 'datetime',
        'ack_sent' => 'boolean',
    ];

    public function packages()
    {
        return $this->hasMany(Package::class);
    }

    public function documents()
    {
        return $this->hasMany(ShipmentDocument::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function toLocation()
    {
        return $this->belongsTo(StockLocation::class, 'to_location_id');
    }
}
