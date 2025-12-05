<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockArrivalAdministration extends Model
{
    use HasFactory;

    protected $table = 'stock_arrivals_administration';

    protected $fillable = [
        'date_arrival', 'sender', 'description_globale', 'stock_location_destination', 'administration_staff',
        'bordereau_delivery', 'certificate_analysis', 'msds', 'other_document', 'staff_transmis_stock',
    ];

    protected $casts = [
        'date_arrival' => 'datetime',
    ];

    public function stockLocationDestination()
    {
        return $this->belongsTo(StockLocation::class, 'stock_location_destination');
    }

    public function administrationStaff()
    {
        return $this->belongsTo(User::class, 'administration_staff');
    }

    public function transmittedTo()
    {
        return $this->belongsTo(User::class, 'staff_transmis_stock');
    }

    public function incomingRecords()
    {
        return $this->hasMany(StockIncomingRecord::class, 'stock_arrival_admin_id');
    }
}
