@extends('layouts.app_new')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h1 class="h4 mb-3">Modifier Réception Stock #{{ $stockReception->id }}</h1>

            <div class="card shadow-sm p-4">
                <form action="{{ route('stock-receptions.update', $stockReception) }}" method="POST" enctype="multipart/form-data" id="receptionForm">
                    @csrf @method('PATCH')

                    <h5 class="mb-3">Informations Générales</h5>

                    <div class="mb-3">
                        <label class="form-label">Réception Administration *</label>
                        <select name="stock_arrival_admin_id" class="form-select @error('stock_arrival_admin_id') is-invalid @enderror" required>
                            <option value="">— Sélectionner une réception admin —</option>
                            @foreach($arrivals as $arrival)
                                <option value="{{ $arrival->id }}" @if(old('stock_arrival_admin_id', $stockReception->stock_arrival_admin_id) == $arrival->id) selected @endif>
                                    {{ $arrival->date_arrival->format('d/m/Y H:i') }} - {{ $arrival->sender ?? 'Expéditeur inconnu' }}
                                </option>
                            @endforeach
                        </select>
                        @error('stock_arrival_admin_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Date de Réception *</label>
                        <input type="datetime-local" name="date_reception" value="{{ old('date_reception', $stockReception->date_reception->format('Y-m-d\TH:i')) }}" required class="form-control @error('date_reception') is-invalid @enderror">
                        @error('date_reception')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description Globale</label>
                        <textarea name="description_globale" id="description_globale" rows="3" class="form-control @error('description_globale') is-invalid @enderror">{{ old('description_globale', $stockReception->description_globale) }}</textarea>
                        @error('description_globale')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Reçu par *</label>
                            <select name="receiver" class="form-select @error('receiver') is-invalid @enderror" required>
                                <option value="">— Sélectionner —</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" @if(old('receiver', $stockReception->receiver_id) == $user->id) selected @endif>{{ $user->name }}</option>
                                @endforeach
                            </select>
                            @error('receiver')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Destination Stock *</label>
                            <select name="stock_location_destination_id" class="form-select @error('stock_location_destination_id') is-invalid @enderror" required>
                                <option value="">— Sélectionner —</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}" @if(old('stock_location_destination_id', $stockReception->stock_location_destination_id) == $location->id) selected @endif>{{ $location->stock_name }}</option>
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
                                    <option value="{{ $project->id }}" @if(old('project_id', $stockReception->project_id) == $project->id) selected @endif>{{ $project->name }}</option>
                                @endforeach
                            </select>
                            @error('project_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Expéditeur</label>
                            <input type="text" name="sender" value="{{ old('sender', $stockReception->sender) }}" class="form-control @error('sender') is-invalid @enderror">
                            @error('sender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <h5 class="mb-3">Documents</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Certificat d'Analyse</label>
                            <input type="file" name="certificat_analyse" accept="application/pdf,image/*" class="form-control @error('certificat_analyse') is-invalid @enderror">
                            @if($stockReception->certificat_analyse)
                                <small class="text-muted">Fichier actuel: <a href="{{ Storage::url($stockReception->certificat_analyse) }}" target="_blank">Voir</a></small>
                            @endif
                            @error('certificat_analyse')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">MSDS</label>
                            <input type="file" name="msds" accept="application/pdf,image/*" class="form-control @error('msds') is-invalid @enderror">
                            @if($stockReception->msds)
                                <small class="text-muted">Fichier actuel: <a href="{{ Storage::url($stockReception->msds) }}" target="_blank">Voir</a></small>
                            @endif
                            @error('msds')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Bordereau de Livraison</label>
                            <input type="file" name="borderau_livraison" accept="application/pdf,image/*" class="form-control @error('borderau_livraison') is-invalid @enderror">
                            @if($stockReception->borderau_livraison)
                                <small class="text-muted">Fichier actuel: <a href="{{ Storage::url($stockReception->borderau_livraison) }}" target="_blank">Voir</a></small>
                            @endif
                            @error('borderau_livraison')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <h5 class="mb-3">Détails des Produits</h5>
                    <div id="detailsContainer">
                        @foreach($stockReception->details as $index => $detail)
                        <div class="detail-row border rounded p-3 mb-3" data-index="{{ $index }}">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Produit *</label>
                                    <select name="details[{{ $index }}][stock_item_id]" class="form-select stock-item-select @error('details.'.$index.'.stock_item_id') is-invalid @enderror" required>
                                        <option value="">— Sélectionner un produit —</option>
                                        @foreach($stockItems as $item)
                                            <option value="{{ $item->id }}" @if(old('details.'.$index.'.stock_item_id', $detail->stock_item_id) == $item->id) selected @endif>{{ $item->name }} ({{ $item->unit }})</option>
                                        @endforeach
                                    </select>
                                    @error('details.'.$index.'.stock_item_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Code Lot *</label>
                                    <input type="text" name="details[{{ $index }}][code_lot]" value="{{ old('details.'.$index.'.code_lot', $detail->code_lot) }}" class="form-control @error('details.'.$index.'.code_lot') is-invalid @enderror" required>
                                    @error('details.'.$index.'.code_lot')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Numéro de Lot</label>
                                    <input type="text" name="details[{{ $index }}][batch_number]" value="{{ old('details.'.$index.'.batch_number', $detail->batch_number) }}" class="form-control">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Quantité *</label>
                                    <input type="number" name="details[{{ $index }}][quantite_lot]" value="{{ old('details.'.$index.'.quantite_lot', $detail->quantite_lot) }}" class="form-control @error('details.'.$index.'.quantite_lot') is-invalid @enderror" min="1" required>
                                    @error('details.'.$index.'.quantite_lot')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-danger btn-sm mt-2 remove-detail-btn" {{ $stockReception->details->count() <= 1 ? 'disabled' : '' }}>
                                <i class="fas fa-trash"></i> Retirer
                            </button>
                        </div>
                        @endforeach
                    </div>

                    <div class="mb-3">
                        <button type="button" class="btn btn-outline-primary btn-sm" id="addDetailBtn">
                            <i class="fas fa-plus"></i> Ajouter un produit
                        </button>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('stock-receptions.show', $stockReception) }}" class="btn btn-light">Annuler</a>
                        <button type="submit" class="btn btn-primary">Mettre à jour</button>
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
    let detailIndex = {{ $stockReception->details->count() }};

    // Clone the last detail row template
    const detailTemplate = document.querySelector('.detail-row:last-child').cloneNode(true);

    document.getElementById('addDetailBtn').addEventListener('click', function() {
        const container = document.getElementById('detailsContainer');
        const newDetail = detailTemplate.cloneNode(true);

        // Update indices in the cloned element
        newDetail.setAttribute('data-index', detailIndex);
        const inputs = newDetail.querySelectorAll('input, select');
        inputs.forEach(input => {
            const name = input.getAttribute('name');
            if (name) {
                // Replace the index in the name
                const newName = name.replace(/\[\d+\]/, '[' + detailIndex + ']');
                input.setAttribute('name', newName);
                input.value = ''; // Clear values
                input.classList.remove('is-invalid'); // Remove error classes
            }
        });

        // Enable remove button for all rows
        document.querySelectorAll('.remove-detail-btn').forEach(btn => {
            btn.disabled = false;
        });

        // Add remove functionality to new row
        const removeBtn = newDetail.querySelector('.remove-detail-btn');
        removeBtn.addEventListener('click', function() {
            newDetail.remove();
            // Disable remove button if only one row left
            if (document.querySelectorAll('.detail-row').length <= 1) {
                document.querySelector('.remove-detail-btn').disabled = true;
            }
        });

        container.appendChild(newDetail);
        detailIndex++;
    });

    // Add remove functionality to existing rows
    document.querySelectorAll('.remove-detail-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (document.querySelectorAll('.detail-row').length > 1) {
                this.closest('.detail-row').remove();
                // Disable remove button if only one row left
                if (document.querySelectorAll('.detail-row').length <= 1) {
                    document.querySelector('.remove-detail-btn').disabled = true;
                }
            }
        });
    });
});
</script>
@endsection
