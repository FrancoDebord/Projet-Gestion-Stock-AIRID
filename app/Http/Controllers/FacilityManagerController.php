<?php

namespace App\Http\Controllers;

use App\Models\StockRequest;
use App\Models\StockItem;
use App\Models\StockMovement;
use App\Models\Project;
use App\Services\ProjectStockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FacilityManagerController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', StockRequest::class);

        // Show pending requests that need facility manager approval
        $requests = StockRequest::with([
            'requester',
            'project',
            'details.stockItem'
        ])
        ->where('status', 'pending')
        ->orderBy('request_date', 'desc')
        ->paginate(20);

        return view('facility-manager.index', compact('requests'));
    }

    public function show(StockRequest $stockRequest)
    {
        $this->authorize('view', $stockRequest);

        // Ensure the request is pending and can be approved by facility manager
        if (!$stockRequest->canBeApprovedByFacilityManager()) {
            return redirect()->route('facility-manager.index')
                ->with('error', 'Cette demande ne peut pas être approuvée par le facility manager.');
        }

        $stockRequest->load([
            'requester',
            'project',
            'details.stockItem',
            'details.project'
        ]);

        // Get available quantities by project for each requested item
        $itemsWithAvailability = [];
        foreach ($stockRequest->details as $detail) {
            $stockItem = $detail->stockItem;

            // Get total available quantity for this item
            $totalAvailable = $this->getTotalAvailableQuantity($stockItem->id);

            // Get available quantities by project
            $availabilityByProject = $this->getAvailabilityByProject($stockItem->id);

            // Check availability with Global + project logic
            $availability = ProjectStockService::checkAvailability(
                $stockItem->id,
                $detail->project_id,
                $detail->requested_quantity
            );

            $itemsWithAvailability[] = [
                'detail' => $detail,
                'stock_item' => $stockItem,
                'total_available' => $totalAvailable,
                'availability_by_project' => $availabilityByProject,
                'requested_quantity' => $detail->requested_quantity,
                'can_fulfill' => $availability['can_fulfill'],
                'availability_info' => $availability,
            ];
        }

        return view('facility-manager.show', compact('stockRequest', 'itemsWithAvailability'));
    }

    public function approve(Request $request, StockRequest $stockRequest)
    {
        $this->authorize('approve', $stockRequest);

        // Validate request
        $validated = $request->validate([
            'approvals' => 'required|array',
            'approvals.*.approved_quantity' => 'required|integer|min:0',
            'approvals.*.source_project_id' => 'nullable|exists:projects,id',
            'approvals.*.notes' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($validated, $stockRequest,  $request) {
            $user = auth()->user();
            $now = now();

            // Update stock request status
            $stockRequest->update([
                'status' => 'approved_facility_manager',
                'facility_manager_id' => $user->id,
                'facility_manager_approval_date' => $now,
                'facility_manager_notes' => $request->input('general_notes'),
            ]);

            // Process each detail approval
            foreach ($validated['approvals'] as $detailId => $approval) {
                $detail = $stockRequest->details()->find($detailId);
                if ($detail) {
                    // Update detail with approval
                    $detail->update([
                        'facility_manager_approval' => true,
                        'approved_quantity' => $approval['approved_quantity'],
                        'project_id' => $approval['source_project_id'] ?? $stockRequest->project_id,
                        'observations' => $approval['notes'] ?? null,
                    ]);
                }
            }
        });

        return redirect()->route('facility-manager.index')
            ->with('success', 'Demande approuvée avec succès par le Facility Manager.');
    }

    public function reject(Request $request, StockRequest $stockRequest)
    {
        $this->authorize('reject', $stockRequest);

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $stockRequest->update([
            'status' => 'rejected',
            'facility_manager_notes' => $validated['rejection_reason'],
        ]);

        return redirect()->route('facility-manager.index')
            ->with('success', 'Demande rejetée avec succès.');
    }

    /**
     * Get total available quantity for a stock item
     */
    private function getTotalAvailableQuantity($stockItemId)
    {
        return ProjectStockService::getTotalAvailable($stockItemId);
    }

    /**
     * Get available quantities by project for a stock item
     */
    private function getAvailabilityByProject($stockItemId)
    {
        return ProjectStockService::getBalancesForStockItem($stockItemId)
            ->map(function ($balance) {
                return [
                    'project' => $balance->project,
                    'available_quantity' => $balance->balance,
                ];
            })
            ->toArray();
    }
}