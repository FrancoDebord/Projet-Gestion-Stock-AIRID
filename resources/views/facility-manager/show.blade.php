@extends('layouts.app_new')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="h4">Validation Demande de Stock #{{ $stockRequest->id }}</h1>
                <div>
                    <a href="{{ route('facility-manager.index') }}" class="btn btn-secondary btn-sm">Retour à la liste</a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <!-- Request Information -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Informations de la Demande</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <strong>Date de demande:</strong><br>
                            {{ $stockRequest->request_date->format('d/m/Y H:i') }}
                        </div>
                        <div class="col-md-3">
                            <strong>Demandeur:</strong><br>
                            {{ $stockRequest->requester->name ?? '—' }}
                        </div>
                        <div class="col-md-3">
                            <strong>Projet:</strong><br>
                            {{ $stockRequest->project->name ?? 'Aucun projet' }}
                        </div>
                        <div class="col-md-3">
                            <strong>Statut:</strong><br>
                            <span class="badge bg-warning">En attente de validation</span>
                        </div>
                    </div>

                    @if($stockRequest->code_machine || $stockRequest->room_number)
                    <div class="row g-3 mt-2">
                        @if($stockRequest->code_machine)
                        <div class="col-md-3">
                            <strong>Numéro de machine:</strong><br>
                            {{ $stockRequest->code_machine }}
                        </div>
                        @endif
                        @if($stockRequest->room_number)
                        <div class="col-md-3">
                            <strong>Numéro de bureau:</strong><br>
                            {{ $stockRequest->room_number }}
                        </div>
                        @endif
                    </div>
                    @endif

                    @if($stockRequest->general_notes)
                    <div class="mt-3">
                        <strong>Notes générales:</strong><br>
                        <p class="text-muted">{{ $stockRequest->general_notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Items with Availability -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-boxes"></i> Produits demandés et disponibilité
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('facility-manager.approve', $stockRequest) }}" method="POST" id="approvalForm">
                        @csrf

                        @foreach($itemsWithAvailability as $index => $item)
                        <div class="item-approval-card border rounded p-4 mb-4 {{ $item['can_fulfill'] ? 'border-success' : 'border-warning' }}">
                            <div class="row">
                                <div class="col-md-4">
                                    <h6 class="text-primary">{{ $item['stock_item']->name }}</h6>
                                    <p class="mb-1"><strong>Unité:</strong> {{ $item['stock_item']->unit ?? 'N/A' }}</p>
                                    <p class="mb-1"><strong>Demandé:</strong> {{ $item['requested_quantity'] }}</p>
                                    <p class="mb-1"><strong>Disponible total:</strong>
                                        <span class="{{ $item['can_fulfill'] ? 'text-success' : 'text-danger' }} fw-bold">
                                            {{ $item['total_available'] }}
                                        </span>
                                    </p>
                                    <p class="mb-0"><strong>Raison:</strong> {{ $item['detail']->request_reason }}</p>
                                    @if($item['detail']->usage_description)
                                    <p class="mb-0"><strong>Description:</strong> {{ $item['detail']->usage_description }}</p>
                                    @endif
                                </div>

                                <div class="col-md-4">
                                    <h6>Disponibilité par projet</h6>
                                    @if(empty($item['availability_by_project']))
                                        <p class="text-muted">Aucune disponibilité par projet</p>
                                    @else
                                        <div class="project-availability">
                                            @foreach($item['availability_by_project'] as $projectAvailability)
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <small>{{ $projectAvailability['project']->name }}:</small>
                                                <span class="badge bg-info">{{ $projectAvailability['available_quantity'] }}</span>
                                            </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <div class="col-md-4">
                                    <h6>Validation</h6>
                                    <input type="hidden" name="approvals[{{ $item['detail']->id }}][detail_id]" value="{{ $item['detail']->id }}">

                                    <div class="mb-3">
                                        <label class="form-label">Quantité approuvée *</label>
                                        <input type="number"
                                               name="approvals[{{ $item['detail']->id }}][approved_quantity]"
                                               class="form-control approved-quantity"
                                               min="0"
                                               max="{{ $item['total_available'] }}"
                                               value="{{ $item['can_fulfill'] ? $item['requested_quantity'] : 0 }}"
                                               required>
                                        <div class="form-text">
                                            Maximum disponible: {{ $item['total_available'] }}
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Projet source *</label>
                                        <select name="approvals[{{ $item['detail']->id }}][source_project_id]" class="form-control" required>
                                            <option value="{{ $stockRequest->project_id ?? '' }}">
                                                {{ $stockRequest->project->name ?? 'Projet de la demande' }}
                                            </option>
                                            @foreach($item['availability_by_project'] as $projectAvailability)
                                            <option value="{{ $projectAvailability['project']->id }}">
                                                {{ $projectAvailability['project']->name }} ({{ $projectAvailability['available_quantity'] }} disponible)
                                            </option>
                                            @endforeach
                                        </select>
                                        <div class="form-text">Sélectionnez le projet duquel prélever le stock pour cette demande</div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Notes/Observations</label>
                                        <textarea name="approvals[{{ $item['detail']->id }}][notes]"
                                                  class="form-control"
                                                  rows="2"
                                                  placeholder="Observations sur cette approbation..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Notes générales d'approbation</label>
                                    <textarea name="general_notes" class="form-control" rows="3" placeholder="Notes générales sur cette demande..."></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex justify-content-end gap-2 mt-4">
                                    <form action="{{ route('facility-manager.reject', $stockRequest) }}" method="POST" class="d-inline" onsubmit="return confirm('Confirmer le rejet de cette demande ?');">
                                        @csrf
                                        <div class="mb-3">
                                            <textarea name="rejection_reason" class="form-control" rows="2" placeholder="Raison du rejet..." required></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-times"></i> Rejeter la demande
                                        </button>
                                    </form>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-check"></i> Approuver la demande
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-adjust approved quantities based on available stock
    document.querySelectorAll('.approved-quantity').forEach(function(input) {
        input.addEventListener('input', function() {
            const max = parseInt(this.getAttribute('max'));
            const value = parseInt(this.value);

            if (value > max) {
                this.value = max;
                alert('La quantité approuvée ne peut pas dépasser la quantité disponible (' + max + ')');
            }
        });
    });
});
</script>
@endsection