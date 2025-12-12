@extends('layouts.app_new')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="h4 mb-0">Record Stock Outgoing</h1>
                <a href="{{ route('movements.index') }}" class="btn btn-light">Back</a>
            </div>

            <!-- Pending Approved Requests Alert -->
            @if($usageRequests->isNotEmpty())
                <div class="alert alert-info mb-4">
                    <h6 class="alert-heading">
                        <i class="fas fa-exclamation-triangle"></i>
                        Demandes approuvées en attente de satisfaction ({{ $usageRequests->count() }})
                    </h6>
                    <p class="mb-2">Ces demandes ont été approuvées et peuvent être satisfaites directement :</p>
                    <div class="row g-2">
                        @foreach($usageRequests->take(3) as $req)
                            <div class="col-md-4">
                                <div class="border rounded p-2 bg-light">
                                    <small class="fw-bold">Demande #{{ $req->id }}</small><br>
                                    <small>{{ $req->requester->name ?? 'N/A' }} - {{ $req->project->name ?? 'No Project' }}</small><br>
                                    <a href="{{ route('movements.fulfill-request', ['request_id' => $req->id]) }}"
                                       class="btn btn-sm btn-success mt-1">
                                        <i class="fas fa-play"></i> Satisfaire
                                    </a>
                                </div>
                            </div>
                        @endforeach
                        @if($usageRequests->count() > 3)
                            <div class="col-md-12">
                                <a href="{{ route('movements.fulfill-request') }}" class="btn btn-outline-primary btn-sm">
                                    Voir toutes les demandes ({{ $usageRequests->count() }})
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <div class="card shadow-sm p-3">
                <form action="{{ route('movements.store-out') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Requests</label>
                        <select id="usage_request_select" name="stock_item_usage_request_id" class="form-select @error('stock_item_usage_request_id') is-invalid @enderror">
                            <option value="">— Select —</option>
                            @foreach($usageRequests as $req)
                                <option value="{{ $req->id }}" @if(old('stock_item_usage_request_id') == $req->id) selected @endif>
                                    #{{ $req->id }} — {{ $req->project->name ?? 'No Project' }} — {{ $req->requester->name ?? 'N/A' }} — {{ $req->status }}
                                </option>
                            @endforeach
                        </select>
                        @error('stock_item_usage_request_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="text-muted">Select an approved request to pre-fill and process all items.</small>
                    </div>

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
                            <label class="form-label">Reception Detail (not applicable)</label>
                            <input type="number" name="stock_incoming_detail_id" class="form-control" value="" disabled>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var select = document.getElementById('usage_request_select');
    if (select) {
        select.addEventListener('change', function () {
            var val = this.value;
            if (val) {
                var url = "{{ route('movements.fulfill-request') }}" + "?request_id=" + encodeURIComponent(val);
                window.location.href = url;
            }
        });
    }
});
</script>
@endpush
