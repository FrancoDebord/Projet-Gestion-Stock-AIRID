<?php

namespace App\Http\Controllers;

use App\Models\StockItem;
use App\Models\StockMovement;
use App\Models\ActivityLog;
use App\Models\StockIncomingRecordDetail;
use App\Models\StockIncomingRecord;
use App\Models\StockRequest;
use App\Services\ProjectStockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Project;

class StockMovementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of stock movements.
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user->hasPermission('view_movements')) {
            return redirect('/dashboard')->with('error', 'Unauthorized');
        }

        $movements = StockMovement::with(['stockItem', 'user'])
            ->latest()
            ->paginate(20);

        return view('movements.index', compact('movements'));
    }

    /**
     * Show the form for recording stock in.
     */
    public function createIn()
    {
        $user = Auth::user();

        if (!$user->hasPermission('record_stock_in')) {
            abort(403, 'Unauthorized');
        }

        $stocks = StockItem::all();
        $projects = Project::all();
        $incomingDetails = StockIncomingRecordDetail::leftJoin('stock_movements', 'stock_movements.stock_incoming_detail_id', '=', 'stock_incoming_record_details.id')
            ->whereNull('stock_movements.stock_incoming_detail_id')
            ->select('stock_incoming_record_details.*')
            ->orderBy('id', 'desc')
            ->get();
        return view('movements.create-in', compact('stocks', 'projects', 'incomingDetails'));
    }

    /**
     * Show the form for recording stock out.
     */
    public function createOut()
    {
        $user = Auth::user();

        if (!$user->hasPermission('record_stock_out')) {
            abort(403, 'Unauthorized');
        }

        $stocks = StockItem::where('quantity', '>', 0)->get();
        $projects = Project::orderBy('name')->get();

        // Only show approved requests that haven't been fulfilled yet
        $usageRequests = StockRequest::with(['details.stockItem', 'details.project', 'requester', 'project'])
            ->where('status', 'approved_facility_manager')
            ->whereHas('details', function ($query) {
                $query->where('facility_manager_approval', true)
                    //   ->where('data_manager_approval', true)
                      ->where('approved_quantity', '>', 0);
            })
            ->latest('request_date')
            ->get();

        return view('movements.create-out', compact('stocks', 'projects', 'usageRequests'));
    }

    /**
     * Show the form for fulfilling approved requests.
     */
    public function fulfillRequest(Request $request)
    {
        $user = Auth::user();


        if (!$user->hasPermission('record_stock_out')) {
            abort(403, 'Unauthorized');
        }

        // Get approved requests that can be fulfilled
        $approvedRequests = StockRequest::with(['details.stockItem', 'details.project', 'requester', 'project'])
            ->where('status', 'approved_facility_manager')
            ->whereHas('details', function ($query) {
                // $query->where('facility_manager_approval', true)
                //       ->where('data_manager_approval', true)
                      $query->where('approved_quantity', '>', 0);
            })
            ->latest('request_date')
            ->get();

        \Log::info('Fulfill Request - User: ' . $user->id . ', Approved requests found: ' . $approvedRequests->count());

        // #region agent log
        file_put_contents('e:\Projets_CREC\Projet-Gestion-Stock\.cursor\debug.log', json_encode([
            'id' => 'log_' . time() . '_' . rand(1000, 9999),
            'timestamp' => time() * 1000,
            'location' => 'StockMovementController.php:113',
            'message' => 'Fulfill request method called',
            'data' => [
                'user_id' => $user->id,
                'request_id' => $request->get('request_id'),
                'approved_requests_count' => $approvedRequests->count()
            ],
            'sessionId' => 'debug-session',
            'runId' => 'debug-run-1',
            'hypothesisId' => 'A'
        ]) . "\n", FILE_APPEND);
        // #endregion

        // If a specific request is selected
        if ($request->has('request_id')) {
            $selectedRequest = StockRequest::with(['details.stockItem', 'details.project', 'requester', 'project'])
                ->findOrFail($request->request_id);

            // Verify the request can be fulfilled
            if (!$selectedRequest->isApproved()) {
                return redirect()->route('movements.fulfill-request')
                    ->with('error', 'Cette demande ne peut pas être satisfaite.');
            }


            return view('movements.fulfill-request', compact('approvedRequests', 'selectedRequest'));
        }

        // Debug: Check if view exists and is accessible
        try {
            return view('movements.fulfill-request', compact('approvedRequests'));
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'View not found',
                'message' => $e->getMessage(),
                'approved_requests_count' => $approvedRequests->count()
            ]);
        }
    }

    /**
     * Process the fulfillment of an approved request.
     */
    public function processFulfillment(Request $request, StockRequest $stockRequest)
    {
        $user = Auth::user();

        if (!$user->hasPermission('record_stock_out')) {
            abort(403, 'Unauthorized');
        }

        // Verify the request can be fulfilled
        if (!$stockRequest->isApproved()) {
            return redirect()->route('movements.fulfill-request')
                ->with('error', 'Cette demande ne peut pas être satisfaite.');
        }

        DB::transaction(function () use ($stockRequest, $user, $request) {
            // Update request status to completed
            $stockRequest->update(['status' => 'completed']);

            // Process each approved detail
            foreach ($stockRequest->details as $detail) {
                if ($detail->isApproved() && $detail->approved_quantity > 0) {
                    $stockItem = $detail->stockItem;

                    // Check availability with Global + project logic
                    $projectId = $detail->project_id ?? $stockRequest->project_id;
                    $availability = ProjectStockService::checkAvailability(
                        $stockItem->id,
                        $projectId,
                        $detail->approved_quantity
                    );

                    if (!$availability['can_fulfill']) {
                        throw new \Exception("Stock insuffisant pour l'article: {$stockItem->name}");
                    }

                    // Use the actual project from availability check
                    $actualProjectId = $availability['source_project']->id;

                    // Create movement record
                    StockMovement::create([
                        'stock_item_id' => $stockItem->id,
                        'user_id' => $user->id,
                        'type' => 'out',
                        "date_mouvement" => now(),
                        'quantity' => $detail->approved_quantity,
                        'reason' => 'Stock request fulfillment',
                        'notes' => "Demande #{$stockRequest->id} - {$detail->request_reason}",
                        'reference' => "FULFILL-{$stockRequest->id}-{$detail->id}",
                        'project_id' => $actualProjectId,
                        'stock_item_usage_request_id' => $stockRequest->id,
                        'purpose' => 'Stock request',
                    ]);

                    // Update stock quantity
                    $stockItem->decrement('quantity', $detail->approved_quantity);

                    // Update project stock balance
                    ProjectStockService::updateBalance(
                        $actualProjectId,
                        $stockItem->id,
                        $detail->approved_quantity,
                        'out'
                    );
                }
            }
        });

        return redirect()->route('movements.fulfill-request')
            ->with('success', 'Demande satisfaite avec succès. Les mouvements de stock ont été créés.');
    }

    /**
     * Show the form for processing stock receptions.
     */
    public function processReception(Request $request)
    {
        $user = Auth::user();

        if (!$user->hasPermission('record_stock_in')) {
            abort(403, 'Unauthorized');
        }

        // Get available receptions that haven't been processed yet
        $availableReceptions = StockIncomingRecord::with(['stockArrivalAdministration', 'details.stockItem', 'receiver', 'project'])
            ->whereDoesntHave('details.stockMovement') // Not yet processed into movements
            ->orderBy('date_reception', 'desc')
            ->get();

        // #region agent log
        file_put_contents('e:\Projets_CREC\Projet-Gestion-Stock\.cursor\debug.log', json_encode([
            'id' => 'log_' . time() . '_' . rand(1000, 9999),
            'timestamp' => time() * 1000,
            'location' => 'StockMovementController.php:246',
            'message' => 'Process reception method called',
            'data' => [
                'user_id' => $user->id,
                'reception_id' => $request->get('reception_id'),
                'available_receptions_count' => $availableReceptions->count()
            ],
            'sessionId' => 'debug-session',
            'runId' => 'debug-run-1',
            'hypothesisId' => 'B'
        ]) . "\n", FILE_APPEND);
        // #endregion

        // If a specific reception is selected
        if ($request->has('reception_id')) {
            $selectedReception = StockIncomingRecord::with(['stockArrivalAdministration', 'details.stockItem', 'receiver', 'project'])
                ->findOrFail($request->reception_id);

            // Check if already processed
            if ($selectedReception->details()->whereHas('stockMovement')->exists()) {
                return redirect()->route('movements.process-reception')
                    ->with('error', 'Cette réception a déjà été traitée.');
            }

            return view('movements.process-reception', compact('availableReceptions', 'selectedReception'));
        }

        return view('movements.process-reception', compact('availableReceptions'));
    }

    /**
     * Process the selected stock reception into movements.
     */
    public function processReceptionMovements(Request $request, StockIncomingRecord $stockIncomingRecord)
    {
        $user = Auth::user();

        if (!$user->hasPermission('record_stock_in')) {
            abort(403, 'Unauthorized');
        }

        // Check if already processed
        if ($stockIncomingRecord->details()->whereHas('stockMovement')->exists()) {
            return redirect()->route('movements.process-reception')
                ->with('error', 'Cette réception a déjà été traitée.');
        }

        DB::transaction(function () use ($stockIncomingRecord, $user, $request) {
            // Process each detail
            foreach ($stockIncomingRecord->details as $detail) {
                $stockItem = $detail->stockItem;

                // Default to Global project if none specified
                $projectId = $stockIncomingRecord->project_id ?? ProjectStockService::getGlobalProject()->id;

                // Create movement record
                $movement = StockMovement::create([
                    'stock_item_id' => $stockItem->id,
                    'user_id' => $user->id,
                    'type' => 'in',
                    'quantity' => $detail->quantite_lot,
                    'reason' => 'Stock reception processing',
                    'notes' => "Réception #{$stockIncomingRecord->id} - Lot: {$detail->code_lot}",
                    'reference' => "RECEPTION-{$stockIncomingRecord->id}-{$detail->id}",
                    'project_id' => $projectId,
                    'batch_number' => $detail->batch_number,
                    'stock_incoming_detail_id' => $detail->id,
                    'purpose' => 'Stock reception',
                ]);

                // Update stock quantity
                $stockItem->increment('quantity', $detail->quantite_lot);

                // Update project stock balance
                ProjectStockService::updateBalance(
                    $projectId,
                    $stockItem->id,
                    $detail->quantite_lot,
                    'in'
                );

                // Link the movement to the detail
                $detail->update(['stock_movement_id' => $movement->id]);
            }
        });

        return redirect()->route('movements.process-reception')
            ->with('success', 'Réception traitée avec succès. Les mouvements de stock ont été créés.');
    }

    /**
     * Show the form for adjusting stock.
     */
    public function createAdjustment()
    {
        $user = Auth::user();

        if (!$user->hasPermission('adjust_stock')) {
            abort(403, 'Unauthorized');
        }

        $stocks = StockItem::all();
        $projects = Project::all();
        return view('movements.create-adjustment', compact('stocks', 'projects'));
    }

    /**
     * Record stock incoming.
     */
    public function storeIn(Request $request)
    {
        $user = Auth::user();

        if (!$user->hasPermission('record_stock_in')) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'stock_item_id' => 'required|exists:stock_items,id',
            'quantity' => 'required|integer|min:1',
            'reason' => 'nullable|string',
            'notes' => 'nullable|string',
            'reference' => 'nullable|string|max:255',
            'batch_number' => 'nullable|string|max:100',
            'stock_incoming_detail_id' => 'nullable|exists:stock_incoming_record_details,id',
            'date_mouvement' => 'nullable|date',
            'stock_item_usage_request_id' => 'nullable|exists:stock_item_usage_requests,id',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        $stock = StockItem::findOrFail($validated['stock_item_id']);
        $oldQuantity = $stock->quantity;

        // Default to Global project if none specified
        $projectId = $validated['project_id'] ?? ProjectStockService::getGlobalProject()->id;

        // Create movement record
        $movement = StockMovement::create([
            'stock_item_id' => $stock->id,
            'user_id' => $user->id,
            'type' => 'in',
            'quantity' => $validated['quantity'],
            'reason' => $validated['reason'],
            'notes' => $validated['notes'],
            'reference' => $validated['reference'],
            'batch_number' => $validated['batch_number'] ?? null,
            'stock_incoming_detail_id' => $validated['stock_incoming_detail_id'] ?? null,
            'date_mouvement' => $validated['date_mouvement'] ?? now(),
            'stock_item_usage_request_id' => $validated['stock_item_usage_request_id'] ?? null,
            'project_id' => $projectId,
        ]);

        // Update stock quantity
        $stock->increment('quantity', $validated['quantity']);

        // Update project stock balance
        ProjectStockService::updateBalance(
            $projectId,
            $stock->id,
            $validated['quantity'],
            'in'
        );

        // Log activity
        $this->logActivity(
            'Recorded stock incoming',
            'stock_in',
            $stock,
            [
                'quantity' => $validated['quantity'],
                'old_quantity' => $oldQuantity,
                'new_quantity' => $stock->quantity,
                'reason' => $validated['reason'],
            ]
        );

        return redirect()->route('movements.index')->with('success', 'Stock incoming recorded successfully');
    }

    /**
     * Record stock outgoing.
     */
    public function storeOut(Request $request)
    {
        $user = Auth::user();

        if (!$user->hasPermission('record_stock_out')) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'stock_item_id' => 'required|exists:stock_items,id',
            'quantity' => 'required|integer|min:1',
            'reason' => 'nullable|string',
            'notes' => 'nullable|string',
            'reference' => 'nullable|string|max:255',
            'batch_number' => 'nullable|string|max:100',
            'stock_incoming_detail_id' => 'nullable|exists:stock_incoming_record_details,id',
            'date_mouvement' => 'nullable|date',
            'stock_item_usage_request_id' => 'nullable|exists:stock_item_usage_requests,id',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        $stock = StockItem::findOrFail($validated['stock_item_id']);

        // Check availability with Global + project logic
        $projectId = $validated['project_id'] ?? null;
        $availability = ProjectStockService::checkAvailability(
            $stock->id,
            $projectId,
            $validated['quantity']
        );

        if (!$availability['can_fulfill']) {
            return back()->with('error', 'Insufficient stock quantity for the selected project');
        }

        $oldQuantity = $stock->quantity;

        // Use the project from availability check
        $actualProjectId = $availability['source_project']->id;

        // Create movement record
        $movement = StockMovement::create([
            'stock_item_id' => $stock->id,
            'user_id' => $user->id,
            'type' => 'out',
            'quantity' => $validated['quantity'],
            'reason' => $validated['reason'],
            'notes' => $validated['notes'],
            'reference' => $validated['reference'],
            'batch_number' => $validated['batch_number'] ?? null,
            'stock_incoming_detail_id' => $validated['stock_incoming_detail_id'] ?? null,
            'date_mouvement' => $validated['date_mouvement'] ?? now(),
            'stock_item_usage_request_id' => $validated['stock_item_usage_request_id'] ?? null,
            'project_id' => $actualProjectId,
        ]);

        // Update stock quantity
        $stock->decrement('quantity', $validated['quantity']);

        // Update project stock balance
        ProjectStockService::updateBalance(
            $actualProjectId,
            $stock->id,
            $validated['quantity'],
            'out'
        );

        // Log activity
        $this->logActivity(
            'Recorded stock outgoing',
            'stock_out',
            $stock,
            [
                'quantity' => $validated['quantity'],
                'old_quantity' => $oldQuantity,
                'new_quantity' => $stock->quantity,
                'reason' => $validated['reason'],
            ]
        );

        return redirect()->route('movements.index')->with('success', 'Stock outgoing recorded successfully');
    }

    /**
     * Record stock adjustment.
     */
    public function storeAdjustment(Request $request)
    {
        $user = Auth::user();

        if (!$user->hasPermission('adjust_stock')) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'stock_item_id' => 'required|exists:stock_items,id',
            'quantity' => 'required|integer',
            'reason' => 'required|string',
            'notes' => 'nullable|string',
            'batch_number' => 'nullable|string|max:100',
            'stock_incoming_detail_id' => 'nullable|exists:stock_incoming_record_details,id',
            'date_mouvement' => 'nullable|date',
            'stock_item_usage_request_id' => 'nullable|exists:stock_item_usage_requests,id',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        $stock = StockItem::findOrFail($validated['stock_item_id']);
        $oldQuantity = $stock->quantity;
        $adjustmentQuantity = $validated['quantity'];

        // Default to Global project if none specified
        $projectId = $validated['project_id'] ?? ProjectStockService::getGlobalProject()->id;

        // Create movement record
        $movement = StockMovement::create([
            'stock_item_id' => $stock->id,
            'user_id' => $user->id,
            'type' => 'adjustment',
            'quantity' => abs($adjustmentQuantity),
            'reason' => $validated['reason'],
            'notes' => $validated['notes'],
            'batch_number' => $validated['batch_number'] ?? null,
            'stock_incoming_detail_id' => $validated['stock_incoming_detail_id'] ?? null,
            'date_mouvement' => $validated['date_mouvement'] ?? now(),
            'stock_item_usage_request_id' => $validated['stock_item_usage_request_id'] ?? null,
            'project_id' => $projectId,
        ]);

        // Update stock quantity
        $stock->quantity += $adjustmentQuantity;
        $stock->save();

        // Update project stock balance
        $type = $adjustmentQuantity > 0 ? 'in' : 'out';
        ProjectStockService::updateBalance(
            $projectId,
            $stock->id,
            abs($adjustmentQuantity),
            $type
        );

        // Log activity
        $this->logActivity(
            'Adjusted stock quantity',
            'stock_adjustment',
            $stock,
            [
                'adjustment' => $adjustmentQuantity,
                'old_quantity' => $oldQuantity,
                'new_quantity' => $stock->quantity,
                'reason' => $validated['reason'],
            ]
        );

        return redirect()->route('movements.index')->with('success', 'Stock adjustment recorded successfully');
    }

    /**
     * Log activity to audit trail.
     */
    protected function logActivity($description, $logName, $subject = null, $properties = [])
    {
        ActivityLog::create([
            'log_name' => $logName,
            'description' => $description,
            'subject_type' => $subject ? StockItem::class : null,
            'subject_id' => $subject?->id,
            'causer_type' => Auth::user()::class,
            'causer_id' => Auth::id(),
            'properties' => $properties,
        ]);
    }

    /**
     * Export movement to PDF or printable view.
     */
    public function pdf(StockMovement $movement)
    {
        $user = Auth::user();
        if (!$user->hasPermission('view_movements')) {
            abort(403);
        }

        $movement->load(['stockItem', 'user', 'incomingDetail.stockIncomingRecord', 'usageRequest.project']);

        if (class_exists('\Barryvdh\DomPDF\Facades\Pdf')) {
            $pdf = \Barryvdh\DomPDF\Facades\Pdf::loadView('movements.pdf', compact('movement'));
            return $pdf->download('stock_movement_'.$movement->id.'.pdf');
        }

        return view('movements.pdf', compact('movement'));
    }

    /**
     * Display a specific movement.
     */
    public function show(StockMovement $movement)
    {
        $user = Auth::user();
        if (!$user->hasPermission('view_movements')) {
            abort(403);
        }

        $movement->load(['stockItem', 'user', 'incomingDetail', 'usageRequest']);
        return view('movements.show', compact('movement'));
    }

    /**
     * Edit movement metadata.
     */
    public function edit(StockMovement $movement)
    {
        $user = Auth::user();
        if (!$user->hasPermission('manage_settings')) {
            abort(403);
        }

        $movement->load(['stockItem']);
        return view('movements.edit', compact('movement'));
    }

    /**
     * Update movement metadata.
     */
    public function update(Request $request, StockMovement $movement)
    {
        $user = Auth::user();
        if (!$user->hasPermission('manage_settings')) {
            abort(403);
        }

        $validated = $request->validate([
            'reason' => 'nullable|string',
            'notes' => 'nullable|string',
            'reference' => 'nullable|string|max:255',
            'batch_number' => 'nullable|string|max:100',
            'stock_incoming_detail_id' => 'nullable|exists:stock_incoming_record_details,id',
            'date_mouvement' => 'nullable|date',
            'stock_item_usage_request_id' => 'nullable|exists:stock_item_usage_requests,id',
        ]);

        $movement->update($validated);

        return redirect()->route('movements.show', $movement)->with('success', 'Mouvement mis à jour');
    }

    /**
     * Delete a movement (reverses stock for in/out).
     */
    public function destroy(StockMovement $movement)
    {
        $user = Auth::user();
        if (!$user->hasPermission('manage_settings')) {
            abort(403);
        }

        $stock = $movement->stockItem;

        if ($movement->type === 'in') {
            $stock->decrement('quantity', $movement->quantity);
        } elseif ($movement->type === 'out') {
            $stock->increment('quantity', $movement->quantity);
        } else {
            return back()->with('error', 'Suppression non autorisée pour les ajustements');
        }

        $movement->delete();

        return redirect()->route('movements.index')->with('success', 'Mouvement supprimé');
    }
}
