<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\StockLocation;
use App\Models\Package;
use App\Models\ShipmentDocument;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ShipmentController extends Controller
{
    public function index()
    {
        $shipments = Shipment::with('toLocation', 'project')->latest()->paginate(20);
        return view('shipments.index', compact('shipments'));
    }

    // Administration records minimal reception info
    public function createAdmin()
    {
        $locations = StockLocation::all();
        $projects = Project::all();
        return view('shipments.admin-create', compact('locations', 'projects'));
    }

    public function storeAdmin(Request $request)
    {
        $data = $request->validate([
            'shipment_number' => 'nullable|string',
            'received_at' => 'required|date',
            'colis_count' => 'required|integer|min:0',
            'sender' => 'nullable|string',
            'to_location_id' => 'nullable|exists:stock_locations,id',
            'admin_notes' => 'nullable|string',
            'project_id' => 'nullable|exists:projects,id',
            'documents.*' => 'nullable|file|max:5120',
        ]);

        $data['received_by'] = auth()->id();

        $shipment = Shipment::create($data);

        // handle documents
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('shipments', 'public');
                ShipmentDocument::create([
                    'shipment_id' => $shipment->id,
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'uploaded_by' => auth()->id(),
                ]);
            }
        }

        return redirect()->route('shipments.show', $shipment)->with('success', 'Shipment received by Administration.');
    }

    public function show(Shipment $shipment)
    {
        $shipment->load('packages', 'documents', 'toLocation', 'project');
        return view('shipments.show', compact('shipment'));
    }

    // Finalize and assign to final unit
    public function finalize(Request $request, Shipment $shipment)
    {
        $data = $request->validate([
            'finalized_at' => 'required|date',
            'finalized_by' => 'nullable|exists:users,id',
            'ack_sent' => 'nullable|boolean',
        ]);
        $shipment->update([
            'finalized_at' => $data['finalized_at'],
            'finalized_by' => auth()->id(),
            'ack_sent' => $data['ack_sent'] ?? false,
        ]);

        return redirect()->route('shipments.show', $shipment)->with('success', 'Shipment finalized.');
    }

    public function downloadAck(Shipment $shipment)
    {
        // simple view for printing acknowledgment
        return view('shipments.ack', compact('shipment'));
    }
}
