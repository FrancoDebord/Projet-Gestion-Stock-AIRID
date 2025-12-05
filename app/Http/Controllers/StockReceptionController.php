<?php

namespace App\Http\Controllers;

use App\Models\StockArrivalAdministration;
use App\Models\StockIncomingRecord;
use App\Models\StockIncomingRecordDetail;
use App\Models\StockItem;
use App\Models\StockLocation;
use App\Models\StockMovement;
use App\Models\User;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class StockReceptionController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', StockIncomingRecord::class);

        $records = StockIncomingRecord::with([
            'stockArrivalAdministration',
            'receiver',
            'stockLocationDestination',
            'project',
            'details.stockItem'
        ])
        ->orderBy('date_reception', 'desc')
        ->paginate(20);

        return view('stock_receptions.index', compact('records'));
    }

    public function create()
    {
        $this->authorize('create', StockIncomingRecord::class);

        $arrivals = StockArrivalAdministration::whereDoesntHave('incomingRecords')
            ->orderBy('date_arrival', 'desc')
            ->get();

        $locations = StockLocation::orderBy('stock_name')->get();
        $users = User::orderBy('name')->get();
        $projects = Project::orderBy('name')->get();
        $stockItems = StockItem::orderBy('name')->get();

        return view('stock_receptions.create', compact('arrivals', 'locations', 'users', 'projects', 'stockItems'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', StockIncomingRecord::class);

        $validated = $request->validate([
            'stock_arrival_admin_id' => 'required|exists:stock_arrivals_administration,id',
            'date_reception' => 'required|date',
            'description_globale' => 'nullable|string',
            'receiver' => 'required|exists:users,id',
            'stock_location_destination_id' => 'required|exists:stock_locations,id',
            'project_id' => 'nullable|exists:projects,id',
            'sender' => 'nullable|string|max:150',
            'certificat_analyse' => 'nullable|file|mimes:pdf,jpeg,png|max:5120',
            'msds' => 'nullable|file|mimes:pdf,jpeg,png|max:5120',
            'borderau_livraison' => 'nullable|file|mimes:pdf,jpeg,png|max:5120',
            'details' => 'required|array|min:1',
            'details.*.stock_item_id' => 'required|exists:stock_items,id',
            'details.*.code_lot' => 'required|string|max:100',
            'details.*.batch_number' => 'nullable|string|max:100',
            'details.*.quantite_lot' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($validated, $request) {
            // Handle file uploads
            $fileFields = ['certificat_analyse', 'msds', 'borderau_livraison'];
            foreach ($fileFields as $field) {
                if ($request->hasFile($field)) {
                    $validated[$field] = $request->file($field)->store('stock_receptions', 'public');
                }
            }

            // Create the main record
            $record = StockIncomingRecord::create($validated);

            // Create the details
            foreach ($validated['details'] as $detail) {
                $record->details()->create($detail);
            }

            // Create stock movements for each received item
            foreach ($validated['details'] as $detail) {
                StockMovement::create([
                    'stock_item_id' => $detail['stock_item_id'],
                    'user_id' => $validated['receiver'], // User who received the stock
                    'type' => 'in',
                    'quantity' => $detail['quantite_lot'],
                    'reason' => 'Stock reception from shipment',
                    'notes' => "Reception ID: {$record->id}, Code Lot: {$detail['code_lot']}" .
                              ($detail['batch_number'] ? ", Batch: {$detail['batch_number']}" : ''),
                    'reference' => "RECEPTION-{$record->id}-{$detail['code_lot']}",
                    'project_id' => $validated['project_id'] ?? null,
                    'purpose' => 'Stock reception',
                ]);
            }
        });

        return redirect()->route('stock-receptions.index')
            ->with('success', 'Réception stock enregistrée avec succès.');
    }

    public function show(StockIncomingRecord $stockReception)
    {
        $this->authorize('view', $stockReception);

        $stockReception->load([
            'stockArrivalAdministration',
            'receiver',
            'stockLocationDestination',
            'project',
            'details.stockItem'
        ]);

        return view('stock_receptions.show', compact('stockReception'));
    }

    public function edit(StockIncomingRecord $stockReception)
    {
        $this->authorize('update', $stockReception);

        $arrivals = StockArrivalAdministration::orderBy('date_arrival', 'desc')->get();
        $locations = StockLocation::orderBy('stock_name')->get();
        $users = User::orderBy('name')->get();
        $projects = Project::orderBy('name')->get();
        $stockItems = StockItem::orderBy('name')->get();

        $stockReception->load('details');

        return view('stock_receptions.edit', compact('stockReception', 'arrivals', 'locations', 'users', 'projects', 'stockItems'));
    }

    public function update(Request $request, StockIncomingRecord $stockReception)
    {
        $this->authorize('update', $stockReception);

        $validated = $request->validate([
            'stock_arrival_admin_id' => 'required|exists:stock_arrivals_administration,id',
            'date_reception' => 'required|date',
            'description_globale' => 'nullable|string',
            'receiver' => 'required|exists:users,id',
            'stock_location_destination_id' => 'required|exists:stock_locations,id',
            'project_id' => 'nullable|exists:projects,id',
            'sender' => 'nullable|string|max:150',
            'certificat_analyse' => 'nullable|file|mimes:pdf,jpeg,png|max:5120',
            'msds' => 'nullable|file|mimes:pdf,jpeg,png|max:5120',
            'borderau_livraison' => 'nullable|file|mimes:pdf,jpeg,png|max:5120',
            'details' => 'required|array|min:1',
            'details.*.stock_item_id' => 'required|exists:stock_items,id',
            'details.*.code_lot' => 'required|string|max:100',
            'details.*.batch_number' => 'nullable|string|max:100',
            'details.*.quantite_lot' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($validated, $request, $stockReception) {
            // Handle file uploads and deletions
            $fileFields = ['certificat_analyse', 'msds', 'borderau_livraison'];
            foreach ($fileFields as $field) {
                if ($request->hasFile($field)) {
                    // Delete old file
                    if ($stockReception->$field) {
                        Storage::disk('public')->delete($stockReception->$field);
                    }
                    $validated[$field] = $request->file($field)->store('stock_receptions', 'public');
                }
            }

            // Update the main record
            $stockReception->update($validated);

            // Delete existing details and create new ones
            $stockReception->details()->delete();
            foreach ($validated['details'] as $detail) {
                $stockReception->details()->create($detail);
            }

            // Delete existing stock movements for this reception
            StockMovement::where('reference', 'like', "RECEPTION-{$stockReception->id}-%")->delete();

            // Create new stock movements for each received item
            foreach ($validated['details'] as $detail) {
                StockMovement::create([
                    'stock_item_id' => $detail['stock_item_id'],
                    'user_id' => $validated['receiver'], // User who received the stock
                    'type' => 'in',
                    'quantity' => $detail['quantite_lot'],
                    'reason' => 'Stock reception from shipment (updated)',
                    'notes' => "Reception ID: {$stockReception->id}, Code Lot: {$detail['code_lot']}" .
                              ($detail['batch_number'] ? ", Batch: {$detail['batch_number']}" : ''),
                    'reference' => "RECEPTION-{$stockReception->id}-{$detail['code_lot']}",
                    'project_id' => $validated['project_id'] ?? null,
                    'purpose' => 'Stock reception',
                ]);
            }
        });

        return redirect()->route('stock-receptions.show', $stockReception)
            ->with('success', 'Réception stock mise à jour avec succès.');
    }

    public function destroy(StockIncomingRecord $stockReception)
    {
        $this->authorize('delete', $stockReception);

        // Delete associated files
        $fileFields = ['certificat_analyse', 'msds', 'borderau_livraison'];
        foreach ($fileFields as $field) {
            if ($stockReception->$field) {
                Storage::disk('public')->delete($stockReception->$field);
            }
        }

        // Delete associated stock movements
        StockMovement::where('reference', 'like', "RECEPTION-{$stockReception->id}-%")->delete();

        // Delete the record (details will be deleted via cascade)
        $stockReception->delete();

        return redirect()->route('stock-receptions.index')
            ->with('success', 'Réception stock supprimée avec succès.');
    }

    // PDF generation
    public function pdf(StockIncomingRecord $stockReception)
    {
        $this->authorize('view', $stockReception);

        // try to use DOMPDF if available
        if (class_exists('\Barryvdh\DomPDF\Facades\Pdf')) {
            $pdf = \Barryvdh\DomPDF\Facades\Pdf::loadView('stock_receptions.pdf', compact('stockReception'));
            return $pdf->download('reception_acknowledgment_'.$stockReception->id.'.pdf');
        }

        // fallback: render printable HTML view
        return view('stock_receptions.pdf', compact('stockReception'));
    }
}
