@extends('layouts.app_new')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3">Demandes de Stock</h1>
        <a href="{{ route('stock-requests.create') }}" class="btn btn-primary">Nouvelle Demande de Stock</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date Demande</th>
                        <th>Demandeur</th>
                        <th>Projet</th>
                        <th>Statut</th>
                        <th>Nombre Items</th>
                        <th>Total Quantité Demandée</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $request)
                        <tr>
                            <td>{{ $request->request_date->format('d/m/Y H:i') }}</td>
                            <td>{{ $request->requester->name ?? '—' }}</td>
                            <td>{{ $request->project->name ?? '—' }}</td>
                            <td>
                                @switch($request->status)
                                    @case('pending')
                                        <span class="badge bg-warning">En attente</span>
                                        @break
                                    @case('approved_facility_manager')
                                        <span class="badge bg-info">Approuvé Facility Manager</span>
                                        @break
                                    @case('approved_data_manager')
                                        <span class="badge bg-success">Approuvé Data Manager</span>
                                        @break
                                    @case('rejected')
                                        <span class="badge bg-danger">Rejeté</span>
                                        @break
                                    @case('completed')
                                        <span class="badge bg-secondary">Satisfait</span>
                                        @break
                                @endswitch
                            </td>
                            <td>{{ $request->details->count() }}</td>
                            <td>{{ $request->details->sum('requested_quantity') }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('stock-requests.show', $request) }}" class="btn btn-outline-secondary">Voir</a>
                                    @if($request->isPending())
                                        <a href="{{ route('stock-requests.edit', $request) }}" class="btn btn-outline-warning">Éditer</a>
                                        <form action="{{ route('stock-requests.destroy', $request) }}" method="POST" onsubmit="return confirm('Confirmer la suppression ?');" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-outline-danger">Supprimer</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Aucune demande trouvée.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($requests->hasPages())
            <div class="card-footer">
                {{ $requests->links() }}
            </div>
        @endif
    </div>
</div>
@endsection