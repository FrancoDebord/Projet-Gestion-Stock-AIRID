<?php

namespace App\Http\Controllers;

use App\Models\StockArrivalAdministration;
use App\Models\StockLocation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StockArrivalAdministrationController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', StockArrivalAdministration::class);

        $arrivals = StockArrivalAdministration::with('stockLocationDestination', 'administrationStaff', 'transmittedTo')
            ->orderBy('date_arrival', 'desc')
            ->paginate(20);

        return view('stock_arrivals_admin.index', compact('arrivals'));
    }

    public function create()
    {
        $this->authorize('create', StockArrivalAdministration::class);

        $locations = StockLocation::orderBy('stock_name')->get();
        $users = User::orderBy('name')->get();

        return view('stock_arrivals_admin.create', compact('locations', 'users'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', StockArrivalAdministration::class);

        $data = $request->validate([
            'date_arrival' => 'required|date',
            'sender' => 'nullable|string|max:150',
            'description_globale' => 'nullable|string',
            'stock_location_destination' => 'required|exists:stock_locations,id',
            'administration_staff' => 'required|exists:users,id',
            'staff_transmis_stock' => 'nullable|exists:users,id',
            'bordereau_delivery' => 'nullable|file|mimes:pdf,jpeg,png|max:5120',
            'certificate_analysis' => 'nullable|file|mimes:pdf,jpeg,png|max:5120',
            'msds' => 'nullable|file|mimes:pdf,jpeg,png|max:5120',
            'other_document' => 'nullable|file|mimes:pdf,jpeg,png|max:5120',
        ]);

        // handle file uploads
        foreach (['bordereau_delivery','certificate_analysis','msds','other_document'] as $f) {
            if ($request->hasFile($f)) {
                $data[$f] = $request->file($f)->store('arrivals_documents', 'public');
            }
        }

        $arrival = StockArrivalAdministration::create($data);

        return redirect()->route('stock-arrivals-admin.index')->with('success', 'Réception enregistrée.');
    }

    public function show(StockArrivalAdministration $stockArrivalAdministration)
    {
        $this->authorize('view', $stockArrivalAdministration);

        return view('stock_arrivals_admin.show', compact('stockArrivalAdministration'));
    }

    public function edit(StockArrivalAdministration $stockArrivalAdministration)
    {
        $this->authorize('update', $stockArrivalAdministration);

        $locations = StockLocation::orderBy('stock_name')->get();
        $users = User::orderBy('name')->get();

        return view('stock_arrivals_admin.edit', compact('stockArrivalAdministration','locations','users'));
    }

    public function update(Request $request, StockArrivalAdministration $stockArrivalAdministration)
    {
        $this->authorize('update', $stockArrivalAdministration);

        $data = $request->validate([
            'date_arrival' => 'required|date',
            'sender' => 'nullable|string|max:150',
            'description_globale' => 'nullable|string',
            'stock_location_destination' => 'required|exists:stock_locations,id',
            'administration_staff' => 'required|exists:users,id',
            'staff_transmis_stock' => 'nullable|exists:users,id',
            'bordereau_delivery' => 'nullable|file|mimes:pdf,jpeg,png|max:5120',
            'certificate_analysis' => 'nullable|file|mimes:pdf,jpeg,png|max:5120',
            'msds' => 'nullable|file|mimes:pdf,jpeg,png|max:5120',
            'other_document' => 'nullable|file|mimes:pdf,jpeg,png|max:5120',
        ]);

        // handle file uploads
        foreach (['bordereau_delivery','certificate_analysis','msds','other_document'] as $f) {
            if ($request->hasFile($f)) {
                // delete old
                if ($stockArrivalAdministration->$f) {
                    Storage::disk('public')->delete($stockArrivalAdministration->$f);
                }
                $data[$f] = $request->file($f)->store('arrivals_documents', 'public');
            }
        }

        $stockArrivalAdministration->update($data);

        return redirect()->route('stock-arrivals-admin.show', $stockArrivalAdministration)->with('success', 'Réception mise à jour.');
    }

    public function destroy(StockArrivalAdministration $stockArrivalAdministration)
    {
        $this->authorize('delete', $stockArrivalAdministration);

        // delete files
        foreach (['bordereau_delivery','certificate_analysis','msds','other_document'] as $f) {
            if ($stockArrivalAdministration->$f) {
                Storage::disk('public')->delete($stockArrivalAdministration->$f);
            }
        }

        $stockArrivalAdministration->delete();

        return redirect()->route('stock-arrivals-admin.index')->with('success', 'Réception supprimée.');
    }

    // PDF generation
    public function pdf(StockArrivalAdministration $stockArrivalAdministration)
    {
        $this->authorize('view', $stockArrivalAdministration);

        // try to use DOMPDF if available
        if (class_exists('\Barryvdh\DomPDF\Facades\Pdf')) {
            $pdf = \Barryvdh\DomPDF\Facades\Pdf::loadView('stock_arrivals_admin.pdf', compact('stockArrivalAdministration'));
            return $pdf->download('reception_'.$stockArrivalAdministration->id.'.pdf');
        }

        // fallback: render printable HTML view
        return view('stock_arrivals_admin.pdf', compact('stockArrivalAdministration'));
    }
}
