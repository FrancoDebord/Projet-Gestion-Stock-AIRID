@extends('layouts.app_new')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="h4 mb-0">Stock Movement #{{ $movement->id }}</h1>
                <div>
                    <a href="{{ route('movements.pdf', $movement) }}" class="btn btn-info" target="_blank">Print PDF</a>
                    @if(auth()->user()->hasPermission('manage_settings'))
                        <a href="{{ route('movements.edit', $movement) }}" class="btn btn-primary">Edit</a>
                    @endif
                    <a href="{{ route('movements.index') }}" class="btn btn-light">Back</a>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="text-muted small">Item</div>
                            <div><a href="{{ route('stock-items.show', $movement->stockItem) }}" class="text-decoration-none">{{ $movement->stockItem->name }}</a></div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Type</div>
                            <div>
                                @php $type = $movement->type; @endphp
                                @if($type === 'in')
                                    <span class="badge bg-success">Incoming</span>
                                @elseif($type === 'out')
                                    <span class="badge bg-danger">Outgoing</span>
                                @else
                                    <span class="badge bg-warning text-dark">Adjustment</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Quantity</div>
                            <div>{{ $movement->quantity }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">User</div>
                            <div>{{ $movement->user->name }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Movement Date</div>
                            <div>{{ optional($movement->date_mouvement)->format('d/m/Y H:i') ?? $movement->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Reason</div>
                            <div>{{ $movement->reason ?? '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Notes</div>
                            <div>{{ $movement->notes ?? '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Reference</div>
                            <div>{{ $movement->reference ?? '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Batch number</div>
                            <div>{{ $movement->batch_number ?? '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Reception Detail</div>
                            <div>{{ optional($movement->incomingDetail)->code_lot ?? '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Usage Request</div>
                            <div>
                                @if($movement->usageRequest)
                                    #{{ $movement->usageRequest->id }} — {{ $movement->usageRequest->project->name ?? 'No Project' }}
                                @else
                                    —
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
