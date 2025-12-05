@extends('layouts.app_new')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3">Réceptions Stock</h1>
        <a href="{{ route('stock-receptions.create') }}" class="btn btn-primary">Nouvelle Réception Stock</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date Réception</th>
                        <th>Réception Admin</th>
                        <th>Destination</th>
                        <th>Reçu Par</th>
                        <th>Nombre Items</th>
                        <th>Total Quantité</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $record)
                        <tr>
                            <td>{{ $record->date_reception->format('d/m/Y H:i') }}</td>
                            <td>
                                {{ $record->stockArrivalAdministration->date_arrival->format('d/m/Y') }}
                                @if($record->stockArrivalAdministration->sender)
                                    <br><small class="text-muted">{{ $record->stockArrivalAdministration->sender }}</small>
                                @endif
                            </td>
                            <td>{{ $record->stockLocationDestination->stock_name ?? '—' }}</td>
                            <td>{{ $record->receiver->name ?? '—' }}</td>
                            <td>{{ $record->details->count() }}</td>
                            <td>{{ $record->details->sum('quantite_lot') }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('stock-receptions.show', $record) }}" class="btn btn-outline-secondary">Voir</a>
                                    <a href="{{ route('stock-receptions.edit', $record) }}" class="btn btn-outline-warning">Éditer</a>
                                    <form action="{{ route('stock-receptions.destroy', $record) }}" method="POST" onsubmit="return confirm('Confirmer la suppression ?');" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-outline-danger">Supprimer</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted">Aucune réception stock trouvée.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $records->links() }}</div>
</div>
@endsection
