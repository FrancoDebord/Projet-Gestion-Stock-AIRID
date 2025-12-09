@extends('layouts.app_new')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="h4 mb-0">Record Stock Outgoing</h1>
                <a href="{{ route('movements.index') }}" class="btn btn-light">Back</a>
            </div>

            <div class="card shadow-sm p-3">
                <form action="{{ route('movements.store-out') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Stock Item *</label>
                        <select name="stock_item_id" required class="form-select @error('stock_item_id') is-invalid @enderror">
                            <option value="">— Select —</option>
                            @foreach($stocks as $stock)
                                <option value="{{ $stock->id }}" @if(old('stock_item_id') == $stock->id) selected @endif>{{ $stock->sku }} — {{ $stock->name }} (Available: {{ $stock->quantity }})</option>
                            @endforeach
                        </select>
                        @error('stock_item_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                            <label class="form-label">Reception Detail (not applicable)</label>
                            <input type="number" name="stock_incoming_detail_id" class="form-control" value="" disabled>
                        </div>
                    </div>

                    <div class="row g-3 mt-0">
                        <div class="col-md-6">
                            <label class="form-label">Usage Request</label>
                            <select name="stock_item_usage_request_id" class="form-select @error('stock_item_usage_request_id') is-invalid @enderror">
                                <option value="">— Select —</option>
                                @foreach($usageRequests as $req)
                                    <option value="{{ $req->id }}" @if(old('stock_item_usage_request_id') == $req->id) selected @endif>
                                        #{{ $req->id }} — {{ $req->project->name ?? 'No Project' }} — {{ $req->requester->name ?? 'N/A' }} — {{ $req->status }}
                                    </option>
                                @endforeach
                            </select>
                            @error('stock_item_usage_request_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        <button class="btn btn-warning">Record Outgoing</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
