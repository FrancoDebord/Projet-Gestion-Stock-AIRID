@extends('layouts.app_new')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="h4 mb-0">Adjust Stock Quantity</h1>
                <a href="{{ route('movements.index') }}" class="btn btn-light">Back</a>
            </div>

            <div class="card shadow-sm p-3">
                <form action="{{ route('movements.store-adjustment') }}" method="POST">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Stock Item *</label>
                            <select name="stock_item_id" required class="form-select @error('stock_item_id') is-invalid @enderror">
                                <option value="">— Select —</option>
                                @foreach($stocks as $stock)
                                    <option value="{{ $stock->id }}" @if(old('stock_item_id') == $stock->id) selected @endif>{{ $stock->sku }} — {{ $stock->name }}</option>
                                @endforeach
                            </select>
                            @error('stock_item_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Project</label>
                            <select name="project_id" class="form-select @error('project_id') is-invalid @enderror">
                                <option value="">— Global (default) —</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" @if(old('project_id') == $project->id) selected @endif>{{ $project->name }}</option>
                                @endforeach
                            </select>
                            @error('project_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Adjustment Quantity *</label>
                            <input type="number" name="quantity" required class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity') }}" placeholder="e.g., 5 or -3">
                            @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Movement Date</label>
                            <input type="datetime-local" name="date_mouvement" class="form-control @error('date_mouvement') is-invalid @enderror" value="{{ old('date_mouvement') }}">
                            @error('date_mouvement')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row g-3 mt-0">
                        <div class="col-md-6">
                            <label class="form-label">Reason *</label>
                            <select name="reason" required class="form-select @error('reason') is-invalid @enderror">
                                <option value="">— Select —</option>
                                <option value="Inventory Count" @if(old('reason')==='Inventory Count') selected @endif>Inventory Count</option>
                                <option value="Damaged" @if(old('reason')==='Damaged') selected @endif>Damaged</option>
                                <option value="Loss" @if(old('reason')==='Loss') selected @endif>Loss</option>
                                <option value="System Error" @if(old('reason')==='System Error') selected @endif>System Error</option>
                                <option value="Return" @if(old('reason')==='Return') selected @endif>Return</option>
                                <option value="Other" @if(old('reason')==='Other') selected @endif>Other</option>
                            </select>
                            @error('reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Reference</label>
                            <input type="text" name="reference" class="form-control" value="{{ old('reference') }}">
                        </div>
                    </div>

                    <div class="mb-3 mt-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Batch number</label>
                            <input type="text" name="batch_number" class="form-control" value="{{ old('batch_number') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Reception Detail (not applicable)</label>
                            <input type="number" name="stock_incoming_detail_id" class="form-control" value="" disabled>
                        </div>
                    </div>

                    <div class="row g-3 mt-0">
                        <div class="col-md-6">
                            <label class="form-label">Usage Request (not applicable)</label>
                            <input type="number" name="stock_item_usage_request_id" class="form-control" value="" disabled>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        <button class="btn btn-primary">Save Adjustment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
