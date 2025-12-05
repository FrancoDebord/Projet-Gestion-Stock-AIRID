@extends('layouts.app_new')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h4 mb-3">Nouvelle Réception (Administration)</h1>

            <div class="card shadow-sm p-4">
                <form action="{{ route('stock-arrivals-admin.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Date de Réception *</label>
                        <input type="datetime-local" name="date_arrival" value="{{ old('date_arrival') }}" required class="form-control @error('date_arrival') is-invalid @enderror">
                        @error('date_arrival')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Expéditeur</label>
                        <input type="text" name="sender" value="{{ old('sender') }}" class="form-control @error('sender') is-invalid @enderror">
                        @error('sender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description Globale</label>
                        <textarea name="description_globale" id="description_globale" rows="5" class="form-control @error('description_globale') is-invalid @enderror">{{ old('description_globale') }}</textarea>
                        @error('description_globale')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Destination (Stock Location) *</label>
                        <select name="stock_location_destination" class="form-select @error('stock_location_destination') is-invalid @enderror" required>
                            <option value="">— Sélectionner —</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" @if(old('stock_location_destination') == $loc->id) selected @endif>{{ $loc->stock_name }}</option>
                            @endforeach
                        </select>
                        @error('stock_location_destination')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Staff Administratif (reçu par) *</label>
                            <select name="administration_staff" class="form-select @error('administration_staff') is-invalid @enderror" required>
                                <option value="">— Sélectionner —</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}" @if(old('administration_staff') == $u->id) selected @endif>{{ $u->name }}</option>
                                @endforeach
                            </select>
                            @error('administration_staff')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Staff Transmis au Stock</label>
                            <select name="staff_transmis_stock" class="form-select @error('staff_transmis_stock') is-invalid @enderror">
                                <option value="">— Sélectionner —</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}" @if(old('staff_transmis_stock') == $u->id) selected @endif>{{ $u->name }}</option>
                                @endforeach
                            </select>
                            @error('staff_transmis_stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <hr>
                    <h6>Documents reçus (optionnel)</h6>
                    <div class="mb-3">
                        <label class="form-label">Bordereau de Livraison (PDF/JPG/PNG)</label>
                        <input type="file" name="bordereau_delivery" accept="application/pdf,image/*" class="form-control @error('bordereau_delivery') is-invalid @enderror">
                        @error('bordereau_delivery')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Certificate d'Analyse</label>
                        <input type="file" name="certificate_analysis" accept="application/pdf,image/*" class="form-control @error('certificate_analysis') is-invalid @enderror">
                        @error('certificate_analysis')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">MSDS</label>
                        <input type="file" name="msds" accept="application/pdf,image/*" class="form-control @error('msds') is-invalid @enderror">
                        @error('msds')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Autre Document</label>
                        <input type="file" name="other_document" accept="application/pdf,image/*" class="form-control @error('other_document') is-invalid @enderror">
                        @error('other_document')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <a href="{{ route('stock-arrivals-admin.index') }}" class="btn btn-light">Annuler</a>
                        <button class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        $('#description_globale').summernote({
            placeholder: 'Entrez votre description...',
            tabsize: 2,
            height: 300,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
    });
</script>
@endsection
