@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Shipments') }}</h2>
@endsection

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-medium">All Shipments</h3>
                <a href="{{ route('shipments.admin.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded">Record reception (Administration)</a>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4">
                    <table class="min-w-full bg-white dark:bg-gray-800">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left">#</th>
                                <th class="px-4 py-2 text-left">Shipment</th>
                                <th class="px-4 py-2 text-left">Received At</th>
                                <th class="px-4 py-2 text-left">To</th>
                                <th class="px-4 py-2 text-left">Project</th>
                                <th class="px-4 py-2 text-left">Colis</th>
                                <th class="px-4 py-2 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($shipments as $s)
                                <tr class="border-t">
                                    <td class="px-4 py-2">{{ $s->id }}</td>
                                    <td class="px-4 py-2">{{ $s->shipment_number ?? '—' }}</td>
                                    <td class="px-4 py-2">{{ optional($s->received_at)->format('Y-m-d H:i') }}</td>
                                    <td class="px-4 py-2">{{ optional($s->toLocation)->name }}</td>
                                    <td class="px-4 py-2">{{ optional($s->project)->name }}</td>
                                    <td class="px-4 py-2">{{ $s->colis_count }}</td>
                                    <td class="px-4 py-2">
                                        <a href="{{ route('shipments.show', $s) }}" class="text-blue-600 hover:underline mr-2">View</a>
                                        @if($s->finalized_at)
                                            <a href="{{ route('shipments.ack', $s) }}" class="text-green-600 hover:underline">Ack</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="px-4 py-2" colspan="7">No shipments found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">{{ $shipments->links() }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection
