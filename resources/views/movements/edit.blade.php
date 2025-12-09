@extends('layouts.app_new')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h1 class="h4 mb-3">Edit Stock Movement #{{ $movement->id }}</h1>

            <div class="card shadow-sm p-3">
                <form method="POST" action="{{ route('movements.update', $movement) }}">
                    @csrf
                    @method('PATCH')

                    <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <input type="text" name="reason" value="{{ old('reason', $movement->reason) }}" class="form-control @error('reason') is-invalid @enderror" />
                        @error('reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes', $movement->notes) }}</textarea>
                        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Reference</label>
                        <input type="text" name="reference" value="{{ old('reference', $movement->reference) }}" class="form-control @error('reference') is-invalid @enderror" />
                        @error('reference')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Batch number</label>
                            <input type="text" name="batch_number" value="{{ old('batch_number', $movement->batch_number) }}" class="form-control @error('batch_number') is-invalid @enderror" />
                            @error('batch_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Movement Date</label>
                            <input type="datetime-local" name="date_mouvement" value="{{ old('date_mouvement', optional($movement->date_mouvement)->format('Y-m-d\TH:i')) }}" class="form-control @error('date_mouvement') is-invalid @enderror" />
                            @error('date_mouvement')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row g-3 mt-0">
                        <div class="col-md-6">
                            <label class="form-label">Reception Detail ID</label>
                            <input type="number" name="stock_incoming_detail_id" value="{{ old('stock_incoming_detail_id', $movement->stock_incoming_detail_id) }}" class="form-control @error('stock_incoming_detail_id') is-invalid @enderror" />
                            @error('stock_incoming_detail_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Usage Request ID</label>
                            <input type="number" name="stock_item_usage_request_id" value="{{ old('stock_item_usage_request_id', $movement->stock_item_usage_request_id) }}" class="form-control @error('stock_item_usage_request_id') is-invalid @enderror" />
                            @error('stock_item_usage_request_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        <a href="{{ route('movements.show', $movement) }}" class="btn btn-light me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
