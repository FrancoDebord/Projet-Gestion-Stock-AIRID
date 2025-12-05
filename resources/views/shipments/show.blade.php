@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Shipment Details') }}</h2>
@endsection

@section('content')
    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-4">
                        <h3 class="text-lg font-medium">Shipment #{{ $shipment->id }} {{ $shipment->shipment_number ? ' — '.$shipment->shipment_number : '' }}</h3>
                        <p class="text-sm text-gray-600">Received at: {{ optional($shipment->received_at)->format('Y-m-d H:i') }} by {{ optional($shipment->receivedBy)->name ?? '—' }}</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <p><strong>To location:</strong> {{ optional($shipment->toLocation)->name ?? '—' }}</p>
                            <p><strong>Project:</strong> {{ optional($shipment->project)->name ?? '—' }}</p>
                            <p><strong>Colis count:</strong> {{ $shipment->colis_count }}</p>
                        </div>
                        <div>
                            <p><strong>Sender:</strong> {{ $shipment->sender ?? '—' }}</p>
                            <p><strong>Admin notes:</strong></p>
                            <p class="text-sm text-gray-700">{{ $shipment->admin_notes ?? '—' }}</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h4 class="font-semibold">Documents</h4>
                        @if($shipment->documents->count())
                            <ul class="list-disc pl-6">
                                @foreach($shipment->documents as $doc)
                                    <li>
                                        <a href="{{ asset('storage/'.$doc->path) }}" target="_blank" class="text-blue-600 hover:underline">{{ $doc->original_name ?? $doc->path }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-gray-500">No documents uploaded.</p>
                        @endif
                    </div>

                    <div class="mb-4">
                        <h4 class="font-semibold">Packages (colis)</h4>
                        @if($shipment->packages->count())
                            <ul class="list-disc pl-6">
                                @foreach($shipment->packages as $pkg)
                                    <li>{{ $pkg->code ?? '—' }} — {{ 
                                        
                                    $pkg->contents ?? '' }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-gray-500">No packages assigned yet.</p>
                        @endif
                    </div>

                    <div class="flex items-center gap-4">
                        @if($shipment->finalized_at)
                            <span class="text-green-600">Finalized at {{ $shipment->finalized_at->format('Y-m-d H:i') }}</span>
                            <a href="{{ route('shipments.ack', $shipment) }}" class="ml-3 px-3 py-1 bg-blue-600 text-white rounded">Print Ack</a>
                        @else
                            <form action="{{ route('shipments.finalize', $shipment) }}" method="POST" class="flex items-center gap-3">
                                @csrf
                                <label class="text-sm">Finalize now</label>
                                <input type="datetime-local" name="finalized_at" required class="border rounded px-2 py-1">
                                <button type="submit" class="px-3 py-1 bg-green-600 text-white rounded">Finalize</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
