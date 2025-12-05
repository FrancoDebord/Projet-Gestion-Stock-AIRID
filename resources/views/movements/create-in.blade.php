@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h2 class="text-2xl font-bold mb-6">Record Stock Incoming</h2>

                <form action="{{ route('movements.store-in') }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <label for="stock_item_id" class="block text-sm font-medium text-gray-700">Stock Item *</label>
                        <select id="stock_item_id" name="stock_item_id" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                            <option value="">-- Select Stock Item --</option>
                            @foreach($stocks as $stock)
                                <option value="{{ $stock->id }}" @if(request('stock_item_id') == $stock->id) selected @endif>
                                    {{ $stock->sku }} - {{ $stock->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('stock_item_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity *</label>
                        <input type="number" id="quantity" name="quantity" required min="1" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" value="{{ old('quantity') }}">
                        @error('quantity')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="reason" class="block text-sm font-medium text-gray-700">Reason</label>
                        <input type="text" id="reason" name="reason" placeholder="e.g., Purchase Order, Donation" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" value="{{ old('reason') }}">
                    </div>

                    <div>
                        <label for="reference" class="block text-sm font-medium text-gray-700">Reference Number</label>
                        <input type="text" id="reference" name="reference" placeholder="e.g., PO-12345, INV-789" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" value="{{ old('reference') }}">
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">{{ old('notes') }}</textarea>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('movements.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Cancel</a>
                        <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-700">Record Incoming</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
