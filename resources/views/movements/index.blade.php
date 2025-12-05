@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold">Stock Movements</h2>
                    <div class="space-x-2">
                        @if(auth()->user()->hasPermission('record_stock_in'))
                            <a href="{{ route('movements.create-in') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Record Incoming
                            </a>
                        @endif
                        @if(auth()->user()->hasPermission('record_stock_out'))
                            <a href="{{ route('movements.create-out') }}" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded">
                                Record Outgoing
                            </a>
                        @endif
                        @if(auth()->user()->hasPermission('adjust_stock'))
                            <a href="{{ route('movements.create-adjustment') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Adjust Stock
                            </a>
                        @endif
                    </div>
                </div>

                @if($movements->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full border-collapse border border-gray-300">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border border-gray-300 px-4 py-2 text-left">Date</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">Stock Item</th>
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
                                            <a href="{{ route('stocks.show', $movement->stockItem) }}" class="text-blue-500 hover:underline">
                                                {{ $movement->stockItem->name }}
                                            </a>
                                        </td>
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
