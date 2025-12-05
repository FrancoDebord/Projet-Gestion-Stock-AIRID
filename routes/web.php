<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StockItemController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;


Route::get('/stock/locations', [App\Http\Controllers\InterfaceController::class, 'dashboard'])->name('stock.locations');
Route::get('/stock/items', [App\Http\Controllers\InterfaceController::class, 'dashboard'])->name('stock.items');
Route::get('/shipment/reception', [App\Http\Controllers\InterfaceController::class, 'dashboard'])->name('shipment.reception');
Route::get('/stock/reception', [App\Http\Controllers\InterfaceController::class, 'dashboard'])->name('stock.reception');
Route::get('/stock/movements', [App\Http\Controllers\InterfaceController::class, 'dashboard'])->name('stock.movements');
Route::get('/statistics', [App\Http\Controllers\InterfaceController::class, 'dashboard'])->name('statistics');


Route::get("/", [App\Http\Controllers\InterfaceController::class, 'dashboard'])
// ->middleware(['auth', 'verified'])
->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Stock management routes
Route::middleware('auth')->prefix('stocks')->group(function () {
    Route::get('/', [StockItemController::class, 'index'])->name('stocks.index');
    Route::get('/create', [StockItemController::class, 'create'])->name('stocks.create');
    Route::post('/', [StockItemController::class, 'store'])->name('stocks.store');
    Route::get('/{stock}', [StockItemController::class, 'show'])->name('stocks.show');
    Route::get('/{stock}/edit', [StockItemController::class, 'edit'])->name('stocks.edit');
    Route::patch('/{stock}', [StockItemController::class, 'update'])->name('stocks.update');
    Route::delete('/{stock}', [StockItemController::class, 'destroy'])->name('stocks.destroy');
});

// Stock movement routes
Route::middleware('auth')->prefix('movements')->group(function () {
    Route::get('/', [StockMovementController::class, 'index'])->name('movements.index');
    Route::get('/in/create', [StockMovementController::class, 'createIn'])->name('movements.create-in');
    Route::post('/in', [StockMovementController::class, 'storeIn'])->name('movements.store-in');
    Route::get('/out/create', [StockMovementController::class, 'createOut'])->name('movements.create-out');
    Route::post('/out', [StockMovementController::class, 'storeOut'])->name('movements.store-out');
    Route::get('/adjustment/create', [StockMovementController::class, 'createAdjustment'])->name('movements.create-adjustment');
    Route::post('/adjustment', [StockMovementController::class, 'storeAdjustment'])->name('movements.store-adjustment');
});

// Shipments: administration reception and finalization
use App\Http\Controllers\ShipmentController;

Route::middleware('auth')->prefix('shipments')->group(function () {
    Route::get('/', [ShipmentController::class, 'index'])->name('shipments.index');
    Route::get('/admin/create', [ShipmentController::class, 'createAdmin'])->name('shipments.admin.create');
    Route::post('/admin', [ShipmentController::class, 'storeAdmin'])->name('shipments.admin.store');
    Route::get('/{shipment}', [ShipmentController::class, 'show'])->name('shipments.show');
    Route::post('/{shipment}/finalize', [ShipmentController::class, 'finalize'])->name('shipments.finalize');
    Route::get('/{shipment}/ack', [ShipmentController::class, 'downloadAck'])->name('shipments.ack');
});

