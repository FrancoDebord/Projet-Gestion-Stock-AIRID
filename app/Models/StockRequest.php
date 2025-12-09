<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockRequest extends Model
{
    use HasFactory;

    protected $table = 'stock_item_usage_requests';

    protected $fillable = [
        'request_date', 'requester_id', 'project_id', 'code_machine', 'room_number',
        'status', 'general_notes', 'facility_manager_id', 'facility_manager_approval_date',
        'facility_manager_notes', 'data_manager_id', 'data_manager_approval_date', 'data_manager_notes',
    ];

    protected $casts = [
        'request_date' => 'datetime',
        'facility_manager_approval_date' => 'datetime',
        'data_manager_approval_date' => 'datetime',
    ];

    // Relations
    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function facilityManager()
    {
        return $this->belongsTo(User::class, 'facility_manager_id');
    }

    public function dataManager()
    {
        return $this->belongsTo(User::class, 'data_manager_id');
    }

    public function details()
    {
        return $this->hasMany(StockRequestDetail::class, 'stock_item_usage_request_id');
    }

    // Helper methods
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return in_array($this->status, ['approved_facility_manager', 'approved_data_manager']);
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function canBeApprovedByFacilityManager()
    {
        return $this->status === 'pending';
    }

    public function canBeApprovedByDataManager()
    {
        return $this->status === 'approved_facility_manager';
    }

    public function canBeRejected()
    {
        return in_array($this->status, ['pending', 'approved_facility_manager']);
    }
}