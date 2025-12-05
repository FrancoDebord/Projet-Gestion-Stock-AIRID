<?php

namespace App\Http\Controllers;

use App\Models\StockLocation;
use App\Models\User;
use App\Models\StockMovement;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockLocationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (! $user->hasPermission('view_stock') && ! $user->hasPermission('view_reports')) {
            abort(403);
        }

        $locations = StockLocation::with('principalManager', 'creatorUser')
            ->orderBy('stock_name')
            ->paginate(12);

        return view('stock_locations.index', compact('locations'));
    }

    public function create()
    {
        $user = Auth::user();
        if (! $user->hasPermission('create_stock') && ! $user->hasPermission('manage_settings')) {
            abort(403);
        }

        $users = User::orderBy('name')->get();
        return view('stock_locations.create', compact('users'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (! $user->hasPermission('create_stock') && ! $user->hasPermission('manage_settings')) {
            abort(403);
        }

        $data = $request->validate([
            'stock_name' => 'required|string|max:150',
            'code_stock' => 'nullable|string|max:100',
            'principal_manager' => 'nullable|exists:users,id',
            'description' => 'nullable|string|max:2000',
        ]);

        $data['creator'] = $user->id;
        $data['creation_date'] = now();

        $location = StockLocation::create($data);

        return redirect()->route('stock-locations.index')->with('success', 'Emplacement créé avec succès.');
    }

    public function show(StockLocation $stockLocation, Request $request)
    {
        $user = Auth::user();
        if (! $user->hasPermission('view_stock')) {
            abort(403);
        }

        $location = $stockLocation->load('items', 'principalManager', 'creatorUser');
        
        // Get items at this location with movements
        $items = $location->items()->with('movements.project')->get();
        
        // Find critical items (quantity <= min_quantity)
        $criticalItems = $items->filter(function ($item) {
            return $item->initial_quantity <= $item->min_quantity;
        });
        
        // Fetch movements for this location's items with filtering
        $movementsQuery = StockMovement::whereIn('stock_item_id', $items->pluck('id'))
            ->with('stockItem', 'user', 'project');
        
        // Apply filters from request
        if ($request->has('movement_type') && $request->movement_type) {
            $movementsQuery->where('type', $request->movement_type);
        }
        
        if ($request->has('date_from') && $request->date_from) {
            $movementsQuery->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $movementsQuery->whereDate('created_at', '<=', $request->date_to);
        }
        
        if ($request->has('project_id') && $request->project_id) {
            $movementsQuery->where('project_id', $request->project_id);
        }
        
        $movements = $movementsQuery->orderBy('created_at', 'desc')->paginate(15);
        
        // Calculate statistics
        $totalIn = StockMovement::whereIn('stock_item_id', $items->pluck('id'))
            ->where('type', 'in')
            ->sum('quantity');
        
        $totalOut = StockMovement::whereIn('stock_item_id', $items->pluck('id'))
            ->where('type', 'out')
            ->sum('quantity');
        
        $totalAdjustments = StockMovement::whereIn('stock_item_id', $items->pluck('id'))
            ->where('type', 'adjustment')
            ->count();
        
        // Get all projects for filter dropdown
        $projects = Project::orderBy('name')->get();
        
        return view('stock_locations.show', compact(
            'location', 'items', 'criticalItems', 'movements', 
            'totalIn', 'totalOut', 'totalAdjustments', 'projects'
        ));
    }

    public function edit(StockLocation $stockLocation)
    {
        $user = Auth::user();
        if (! $user->hasPermission('edit_stock') && ! $user->hasPermission('manage_settings')) {
            abort(403);
        }

        $users = User::orderBy('name')->get();
        return view('stock_locations.edit', compact('stockLocation', 'users'));
    }

    public function update(Request $request, StockLocation $stockLocation)
    {
        $user = Auth::user();
        if (! $user->hasPermission('edit_stock') && ! $user->hasPermission('manage_settings')) {
            abort(403);
        }

        $data = $request->validate([
            'stock_name' => 'required|string|max:150',
            'code_stock' => 'nullable|string|max:100',
            'principal_manager' => 'nullable|exists:users,id',
            'description' => 'nullable|string|max:2000',
        ]);

        $stockLocation->update($data);

        return redirect()->route('stock-locations.show', $stockLocation)->with('success', 'Emplacement mis à jour.');
    }

    public function destroy(StockLocation $stockLocation)
    {
        $user = Auth::user();
        if (! $user->hasPermission('delete_stock') && ! $user->hasPermission('manage_settings')) {
            abort(403);
        }

        $stockLocation->delete();
        return redirect()->route('stock-locations.index')->with('success', 'Emplacement supprimé.');
    }
}
