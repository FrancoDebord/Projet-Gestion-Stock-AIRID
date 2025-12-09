@extends('layouts.app_new')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Stock Movements</h1>
        <div class="btn-group">
            @if(auth()->user()->hasPermission('record_stock_in'))
                <a href="{{ route('movements.create-in') }}" class="btn btn-success">Record Incoming</a>
            @endif
            @if(auth()->user()->hasPermission('record_stock_out'))
                <a href="{{ route('movements.create-out') }}" class="btn btn-warning">Record Outgoing</a>
            @endif
            @if(auth()->user()->hasPermission('adjust_stock'))
                <a href="{{ route('movements.create-adjustment') }}" class="btn btn-primary">Adjust Stock</a>
            @endif
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Item</th>
                        <th>Type</th>
                        <th class="text-center">Quantity</th>
                        <th>User</th>
                        <th>Reason</th>
                        <th>Reference</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movements as $movement)
                        <tr>
                            <td>{{ ($movement->date_mouvement ?? $movement->created_at)->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('stock-items.show', $movement->stockItem) }}" class="text-decoration-none">{{ $movement->stockItem->name }}</a>
                            </td>
                            <td>
                                @php $type = $movement->type; @endphp
                                @if($type === 'in')
                                    <span class="badge bg-success">Incoming</span>
                                @elseif($type === 'out')
                                    <span class="badge bg-danger">Outgoing</span>
                                @else
                                    <span class="badge bg-warning text-dark">Adjustment</span>
                                @endif
                            </td>
                            <td class="text-center fw-semibold">
                                @if($movement->type === 'out')
                                    -{{ $movement->quantity }}
                                @elseif($movement->type === 'in')
                                    +{{ $movement->quantity }}
                                @else
                                    {{ $movement->quantity }}
                                @endif
                            </td>
                            <td>{{ $movement->user->name }}</td>
                            <td>{{ $movement->reason ?? '—' }}</td>
                            <td>{{ $movement->reference ?? '—' }}</td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('movements.show', $movement) }}" class="btn btn-outline-secondary">View</a>
                                    <a href="{{ route('movements.pdf', $movement) }}" class="btn btn-outline-info" target="_blank">PDF</a>
                                    @if(auth()->user()->hasPermission('manage_settings'))
                                        <a href="{{ route('movements.edit', $movement) }}" class="btn btn-outline-primary">Edit</a>
                                        @if($movement->type !== 'adjustment')
                                            <form action="{{ route('movements.destroy', $movement) }}" method="POST" onsubmit="return confirm('Delete this movement?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-outline-danger">Delete</button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No movements found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($movements->hasPages())
            <div class="card-footer">{{ $movements->links() }}</div>
        @endif
    </div>
</div>
@endsection
