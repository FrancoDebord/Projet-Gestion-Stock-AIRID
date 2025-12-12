@extends('layouts.app_new')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h4 mb-0">
                    <i class="fas fa-clipboard-check text-success"></i>
                    Fulfill Approved Request
                </h1>
                <div>
                    <a href="{{ route('movements.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <a href="{{ route('movements.fulfill-request') }}" class="btn btn-outline-primary">
                        <i class="fas fa-list"></i> Approved Requests
                    </a>
                </div>
            </div>
                <div>
                    <a href="{{ route('movements.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <a href="{{ route('movements.fulfill-request') }}" class="btn btn-outline-primary">
                        <i class="fas fa-list"></i> Approved Requests
                    </a>
                </div>
            </div>

            @if(isset($selectedRequest))
                <!-- Selected Request Details -->
                <div class="card mb-4 border-success">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-check-circle"></i>
                            Request #{{ $selectedRequest->id }} - {{ $selectedRequest->requester->name }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Date:</strong> {{ $selectedRequest->request_date->format('d/m/Y') }}</p>
                                <p><strong>Project:</strong> {{ $selectedRequest->project->name ?? 'N/A' }}</p>
                                <p><strong>Machine:</strong> {{ $selectedRequest->code_machine ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Status:</strong>
                                    <span class="badge bg-success">Approved</span>
                                </p>
                                <p><strong>Location:</strong> {{ $selectedRequest->room_number ?? 'N/A' }}</p>
                            </div>
                        </div>

                        @if($selectedRequest->general_notes)
                            <div class="mt-3">
                                <strong>General Notes:</strong>
                                <p class="text-muted">{{ $selectedRequest->general_notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Request Items Table -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Items to take out from stock</h5>
                        <div class="text-muted small">
                            {{ $selectedRequest->details->count() }} item(s)
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Item</th>
                                        <th>Requested Quantity</th>
                                        <th>Approved Quantity</th>
                                        <th>Project</th>
                                        <th>Reason</th>
                                        <th>Availability</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($selectedRequest->details as $detail)
                                        @if($detail->isApproved() && $detail->approved_quantity > 0)
                                            @php
                                                $availability = \App\Services\ProjectStockService::checkAvailability(
                                                    $detail->stockItem->id,
                                                    $detail->project_id ?? $selectedRequest->project_id,
                                                    $detail->approved_quantity
                                                );
                                            @endphp
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
                                                <td>{{ $detail->requested_quantity }}</td>
                                                <td>
                                                    <span class="badge bg-success">{{ $detail->approved_quantity }}</span>
                                                </td>
                                                <td>
                                                    {{ $detail->project->name ?? $selectedRequest->project->name ?? 'Global' }}
                                                </td>
                                                <td>{{ $detail->request_reason }}</td>
                                                <td>
                                                    @if($availability['can_fulfill'])
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check"></i> Available
                                                        </span>
                                                        <br><small class="text-muted">
                                                            Source: {{ $availability['source_project']->name }}
                                                        </small>
                                                    @else
                                                        <span class="badge bg-danger">
                                                            <i class="fas fa-times"></i> Insufficient
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-end mt-4">
                            <form action="{{ route('movements.process-fulfillment', $selectedRequest) }}" method="POST" class="me-2">
                                @csrf
                                @method('POST')
                                <button type="submit" class="btn btn-success"
                                        onclick="return confirm('Are you sure you want to fulfill this request? This will debit stock.')">
                                    <i class="fas fa-check-circle"></i>
                                    Record Stock Outgoing
                                </button>
                            </form>
                            <a class="btn btn-info me-2" target="_blank" href="{{ route('stock-requests.pdf', $selectedRequest) }}">
                                <i class="fas fa-file-pdf"></i> PDF
                            </a>
                            <button class="btn btn-primary" onclick="window.print()">
                                <i class="fas fa-print"></i> Print
                            </button>
                        </div>

                        <!-- User Info -->
                        <div class="mt-3 text-muted small text-end">
                            <i class="fas fa-user"></i> Processed by: {{ auth()->user()->name }} on {{ now()->format('d/m/Y H:i') }}
                        </div>
                    </div>
                </div>

            @else
                <!-- List of Approved Requests -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-list"></i>
                            Approved Requests to Fulfill ({{ $approvedRequests->count() }})
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($approvedRequests->isEmpty())
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No approved requests to fulfill</h5>
                                <p class="text-muted">All approved requests have been fulfilled.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Requester</th>
                                            <th>Date</th>
                                            <th>Project</th>
                                            <th>Items</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($approvedRequests as $request)
                                            <tr>
                                                <td>#{{ $request->id }}</td>
                                                <td>{{ $request->requester->name }}</td>
                                                <td>{{ $request->request_date->format('d/m/Y') }}</td>
                                                <td>{{ $request->project->name ?? 'N/A' }}</td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        {{ $request->details->where('facility_manager_approval', true)->where('data_manager_approval', true)->count() }} item(s)
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($request->status === 'approved_data_manager')
                                                        <span class="badge bg-success">Approved</span>
                                                    @else
                                                        <span class="badge bg-warning">Partially Approved</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('movements.fulfill-request', ['request_id' => $request->id]) }}"
                                                       class="btn btn-sm btn-success">
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
