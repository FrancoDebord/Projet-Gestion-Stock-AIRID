<?php

namespace App\Http\Controllers;

use App\Models\StockItem;
use App\Models\StockMovement;
use App\Models\ActivityLog;
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
        return view('movements.create-in', compact('stocks'));
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
        return view('movements.create-out', compact('stocks'));
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
}
