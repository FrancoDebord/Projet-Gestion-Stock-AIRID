<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockRequestDetail extends Model
{
    use HasFactory;

    protected $table = 'stock_item_usage_request_details';

    protected $fillable = [
        'stock_item_usage_request_id', 'project_id', 'stock_item_id', 'requested_quantity', 'usage_description',
        'request_reason', 'facility_manager_approval', 'data_manager_approval',
        'observations', 'approved_quantity',
    ];

    protected $casts = [
        'facility_manager_approval' => 'boolean',
        'data_manager_approval' => 'boolean',
    ];

    // Relations
    public function stockRequest()
    {
        return $this->belongsTo(StockRequest::class, 'stock_item_usage_request_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function stockItem()
    {
        return $this->belongsTo(StockItem::class);
    }

    // Helper methods
    public function isApproved()
    {
        return 1;
        // return $this->facility_manager_approval && $this->data_manager_approval;
    }

    public function canBeApprovedByFacilityManager()
    {
        return !$this->facility_manager_approval;
    }

    public function canBeApprovedByDataManager()
    {
        return $this->facility_manager_approval && !$this->data_manager_approval;
    }
}

// class StockRequestDetail extends Model
// {
//     use HasFactory;

//     protected $table = 'stock_item_usage_request_details';

//     protected $fillable = [
//         'stock_item_usage_request_id', 'project_id', 'stock_item_id', 'requested_quantity', 'usage_description',
//         'request_reason', 'facility_manager_approval', 'data_manager_approval',
//         'observations', 'approved_quantity',
//     ];

//     protected $casts = [
//         'facility_manager_approval' => 'boolean',
//         'data_manager_approval' => 'boolean',
//     ];

//     // Relations
//     public function stockRequest()
//     {
//         return $this->belongsTo(StockRequest::class, 'stock_item_usage_request_id');
//     }

//     public function project()
//     {
//         return $this->belongsTo(Project::class);
//     }

//     public function stockItem()
//     {
//         return $this->belongsTo(StockItem::class);
//     }

//     // Helper methods
//     public function isApproved()
//     {
//         return $this->facility_manager_approval && $this->data_manager_approval;
//     }

//     public function canBeApprovedByFacilityManager()
//     {
//         return !$this->facility_manager_approval;
//     }

//     public function canBeApprovedByDataManager()
//     {
//         return $this->facility_manager_approval && !$this->data_manager_approval;
//     }
// }