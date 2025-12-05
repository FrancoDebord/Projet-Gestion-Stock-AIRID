@extends('layouts.app_new')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between mb-3">
        <h1 class="h3">Réception #{{ $stockArrivalAdministration->id }}</h1>
        <div class="btn-group">
            <a href="{{ route('stock-arrivals-admin.pdf', $stockArrivalAdministration) }}" class="btn btn-outline-info">Télécharger PDF</a>
            @if($stockArrivalAdministration->incomingRecords->count() == 0)
                <a href="{{ route('stock-arrivals-admin.edit', $stockArrivalAdministration) }}" class="btn btn-outline-warning">Éditer</a>
            @else
                <span class="btn btn-outline-success disabled" title="Réception stock effectuée - Modification impossible">Stock Reçu</span>
            @endif
        </div>
    </div>

    <div class="card shadow-sm p-3 mb-3">
        <div class="row">
            <div class="col-md-4">
                <strong>Date de Réception</strong>
                <div>{{ $stockArrivalAdministration->date_arrival->format('d/m/Y H:i') }}</div>
            </div>
            <div class="col-md-4">
                <strong>Expéditeur</strong>
                <div>{{ $stockArrivalAdministration->sender ?? '—' }}</div>
            </div>
            <div class="col-md-4">
                <strong>Destination</strong>
                <div>{{ $stockArrivalAdministration->stockLocationDestination->stock_name ?? '—' }}</div>
            </div>
        </div>

        <hr>
        <div class="mb-3">
            <strong>Description Globale</strong>
            <div class="mt-2">{!! $stockArrivalAdministration->description_globale !!}</div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <strong>Reçu par</strong>
                <div>{{ $stockArrivalAdministration->administrationStaff->name ?? '—' }}</div>
            </div>
            <div class="col-md-6">
                <strong>Transmis à</strong>
                <div>{{ $stockArrivalAdministration->transmittedTo->name ?? '—' }}</div>
            </div>
        </div>

        <hr>
        <div>
            <strong>Documents reçus</strong>
            <ul>
                @if($stockArrivalAdministration->bordereau_delivery)
                    <li><a href="{{ asset('storage/' . $stockArrivalAdministration->bordereau_delivery) }}" target="_blank">Bordereau de Livraison</a></li>
                @endif
                @if($stockArrivalAdministration->certificate_analysis)
                    <li><a href="{{ asset('storage/' . $stockArrivalAdministration->certificate_analysis) }}" target="_blank">Certificate d'Analyse</a></li>
                @endif
                @if($stockArrivalAdministration->msds)
                    <li><a href="{{ asset('storage/' . $stockArrivalAdministration->msds) }}" target="_blank">MSDS</a></li>
                @endif
                @if($stockArrivalAdministration->other_document)
                    <li><a href="{{ asset('storage/' . $stockArrivalAdministration->other_document) }}" target="_blank">Autre Document</a></li>
                @endif
            </ul>
        </div>
    </div>

    <a href="{{ route('stock-arrivals-admin.index') }}" class="btn btn-light">Retour</a>
</div>
@endsection
