@extends('layouts.app_new')

@section('content')
<div class="container-fluid py-4">
    <!-- Header with Image -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center">
                @if($stockItem->image)
                    <img src="{{ asset('storage/' . $stockItem->image) }}" alt="{{ $stockItem->name }}" class="card-img-top" style="height: 250px; object-fit: cover;">
                @else
                    <div class="card-body d-flex align-items-center justify-content-center" style="height: 250px; background: #f8f9fa;">
                        <i class="fas fa-box fa-5x text-secondary"></i>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-md-9">
            <h1 class="h2 mb-2">{{ $stockItem->name }}</h1>

            <div class="row g-2 mb-3">
                <div class="col-auto">
                    <span class="badge bg-primary">{{ $stockItem->brand ?? '—' }}</span>
                </div>
                <div class="col-auto">
                    <span class="badge bg-info">{{ $stockItem->category ?? '—' }}</span>
                </div>
                @if($stockItem->isLowStock())
                    <div class="col-auto">
                        <span class="badge bg-danger">Stock Critique</span>
                    </div>
                @endif
            </div>

            <p class="text-muted mb-3">{{ $stockItem->description }}</p>

            <!-- Info Cards -->
            <div class="row g-2">
                <div class="col-md-6">
                    <div class="card border-left border-4 border-info shadow-sm">
                        <div class="card-body py-2">
                            <small class="text-muted">Localisation</small>
                            <h6 class="mb-0">{{ $stockItem->stockLocation->stock_name ?? '—' }}</h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-left border-4 border-success shadow-sm">
                        <div class="card-body py-2">
                            <small class="text-muted">Quantité Actuelle</small>
                            <h6 class="mb-0">{{ $stockItem->initial_quantity }} {{ $stockItem->unit }}</h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-left border-4 border-warning shadow-sm">
                        <div class="card-body py-2">
                            <small class="text-muted">Quantité Minimum</small>
                            <h6 class="mb-0">{{ $stockItem->min_quantity }} {{ $stockItem->unit }}</h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-left border-4 border-secondary shadow-sm">
                        <div class="card-body py-2">
                            <small class="text-muted">Prix Unitaire</small>
                            <h6 class="mb-0">{{ $stockItem->unit_price ? number_format($stockItem->unit_price, 2) . ' €' : '—' }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Section -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-left border-4 border-success shadow-sm">
                <div class="card-body">
                    <h6 class="card-title text-muted">Entrées Totales</h6>
                    <h3 class="mb-0">{{ $movementsIn }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left border-4 border-danger shadow-sm">
                <div class="card-body">
                    <h6 class="card-title text-muted">Sorties Totales</h6>
                    <h3 class="mb-0">{{ $movementsOut }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left border-4 border-warning shadow-sm">
                <div class="card-body">
                    <h6 class="card-title text-muted">Mouvements</h6>
                    <h3 class="mb-0">{{ $totalMovements }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left border-4 border-info shadow-sm">
                <div class="card-body">
                    <h6 class="card-title text-muted">Valeur Stock</h6>
                    <h3 class="mb-0">{{ $stockItem->unit_price ? number_format($stockItem->initial_quantity * $stockItem->unit_price, 2) : '—' }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters for movements and usage -->
    <div class="card mb-3 shadow-sm">
        <div class="card-body">
            <form method="GET" action="" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small">Date début</label>
                    <input type="date" name="from" value="{{ request('from') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Date fin</label>
                    <input type="date" name="to" value="{{ request('to') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Projet</label>
                    <select name="project_id" class="form-select form-select-sm">
                        <option value="">— Tous les projets —</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}" @if(request('project_id') == $p->id) selected @endif>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Demandeur</label>
                    <select name="requester_id" class="form-select form-select-sm">
                        <option value="">— Tous —</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" @if(request('requester_id') == $u->id) selected @endif>{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <button class="btn btn-primary btn-sm w-100">Appliquer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="movements-tab" data-bs-toggle="tab" data-bs-target="#movements-pane" type="button">Mouvements</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="projects-tab" data-bs-toggle="tab" data-bs-target="#projects-pane" type="button">Utilisation par Projet</button>
        </li>
    </ul>

    <div class="tab-content">
        <!-- Movements Tab -->
        <div class="tab-pane fade show active" id="movements-pane" role="tabpanel">
            @if($movements->count())
                <div class="table-responsive shadow-sm rounded">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
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
                                        {{ $move->project?->name ?? 'NA (Usage Général)' }}
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
                    Aucun mouvement enregistré pour cet article.
                </div>
            @endif
        </div>

        <!-- Projects Tab -->
        <div class="tab-pane fade" id="projects-pane" role="tabpanel">
            @if($projectUsage->count() > 0 || (isset($dailyUsage) && $dailyUsage->count() > 0))
                <div class="row g-3">
                    <!-- Chart -->
                    <div class="col-md-8">
                        <div class="card shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Utilisation par Projet</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="projectChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="col-md-4">
                        <div class="card shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Détails d'Utilisation</h6>
                            </div>
                            <div class="list-group list-group-flush">
                                @foreach($projectUsage as $usage)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $usage->project?->name ?? '—' }}</strong>
                                            <div class="small text-muted">{{ $usage->count }} mouvement(s)</div>
                                        </div>
                                        <span class="badge bg-primary">{{ $usage->total_qty ?? 0 }} total</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                @if(isset($dailyUsage) && $dailyUsage->count() > 0)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card shadow-sm">
                                <div class="card-header bg-light"><h6 class="mb-0">Historique d'utilisation (par jour)</h6></div>
                                <div class="card-body">
                                    <canvas id="dailyChart" height="100"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <div class="alert alert-info">
                    Cet article n'a pas encore été utilisé par des projets spécifiques.
                </div>
            @endif
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mt-4">
        <a href="{{ route('stock-items.index') }}" class="btn btn-light">Retour</a>
        @if(auth()->user()->hasPermission('edit_stock') || auth()->user()->hasPermission('manage_settings'))
            <a href="{{ route('stock-items.edit', $stockItem) }}" class="btn btn-warning">Éditer</a>
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
    .border-left.border-secondary {
        border-left-color: #6c757d !important;
    }
</style>

@if($projectUsage->count() > 0)
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('projectChart').getContext('2d');
    
    const labels = [
        @foreach($projectUsage as $usage)
            '{{ $usage->project?->name ?? "NA" }}',
        @endforeach
    ];

    const quantities = [
        @foreach($projectUsage as $usage)
            {{ $usage->total_qty ?? 0 }},
        @endforeach
    ];

    const colors = [
        '#0d6efd', '#6f42c1', '#dc3545', '#fd7e14', '#198754',
        '#20c997', '#17a2b8', '#e83e8c', '#6f42c1', '#273c75'
    ];

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: quantities,
                backgroundColor: colors.slice(0, labels.length),
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>
@endif
@if(isset($dailyUsage) && $dailyUsage->count() > 0)
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx2 = document.getElementById('dailyChart').getContext('2d');
    const labels = [
        @foreach($dailyUsage as $d)
            '{{ $d->date }}',
        @endforeach
    ];
    const data = [
        @foreach($dailyUsage as $d)
            {{ $d->total_qty }},
        @endforeach
    ];

    new Chart(ctx2, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Quantité utilisée (sorties)',
                data: data,
                borderColor: '#dc3545',
                backgroundColor: 'rgba(220,53,69,0.1)',
                fill: true,
                tension: 0.2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { display: true },
                y: { display: true }
            }
        }
    });
});
</script>
@endif
@endsection
