@extends('layouts.app_new')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h1 class="h4 mb-3">Modifier Demande de Stock #{{ $stockRequest->id }}</h1>

            <div class="card shadow-sm p-4">
                <form action="{{ route('stock-requests.update', $stockRequest) }}" method="POST" id="requestForm">
                    @csrf @method('PATCH')

                    <h5 class="mb-3">Informations Générales</h5>

                    <div class="mb-3">
                        <label class="form-label">Date de Demande *</label>
                        <input type="datetime-local" name="request_date" value="{{ old('request_date', $stockRequest->request_date->format('Y-m-d\TH:i')) }}" required class="form-control @error('request_date') is-invalid @enderror">
                        @error('request_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Projet</label>
                            <select name="project_id" class="form-select @error('project_id') is-invalid @enderror">
                                <option value="">— Aucun projet —</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" @if(old('project_id', $stockRequest->project_id) == $project->id) selected @endif>{{ $project->name }}</option>
                                @endforeach
                            </select>
                            @error('project_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Numéro de Machine</label>
                            <input type="text" name="code_machine" value="{{ old('code_machine', $stockRequest->code_machine) }}" class="form-control @error('code_machine') is-invalid @enderror" maxlength="100">
                            @error('code_machine')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Numéro de Bureau</label>
                            <input type="text" name="room_number" value="{{ old('room_number', $stockRequest->room_number) }}" class="form-control @error('room_number') is-invalid @enderror" maxlength="50">
                            @error('room_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notes Générales</label>
                        <textarea name="general_notes" id="general_notes" rows="3" class="form-control @error('general_notes') is-invalid @enderror">{{ old('general_notes', $stockRequest->general_notes) }}</textarea>
                        @error('general_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <h5 class="mb-3">Détails des Produits Demandés</h5>
                    <div id="detailsContainer">
                        @foreach($stockRequest->details as $index => $detail)
                        <div class="detail-row border rounded p-3 mb-3" data-index="{{ $index }}">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Produit *</label>
                                    <select name="details[{{ $index }}][stock_item_id]" class="form-select @error('details.'.$index.'.stock_item_id') is-invalid @enderror" required>
                                        <option value="">— Sélectionner un produit —</option>
                                        @foreach($stockItems as $item)
                                            <option value="{{ $item->id }}" @if(old('details.'.$index.'.stock_item_id', $detail->stock_item_id) == $item->id) selected @endif>{{ $item->name }} ({{ $item->unit }})</option>
                                        @endforeach
                                    </select>
                                    @error('details.'.$index.'.stock_item_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Quantité Demandée *</label>
                                    <input type="number" name="details[{{ $index }}][requested_quantity]" value="{{ old('details.'.$index.'.requested_quantity', $detail->requested_quantity) }}" class="form-control @error('details.'.$index.'.requested_quantity') is-invalid @enderror" min="1" required>
                                    @error('details.'.$index.'.requested_quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Raison de la Demande *</label>
                                    <input type="text" name="details[{{ $index }}][request_reason]" value="{{ old('details.'.$index.'.request_reason', $detail->request_reason) }}" class="form-control @error('details.'.$index.'.request_reason') is-invalid @enderror" required>
                                    @error('details.'.$index.'.request_reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="row g-3 mt-2">
                                <div class="col-12">
                                    <label class="form-label">Description de l'Usage</label>
                                    <textarea name="details[{{ $index }}][usage_description]" rows="2" class="form-control">{{ old('details.'.$index.'.usage_description', $detail->usage_description) }}</textarea>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-danger btn-sm mt-2 remove-detail-btn" {{ $stockRequest->details->count() <= 1 ? 'style="display: none;"' : '' }}>
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
                        <a href="{{ route('stock-requests.show', $stockRequest) }}" class="btn btn-light">Annuler</a>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    let detailIndex = {{ $stockRequest->details->count() }};

    // Clone the last detail row template
    const detailTemplate = document.querySelector('.detail-row:last-child').cloneNode(true);

    document.getElementById('addDetailBtn').addEventListener('click', function() {
        const container = document.getElementById('detailsContainer');
        const newDetail = detailTemplate.cloneNode(true);

        // Update indices in the cloned element
        newDetail.setAttribute('data-index', detailIndex);
        const inputs = newDetail.querySelectorAll('input, select, textarea');
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

        // Show remove button for all rows
        document.querySelectorAll('.remove-detail-btn').forEach(btn => {
            btn.style.display = 'inline-block';
        });

        // Add remove functionality to new row
        const removeBtn = newDetail.querySelector('.remove-detail-btn');
        removeBtn.addEventListener('click', function() {
            newDetail.remove();
            // Hide remove button if only one row left
            if (document.querySelectorAll('.detail-row').length <= 1) {
                document.querySelector('.remove-detail-btn').style.display = 'none';
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
                // Hide remove button if only one row left
                if (document.querySelectorAll('.detail-row').length <= 1) {
                    document.querySelector('.remove-detail-btn').style.display = 'none';
                }
            }
        });
    });
});
</script>
@endsection