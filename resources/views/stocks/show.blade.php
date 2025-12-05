@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold">{{ $stock->name }}</h2>
                    <div class="space-x-2">
                        @if(auth()->user()->hasPermission('edit_stock'))
                            <a href="{{ route('stocks.edit', $stock) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Edit
                            </a>
                        @endif
                        <a href="{{ route('stocks.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Back
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6 mb-8">
                    <div>
                        <p class="text-sm text-gray-600">SKU</p>
                        <p class="text-lg font-semibold">{{ $stock->sku }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Category</p>
                        <p class="text-lg font-semibold">{{ $stock->category ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Current Quantity</p>
                        <p class="text-lg font-semibold @if($stock->isLowStock()) text-red-600 @else text-green-600 @endif">{{ $stock->quantity }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Location</p>
                        <p class="text-lg font-semibold">{{ $stock->location ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Unit Price</p>
                        <p class="text-lg font-semibold">${{ number_format($stock->unit_price ?? 0, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Value</p>
                        <p class="text-lg font-semibold">${{ number_format($stock->total_value, 2) }}</p>
                    </div>
                </div>

                @if($stock->description)
                    <div class="mb-8 p-4 bg-gray-50 rounded">
                        <p class="text-sm text-gray-600">Description</p>
                        <p>{{ $stock->description }}</p>
                    </div>
                @endif

                @if(auth()->user()->hasPermission('record_stock_in') || auth()->user()->hasPermission('record_stock_out'))
                    <div class="mb-8 space-x-2">
                        @if(auth()->user()->hasPermission('record_stock_in'))
                            <a href="{{ route('movements.create-in') }}?stock_item_id={{ $stock->id }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Record Incoming
                            </a>
                        @endif
                        @if(auth()->user()->hasPermission('record_stock_out'))
                            <a href="{{ route('movements.create-out') }}?stock_item_id={{ $stock->id }}" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded">
                                Record Outgoing
                            </a>
                        @endif
                    </div>
                @endif

                <h3 class="text-xl font-bold mb-4">Movement History</h3>
                @if($movements->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full border-collapse border border-gray-300">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border border-gray-300 px-4 py-2 text-left">Date</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">Type</th>
                                    <th class="border border-gray-300 px-4 py-2 text-center">Quantity</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">User</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">Reason</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">Reference</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($movements as $movement)
                                    <tr>
                                        <td class="border border-gray-300 px-4 py-2">{{ $movement->created_at->format('M d, Y H:i') }}</td>
                                        <td class="border border-gray-300 px-4 py-2">
                                            <span class="px-2 py-1 rounded text-sm font-semibold
                                                @if($movement->type === 'in') bg-green-100 text-green-800
                                                @elseif($movement->type === 'out') bg-red-100 text-red-800
                                                @else bg-blue-100 text-blue-800
                                                @endif">
                                                {{ ucfirst($movement->type) }}
                                            </span>
                                        </td>
                                        <td class="border border-gray-300 px-4 py-2 text-center font-semibold">
                                            @if($movement->type === 'out')
                                                -{{ $movement->quantity }}
                                            @elseif($movement->type === 'in')
                                                +{{ $movement->quantity }}
                                            @else
                                                {{ $movement->quantity }}
                                            @endif
                                        </td>
                                        <td class="border border-gray-300 px-4 py-2">{{ $movement->user->name }}</td>
                                        <td class="border border-gray-300 px-4 py-2">{{ $movement->reason ?? '-' }}</td>
                                        <td class="border border-gray-300 px-4 py-2">{{ $movement->reference ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $movements->links() }}
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">No movements recorded yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
