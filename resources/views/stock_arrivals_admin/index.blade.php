@extends('layouts.app_new')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3">Réceptions - Administration</h1>
        <a href="{{ route('stock-arrivals-admin.create') }}" class="btn btn-primary">Nouvelle Réception</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Expéditeur</th>
                        <th>Destination</th>
                        <th>Reçu Par</th>
                        <th>Transmis À</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($arrivals as $a)
                        <tr>
                            <td>{{ $a->date_arrival->format('d/m/Y H:i') }}</td>
                            <td>{{ $a->sender ?? '—' }}</td>
                            <td>{{ $a->stockLocationDestination->stock_name ?? '—' }}</td>
                            <td>{{ $a->administrationStaff->name ?? '—' }}</td>
                            <td>{{ $a->transmittedTo->name ?? '—' }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('stock-arrivals-admin.show', $a) }}" class="btn btn-outline-secondary">Voir</a>
                                    @if($a->incomingRecords->count() > 0)
                                        <span class="btn btn-outline-success disabled" title="Réception stock effectuée">Stock Reçu</span>
                                        <a href="{{ route('stock-arrivals-admin.pdf', $a) }}" class="btn btn-outline-info">PDF</a>
                                    @else
                                        <a href="{{ route('stock-arrivals-admin.edit', $a) }}" class="btn btn-outline-warning">Éditer</a>
                                        <a href="{{ route('stock-arrivals-admin.pdf', $a) }}" class="btn btn-outline-info">PDF</a>
                                        <form action="{{ route('stock-arrivals-admin.destroy', $a) }}" method="POST" onsubmit="return confirm('Confirmer la suppression ?');" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-outline-danger">Supprimer</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted">Aucune réception trouvée.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $arrivals->links() }}</div>
</div>
@endsection
