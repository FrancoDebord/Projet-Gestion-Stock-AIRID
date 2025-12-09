@extends('layouts.app_new')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3">Validation des Demandes de Stock - Facility Manager</h1>
        <a href="{{ route('stock-requests.index') }}" class="btn btn-secondary">Voir toutes les demandes</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-warning text-white">
            <h5 class="mb-0">
                <i class="fas fa-clock"></i> Demandes en attente de validation
            </h5>
        </div>

        <div class="card-body">
            @if($requests->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <h4 class="text-muted">Aucune demande en attente</h4>
                    <p class="text-muted">Toutes les demandes ont été traitées.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date Demande</th>
                                <th>Demandeur</th>
                                <th>Projet</th>
                                <th>Nombre Items</th>
                                <th>Total Quantité</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requests as $request)
                                <tr>
                                    <td>{{ $request->request_date->format('d/m/Y H:i') }}</td>
                                    <td>{{ $request->requester->name ?? '—' }}</td>
                                    <td>{{ $request->project->name ?? 'Aucun projet' }}</td>
                                    <td>{{ $request->details->count() }}</td>
                                    <td>{{ $request->details->sum('requested_quantity') }}</td>
                                    <td>
                                        <a href="{{ route('facility-manager.show', $request) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye"></i> Examiner
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Aucune demande trouvée.</td>
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
            @endif
        </div>
    </div>
</div>
@endsection