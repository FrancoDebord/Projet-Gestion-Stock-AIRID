@extends('layouts.app_new')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="h4 mb-0">Record Stock Incoming</h1>
                <a href="{{ route('movements.index') }}" class="btn btn-light">Back</a>
            </div>

            <div class="card shadow-sm p-3">
                <form action="{{ route('movements.store-in') }}" method="POST">
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
                            <label class="form-label">Quantity *</label>
                            <input type="number" name="quantity" min="1" required class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity') }}">
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
                            <label class="form-label">Reason</label>
                            <input type="text" name="reason" class="form-control" value="{{ old('reason') }}">
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
                            <label class="form-label">Reception Detail</label>
                            <select name="stock_incoming_detail_id" class="form-select @error('stock_incoming_detail_id') is-invalid @enderror">
                                <option value="">— Select —</option>
                                @foreach($incomingDetails as $detail)
                                    <option value="{{ $detail->id }}" @if(old('stock_incoming_detail_id') == $detail->id) selected @endif>
                                        #{{ $detail->id }} — Lot: {{ $detail->code_lot }} — Qty: {{ $detail->quantite_lot }} — Item: {{ $detail->stockItem->name ?? 'N/A' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('stock_incoming_detail_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row g-3 mt-0">
                        <div class="col-md-6">
                            <label class="form-label">Usage Request (not applicable for incoming)</label>
                            <input type="number" name="stock_item_usage_request_id" class="form-control" value="" disabled>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        <button class="btn btn-success">Record Incoming</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
