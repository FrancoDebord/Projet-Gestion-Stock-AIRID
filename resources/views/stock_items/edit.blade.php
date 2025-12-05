@extends('layouts.app_new')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="h4 mb-3">Modifier l'Article</h1>

            <div class="card shadow-sm p-4">
                <form action="{{ route('stock-items.update', $stockItem) }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PATCH')

                    <div class="row g-3">
                        <!-- Name -->
                        <div class="col-md-6">
                            <label class="form-label">Nom de l'article *</label>
                            <input type="text" name="name" value="{{ old('name', $stockItem->name) }}" required class="form-control @error('name') is-invalid @enderror">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Brand -->
                        <div class="col-md-6">
                            <label class="form-label">Marque</label>
                            <input type="text" name="brand" value="{{ old('brand', $stockItem->brand) }}" class="form-control @error('brand') is-invalid @enderror">
                            @error('brand')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Stock Location -->
                        <div class="col-md-6">
                            <label class="form-label">Localisation *</label>
                            <select name="stock_location_id" required class="form-select @error('stock_location_id') is-invalid @enderror">
                                <option value="">— Sélectionner —</option>
                                @foreach($locations as $loc)
                                    <option value="{{ $loc->id }}" @if(old('stock_location_id', $stockItem->stock_location_id) == $loc->id) selected @endif>{{ $loc->stock_name }}</option>
                                @endforeach
                            </select>
                            @error('stock_location_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Product Category -->
                        <div class="col-md-6">
                            <label class="form-label">Catégorie</label>
                            <select id="productCategorySelect" name="product_category_id" class="form-select @error('product_category_id') is-invalid @enderror">
                                <option value="">— Sélectionner —</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" @if(old('product_category_id', $stockItem->product_category_id) == $cat->id) selected @endif>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('product_category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Sub Category -->
                        <div class="col-md-6">
                            <label class="form-label">Sous-catégorie</label>
                            <select id="subCategorySelect" name="sub_category_id" class="form-select @error('sub_category_id') is-invalid @enderror">
                                <option value="">— Sélectionner une sous-catégorie —</option>
                            </select>
                            @error('sub_category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Initial Quantity -->
                        <div class="col-md-4">
                            <label class="form-label">Quantité Initiale *</label>
                            <input type="number" name="initial_quantity" value="{{ old('initial_quantity', $stockItem->initial_quantity) }}" required min="0" class="form-control @error('initial_quantity') is-invalid @enderror">
                            @error('initial_quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Min Quantity -->
                        <div class="col-md-4">
                            <label class="form-label">Quantité Minimum *</label>
                            <input type="number" name="min_quantity" value="{{ old('min_quantity', $stockItem->min_quantity) }}" required min="0" class="form-control @error('min_quantity') is-invalid @enderror">
                            @error('min_quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Unit -->
                        <div class="col-md-4">
                            <label class="form-label">Unité *</label>
                            <input type="text" name="unit" value="{{ old('unit', $stockItem->unit) }}" required class="form-control @error('unit') is-invalid @enderror">
                            @error('unit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Unit Price -->
                        <div class="col-md-6">
                            <label class="form-label">Prix Unitaire</label>
                            <input type="number" name="unit_price" value="{{ old('unit_price', $stockItem->unit_price) }}" step="0.01" min="0" class="form-control @error('unit_price') is-invalid @enderror">
                            @error('unit_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Type Usage -->
                        <div class="col-md-6">
                            <label class="form-label">Type d'Utilisation *</label>
                            <select name="type_usage_product" required class="form-select @error('type_usage_product') is-invalid @enderror">
                                <option value="consumed" @if(old('type_usage_product', $stockItem->type_usage_product) == 'consumed') selected @endif>Consommé</option>
                                <option value="finished" @if(old('type_usage_product', $stockItem->type_usage_product) == 'finished') selected @endif>Produit Fini</option>
                            </select>
                            @error('type_usage_product')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Image -->
                        <div class="col-12">
                            <label class="form-label">Image du Produit</label>
                            @if($stockItem->image)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $stockItem->image) }}" alt="{{ $stockItem->name }}" class="img-thumbnail" style="max-height: 150px;">
                                </div>
                            @endif
                            <input type="file" name="image" accept="image/*" class="form-control @error('image') is-invalid @enderror">
                            <small class="text-muted">Format: JPEG, PNG, GIF (Max 2MB)</small>
                            @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Description -->
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $stockItem->description) }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('stock-items.show', $stockItem) }}" class="btn btn-light">Annuler</a>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
            @section('scripts')
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const locationSelect = document.querySelector('select[name="stock_location_id"]');
                const categorySelect = document.getElementById('productCategorySelect');
                const subSelect = document.getElementById('subCategorySelect');

                async function loadCategories(locationId) {
                    categorySelect.innerHTML = '<option value="">— Chargement —</option>';
                    subSelect.innerHTML = '<option value="">— Sélectionner une sous-catégorie —</option>';
                    if (!locationId) {
                        categorySelect.innerHTML = '<option value="">— Sélectionner —</option>';
                        return;
                    }
                    const res = await fetch("/stock-items/ajax/categories?location_id=" + locationId);
                    const data = await res.json();
                    categorySelect.innerHTML = '<option value="">— Sélectionner —</option>';
                    data.forEach(c => {
                        const opt = document.createElement('option');
                        opt.value = c.id; opt.textContent = c.name;
                        categorySelect.appendChild(opt);
                    });
                }

                async function loadSubCategories(categoryId, preselect=null) {
                    subSelect.innerHTML = '<option value="">— Chargement —</option>';
                    if (!categoryId) {
                        subSelect.innerHTML = '<option value="">— Sélectionner une sous-catégorie —</option>';
                        return;
                    }
                    const res = await fetch("/stock-items/ajax/subcategories?category_id=" + categoryId);
                    const data = await res.json();
                    subSelect.innerHTML = '<option value="">— Sélectionner une sous-catégorie —</option>';
                    data.forEach(s => {
                        const opt = document.createElement('option');
                        opt.value = s.id; opt.textContent = s.name;
                        if (preselect && preselect == s.id) opt.selected = true;
                        subSelect.appendChild(opt);
                    });
                }

                // On location change, load categories
                locationSelect.addEventListener('change', function() {
                    loadCategories(this.value);
                });

                // On category change, load subcategories
                categorySelect.addEventListener('change', function() {
                    loadSubCategories(this.value);
                });

                // Initial population: if location selected, load categories and subcategories
                const initialLocation = locationSelect.value;
                const initialCategory = categorySelect.value;
                const initialSub = '{{ old('sub_category_id', $stockItem->sub_category_id ?? '') }}';
                if (initialLocation) {
                    loadCategories(initialLocation).then(() => {
                        if (initialCategory) {
                            loadSubCategories(initialCategory, initialSub);
                        }
                    });
                } else if (initialCategory) {
                    loadSubCategories(initialCategory, initialSub);
                }
            });
            </script>
            @endsection
