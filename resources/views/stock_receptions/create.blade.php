@extends('layouts.app_new')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h1 class="h4 mb-3">Nouvelle Réception Stock</h1>

            <div class="card shadow-sm p-4">
                <form action="{{ route('stock-receptions.store') }}" method="POST" enctype="multipart/form-data" id="receptionForm">
                    @csrf

                    <h5 class="mb-3">Informations Générales</h5>

                    <div class="mb-3">
                        <label class="form-label">Réception Administration *</label>
                        <select name="stock_arrival_admin_id" class="form-select @error('stock_arrival_admin_id') is-invalid @enderror" required>
                            <option value="">— Sélectionner une réception admin —</option>
                            @foreach($arrivals as $arrival)
                                <option value="{{ $arrival->id }}" @if(old('stock_arrival_admin_id') == $arrival->id) selected @endif>
                                    {{ $arrival->date_arrival->format('d/m/Y H:i') }} - {{ $arrival->sender ?? 'Expéditeur inconnu' }}
                                </option>
                            @endforeach
                        </select>
                        @error('stock_arrival_admin_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Date de Réception *</label>
                        <input type="datetime-local" name="date_reception" value="{{ old('date_reception', now()->format('Y-m-d\TH:i')) }}" required class="form-control @error('date_reception') is-invalid @enderror">
                        @error('date_reception')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description Globale</label>
                        <textarea name="description_globale" id="description_globale" rows="3" class="form-control @error('description_globale') is-invalid @enderror">{{ old('description_globale') }}</textarea>
                        @error('description_globale')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Reçu par *</label>
                            <select name="receiver" class="form-select @error('receiver') is-invalid @enderror" required>
                                <option value="">— Sélectionner —</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" @if(old('receiver') == $user->id) selected @endif>{{ $user->name }}</option>
                                @endforeach
                            </select>
                            @error('receiver')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Destination Stock *</label>
                            <select name="stock_location_destination_id" class="form-select @error('stock_location_destination_id') is-invalid @enderror" required>
                                <option value="">— Sélectionner —</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}" @if(old('stock_location_destination_id') == $location->id) selected @endif>{{ $location->stock_name }}</option>
                                @endforeach
                            </select>
                            @error('stock_location_destination_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Projet</label>
                            <select name="project_id" class="form-select @error('project_id') is-invalid @enderror">
                                <option value="">— Aucun projet —</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" @if(old('project_id') == $project->id) selected @endif>{{ $project->name }}</option>
                                @endforeach
                            </select>
                            @error('project_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Expéditeur</label>
                            <input type="text" name="sender" value="{{ old('sender') }}" class="form-control @error('sender') is-invalid @enderror">
                            @error('sender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <h5 class="mb-3">Documents</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Certificat d'Analyse</label>
                            <input type="file" name="certificat_analyse" accept="application/pdf,image/*" class="form-control @error('certificat_analyse') is-invalid @enderror">
                            @error('certificat_analyse')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">MSDS</label>
                            <input type="file" name="msds" accept="application/pdf,image/*" class="form-control @error('msds') is-invalid @enderror">
                            @error('msds')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Bordereau de Livraison</label>
                            <input type="file" name="borderau_livraison" accept="application/pdf,image/*" class="form-control @error('borderau_livraison') is-invalid @enderror">
                            @error('borderau_livraison')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <h5 class="mb-3">Détails des Produits</h5>
                    <div id="detailsContainer">
                        <div class="detail-row border rounded p-3 mb-3" data-index="0">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Produit *</label>
                                    <select name="details[0][stock_item_id]" class="form-select stock-item-select @error('details.0.stock_item_id') is-invalid @enderror" required>
                                        <option value="">— Sélectionner un produit —</option>
                                        @foreach($stockItems as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->unit }})</option>
                                        @endforeach
                                    </select>
                                    @error('details.0.stock_item_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Code Lot *</label>
                                    <input type="text" name="details[0][code_lot]" class="form-control @error('details.0.code_lot') is-invalid @enderror" required>
                                    @error('details.0.code_lot')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Numéro de Lot</label>
                                    <input type="text" name="details[0][batch_number]" class="form-control">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Quantité *</label>
                                    <input type="number" name="details[0][quantite_lot]" class="form-control @error('details.0.quantite_lot') is-invalid @enderror" min="1" required>
                                    @error('details.0.quantite_lot')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <button type="button" class="btn btn-outline-primary btn-sm" id="addDetailBtn">
                            <i class="fas fa-plus"></i> Ajouter un produit
                        </button>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('stock-receptions.index') }}" class="btn btn-light">Annuler</a>
                        <button type="submit" class="btn btn-primary">Enregistrer la Réception</button>
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
        height: 200,
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
    let detailIndex = 1;

    // Clone the first detail row template
    const detailTemplate = document.querySelector('.detail-row').cloneNode(true);

    document.getElementById('addDetailBtn').addEventListener('click', function() {
        const container = document.getElementById('detailsContainer');
        const newDetail = detailTemplate.cloneNode(true);

        // Update indices in the cloned element
        newDetail.setAttribute('data-index', detailIndex);
        const inputs = newDetail.querySelectorAll('input, select');
        inputs.forEach(input => {
            const name = input.getAttribute('name');
            if (name) {
                input.setAttribute('name', name.replace('[0]', '[' + detailIndex + ']'));
                input.value = ''; // Clear values
                input.classList.remove('is-invalid'); // Remove error classes
            }
        });

        // Add remove button
        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'btn btn-outline-danger btn-sm mt-2';
        removeBtn.innerHTML = '<i class="fas fa-trash"></i> Retirer';
        removeBtn.addEventListener('click', function() {
            newDetail.remove();
        });

        newDetail.appendChild(removeBtn);
        container.appendChild(newDetail);
        detailIndex++;
    });

    // Add remove button to the first row
    const firstRow = document.querySelector('.detail-row');
    if (firstRow) {
        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'btn btn-outline-danger btn-sm mt-2';
        removeBtn.innerHTML = '<i class="fas fa-trash"></i> Retirer';
        removeBtn.addEventListener('click', function() {
            if (document.querySelectorAll('.detail-row').length > 1) {
                firstRow.remove();
            }
        });
        firstRow.appendChild(removeBtn);
    }
});
</script>
@endsection
