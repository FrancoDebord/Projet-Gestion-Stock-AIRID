<?php

namespace App\Http\Controllers;

use App\Models\StockItem;
use App\Models\StockMovement;
use App\Models\ActivityLog;
use App\Models\StockIncomingRecordDetail;
use App\Models\StockRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $incomingDetails = StockIncomingRecordDetail::leftJoin('stock_movements', 'stock_movements.stock_incoming_detail_id', '=', 'stock_incoming_record_details.id')
            ->whereNull('stock_movements.stock_incoming_detail_id')
            ->select('stock_incoming_record_details.*')
            ->orderBy('id', 'desc')
            ->get();
        return view('movements.create-in', compact('stocks', 'incomingDetails'));
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
        $usageRequests = StockRequest::whereIn('status', ['approved_facility_manager', 'approved_data_manager'])
            ->latest('request_date')
            ->get();
        return view('movements.create-out', compact('stocks', 'usageRequests'));
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
        return view('movements.create-adjustment', compact('stocks'));
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
        ]);

        $stock = StockItem::findOrFail($validated['stock_item_id']);
        $oldQuantity = $stock->quantity;

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
        ]);

        // Update stock quantity
        $stock->increment('quantity', $validated['quantity']);

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
        ]);

        $stock = StockItem::findOrFail($validated['stock_item_id']);

        if ($stock->quantity < $validated['quantity']) {
            return back()->with('error', 'Insufficient stock quantity');
        }

        $oldQuantity = $stock->quantity;

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
        ]);

        // Update stock quantity
        $stock->decrement('quantity', $validated['quantity']);

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
        ]);

        $stock = StockItem::findOrFail($validated['stock_item_id']);
        $oldQuantity = $stock->quantity;
        $adjustmentQuantity = $validated['quantity'];

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
        ]);

        
        
        

        // Update stock quantity
        $stock->quantity += $adjustmentQuantity;
        $stock->save();

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
