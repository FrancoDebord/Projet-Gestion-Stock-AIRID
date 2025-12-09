<?php

namespace App\Http\Controllers;

use App\Models\StockItem;
use App\Models\StockLocation;
use App\Models\ProductCategory;
use App\Models\SubCategory;
use App\Models\StockMovement;
use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StockItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasPermission('view_stock')) {
            abort(403);
        }

        $query = StockItem::with('stockLocation', 'movements');

        // Filter by location
        if ($request->has('location_id') && $request->location_id) {
            $query->where('stock_location_id', $request->location_id);
        }

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        // Search by name
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $items = $query->orderBy('name')->paginate(20);
        $locations = StockLocation::orderBy('stock_name')->get();
        $categories = ProductCategory::orderBy('name')->get();

        return view('stock_items.index', compact('items', 'locations', 'categories'));
    }

    public function create()
    {
        $user = Auth::user();
        if (!$user->hasPermission('create_stock') && !$user->hasPermission('manage_settings')) {
            abort(403);
        }

        $locations = StockLocation::orderBy('stock_name')->get();
        $categories = ProductCategory::orderBy('name')->get();

        return view('stock_items.create', compact('locations', 'categories'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasPermission('create_stock') && !$user->hasPermission('manage_settings')) {
            abort(403);
        }

        $data = $request->validate([
            'name' => 'required|string|max:150',
            'brand' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:100',
            'product_category_id' => 'nullable|exists:product_categories,id',
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'description' => 'nullable|string|max:2000',
            'initial_quantity' => 'required|integer|min:0',
            'min_quantity' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'unit_price' => 'nullable|numeric|min:0',
            'stock_location_id' => 'required|exists:stock_locations,id',
            'type_usage_product' => 'required|in:finished,consumed',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('stock_items', 'public');
            $data['image'] = $imagePath;
        }

        // If product_category_id provided, store its name in the legacy 'category' string for compatibility
        if (!empty($data['product_category_id'])) {
            $pc = ProductCategory::find($data['product_category_id']);
            if ($pc) {
                $data['category'] = $pc->name;
            }
        }

        //initial_quantity
        $data['quantity'] = $data['initial_quantity'];

        StockItem::create($data);

        return redirect()->route('stock-items.index')
            ->with('success', 'Article créé avec succès.');
    }

    public function show(StockItem $stockItem)
    {
        $user = Auth::user();
        if (!$user->hasPermission('view_stock')) {
            abort(403);
        }
        $stockItem->load('stockLocation', 'movements.user', 'movements.project', 'productCategory', 'subCategory');

        // Filters from request
        $from = request('from') ? Carbon::parse(request('from'))->startOfDay() : null;
        $to = request('to') ? Carbon::parse(request('to'))->endOfDay() : null;
        $projectFilter = request('project_id');
        $requesterFilter = request('requester_id');

        // Base movements query for this item
        $movQuery = $stockItem->movements()->with('user', 'project');

        if ($from) {
            $movQuery->where('created_at', '>=', $from);
        }
        if ($to) {
            $movQuery->where('created_at', '<=', $to);
        }
        if ($projectFilter) {
            $movQuery->where('project_id', $projectFilter);
        }
        if ($requesterFilter) {
            $movQuery->where('user_id', $requesterFilter);
        }

        // Movement statistics
        $movementsIn = (clone $movQuery)->where('type', 'in')->sum('quantity');
        $movementsOut = (clone $movQuery)->where('type', 'out')->sum('quantity');
        $totalMovements = (clone $movQuery)->count();

        // Movements with pagination
        $movements = $movQuery->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        // Project usage statistics (respecting date/requester filters)
        $projectUsageQuery = StockMovement::where('stock_item_id', $stockItem->id)->whereNotNull('project_id')->where('type', 'out');
        if ($from) { $projectUsageQuery->where('created_at', '>=', $from); }
        if ($to) { $projectUsageQuery->where('created_at', '<=', $to); }
        if ($requesterFilter) { $projectUsageQuery->where('user_id', $requesterFilter); }

        if ($projectFilter) {
            $projectUsageQuery->where('project_id', $projectFilter);
        }

        $projectUsage = $projectUsageQuery->groupBy('project_id')
            ->selectRaw('project_id, COUNT(*) as count, SUM(quantity) as total_qty')
            ->with('project')
            ->get();

        $projects = Project::orderBy('name')->get();
        $users = User::orderBy('name')->get();

        // Daily usage series for chart (respecting filters)
        $dailyQuery = StockMovement::where('stock_item_id', $stockItem->id)->where('type', 'out');
        if ($from) { $dailyQuery->where('created_at', '>=', $from); }
        if ($to) { $dailyQuery->where('created_at', '<=', $to); }
        if ($projectFilter) { $dailyQuery->where('project_id', $projectFilter); }
        if ($requesterFilter) { $dailyQuery->where('user_id', $requesterFilter); }

        $dailyUsage = $dailyQuery->selectRaw('DATE(created_at) as date, SUM(quantity) as total_qty')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('stock_items.show', compact(
            'stockItem',
            'movements',
            'movementsIn',
            'movementsOut',
            'totalMovements',
            'projectUsage',
            'projects',
            'users'
            , 'dailyUsage'
        ));
    }

    // AJAX endpoint: categories by location
    public function getCategoriesByLocation(Request $request)
    {
        $locationId = $request->get('location_id');
        if (!$locationId) {
            return response()->json([], 200);
        }

        $categories = ProductCategory::where('stock_location_id', $locationId)->orderBy('name')->get(['id', 'name']);
        return response()->json($categories);
    }

    // AJAX endpoint: subcategories by product category
    public function getSubCategoriesByCategory(Request $request)
    {
        $categoryId = $request->get('category_id');
        if (!$categoryId) {
            return response()->json([], 200);
        }

        $subs = SubCategory::where('product_category_id', $categoryId)->orderBy('name')->get(['id', 'name']);
        return response()->json($subs);
    }

    public function edit(StockItem $stockItem)
    {
        $user = Auth::user();
        if (!$user->hasPermission('edit_stock') && !$user->hasPermission('manage_settings')) {
            abort(403);
        }

        $locations = StockLocation::orderBy('stock_name')->get();
        $categories = ProductCategory::orderBy('name')->get();

        return view('stock_items.edit', compact('stockItem', 'locations', 'categories'));
    }

    public function update(Request $request, StockItem $stockItem)
    {
        $user = Auth::user();
        if (!$user->hasPermission('edit_stock') && !$user->hasPermission('manage_settings')) {
            abort(403);
        }

        $data = $request->validate([
            'name' => 'required|string|max:150',
            'brand' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:100',
            'product_category_id' => 'nullable|exists:product_categories,id',
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'description' => 'nullable|string|max:2000',
            'initial_quantity' => 'required|integer|min:0',
            'min_quantity' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'unit_price' => 'nullable|numeric|min:0',
            'stock_location_id' => 'required|exists:stock_locations,id',
            'type_usage_product' => 'required|in:finished,consumed',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            if ($stockItem->image) {
                Storage::disk('public')->delete($stockItem->image);
            }
            $imagePath = $request->file('image')->store('stock_items', 'public');
            $data['image'] = $imagePath;
        }

        if (!empty($data['product_category_id'])) {
            $pc = ProductCategory::find($data['product_category_id']);
            if ($pc) {
                $data['category'] = $pc->name;
            }
        }

           //initial_quantity
        $data['quantity'] = $data['initial_quantity'];
        
        $stockItem->update($data);

        return redirect()->route('stock-items.show', $stockItem)
            ->with('success', 'Article mis à jour.');
    }

    public function destroy(StockItem $stockItem)
    {
        $user = Auth::user();
        if (!$user->hasPermission('delete_stock') && !$user->hasPermission('manage_settings')) {
            abort(403);
        }

        // Delete image
        if ($stockItem->image) {
            Storage::disk('public')->delete($stockItem->image);
        }

        $stockItem->delete();

        return redirect()->route('stock-items.index')
            ->with('success', 'Article supprimé.');
    }
}
