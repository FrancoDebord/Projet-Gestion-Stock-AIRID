@extends('layouts.app_new')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Articles de Stock</h1>
        @if(auth()->user()->hasPermission('create_stock') || auth()->user()->hasPermission('manage_settings'))
            <a href="{{ route('stock-items.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Ajouter un Article
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- View Toggle -->
    <div class="mb-3 d-flex gap-2">
        <button id="gridViewBtn" class="btn btn-outline-primary btn-sm active" onclick="switchView('grid')">
            <i class="fas fa-th"></i> Grille
        </button>
        <button id="tableViewBtn" class="btn btn-outline-primary btn-sm" onclick="switchView('table')">
            <i class="fas fa-list"></i> Tableau
        </button>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('stock-items.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small">Rechercher</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom de l'article..." class="form-control form-control-sm">
                </div>

                <div class="col-md-3">
                    <label class="form-label small">Localisation</label>
                    <select name="location_id" class="form-select form-select-sm">
                        <option value="">— Tous les emplacements —</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" @if(request('location_id') == $loc->id) selected @endif>{{ $loc->stock_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small">Catégorie</label>
                    <select name="category" class="form-select form-select-sm">
                        <option value="">— Toutes les catégories —</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->name }}" @if(request('category') == $cat->name) selected @endif>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm w-100">Filtrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Items Grid/Table -->
    <div id="gridView" class="row g-3">
        @forelse($items as $item)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0 overflow-hidden">
                    <!-- Image -->
                    <div class="bg-light" style="height: 180px; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                        @if($item->image)
                            <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="img-fluid" style="max-height: 180px; object-fit: cover; width: 100%;">
                        @else
                            <i class="fas fa-box fa-3x text-secondary"></i>
                        @endif
                    </div>

                    <!-- Content -->
                    <div class="card-body">
                        <h5 class="card-title">{{ $item->name }}</h5>
                        <p class="card-text text-muted small">{{ $item->brand ?? '—' }}</p>

                        <div class="mb-2">
                            <span class="badge bg-info">{{ $item->initial_quantity }} {{ $item->unit }}</span>
                            <span class="badge bg-secondary small">Min: {{ $item->min_quantity }}</span>
                        </div>

                        <div class="small text-muted">
                            <i class="fas fa-map-marker-alt"></i> {{ $item->stockLocation->stock_name ?? '—' }}
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="card-footer bg-light">
                        <div class="btn-group btn-group-sm w-100" role="group">
                            <a href="{{ route('stock-items.show', $item) }}" class="btn btn-outline-secondary" title="Voir">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if(auth()->user()->hasPermission('edit_stock') || auth()->user()->hasPermission('manage_settings'))
                                <a href="{{ route('stock-items.edit', $item) }}" class="btn btn-outline-warning" title="Éditer">
                                    <i class="fas fa-edit"></i>
                                </a>
                            @endif
                            @if(auth()->user()->hasPermission('delete_stock') || auth()->user()->hasPermission('manage_settings'))
                                <form action="{{ route('stock-items.destroy', $item) }}" method="POST" onsubmit="return confirm('Confirmer la suppression ?');" class="d-inline w-100">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger w-100" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center py-5">
                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                    <strong>Aucun article trouvé</strong><br>
                    <small class="text-muted">Commencez par créer un nouvel article</small>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Table View -->
    <div id="tableView" class="card shadow-sm d-none">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Image</th>
                        <th>Nom</th>
                        <th>Marque</th>
                        <th>Quantité</th>
                        <th>Min</th>
                        <th>Unité</th>
                        <th>Localisation</th>
                        <th>Prix Unitaire</th>
                        <th>Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td>
                                @if($item->image)
                                    <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" style="max-width: 40px; max-height: 40px; object-fit: cover; border-radius: 4px;">
                                @else
                                    <i class="fas fa-box text-secondary"></i>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $item->name }}</strong>
                            </td>
                            <td>{{ $item->brand ?? '—' }}</td>
                            <td>
                                <span class="badge bg-info">{{ $item->initial_quantity }}</span>
                            </td>
                            <td>{{ $item->min_quantity }}</td>
                            <td>{{ $item->unit }}</td>
                            <td>
                                <small class="text-muted">{{ $item->stockLocation->stock_name ?? '—' }}</small>
                            </td>
                            <td>
                                @if($item->unit_price)
                                    <strong>${{ number_format($item->unit_price, 2) }}</strong>
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $item->type_usage_product == 'finished' ? 'bg-success' : 'bg-warning' }}">
                                    {{ $item->type_usage_product == 'finished' ? 'Fini' : 'Consommé' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('stock-items.show', $item) }}" class="btn btn-outline-secondary" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(auth()->user()->hasPermission('edit_stock') || auth()->user()->hasPermission('manage_settings'))
                                        <a href="{{ route('stock-items.edit', $item) }}" class="btn btn-outline-warning" title="Éditer">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                    @if(auth()->user()->hasPermission('delete_stock') || auth()->user()->hasPermission('manage_settings'))
                                        <form action="{{ route('stock-items.destroy', $item) }}" method="POST" onsubmit="return confirm('Confirmer la suppression ?');" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">Aucun article trouvé</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $items->links() }}
    </div>
</div>

<script>
function switchView(view) {
    const gridView = document.getElementById('gridView');
    const tableView = document.getElementById('tableView');
    const gridBtn = document.getElementById('gridViewBtn');
    const tableBtn = document.getElementById('tableViewBtn');

    if (view === 'grid') {
        gridView.classList.remove('d-none');
        tableView.classList.add('d-none');
        gridBtn.classList.add('active');
        tableBtn.classList.remove('active');
        localStorage.setItem('stockItemsView', 'grid');
    } else {
        gridView.classList.add('d-none');
        tableView.classList.remove('d-none');
        gridBtn.classList.remove('active');
        tableBtn.classList.add('active');
        localStorage.setItem('stockItemsView', 'table');
    }
}

// Restore saved view preference on page load
document.addEventListener('DOMContentLoaded', function() {
    const savedView = localStorage.getItem('stockItemsView') || 'grid';
    switchView(savedView);
});
</script>
@endsection
