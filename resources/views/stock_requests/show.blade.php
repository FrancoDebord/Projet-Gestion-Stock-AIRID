@extends('layouts.app_new')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="h4">Stock Item Request Sheet #{{ $stockRequest->id }}</h1>
                <div>
                    <a href="{{ route('stock-requests.pdf', $stockRequest) }}" class="btn btn-info btn-sm" target="_blank">
                        <i class="fas fa-print"></i> Imprimer PDF
                    </a>
                    @if($stockRequest->isPending())
                        @can('update', $stockRequest)
                            <a href="{{ route('stock-requests.edit', $stockRequest) }}" class="btn btn-warning btn-sm ms-2">Éditer</a>
                        @endcan
                        @can('delete', $stockRequest)
                            <form action="{{ route('stock-requests.destroy', $stockRequest) }}" method="POST" onsubmit="return confirm('Confirmer la suppression ?');" class="d-inline ms-2">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm">Supprimer</button>
                            </form>
                        @endcan
                    @endif
                    <a href="{{ route('stock-requests.index') }}" class="btn btn-secondary btn-sm ms-2">Retour à la liste</a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Informations Générales</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <strong>Date de Demande:</strong><br>
                            {{ $stockRequest->request_date->format('d/m/Y H:i') }}
                        </div>
                        <div class="col-md-3">
                            <strong>Demandeur:</strong><br>
                            {{ $stockRequest->requester->name ?? '—' }}
                        </div>
                        <div class="col-md-3">
                            <strong>Projet:</strong><br>
                            {{ $stockRequest->project->name ?? '—' }}
                        </div>
                        <div class="col-md-3">
                            <strong>Statut:</strong><br>
                            @switch($stockRequest->status)
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
                        </div>
                    </div>

                    @if($stockRequest->code_machine || $stockRequest->room_number)
                    <div class="row g-3 mt-2">
                        @if($stockRequest->code_machine)
                        <div class="col-md-3">
                            <strong>Numéro de Machine:</strong><br>
                            {{ $stockRequest->code_machine }}
                        </div>
                        @endif
                        @if($stockRequest->room_number)
                        <div class="col-md-3">
                            <strong>Numéro de Bureau:</strong><br>
                            {{ $stockRequest->room_number }}
                        </div>
                        @endif
                    </div>
                    @endif

                    @if($stockRequest->general_notes)
                    <div class="mt-3">
                        <strong>Notes Générales:</strong><br>
                        <p class="text-muted">{{ $stockRequest->general_notes }}</p>
                    </div>
                    @endif

                    @if($stockRequest->facility_manager_id)
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <strong>Facility Manager:</strong><br>
                            {{ $stockRequest->facilityManager->name ?? '—' }}
                            @if($stockRequest->facility_manager_approval_date)
                                (approuvé le {{ $stockRequest->facility_manager_approval_date->format('d/m/Y H:i') }})
                            @endif
                        </div>
                        @if($stockRequest->facility_manager_notes)
                        <div class="col-md-6">
                            <strong>Notes Facility Manager:</strong><br>
                            <p class="text-muted">{{ $stockRequest->facility_manager_notes }}</p>
                        </div>
                        @endif
                    </div>
                    @endif

                    @if($stockRequest->data_manager_id)
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <strong>Data Manager:</strong><br>
                            {{ $stockRequest->dataManager->name ?? '—' }}
                            @if($stockRequest->data_manager_approval_date)
                                (approuvé le {{ $stockRequest->data_manager_approval_date->format('d/m/Y H:i') }})
                            @endif
                        </div>
                        @if($stockRequest->data_manager_notes)
                        <div class="col-md-6">
                            <strong>Notes Data Manager:</strong><br>
                            <p class="text-muted">{{ $stockRequest->data_manager_notes }}</p>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <div class="card shadow-sm mt-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Détails des Produits Demandés</h5>
                    @if($stockRequest->canBeApprovedByFacilityManager() && auth()->user()->hasPermission('approve_stock_requests_facility'))
                        <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#facilityApprovalModal">Approuver (Facility)</button>
                    @elseif($stockRequest->canBeApprovedByDataManager() && auth()->user()->hasPermission('approve_stock_requests_data'))
                        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#dataApprovalModal">Approuver (Data)</button>
                    @elseif($stockRequest->canBeRejected() && (auth()->user()->hasPermission('approve_stock_requests_facility') || auth()->user()->hasPermission('approve_stock_requests_data')))
                        <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal">Rejeter</button>
                    @elseif($stockRequest->isApproved() && auth()->user()->hasPermission('fulfill_stock_requests'))
                        <form action="{{ route('stock-requests.fulfill', $stockRequest) }}" method="POST" class="d-inline" onsubmit="return confirm('Confirmer la satisfaction de cette demande ?');">
                            @csrf
                            <button class="btn btn-primary btn-sm">Satisfaire la Demande</button>
                        </form>
                    @endif
                </div>
                <div class="card-body">
                    @if($stockRequest->details->isEmpty())
                        <p class="text-muted">Aucun détail trouvé.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Produit</th>
                                        <th>Quantité Demandée</th>
                                        <th>Quantité Approuvée</th>
                                        <th>Raison</th>
                                        <th>Description Usage</th>
                                        <th>Projet</th>
                                        <th>Observations</th>
                                        <th>Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stockRequest->details as $detail)
                                    <tr>
                                        <td>{{ $detail->stockItem->name ?? '—' }} ({{ $detail->stockItem->unit ?? '' }})</td>
                                        <td>{{ $detail->requested_quantity }}</td>
                                        <td>{{ $detail->approved_quantity ?? '—' }}</td>
                                        <td>{{ $detail->request_reason }}</td>
                                        <td>{{ $detail->usage_description ?? '—' }}</td>
                                        <td>{{ $detail->project->name ?? '—' }}</td>
                                        <td>{{ $detail->observations ?? '—' }}</td>
                                        <td>
                                            @if($detail->isApproved())
                                                <span class="badge bg-success">Approuvé</span>
                                            @elseif($detail->facility_manager_approval)
                                                <span class="badge bg-info">Facility OK</span>
                                            @else
                                                <span class="badge bg-warning">En attente</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Facility Manager Approval Modal -->
