@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h2 class="text-2xl font-bold mb-6">Edit Stock Item</h2>

                <form action="{{ route('stocks.update', $stock) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Name *</label>
                        <input type="text" id="name" name="name" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" value="{{ old('name', $stock->name) }}">
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="sku" class="block text-sm font-medium text-gray-700">SKU *</label>
                        <input type="text" id="sku" name="sku" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" value="{{ old('sku', $stock->sku) }}">
                        @error('sku')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea id="description" name="description" rows="3" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">{{ old('description', $stock->description) }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="min_quantity" class="block text-sm font-medium text-gray-700">Min Quantity *</label>
                            <input type="number" id="min_quantity" name="min_quantity" required min="0" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" value="{{ old('min_quantity', $stock->min_quantity) }}">
                            @error('min_quantity')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="unit" class="block text-sm font-medium text-gray-700">Unit *</label>
                            <input type="text" id="unit" name="unit" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" value="{{ old('unit', $stock->unit) }}">
                            @error('unit')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="unit_price" class="block text-sm font-medium text-gray-700">Unit Price</label>
                            <input type="number" id="unit_price" name="unit_price" step="0.01" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" value="{{ old('unit_price', $stock->unit_price) }}">
                        </div>

                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                            <input type="text" id="category" name="category" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" value="{{ old('category', $stock->category) }}">
                        </div>
                    </div>

                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                        <input type="text" id="location" name="location" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" value="{{ old('location', $stock->location) }}">
                    </div>

                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('stocks.show', $stock) }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Cancel</a>
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-700">Update Stock Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
