<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reception Acknowledgment - {{ $stockReception->id }}</title>
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
            border-bottom: 3px solid #2563eb;
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

        .lab-info {
            text-align: center;
            flex: 1;
        }

        .lab-name {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin: 0;
        }

        .lab-subtitle {
            font-size: 14px;
            color: #666;
            margin: 5px 0;
        }

        .lab-address {
            font-size: 11px;
            color: #666;
            margin: 2px 0;
        }

        .title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            color: #2563eb;
            margin: 30px 0 20px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .info-section {
            margin-bottom: 20px;
        }

        .info-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .info-row {
            display: table-row;
        }

        .info-label {
            display: table-cell;
            width: 30%;
            font-weight: bold;
            padding: 8px 12px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
        }

        .info-value {
            display: table-cell;
            padding: 8px 12px;
            border: 1px solid #dee2e6;
        }

        .description-section {
            margin: 20px 0;
        }

        .description-label {
            font-weight: bold;
            margin-bottom: 8px;
        }

        .description-content {
            padding: 12px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            min-height: 60px;
        }

        .products-section {
            margin-top: 30px;
        }

        .products-title {
            font-size: 16px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 15px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 5px;
        }

        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .products-table th {
            background-color: #2563eb;
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #2563eb;
        }

        .products-table td {
            padding: 8px;
            border: 1px solid #dee2e6;
        }

        .products-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .products-table tbody tr:hover {
            background-color: #e3f2fd;
        }

        .total-row {
            background-color: #f0f8ff !important;
            font-weight: bold;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            font-size: 10px;
            color: #666;
        }

        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            width: 45%;
            text-align: center;
        }

        .signature-line {
            border-bottom: 1px solid #333;
            margin: 40px 20px 5px 20px;
        }

        .signature-label {
            font-size: 11px;
            color: #666;
        }

        @media print {
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }

            .logo-left, .logo-right {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo-left">
            <img src="{{ storage_path('app/public/assets/logo/airid1.jpg') }}" alt="AIRID Logo" style="width: 100%; height: 100%; object-fit: contain;">
        </div>

        <div class="lab-info">
            <h1 class="lab-name">AIRID</h1>
            <div class="lab-subtitle">African Institute for Research in Infectious Diseases</div>
            <div class="lab-address">Secrétariat AIRID, Maison 115, Rue 1543 Donaten</div>
            <div class="lab-address">AKPAKPA, (Rue SOBEPEC, 4e Von à gauche, dernier immeuble à gauche)</div>
            <div class="lab-address">Cotonou, Benin</div>
            <div class="lab-address">Email: info@airid-africa.com | Phone: (+229) 01 67 16 44 99</div>
        </div>

        <div class="logo-right">
            <img src="{{ storage_path('app/public/assets/logo/airid1.jpg') }}" alt="AIRID Logo" style="width: 100%; height: 100%; object-fit: contain;">
        </div>
    </div>

    <!-- Title -->
    <div class="title">
        Reception Acknowledgment
    </div>

    <!-- Basic Information -->
    <div class="info-section">
        <table class="info-grid">
            <tr class="info-row">
                <td class="info-label">Reception ID:</td>
                <td class="info-value">{{ $stockReception->id }}</td>
                <td class="info-label">Date de Réception:</td>
                <td class="info-value">{{ $stockReception->date_reception->format('d/m/Y H:i') }}</td>
            </tr>
            <tr class="info-row">
                <td class="info-label">Réception Admin:</td>
                <td class="info-value">
                    <strong>Report #{{ $stockReception->stockArrivalAdministration->id }}</strong><br>
                    {{ $stockReception->stockArrivalAdministration->date_arrival->format('d/m/Y H:i') }}
                    @if($stockReception->stockArrivalAdministration->sender)
                        ({{ $stockReception->stockArrivalAdministration->sender }})
                    @endif
                </td>
                <td class="info-label">Reçu par:</td>
                <td class="info-value">{{ $stockReception->receiver->name ?? 'N/A' }}</td>
            </tr>
            <tr class="info-row">
                <td class="info-label">Destination:</td>
                <td class="info-value">{{ $stockReception->stockLocationDestination->stock_name ?? 'N/A' }}</td>
                <td class="info-label">Projet:</td>
                <td class="info-value">{{ $stockReception->project->name ?? 'N/A' }}</td>
            </tr>
            <tr class="info-row">
                <td class="info-label">Expéditeur:</td>
                <td class="info-value">{{ $stockReception->sender ?? 'N/A' }}</td>
                <td class="info-label">Date d'impression:</td>
                <td class="info-value">{{ now()->format('d/m/Y H:i') }}</td>
            </tr>
        </table>

        <div style="margin-top: 15px; padding: 10px; background-color: #e3f2fd; border: 1px solid #2196f3; border-radius: 4px;">
            <strong>Note:</strong> This stock reception is linked to Administrative Shipment Report #{{ $stockReception->stockArrivalAdministration->id }}.
            For complete shipment documentation, refer to the original shipment reception report.
        </div>
    </div>

    <!-- Description -->
    @if($stockReception->description_globale)
    <div class="description-section">
        <div class="description-label">Description Globale:</div>
        <div class="description-content">{!! $stockReception->description_globale !!}</div>
    </div>
    @endif

    <!-- Products List -->
    <div class="products-section">
        <div class="products-title">Articles Reçus</div>
        <table class="products-table">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Code Lot</th>
                    <th>Numéro de Lot</th>
                    <th>Quantité</th>
                    <th>Unité</th>
                    <th>Marque</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stockReception->details as $detail)
                <tr>
                    <td>{{ $detail->stockItem->name }}</td>
                    <td>{{ $detail->code_lot }}</td>
                    <td>{{ $detail->batch_number ?? '—' }}</td>
                    <td>{{ number_format($detail->quantite_lot, 0, ',', ' ') }}</td>
                    <td>{{ $detail->stockItem->unit }}</td>
                    <td>{{ $detail->stockItem->brand ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #666;">Aucun article trouvé</td>
                </tr>
                @endforelse
            </tbody>
            @if($stockReception->details->count() > 0)
            <tfoot>
                <tr class="total-row">
                    <td colspan="3" style="text-align: right; font-weight: bold;">Total Articles:</td>
                    <td style="font-weight: bold;">{{ number_format($stockReception->details->sum('quantite_lot'), 0, ',', ' ') }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line"></div>
            <div class="signature-label">Reçu par<br>{{ $stockReception->receiver->name ?? 'N/A' }}</div>
        </div>
        <div class="signature-box">
            <div class="signature-line"></div>
            <div class="signature-label">Approuvé par<br>Responsable Stock</div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>
            Document généré automatiquement par le système de gestion de stock AIRID<br>
            Date: {{ now()->format('d/m/Y H:i:s') }} | Reception ID: {{ $stockReception->id }}
        </p>
    </div>
</body>
</html>
