@extends('layouts.app_new')

@section('content')
<div class="container py-5">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Emplacements de stock</h1>
        @if(auth()->user()->hasPermission('create_stock') || auth()->user()->hasPermission('manage_settings'))
            <a href="{{ route('stock-locations.create') }}" class="btn btn-primary">Add new Stock Location</a>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="list-group list-group-flush">
            @foreach($locations as $loc)
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-semibold">{{ $loc->stock_name }} @if($loc->code_stock)<small class="text-muted">({{ $loc->code_stock }})</small>@endif</div>
                        <div class="text-muted small">Responsable: {{ $loc->principalManager?->name ?? '—' }} · Créé par {{ $loc->creatorUser?->name ?? '—' }}</div>
                    </div>
                    <div class="btn-group btn-group-sm" role="group">
                        <a href="{{ route('stock-locations.show', $loc) }}" class="btn btn-outline-secondary">Voir</a>
                        @if(auth()->user()->hasPermission('edit_stock') || auth()->user()->hasPermission('manage_settings'))
                            <a href="{{ route('stock-locations.edit', $loc) }}" class="btn btn-outline-warning">Éditer</a>
                        @endif
                        @if(auth()->user()->hasPermission('delete_stock') || auth()->user()->hasPermission('manage_settings'))
                            <form action="{{ route('stock-locations.destroy', $loc) }}" method="POST" onsubmit="return confirm('Supprimer cet emplacement ?');" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger">Supprimer</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="mt-3">{{ $locations->links() }}</div>
</div>
@endsection
