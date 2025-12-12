<?php

namespace App\Http\Controllers;

use App\Models\StockRequest;
use App\Models\StockRequestDetail;
use App\Models\StockItem;
use App\Models\StockMovement;
use App\Models\User;
use App\Models\Project;
use App\Services\ProjectStockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockRequestController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', StockRequest::class);

        $requests = StockRequest::with([
            'requester',
            'project',
            'facilityManager',
            'dataManager',
            'details.stockItem',
            'details.project'
        ])
        ->orderBy('request_date', 'desc')
        ->paginate(20);

        return view('stock_requests.index', compact('requests'));
    }

    public function create()
    {
        $this->authorize('create', StockRequest::class);

        $projects = Project::orderBy('name')->get();
        $stockItems = StockItem::orderBy('name')->get();

        return view('stock_requests.create', compact('projects', 'stockItems'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', StockRequest::class);

        $validated = $request->validate([
            'request_date' => 'required|date',
            'project_id' => 'nullable|exists:projects,id',
            'code_machine' => 'nullable|string|max:100',
            'room_number' => 'nullable|string|max:50',
            'general_notes' => 'nullable|string',
            'details' => 'required|array|min:1',
            'details.*.stock_item_id' => 'required|exists:stock_items,id',
            'details.*.requested_quantity' => 'required|integer|min:1',
            'details.*.usage_description' => 'nullable|string',
            'details.*.request_reason' => 'required|string',
        ]);

        $validated['requester_id'] = Auth::id();
        $validated['status'] = 'pending';

        DB::transaction(function () use ($validated) {
            // Create the main request
            $request = StockRequest::create($validated);

            // Create the details
            foreach ($validated['details'] as $detail) {
                $request->details()->create($detail);
            }
        });

        return redirect()->route('stock-requests.index')
            ->with('success', 'Demande de stock créée avec succès.');
    }

    public function show(StockRequest $stockRequest)
    {
        $this->authorize('view', $stockRequest);

        $stockRequest->load([
            'requester',
            'project',
            'facilityManager',
            'dataManager',
            'details.stockItem',
            'details.project'
        ]);

        return view('stock_requests.show', compact('stockRequest'));
    }

    public function edit(StockRequest $stockRequest)
    {
        $this->authorize('update', $stockRequest);

        // Only allow editing if the request is still pending
        if (!$stockRequest->isPending()) {
            return redirect()->route('stock-requests.show', $stockRequest)
                ->with('error', 'Cette demande ne peut plus être modifiée car elle a déjà été traitée.');
        }

        $projects = Project::orderBy('name')->get();
        $stockItems = StockItem::orderBy('name')->get();

        $stockRequest->load('details');

        return view('stock_requests.edit', compact('stockRequest', 'projects', 'stockItems'));
    }

    public function update(Request $request, StockRequest $stockRequest)
    {
        $this->authorize('update', $stockRequest);

        // Only allow updating if the request is still pending
        if (!$stockRequest->isPending()) {
            return redirect()->route('stock-requests.show', $stockRequest)
                ->with('error', 'Cette demande ne peut plus être modifiée car elle a déjà été traitée.');
        }

        $validated = $request->validate([
            'request_date' => 'required|date',
            'project_id' => 'nullable|exists:projects,id',
            'code_machine' => 'nullable|string|max:100',
            'room_number' => 'nullable|string|max:50',
            'general_notes' => 'nullable|string',
            'details' => 'required|array|min:1',
            'details.*.stock_item_id' => 'required|exists:stock_items,id',
            'details.*.requested_quantity' => 'required|integer|min:1',
            'details.*.usage_description' => 'nullable|string',
            'details.*.request_reason' => 'required|string',
        ]);

        DB::transaction(function () use ($validated, $stockRequest) {
            // Update the main request
            $stockRequest->update($validated);

            // Delete existing details and create new ones
            $stockRequest->details()->delete();
            foreach ($validated['details'] as $detail) {
                $stockRequest->details()->create($detail);
            }
        });

        return redirect()->route('stock-requests.show', $stockRequest)
            ->with('success', 'Demande de stock mise à jour avec succès.');
    }

    public function destroy(StockRequest $stockRequest)
    {
        $this->authorize('delete', $stockRequest);

        // Only allow deletion if the request is still pending
        if (!$stockRequest->isPending()) {
            return redirect()->route('stock-requests.show', $stockRequest)
                ->with('error', 'Cette demande ne peut plus être supprimée car elle a déjà été traitée.');
        }

        $stockRequest->delete();

        return redirect()->route('stock-requests.index')
            ->with('success', 'Demande de stock supprimée avec succès.');
    }

    public function approve(Request $request, StockRequest $stockRequest)
    {
        $this->authorize('update', $stockRequest);

        $validated = $request->validate([
            'approval_type' => 'required|in:facility_manager,data_manager',
            'notes' => 'nullable|string',
            'details' => 'sometimes|array',
            'details.*.approved_quantity' => 'nullable|integer|min:0',
            'details.*.observations' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $stockRequest) {
            $user = Auth::user();
            $now = now();

            if ($validated['approval_type'] === 'facility_manager') {
                if (!$stockRequest->canBeApprovedByFacilityManager()) {
                    throw new \Exception('Cette demande ne peut pas être approuvée par le facility manager.');
                }

                $stockRequest->update([
                    'status' => 'approved_facility_manager',
                    'facility_manager_id' => $user->id,
                    'facility_manager_approval_date' => $now,
                    'facility_manager_notes' => $validated['notes'] ?? null,
                ]);

                // Update details approvals
                if (isset($validated['details'])) {
                    foreach ($validated['details'] as $detailId => $detailData) {
                        $detail = $stockRequest->details()->find($detailId);
                        if ($detail) {
                            $detail->update([
                                'facility_manager_approval' => true,
                                'approved_quantity' => $detailData['approved_quantity'] ?? $detail->requested_quantity,
                                'observations' => $detailData['observations'] ?? null,
                            ]);
                        }
                    }
                }

            } elseif ($validated['approval_type'] === 'data_manager') {
                if (!$stockRequest->canBeApprovedByDataManager()) {
                    throw new \Exception('Cette demande ne peut pas être approuvée par le data manager.');
                }

                $stockRequest->update([
                    'status' => 'approved_data_manager',
                    'data_manager_id' => $user->id,
                    'data_manager_approval_date' => $now,
                    'data_manager_notes' => $validated['notes'] ?? null,
                ]);

                // Update details approvals
                if (isset($validated['details'])) {
                    foreach ($validated['details'] as $detailId => $detailData) {
                        $detail = $stockRequest->details()->find($detailId);
                        if ($detail) {
                            $detail->update([
                                'data_manager_approval' => true,
                                'approved_quantity' => $detailData['approved_quantity'] ?? $detail->approved_quantity,
                                'observations' => $detailData['observations'] ?? null,
                            ]);
                        }
                    }
                }
            }
        });

        return redirect()->route('stock-requests.show', $stockRequest)
            ->with('success', 'Demande approuvée avec succès.');
    }

    public function reject(Request $request, StockRequest $stockRequest)
    {
        $this->authorize('update', $stockRequest);

        if (!$stockRequest->canBeRejected()) {
            return redirect()->route('stock-requests.show', $stockRequest)
                ->with('error', 'Cette demande ne peut plus être rejetée.');
        }

        $validated = $request->validate([
            'rejection_notes' => 'required|string',
        ]);

        $stockRequest->update([
            'status' => 'rejected',
            'facility_manager_notes' => $validated['rejection_notes'],
        ]);

        return redirect()->route('stock-requests.show', $stockRequest)
            ->with('success', 'Demande rejetée avec succès.');
    }

    public function fulfill(Request $request, StockRequest $stockRequest)
    {
        $this->authorize('update', $stockRequest);

        if (!$stockRequest->isApproved()) {
            return redirect()->route('stock-requests.show', $stockRequest)
                ->with('error', 'Cette demande doit être approuvée avant de pouvoir être satisfaite.');
        }

        DB::transaction(function () use ($stockRequest) {
            // Update status to completed
            $stockRequest->update(['status' => 'completed']);

            // Create stock movements for each approved item
            foreach ($stockRequest->details as $detail) {
                if ($detail->isApproved() && $detail->approved_quantity > 0) {
                    $projectId = $detail->project_id ?? $stockRequest->project_id;

                    StockMovement::create([
                        'stock_item_id' => $detail->stock_item_id,
                        'user_id' => $stockRequest->requester_id,
                        'type' => 'out',
                        'quantity' => $detail->approved_quantity,
                        'reason' => 'Stock request fulfillment',
                        'notes' => "Request ID: {$stockRequest->id}, Reason: {$detail->request_reason}",
                        'reference' => "REQUEST-{$stockRequest->id}-{$detail->id}",
                        'project_id' => $projectId,
                        'purpose' => 'Stock request',
                    ]);

                    // Update project stock balance
                    ProjectStockService::updateBalance(
                        $projectId,
                        $detail->stock_item_id,
                        $detail->approved_quantity,
                        'out'
                    );
                }
            }
        });

        return redirect()->route('stock-requests.show', $stockRequest)
            ->with('success', 'Demande satisfaite avec succès. Les mouvements de stock ont été créés.');
    }

    // PDF generation
    public function pdf(StockRequest $stockRequest)
    {
        $this->authorize('view', $stockRequest);

        $stockRequest->load([
            'requester',
            'project',
            'facilityManager',
            'dataManager',
            'details.stockItem',
            'details.project'
        ]);

        // try to use DOMPDF if available
        if (class_exists('\Barryvdh\DomPDF\Facades\Pdf')) {
            $pdf = \Barryvdh\DomPDF\Facades\Pdf::loadView('stock_requests.pdf', compact('stockRequest'));
            return $pdf->download('stock_request_'.$stockRequest->id.'.pdf');
        }

        // fallback: render printable HTML view
        return view('stock_requests.pdf', compact('stockRequest'));
    }
}