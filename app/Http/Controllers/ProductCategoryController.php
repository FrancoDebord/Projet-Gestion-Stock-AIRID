<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use App\Models\StockLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductCategoryController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user->hasPermission('view_stock') && !$user->hasPermission('manage_settings')) {
            abort(403);
        }

        $categories = ProductCategory::withCount('items')
            ->with('stockLocation')
            ->orderBy('name')
            ->paginate(20);

        return view('product_categories.index', compact('categories'));
    }

    public function create()
    {
        $user = Auth::user();
        if (!$user->hasPermission('create_stock') && !$user->hasPermission('manage_settings')) {
            abort(403);
        }

        $stockLocations = StockLocation::orderBy('stock_name')->get();
        return view('product_categories.create', compact('stockLocations'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasPermission('create_stock') && !$user->hasPermission('manage_settings')) {
            abort(403);
        }

        $data = $request->validate([
            'name' => 'required|string|max:150|unique:product_categories',
            'description' => 'nullable|string|max:2000',
            'stock_location_id' => 'nullable|exists:stock_locations,id',
        ]);

        ProductCategory::create($data);

        return redirect()->route('product-categories.index')
            ->with('success', 'Catégorie créée avec succès.');
    }

    public function show(ProductCategory $productCategory)
    {
        $user = Auth::user();
        if (!$user->hasPermission('view_stock')) {
            abort(403);
        }

        $items = $productCategory->items()->with('stockLocation')->paginate(15);

        return view('product_categories.show', compact('productCategory', 'items'));
    }

    public function edit(ProductCategory $productCategory)
    {
        $user = Auth::user();
        if (!$user->hasPermission('edit_stock') && !$user->hasPermission('manage_settings')) {
            abort(403);
        }

        $stockLocations = StockLocation::orderBy('stock_name')->get();
        return view('product_categories.edit', compact('productCategory', 'stockLocations'));
    }

    public function update(Request $request, ProductCategory $productCategory)
    {
        $user = Auth::user();
        if (!$user->hasPermission('edit_stock') && !$user->hasPermission('manage_settings')) {
            abort(403);
        }

        $data = $request->validate([
            'name' => 'required|string|max:150|unique:product_categories,name,' . $productCategory->id,
            'description' => 'nullable|string|max:2000',
            'stock_location_id' => 'nullable|exists:stock_locations,id',
        ]);

        $productCategory->update($data);

        return redirect()->route('product-categories.show', $productCategory)
            ->with('success', 'Catégorie mise à jour.');
    }

    public function destroy(ProductCategory $productCategory)
    {
        $user = Auth::user();
        if (!$user->hasPermission('delete_stock') && !$user->hasPermission('manage_settings')) {
            abort(403);
        }

        $productCategory->delete();

        return redirect()->route('product-categories.index')
            ->with('success', 'Catégorie supprimée.');
    }
}

