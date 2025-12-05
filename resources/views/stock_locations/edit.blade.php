@extends('layouts.app_new')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h1 class="h4 mb-3">Modifier l'emplacement</h1>

            <div class="card shadow-sm p-3">
                <form action="{{ route('stock-locations.update', $stockLocation) }}" method="POST">
                    @method('PATCH')
                    @include('stock_locations._form')
                    <div class="d-flex justify-content-end mt-3">
                        <a href="{{ route('stock-locations.show', $stockLocation) }}" class="btn btn-light me-2">Annuler</a>
                        <button class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
