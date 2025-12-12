@extends('layouts.app_new')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h4 mb-0">
                    <i class="fas fa-box-open text-primary"></i>
                    Process Stock Reception
                </h1>
                <div>
                    <a href="{{ route('movements.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <a href="{{ route('movements.process-reception') }}" class="btn btn-outline-primary">
                        <i class="fas fa-list"></i> Receptions List
                    </a>
                </div>
            </div>

            @if(isset($selectedReception))
                <!-- Selected Reception Details -->
                <div class="card mb-4 border-primary">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-truck-loading"></i>
                            Reception #{{ $selectedReception->id }} - {{ $selectedReception->stockArrivalAdministration->sender ?? 'N/A' }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Reception Date:</strong> {{ $selectedReception->date_reception->format('d/m/Y') }}</p>
                                <p><strong>Project:</strong> {{ $selectedReception->project->name ?? 'Global' }}</p>
                                <p><strong>Receiver:</strong> {{ $selectedReception->receiver->name }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Location:</strong> {{ $selectedReception->stockLocationDestination->stock_name }}</p>
                                <p><strong>Sender:</strong> {{ $selectedReception->sender ?? 'N/A' }}</p>
                            </div>
                        </div>

                        @if($selectedReception->description_globale)
                            <div class="mt-3">
                                <strong>Description:</strong>
                                <p class="text-muted">{{ $selectedReception->description_globale }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Reception Items Table -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Items to record in stock</h5>
                        <div class="text-muted small">
                            {{ $selectedReception->details->count() }} item(s)
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Item</th>
                                        <th>Lot Code</th>
                                        <th>Batch Number</th>
                                        <th>Quantity</th>
                                        <th>Destination Project</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($selectedReception->details as $detail)
                                        <tr>
                                            <td>
                                                <div>
                                                    <strong>{{ $detail->stockItem->name }}</strong>
                                                    @if($detail->stockItem->sku)
                                                        <br><small class="text-muted">SKU: {{ $detail->stockItem->sku }}</small>
                                                    @endif
                                                    @if($detail->stockItem->brand)
                                                        <br><small class="text-muted">{{ $detail->stockItem->brand }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>{{ $detail->code_lot }}</td>
                                            <td>{{ $detail->batch_number ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ $detail->quantite_lot }}</span>
                                            </td>
                                            <td>
                                                {{ $selectedReception->project->name ?? 'Global' }}
                                            </td>
                                            <td>
                                                @if($detail->stock_movement_id)
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check"></i> Processed
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-clock"></i> Pending
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Action Buttons -->
                        @if($selectedReception->details->whereNull('stock_movement_id')->count() > 0)
                            <div class="d-flex justify-content-end mt-4">
                                <form action="{{ route('movements.process-reception-movements', $selectedReception) }}" method="POST" class="me-2">
                                    @csrf
                                    @method('POST')
                                    <button type="submit" class="btn btn-primary"
                                            onclick="return confirm('Are you sure you want to process this reception? This will credit stock.')">
                                        <i class="fas fa-plus-circle"></i>
                                        Record in Stock
                                    </button>
                                </form>
                                <button class="btn btn-secondary" onclick="window.print()">
                                    <i class="fas fa-print"></i> Print
                                </button>
                            </div>
                        @else
                            <div class="alert alert-success mt-4">
                                <i class="fas fa-check-circle"></i>
                                This reception has already been fully processed.
                            </div>
                        @endif

                        <!-- User Info -->
                        <div class="mt-3 text-muted small text-end">
                            <i class="fas fa-user"></i> Processed by: {{ auth()->user()->name }} on {{ now()->format('d/m/Y H:i') }}
                        </div>
                    </div>
                </div>

            @else
                <!-- List of Available Receptions -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-list"></i>
                            Receptions to process ({{ $availableReceptions->count() }})
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($availableReceptions->isEmpty())
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No receptions to process</h5>
                                <p class="text-muted">All receptions have been processed.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Sender</th>
                                            <th>Date</th>
                                            <th>Receiver</th>
                                            <th>Items</th>
                                            <th>Location</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($availableReceptions as $reception)
                                            <tr>
                                                <td>#{{ $reception->id }}</td>
                                                <td>{{ $reception->stockArrivalAdministration->sender ?? 'N/A' }}</td>
                                                <td>{{ $reception->date_reception->format('d/m/Y') }}</td>
                                                <td>{{ $reception->receiver->name }}</td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        {{ $reception->details->count() }} item(s)
                                                    </span>
                                                </td>
                                                <td>{{ $reception->stockLocationDestination->stock_name }}</td>
                                                <td>
                                                    <a href="{{ route('movements.process-reception', ['reception_id' => $reception->id]) }}"
                                                       class="btn btn-sm btn-primary">
                                                        <i class="fas fa-play"></i> Process
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
@media print {
    .btn, .card-header, .table-responsive .btn {
        display: none !important;
    }
    .container {
        max-width: none !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    .card {
        border: 1px solid #000 !important;
    }
}
</style>

<script>
// Auto-refresh every 30 seconds to check for updates
setTimeout(function() {
    if (!document.hidden) {
        location.reload();
    }
}, 30000);
</script>
@endsection
