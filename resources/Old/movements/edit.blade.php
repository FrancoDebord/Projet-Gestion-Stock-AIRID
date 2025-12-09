@extends('layouts.app_new')

@section('content')
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h2 class="text-2xl font-bold mb-6">Modifier le mouvement #{{ $movement->id }}</h2>

                <form method="POST" action="{{ route('movements.update', $movement) }}" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Raison</label>
                        <input type="text" name="reason" value="{{ old('reason', $movement->reason) }}" class="mt-1 block w-full border rounded px-3 py-2" />
                        @error('reason')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea name="notes" class="mt-1 block w-full border rounded px-3 py-2">{{ old('notes', $movement->notes) }}</textarea>
                        @error('notes')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Référence</label>
                        <input type="text" name="reference" value="{{ old('reference', $movement->reference) }}" class="mt-1 block w-full border rounded px-3 py-2" />
                        @error('reference')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Batch number</label>
                            <input type="text" name="batch_number" value="{{ old('batch_number', $movement->batch_number) }}" class="mt-1 block w-full border rounded px-3 py-2" />
                            @error('batch_number')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Date mouvement</label>
                            <input type="datetime-local" name="date_mouvement" value="{{ old('date_mouvement', optional($movement->date_mouvement)->format('Y-m-d\TH:i')) }}" class="mt-1 block w-full border rounded px-3 py-2" />
                            @error('date_mouvement')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">ID détail réception</label>
                            <input type="number" name="stock_incoming_detail_id" value="{{ old('stock_incoming_detail_id', $movement->stock_incoming_detail_id) }}" class="mt-1 block w-full border rounded px-3 py-2" />
                            @error('stock_incoming_detail_id')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">ID demande d’utilisation</label>
                            <input type="number" name="stock_item_usage_request_id" value="{{ old('stock_item_usage_request_id', $movement->stock_item_usage_request_id) }}" class="mt-1 block w-full border rounded px-3 py-2" />
                            @error('stock_item_usage_request_id')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="flex justify-end space-x-2 mt-6">
                        <a href="{{ route('movements.show', $movement) }}" class="px-4 py-2 border rounded">Annuler</a>
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

