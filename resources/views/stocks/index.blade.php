@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold">Stock Inventory</h2>
                    @if(auth()->user()->hasPermission('create_stock'))
                        <a href="{{ route('stocks.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Add Stock Item
                        </a>
                    @endif
                </div>

                @if($stocks->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full border-collapse border border-gray-300">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border border-gray-300 px-4 py-2 text-left">SKU</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">Name</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center">Quantity</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center">Min Qty</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">Category</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">Unit Price</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center">Status</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stocks as $stock)
                                    <tr class="{{ $stock->isLowStock() ? 'bg-yellow-50' : 'bg-white' }}">
                                        <td class="border border-gray-300 px-4 py-2">{{ $stock->sku }}</td>
                                        <td class="border border-gray-300 px-4 py-2">{{ $stock->name }}</td>
                                        <td class="border border-gray-300 px-4 py-2 text-center font-bold">{{ $stock->quantity }}</td>
                                        <td class="border border-gray-300 px-4 py-2 text-center">{{ $stock->min_quantity }}</td>
                                        <td class="border border-gray-300 px-4 py-2">{{ $stock->category ?? '-' }}</td>
                                        <td class="border border-gray-300 px-4 py-2">
                                            @if($stock->unit_price)
                                                ${{ number_format($stock->unit_price, 2) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="border border-gray-300 px-4 py-2 text-center">
                                            @if($stock->isLowStock())
                                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-sm">Low Stock</span>
                                            @else
                                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm">OK</span>
                                            @endif
                                        </td>
                                        <td class="border border-gray-300 px-4 py-2 text-center">
                                            <a href="{{ route('stocks.show', $stock) }}" class="text-blue-500 hover:underline text-sm">View</a>
                                            @if(auth()->user()->hasPermission('edit_stock'))
                                                | <a href="{{ route('stocks.edit', $stock) }}" class="text-green-500 hover:underline text-sm">Edit</a>
                                            @endif
                                            @if(auth()->user()->hasPermission('delete_stock'))
                                                | <a href="#" onclick="deleteStock({{ $stock->id }})" class="text-red-500 hover:underline text-sm">Delete</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $stocks->links() }}
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">No stock items found. <a href="{{ route('stocks.create') }}" class="text-blue-500 underline">Create one</a></p>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function deleteStock(id) {
    if (confirm('Are you sure you want to delete this stock item?')) {
        fetch(`/stocks/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        }).then(() => location.reload());
    }
}
</script>
@endsection
