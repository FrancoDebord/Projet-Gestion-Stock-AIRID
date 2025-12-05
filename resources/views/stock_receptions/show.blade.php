@extends('layouts.app_new')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between mb-3">
        <h1 class="h3">Réception Stock #{{ $stockReception->id }}</h1>
        <div class="btn-group">
            <a href="{{ route('stock-arrivals-admin.pdf', $stockReception->stockArrivalAdministration) }}" class="btn btn-outline-primary">Rapport Shipment</a>
            <a href="{{ route('stock-receptions.pdf', $stockReception) }}" class="btn btn-outline-info">Télécharger PDF</a>
            <a href="{{ route('stock-receptions.edit', $stockReception) }}" class="btn btn-outline-warning">Éditer</a>
            <form action="{{ route('stock-receptions.destroy', $stockReception) }}" method="POST" onsubmit="return confirm('Confirmer la suppression ?');" class="d-inline">
                @csrf @method('DELETE')
                <button class="btn btn-outline-danger">Supprimer</button>
            </form>
        </div>
    </div>

    <div class="card shadow-sm p-3 mb-3">
        <h5 class="mb-3">Informations Générales</h5>
        <div class="row">
            <div class="col-md-4">
                <strong>Date de Réception</strong>
                <div>{{ $stockReception->date_reception->format('d/m/Y H:i') }}</div>
            </div>
            <div class="col-md-4">
                <strong>Réception Admin</strong>
                <div>
                    {{ $stockReception->stockArrivalAdministration->date_arrival->format('d/m/Y H:i') }}
                    @if($stockReception->stockArrivalAdministration->sender)
                        <br><small class="text-muted">{{ $stockReception->stockArrivalAdministration->sender }}</small>
                    @endif
                </div>
            </div>
            <div class="col-md-4">
                <strong>Destination</strong>
                <div>{{ $stockReception->stockLocationDestination->stock_name ?? '—' }}</div>
            </div>
        </div>

        <hr>
        <div class="row">
            <div class="col-md-4">
                <strong>Reçu par</strong>
                <div>{{ $stockReception->receiver->name ?? '—' }}</div>
            </div>
            <div class="col-md-4">
                <strong>Projet</strong>
                <div>{{ $stockReception->project->name ?? '—' }}</div>
            </div>
            <div class="col-md-4">
                <strong>Expéditeur</strong>
                <div>{{ $stockReception->sender ?? '—' }}</div>
            </div>
        </div>

        @if($stockReception->description_globale)
        <hr>
        <div class="mb-3">
            <strong>Description Globale</strong>
            <div class="mt-2">{!! $stockReception->description_globale !!}</div>
        </div>
        @endif

        @if($stockReception->certificat_analyse || $stockReception->msds || $stockReception->borderau_livraison)
        <hr>
        <div>
            <strong>Documents</strong>
            <ul class="list-unstyled mt-2">
                @if($stockReception->certificat_analyse)
                    <li><a href="{{ Storage::url($stockReception->certificat_analyse) }}" target="_blank">Certificat d'Analyse</a></li>
                @endif
                @if($stockReception->msds)
                    <li><a href="{{ Storage::url($stockReception->msds) }}" target="_blank">MSDS</a></li>
                @endif
                @if($stockReception->borderau_livraison)
                    <li><a href="{{ Storage::url($stockReception->borderau_livraison) }}" target="_blank">Bordereau de Livraison</a></li>
                @endif
            </ul>
        </div>
        @endif
    </div>

    <div class="card shadow-sm p-3">
        <h5 class="mb-3">Détails des Produits</h5>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Code Lot</th>
                        <th>Numéro de Lot</th>
                        <th>Quantité</th>
                        <th>Unité</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stockReception->details as $detail)
                        <tr>
                            <td>{{ $detail->stockItem->name }}</td>
                            <td>{{ $detail->code_lot }}</td>
                            <td>{{ $detail->batch_number ?? '—' }}</td>
                            <td>{{ $detail->quantite_lot }}</td>
                            <td>{{ $detail->stockItem->unit }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted">Aucun détail trouvé.</td></tr>
                    @endforelse
                </tbody>
                @if($stockReception->details->count() > 0)
                <tfoot>
                    <tr class="table-secondary">
                        <th colspan="3">Total</th>
                        <th>{{ $stockReception->details->sum('quantite_lot') }}</th>
                        <th></th>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('stock-receptions.index') }}" class="btn btn-light">Retour à la liste</a>
    </div>
</div>
@endsection
