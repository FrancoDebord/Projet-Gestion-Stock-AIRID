<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demande de Stock - {{ $stockRequest->id }}</title>
    <style>
        @page {
            margin: 1cm;
            size: A4;
        }

        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid #dc2626;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .logo-left, .logo-right {
            width: 80px;
            height: 80px;
            background-color: #f0f0f0;
            border: 1px solid #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            color: #666;
        }

        .doc-info {
            text-align: center;
            flex: 1;
        }

        .doc-title {
            font-size: 24px;
            font-weight: bold;
            color: #dc2626;
            margin: 0;
        }

        .doc-subtitle {
            font-size: 14px;
            color: #666;
            margin: 5px 0 0 0;
        }

        .doc-number {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin: 10px 0 0 0;
        }

        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .info-box {
            flex: 1;
            border: 1px solid #ddd;
            padding: 10px;
            margin: 0 5px;
        }

        .info-box h4 {
            margin: 0 0 8px 0;
            font-size: 13px;
            color: #dc2626;
            border-bottom: 1px solid #eee;
            padding-bottom: 3px;
        }

        .info-box p {
            margin: 3px 0;
            font-size: 11px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-pending { background-color: #fef3c7; color: #d97706; }
        .status-approved_facility_manager { background-color: #dbeafe; color: #2563eb; }
        .status-approved_data_manager { background-color: #d1fae5; color: #065f46; }
        .status-rejected { background-color: #fee2e2; color: #dc2626; }
        .status-completed { background-color: #f3f4f6; color: #374151; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 11px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
        }

        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .footer {
            margin-top: 40px;
            border-top: 2px solid #dc2626;
            padding-top: 20px;
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            width: 30%;
            border: 1px solid #ddd;
            padding: 20px;
            text-align: center;
        }

        .signature-title {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 40px;
            color: #666;
        }

        .date-info {
            font-size: 10px;
            color: #666;
            margin-top: 10px;
        }

        .approval-section {
            margin: 20px 0;
            padding: 15px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }

        .approval-section h5 {
            margin: 0 0 10px 0;
            color: #495057;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-left">Logo</div>
        <div class="doc-info">
            <h1 class="doc-title">Stock Item Request Sheet</h1>
            <p class="doc-subtitle">Stock Management System</p>
            <p class="doc-number">N° {{ $stockRequest->id }}</p>
        </div>
        <div class="logo-right">Logo</div>
    </div>

    <div class="info-section">
        <div class="info-box">
            <h4>Informations Générales</h4>
            <p><strong>Date de demande:</strong> {{ $stockRequest->request_date->format('d/m/Y H:i') }}</p>
            <p><strong>Demandeur:</strong> {{ $stockRequest->requester->name ?? 'N/A' }}</p>
            <p><strong>Projet:</strong> {{ $stockRequest->project->name ?? 'Aucun projet' }}</p>
            @if($stockRequest->code_machine)
            <p><strong>Numéro de machine:</strong> {{ $stockRequest->code_machine }}</p>
            @endif
            @if($stockRequest->room_number)
            <p><strong>Numéro de bureau:</strong> {{ $stockRequest->room_number }}</p>
            @endif
        </div>

        <div class="info-box">
            <h4>Statut de la Demande</h4>
            <p><span class="status-badge status-{{ $stockRequest->status }}">
                @switch($stockRequest->status)
                    @case('pending')
                        En attente
                        @break
                    @case('approved_facility_manager')
                        Approuvé Facility Manager
                        @break
                    @case('approved_data_manager')
                        Approuvé Data Manager
                        @break
                    @case('rejected')
                        Rejeté
                        @break
                    @case('completed')
                        Satisfait
                        @break
                @endswitch
            </span></p>
            @if($stockRequest->facility_manager_id)
            <p><strong>Facility Manager:</strong> {{ $stockRequest->facilityManager->name ?? 'N/A' }}</p>
            <p><strong>Approuvé le:</strong> {{ $stockRequest->facility_manager_approval_date?->format('d/m/Y H:i') }}</p>
            @endif
            @if($stockRequest->data_manager_id)
            <p><strong>Data Manager:</strong> {{ $stockRequest->dataManager->name ?? 'N/A' }}</p>
            <p><strong>Approuvé le:</strong> {{ $stockRequest->data_manager_approval_date?->format('d/m/Y H:i') }}</p>
            @endif
        </div>
    </div>

    @if($stockRequest->general_notes)
    <div class="info-box">
        <h4>Notes Générales</h4>
        <p>{{ $stockRequest->general_notes }}</p>
    </div>
    @endif

    <h4 style="color: #dc2626; margin: 30px 0 15px 0;">Détails des Produits Demandés</h4>

    <table>
        <thead>
            <tr>
                <th>Produit</th>
                <th>Unité</th>
                <th>Quantité Demandée</th>
                <th>Quantité Approuvée</th>
                <th>Raison de la Demande</th>
                <th>Description d'Usage</th>
                <th>Projet</th>
                <th>Observations</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stockRequest->details as $detail)
            <tr>
                <td>{{ $detail->stockItem->name ?? 'N/A' }}</td>
                <td>{{ $detail->stockItem->unit ?? 'N/A' }}</td>
                <td>{{ $detail->requested_quantity }}</td>
                <td>{{ $detail->approved_quantity ?? '-' }}</td>
                <td>{{ $detail->request_reason }}</td>
                <td>{{ $detail->usage_description ?? '-' }}</td>
                <td>{{ $detail->observations ?? '-' }}</td>
                <td>
                    @if($detail->isApproved())
                        <span style="color: #065f46; font-weight: bold;">✓ Approuvé</span>
                    @elseif($detail->facility_manager_approval)
                        <span style="color: #2563eb; font-weight: bold;">Facility OK</span>
                    @else
                        <span style="color: #d97706; font-weight: bold;">En attente</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($stockRequest->facility_manager_notes || $stockRequest->data_manager_notes)
    <div class="approval-section">
        <h5>Notes d'Approbation</h5>
        @if($stockRequest->facility_manager_notes)
        <p><strong>Facility Manager:</strong> {{ $stockRequest->facility_manager_notes }}</p>
        @endif
        @if($stockRequest->data_manager_notes)
        <p><strong>Data Manager:</strong> {{ $stockRequest->data_manager_notes }}</p>
        @endif
    </div>
    @endif

    <div class="footer">
        <div class="signature-box">
            <div class="signature-title">Demandeur</div>
            <div class="date-info">Date: {{ $stockRequest->request_date->format('d/m/Y') }}</div>
        </div>

        @if($stockRequest->facility_manager_id)
        <div class="signature-box">
            <div class="signature-title">Facility Manager</div>
            <div class="date-info">Date: {{ $stockRequest->facility_manager_approval_date?->format('d/m/Y') }}</div>
        </div>
        @endif

        @if($stockRequest->data_manager_id)
        <div class="signature-box">
            <div class="signature-title">Data Manager</div>
            <div class="date-info">Date: {{ $stockRequest->data_manager_approval_date?->format('d/m/Y') }}</div>
        </div>
        @endif
    </div>

    <div style="text-align: center; margin-top: 20px; font-size: 10px; color: #666;">
        Document généré le {{ now()->format('d/m/Y à H:i') }}
    </div>
</body>
</html>