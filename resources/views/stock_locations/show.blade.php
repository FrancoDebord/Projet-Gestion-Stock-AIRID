@extends('layouts.app_new')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="h3 mb-2">{{ $location->stock_name }}</h1>
                    <p class="text-muted mb-1">
                        <small>Code: <strong>{{ $location->code_stock ?? '—' }}</strong></small>
                    </p>
                    <p class="mb-0">{{ $location->description }}</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="small text-muted mb-2">
                        <strong>Responsable:</strong> {{ $location->principalManager?->name ?? '—' }}
                    </div>
                    <div class="small text-muted mb-2">
                        <strong>Créé par:</strong> {{ $location->creatorUser?->name ?? '—' }}
                    </div>
                    <div class="small text-muted">
                        <strong>Le:</strong> {{ $location->creation_date?->format('d/m/Y à H:i') ?? '—' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-left border-4 border-success shadow-sm">
                <div class="card-body">
                    <h6 class="card-title text-muted">Mouvements "In"</h6>
                    <h3 class="mb-0">{{ $totalIn }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left border-4 border-danger shadow-sm">
                <div class="card-body">
                    <h6 class="card-title text-muted">Mouvements "Out"</h6>
                    <h3 class="mb-0">{{ $totalOut }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left border-4 border-warning shadow-sm">
                <div class="card-body">
                    <h6 class="card-title text-muted">Ajustements</h6>
                    <h3 class="mb-0">{{ $totalAdjustments }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left border-4 border-info shadow-sm">
                <div class="card-body">
                    <h6 class="card-title text-muted">Articles Critiques</h6>
                    <h3 class="mb-0">{{ $criticalItems->count() }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="items-tab" data-bs-toggle="tab" data-bs-target="#items-pane" type="button">Articles</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="critical-tab" data-bs-toggle="tab" data-bs-target="#critical-pane" type="button">
                Articles Critiques <span class="badge bg-warning ms-1">{{ $criticalItems->count() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="movements-tab" data-bs-toggle="tab" data-bs-target="#movements-pane" type="button">Mouvements & Statistiques</button>
        </li>
    </ul>

    <div class="tab-content">
        <!-- Articles Tab -->
        <div class="tab-pane fade show active" id="items-pane" role="tabpanel">
            @if($items->count())
                <div class="list-group shadow-sm">
                    @foreach($items as $item)
                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <div class="fw-semibold">{{ $item->name }}</div>
                                    <div class="small text-muted">
                                        Marque: {{ $item->brand ?? '—' }} • Catégorie: {{ $item->category ?? '—' }} • Unité: {{ $item->unit ?? '—' }}
                                    </div>
                                </div>
                                <div class="col-md-4 text-md-end">
                                    <span class="badge bg-info">Qty: {{ $item->initial_quantity }}</span>
                                    <span class="badge bg-secondary">Min: {{ $item->min_quantity }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-info">Aucun article dans cet emplacement.</div>
            @endif
        </div>

        <!-- Critical Items Tab -->
        <div class="tab-pane fade" id="critical-pane" role="tabpanel">
            @if($criticalItems->count())
                <div class="alert alert-warning d-flex align-items-center" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Attention!</strong> {{ $criticalItems->count() }} article(s) au-dessous du seuil minimum.
                </div>
                <div class="list-group shadow-sm">
                    @foreach($criticalItems as $item)
                        <div class="list-group-item list-group-item-warning">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <div class="fw-semibold">{{ $item->name }}</div>
                                    <div class="small text-dark">
                                        Marque: {{ $item->brand ?? '—' }} • Catégorie: {{ $item->category ?? '—' }}
                                    </div>
                                </div>
                                <div class="col-md-4 text-md-end">
                                    <span class="badge bg-danger">Qty: {{ $item->initial_quantity }}</span>
                                    <span class="badge bg-dark">Min: {{ $item->min_quantity }}</span>
                                    <span class="badge bg-danger">Rupture imminente</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    Tous les articles sont au-dessus du seuil minimum. Situation normale.
                </div>
            @endif
        </div>

        <!-- Movements & Statistics Tab -->
        <div class="tab-pane fade" id="movements-pane" role="tabpanel">
            <!-- Filters -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Filtres</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('stock-locations.show', $location) }}" method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label small">Type de mouvement</label>
                            <select name="movement_type" class="form-select form-select-sm">
                                <option value="">— Tous —</option>
                                <option value="in" @if(request('movement_type') == 'in') selected @endif>Entrée (In)</option>
                                <option value="out" @if(request('movement_type') == 'out') selected @endif>Sortie (Out)</option>
                                <option value="adjustment" @if(request('movement_type') == 'adjustment') selected @endif>Ajustement</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label small">Du</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control form-control-sm">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label small">Au</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control form-control-sm">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small">Projet</label>
                            <select name="project_id" class="form-select form-select-sm">
                                <option value="">— Tous les projets —</option>
                                @foreach($projects as $proj)
                                    <option value="{{ $proj->id }}" @if(request('project_id') == $proj->id) selected @endif>{{ $proj->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary btn-sm w-100">Filtrer</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Movements List -->
            @if($movements->count())
                <div class="table-responsive shadow-sm">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Article</th>
                                <th>Type</th>
                                <th>Quantité</th>
                                <th>Projet</th>
                                <th>Utilisateur</th>
                                <th>Raison</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($movements as $move)
                                <tr>
                                    <td>
                                        <small>{{ $move->created_at->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $move->stockItem?->name ?? '—' }}</strong>
                                    </td>
                                    <td>
                                        @if($move->type === 'in')
                                            <span class="badge bg-success">Entrée</span>
                                        @elseif($move->type === 'out')
                                            <span class="badge bg-danger">Sortie</span>
                                        @else
                                            <span class="badge bg-warning">Ajustement</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $move->quantity }}</strong>
                                    </td>
                                    <td>
                                        {{ $move->project?->name ?? '—' }}
                                    </td>
                                    <td>
                                        <small>{{ $move->user?->name ?? '—' }}</small>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $move->reason ?? '—' }}</small>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $movements->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    Aucun mouvement trouvé pour les critères sélectionnés.
                </div>
            @endif
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mt-4">
        <a href="{{ route('stock-locations.index') }}" class="btn btn-light">Retour</a>
        @if(auth()->user()->hasPermission('edit_stock') || auth()->user()->hasPermission('manage_settings'))
            <a href="{{ route('stock-locations.edit', $location) }}" class="btn btn-warning">Éditer</a>
        @endif
    </div>
</div>

<style>
    .border-left {
        border-left: 4px solid #dee2e6 !important;
    }
    .border-left.border-success {
        border-left-color: #198754 !important;
    }
    .border-left.border-danger {
        border-left-color: #dc3545 !important;
    }
    .border-left.border-warning {
        border-left-color: #ffc107 !important;
    }
    .border-left.border-info {
        border-left-color: #0dcaf0 !important;
    }
</style>
@endsection