// User management routes
Route::middleware('auth')->prefix('users')->group(function () {
    Route::get('/', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('/{user}', [UserManagementController::class, 'show'])->name('users.show');
    Route::get('/{user}/edit-roles', [UserManagementController::class, 'editRoles'])->name('users.edit-roles');
    Route::patch('/{user}/roles', [UserManagementController::class, 'updateRoles'])->name('users.update-roles');
});

// Stock locations CRUD
use App\Http\Controllers\StockLocationController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\StockArrivalAdministrationController;

Route::middleware('auth')->prefix('stock-locations')->group(function () {
    Route::get('/', [StockLocationController::class, 'index'])->name('stock-locations.index');
    Route::get('/create', [StockLocationController::class, 'create'])->name('stock-locations.create');
    Route::post('/', [StockLocationController::class, 'store'])->name('stock-locations.store');
    Route::get('/{stockLocation}', [StockLocationController::class, 'show'])->name('stock-locations.show');
    Route::get('/{stockLocation}/edit', [StockLocationController::class, 'edit'])->name('stock-locations.edit');
    Route::patch('/{stockLocation}', [StockLocationController::class, 'update'])->name('stock-locations.update');
    Route::delete('/{stockLocation}', [StockLocationController::class, 'destroy'])->name('stock-locations.destroy');
});

// Stock items CRUD
Route::middleware('auth')->prefix('stock-items')->group(function () {
    Route::get('/', [StockItemController::class, 'index'])->name('stock-items.index');
    Route::get('/create', [StockItemController::class, 'create'])->name('stock-items.create');
    Route::post('/', [StockItemController::class, 'store'])->name('stock-items.store');
    // AJAX endpoints for dynamic selects
    Route::get('/ajax/categories', [StockItemController::class, 'getCategoriesByLocation'])->name('stock-items.ajax.categories');
    Route::get('/ajax/subcategories', [StockItemController::class, 'getSubCategoriesByCategory'])->name('stock-items.ajax.subcategories');
    Route::get('/{stockItem}', [StockItemController::class, 'show'])->name('stock-items.show');
    Route::get('/{stockItem}/edit', [StockItemController::class, 'edit'])->name('stock-items.edit');
    Route::patch('/{stockItem}', [StockItemController::class, 'update'])->name('stock-items.update');
    Route::delete('/{stockItem}', [StockItemController::class, 'destroy'])->name('stock-items.destroy');
});

// Product categories CRUD
Route::middleware('auth')->prefix('product-categories')->group(function () {
    Route::get('/', [ProductCategoryController::class, 'index'])->name('product-categories.index');
    Route::get('/create', [ProductCategoryController::class, 'create'])->name('product-categories.create');
    Route::post('/', [ProductCategoryController::class, 'store'])->name('product-categories.store');
    Route::get('/{productCategory}', [ProductCategoryController::class, 'show'])->name('product-categories.show');
    Route::get('/{productCategory}/edit', [ProductCategoryController::class, 'edit'])->name('product-categories.edit');
    Route::patch('/{productCategory}', [ProductCategoryController::class, 'update'])->name('product-categories.update');
    Route::delete('/{productCategory}', [ProductCategoryController::class, 'destroy'])->name('product-categories.destroy');
});

// Stock arrival administration CRUD
Route::middleware('auth')->prefix('stock-arrivals-admin')->group(function () {
    Route::get('/', [StockArrivalAdministrationController::class, 'index'])->name('stock-arrivals-admin.index');
    Route::get('/create', [StockArrivalAdministrationController::class, 'create'])->name('stock-arrivals-admin.create');
    Route::post('/', [StockArrivalAdministrationController::class, 'store'])->name('stock-arrivals-admin.store');
    Route::get('/{stockArrivalAdministration}', [StockArrivalAdministrationController::class, 'show'])->name('stock-arrivals-admin.show');
    Route::get('/{stockArrivalAdministration}/edit', [StockArrivalAdministrationController::class, 'edit'])->name('stock-arrivals-admin.edit');
    Route::patch('/{stockArrivalAdministration}', [StockArrivalAdministrationController::class, 'update'])->name('stock-arrivals-admin.update');
    Route::delete('/{stockArrivalAdministration}', [StockArrivalAdministrationController::class, 'destroy'])->name('stock-arrivals-admin.destroy');
    Route::get('/{stockArrivalAdministration}/pdf', [StockArrivalAdministrationController::class, 'pdf'])->name('stock-arrivals-admin.pdf');
});

// Stock receptions CRUD
use App\Http\Controllers\StockReceptionController;

Route::middleware('auth')->prefix('stock-receptions')->group(function () {
    Route::get('/', [StockReceptionController::class, 'index'])->name('stock-receptions.index');
    Route::get('/create', [StockReceptionController::class, 'create'])->name('stock-receptions.create');
    Route::post('/', [StockReceptionController::class, 'store'])->name('stock-receptions.store');
    Route::get('/{stockReception}', [StockReceptionController::class, 'show'])->name('stock-receptions.show');
    Route::get('/{stockReception}/edit', [StockReceptionController::class, 'edit'])->name('stock-receptions.edit');
    Route::patch('/{stockReception}', [StockReceptionController::class, 'update'])->name('stock-receptions.update');
    Route::delete('/{stockReception}', [StockReceptionController::class, 'destroy'])->name('stock-receptions.destroy');
    Route::get('/{stockReception}/pdf', [StockReceptionController::class, 'pdf'])->name('stock-receptions.pdf');
});

require __DIR__.'/auth.php';
