<?php

namespace App\Http\Controllers;

use App\Models\StockLocation;
use App\Models\StockArrivalAdministration;
use App\Models\StockRequest;
use Illuminate\Http\Request;

class InterfaceController extends Controller
{
    //
    function __construct()
    {
        $this->middleware("auth");
    }

    function dashboard()
    {
        $locations = StockLocation::all();

        // Get pending arrivals count for each location
        $pendingArrivals = [];
        foreach ($locations as $location) {
            $pendingArrivals[$location->id] = StockArrivalAdministration::where('stock_location_destination', $location->id)
                ->whereDoesntHave('incomingRecords')
                ->count();
        }

        // Get pending requests for facility manager approval
        $pendingFacilityRequests = StockRequest::where('status', 'pending')->count();

        // Get approved requests that haven't been fulfilled yet
        $approvedRequestsToFulfill = StockRequest::whereIn('status', ['approved_facility_manager', 'approved_data_manager'])
            ->whereHas('details', function ($query) {
                $query->where('facility_manager_approval', true)
                      ->where('data_manager_approval', true)
                      ->where('approved_quantity', '>', 0);
            })
            ->count();

        return view("dashboard_new", compact(
            'locations',
            'pendingArrivals',
            'pendingFacilityRequests',
            'approvedRequestsToFulfill'
        ));
    }
}
