<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockIncomingRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'date_reception', 'stock_arrival_admin_id', 'description_globale', 'receiver',
        'stock_location_destination_id', 'project_id', 'sender', 'certificat_analyse', 'msds', 'borderau_livraison',
    ];

    protected $casts = [
        'date_reception' => 'datetime',
    ];

    public function stockArrivalAdministration()
    {
        return $this->belongsTo(StockArrivalAdministration::class, 'stock_arrival_admin_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver');
    }

    public function stockLocationDestination()
    {
        return $this->belongsTo(StockLocation::class, 'stock_location_destination_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function details()
    {
        return $this->hasMany(StockIncomingRecordDetail::class, 'stock_incoming_record_id');
    }
}
