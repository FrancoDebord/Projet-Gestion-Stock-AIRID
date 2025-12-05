@extends('layouts.app_new')

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Catégories de Produits</h1>
        @if(auth()->user()->hasPermission('create_stock') || auth()->user()->hasPermission('manage_settings'))
            <a href="{{ route('product-categories.create') }}" class="btn btn-primary">Ajouter une Catégorie</a>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nom</th>
                        <th>Stock Location</th>
                        <th>Description</th>
                        <th>Articles</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $cat)
                        <tr>
                            <td>
                                <strong>{{ $cat->name }}</strong>
                            </td>
                            <td>
                                @if($cat->stockLocation)
                                    <span class="badge bg-secondary">{{ $cat->stockLocation->stock_name }}</span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ Str::limit($cat->description, 60) ?? '—' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $cat->items_count }}</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('product-categories.show', $cat) }}" class="btn btn-outline-secondary">Voir</a>
                                    @if(auth()->user()->hasPermission('edit_stock') || auth()->user()->hasPermission('manage_settings'))
                                        <a href="{{ route('product-categories.edit', $cat) }}" class="btn btn-outline-warning">Éditer</a>
                                    @endif
                                    @if(auth()->user()->hasPermission('delete_stock') || auth()->user()->hasPermission('manage_settings'))
                                        <form action="{{ route('product-categories.destroy', $cat) }}" method="POST" onsubmit="return confirm('Confirmer la suppression ?');" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger">Supprimer</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Aucune catégorie trouvée.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $categories->links() }}</div>
</div>
@endsection
