@extends('layouts.app_new')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold">Mouvement #{{ $movement->id }}</h2>
                    <div>
                        @if(auth()->user()->hasPermission('manage_settings'))
                            <a href="{{ route('movements.edit', $movement) }}" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">Modifier</a>
                        @endif
                        <a href="{{ route('movements.index') }}" class="ml-2 text-gray-600 hover:underline">Retour</a>
                    </div>
                </div>

                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="font-semibold">Article</dt>
                        <dd><a href="{{ route('stock-items.show', $movement->stockItem) }}" class="text-blue-600 hover:underline">{{ $movement->stockItem->name }}</a></dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Type</dt>
                        <dd>{{ ucfirst($movement->type) }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Quantité</dt>
                        <dd>{{ $movement->quantity }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Utilisateur</dt>
                        <dd>{{ $movement->user->name }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Date mouvement</dt>
                        <dd>{{ optional($movement->date_mouvement)->format('Y-m-d H:i') ?? $movement->created_at->format('Y-m-d H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Raison</dt>
                        <dd>{{ $movement->reason ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Notes</dt>
                        <dd>{{ $movement->notes ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Référence</dt>
                        <dd>{{ $movement->reference ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Batch number</dt>
                        <dd>{{ $movement->batch_number ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Détail réception</dt>
                        <dd>{{ optional($movement->incomingDetail)->code_lot ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold">Demande d’utilisation</dt>
                        <dd>{{ optional($movement->usageRequest)->id ?? '—' }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection

