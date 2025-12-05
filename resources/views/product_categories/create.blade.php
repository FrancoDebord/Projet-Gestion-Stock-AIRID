@extends('layouts.app_new')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h1 class="h4 mb-3">Créer une Catégorie</h1>

            <div class="card shadow-sm p-4">
                <form action="{{ route('product-categories.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Nom <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="form-control @error('name') is-invalid @enderror">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Stock Location</label>
                        <select name="stock_location_id" class="form-select @error('stock_location_id') is-invalid @enderror">
                            <option value="">-- Sélectionner une location --</option>
                            @foreach($stockLocations as $location)
                                <option value="{{ $location->id }}" {{ old('stock_location_id') == $location->id ? 'selected' : '' }}>
                                    {{ $location->stock_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('stock_location_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('product-categories.index') }}" class="btn btn-light">Annuler</a>
                        <button type="submit" class="btn btn-primary">Créer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

