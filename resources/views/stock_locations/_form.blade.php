@csrf

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Nom de l'emplacement</label>
        <input type="text" name="stock_name" value="{{ old('stock_name', $stockLocation->stock_name ?? '') }}" required
               class="form-control" />
        @error('stock_name')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Code (optionnel)</label>
        <input type="text" name="code_stock" value="{{ old('code_stock', $stockLocation->code_stock ?? '') }}" class="form-control" />
        @error('code_stock')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Responsable principal</label>
        <select name="principal_manager" class="form-select">
            <option value="">— Aucun —</option>
            @foreach($users as $u)
                <option value="{{ $u->id }}" @if(old('principal_manager', $stockLocation->principal_manager ?? '') == $u->id) selected @endif>{{ $u->name }} ({{ $u->email }})</option>
            @endforeach
        </select>
        @error('principal_manager')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label class="form-label">Description</label>
        <textarea name="description" rows="4" class="form-control">{{ old('description', $stockLocation->description ?? '') }}</textarea>
        @error('description')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
</div>