<div class="modal fade" id="facilityApprovalModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('stock-requests.approve', $stockRequest) }}" method="POST">
                @csrf
                <input type="hidden" name="approval_type" value="facility_manager">
                <div class="modal-header">
                    <h5 class="modal-title">Approbation Facility Manager</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" rows="3" class="form-control" placeholder="Ajouter des notes..."></textarea>
                    </div>

                    <h6>Détails des produits :</h6>
                    @foreach($stockRequest->details as $index => $detail)
                    <div class="border rounded p-3 mb-3">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <strong>{{ $detail->stockItem->name }}</strong><br>
                                <small class="text-muted">Demandé: {{ $detail->requested_quantity }}</small>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Quantité approuvée</label>
                                <input type="number" name="details[{{ $detail->id }}][approved_quantity]" value="{{ $detail->requested_quantity }}" min="0" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Observations</label>
                                <input type="text" name="details[{{ $detail->id }}][observations]" class="form-control" placeholder="Notes...">
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-info">Approuver</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Data Manager Approval Modal -->
<div class="modal fade" id="dataApprovalModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('stock-requests.approve', $stockRequest) }}" method="POST">
                @csrf
                <input type="hidden" name="approval_type" value="data_manager">
                <div class="modal-header">
                    <h5 class="modal-title">Approbation Data Manager</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" rows="3" class="form-control" placeholder="Ajouter des notes..."></textarea>
                    </div>

                    <h6>Détails des produits :</h6>
                    @foreach($stockRequest->details as $index => $detail)
                    <div class="border rounded p-3 mb-3">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <strong>{{ $detail->stockItem->name }}</strong><br>
                                <small class="text-muted">Demandé: {{ $detail->requested_quantity }} | Facility: {{ $detail->approved_quantity ?? '—' }}</small>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Quantité approuvée</label>
                                <input type="number" name="details[{{ $detail->id }}][approved_quantity]" value="{{ $detail->approved_quantity ?? $detail->requested_quantity }}" min="0" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Observations</label>
                                <input type="text" name="details[{{ $detail->id }}][observations]" class="form-control" placeholder="Notes...">
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">Approuver</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('stock-requests.reject', $stockRequest) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Rejeter la Demande</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Raison du rejet *</label>
                        <textarea name="rejection_notes" rows="4" class="form-control" required placeholder="Expliquez la raison du rejet..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">Rejeter</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection