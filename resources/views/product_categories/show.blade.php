@extends('layouts.app_new')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h3 mb-3">{{ $productCategory->name }}</h1>
            <p class="text-muted mb-0">{{ $productCategory->description }}</p>
        </div>
    </div>

    <!-- Articles -->
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h6 class="mb-0">Articles dans cette catégorie ({{ $items->total() }})</h6>
        </div>

        @if($items->count())
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nom</th>
                            <th>Marque</th>
                            <th>Localisation</th>
                            <th>Quantité</th>
                            <th>Unité</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                            <tr>
                                <td><strong>{{ $item->name }}</strong></td>
                                <td>{{ $item->brand ?? '—' }}</td>
                                <td><small>{{ $item->stockLocation->stock_name ?? '—' }}</small></td>
                                <td><span class="badge bg-info">{{ $item->initial_quantity }}</span></td>
                                <td>{{ $item->unit }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card-footer">{{ $items->links() }}</div>
        @else
            <div class="card-body text-center text-muted py-4">
                Aucun article dans cette catégorie.
            </div>
        @endif
    </div>

    <div class="mt-4">
        <a href="{{ route('product-categories.index') }}" class="btn btn-light">Retour</a>
        @if(auth()->user()->hasPermission('edit_stock') || auth()->user()->hasPermission('manage_settings'))
            <a href="{{ route('product-categories.edit', $productCategory) }}" class="btn btn-warning">Éditer</a>
        @endif
    </div>
</div>
@endsection
