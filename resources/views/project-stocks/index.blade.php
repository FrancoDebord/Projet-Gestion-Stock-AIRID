@extends('layouts.app_new')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h4 mb-0">Stock par Projet</h1>
                <div>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                    <button class="btn btn-outline-primary" onclick="window.print()">
                        <i class="fas fa-print"></i> Imprimer
                    </button>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h3 class="card-title mb-2">{{ number_format($stats['total_items']) }}</h3>
                            <p class="card-text mb-0">Articles en stock</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h3 class="card-title mb-2">{{ number_format($stats['total_quantity']) }}</h3>
                            <p class="card-text mb-0">Quantité totale</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h3 class="card-title mb-2">{{ number_format($stats['total_projects']) }}</h3>
                            <p class="card-text mb-0">Projets actifs</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtres -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-filter"></i> Filtres
                    </h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('project-stocks.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Stock Location</label>
                            <select name="stock_location_id" class="form-select">
                                <option value="">Toutes les locations</option>
                                @foreach($stockLocations as $location)
                                    <option value="{{ $location->id }}" {{ $stockLocationId == $location->id ? 'selected' : '' }}>
                                        {{ $location->stock_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Projet</label>
                            <select name="project_id" class="form-select">
                                <option value="">Tous les projets</option>
                                <option value="global" {{ $projectId === 'global' ? 'selected' : '' }}>Global uniquement</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ $projectId == $project->id ? 'selected' : '' }}>
                                        {{ $project->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Catégorie</label>
                            <select name="product_category_id" class="form-select">
                                <option value="">Toutes les catégories</option>
                                @foreach($productCategories as $category)
                                    <option value="{{ $category->id }}" {{ $productCategoryId == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Recherche</label>
                            <input type="text" name="search" class="form-control"
                                   placeholder="Nom, SKU, marque..."
                                   value="{{ $search }}">
                        </div>

                        <div class="col-12">
                            <div class="form-check">
                                <input type="checkbox" name="show_empty" value="1"
                                       class="form-check-input" id="show_empty" {{ $showEmpty ? 'checked' : '' }}>
                                <label class="form-check-label" for="show_empty">
                                    Afficher aussi les articles sans stock
                                </label>
                            </div>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search"></i> Filtrer
                            </button>
                            <a href="{{ route('project-stocks.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Réinitialiser
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Résultats -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Résultats ({{ $stocks->total() }} articles)</h5>
                    <div class="text-muted small">
                        Page {{ $stocks->currentPage() }} sur {{ $stocks->lastPage() }}
                    </div>
                </div>
                <div class="card-body">
                    @if($stocks->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucun article trouvé</h5>
                            <p class="text-muted">Essayez de modifier vos critères de recherche.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Article</th>
                                        <th>Catégorie</th>
                                        <th>Location</th>
                                        <th>Projet</th>
                                        <th class="text-end">Quantité</th>
                                        <th>Dernier mouvement</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $currentItem = null; @endphp
                                    @foreach($stocks as $stock)
                                        @if($currentItem !== $stock->stock_item_id)
                                            @php $currentItem = $stock->stock_item_id; $rowspan = $stocks->where('stock_item_id', $stock->stock_item_id)->count(); @endphp
                                            <tr>
                                                <td rowspan="{{ $rowspan }}" class="align-middle">
                                                    <div>
                                                        <strong>{{ $stock->stockItem->name }}</strong>
                                                        @if($stock->stockItem->sku)
                                                            <br><small class="text-muted">SKU: {{ $stock->stockItem->sku }}</small>
                                                        @endif
                                                        @if($stock->stockItem->brand)
                                                            <br><small class="text-muted">{{ $stock->stockItem->brand }}</small>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td rowspan="{{ $rowspan }}" class="align-middle">
                                                    {{ $stock->stockItem->productCategory->name ?? 'N/A' }}
                                                </td>
                                                <td rowspan="{{ $rowspan }}" class="align-middle">
                                                    {{ $stock->stockItem->stockLocation->stock_name ?? 'N/A' }}
                                                </td>
                                        @else
                                            <tr>
                                        @endif
                                                <td>
                                                    <span class="badge bg-{{ $stock->project->name === 'Global' ? 'secondary' : 'primary' }}">
                                                        {{ $stock->project->name }}
                                                    </span>
                                                </td>
                                                <td class="text-end">
                                                    <span class="badge bg-{{ $stock->balance > 0 ? 'success' : 'danger' }} fs-6">
                                                        {{ number_format($stock->balance, 2) }}
                                                    </span>
                                                </td>
                                                <td class="text-muted small">
                                                    @if($stock->last_movement_at)
                                                        {{ $stock->last_movement_at->format('d/m/Y H:i') }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                            </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $stocks->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .btn, .card-header, .pagination, .form-check {
        display: none !important;
    }
    .container {
        max-width: none !important;
        margin: 0 !important;
        padding: 0 !important;
    }
}
</style>
@endsection
