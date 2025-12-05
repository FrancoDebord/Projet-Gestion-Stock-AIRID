<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipment Reception Report - {{ $stockArrivalAdministration->id }}</title>
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

        .documents-section {
            margin-top: 30px;
        }

        .documents-title {
            font-size: 16px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 15px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 5px;
        }

        .documents-list {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 15px;
        }

        .document-item {
            margin-bottom: 8px;
            padding: 8px;
            background-color: white;
            border: 1px solid #dee2e6;
            border-radius: 4px;
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
            <img src="{{ public_path('storage/assets/logo/airid1.jpg') }}" alt="AIRID Logo" style="width: 100%; height: 100%; object-fit: contain;">
        </div>
    </div>

    <!-- Title -->
    <div class="title">
        Shipment Reception Report
    </div>

    <!-- Basic Information -->
    <div class="info-section">
        <table class="info-grid">
            <tr class="info-row">
                <td class="info-label">Report ID:</td>
                <td class="info-value">{{ $stockArrivalAdministration->id }}</td>
                <td class="info-label">Reception Date:</td>
                <td class="info-value">{{ $stockArrivalAdministration->date_arrival->format('d/m/Y H:i') }}</td>
            </tr>
            <tr class="info-row">
                <td class="info-label">Sender:</td>
                <td class="info-value">{{ $stockArrivalAdministration->sender ?? 'N/A' }}</td>
                <td class="info-label">Received By:</td>
                <td class="info-value">{{ $stockArrivalAdministration->administrationStaff->name ?? 'N/A' }}</td>
            </tr>
            <tr class="info-row">
                <td class="info-label">Destination:</td>
                <td class="info-value">{{ $stockArrivalAdministration->stockLocationDestination->stock_name ?? 'N/A' }}</td>
                <td class="info-label">Transmitted To:</td>
                <td class="info-value">{{ $stockArrivalAdministration->transmittedTo->name ?? 'N/A' }}</td>
            </tr>
            <tr class="info-row">
                <td class="info-label">Status:</td>
                <td class="info-value">
                    @if($stockArrivalAdministration->incomingRecords->count() > 0)
                        <span style="color: #28a745; font-weight: bold;">Processed</span>
                    @else
                        <span style="color: #ffc107; font-weight: bold;">Pending Stock Reception</span>
                    @endif
                </td>
                <td class="info-label">Print Date:</td>
                <td class="info-value">{{ now()->format('d/m/Y H:i') }}</td>
            </tr>
        </table>
    </div>

    <!-- Description -->
    @if($stockArrivalAdministration->description_globale)
    <div class="description-section">
        <div class="description-label">Global Description:</div>
        <div class="description-content">{!! $stockArrivalAdministration->description_globale !!}</div>
    </div>
    @endif

    <!-- Documents Section -->
    <div class="documents-section">
        <div class="documents-title">Attached Documents</div>
        <div class="documents-list">
            @php
                $documents = [
                    'bordereau_delivery' => 'Delivery Note',
                    'certificate_analysis' => 'Certificate of Analysis',
                    'msds' => 'Material Safety Data Sheet (MSDS)',
                    'other_document' => 'Other Document'
                ];
                $hasDocuments = false;
            @endphp

            @foreach($documents as $field => $label)
                @if($stockArrivalAdministration->$field)
                    @php $hasDocuments = true; @endphp
                    <div class="document-item">
                        <strong>{{ $label }}:</strong> {{ basename($stockArrivalAdministration->$field) }}
                        @if(strpos($stockArrivalAdministration->$field, '.pdf') !== false)
                            <span style="color: #dc3545;">(PDF Document)</span>
                        @elseif(strpos($stockArrivalAdministration->$field, '.jpg') !== false || strpos($stockArrivalAdministration->$field, '.jpeg') !== false || strpos($stockArrivalAdministration->$field, '.png') !== false)
                            <span style="color: #28a745;">(Image Document)</span>
                        @endif
                    </div>
                @endif
            @endforeach

            @if(!$hasDocuments)
                <div class="document-item" style="text-align: center; color: #666;">
                    No documents attached to this reception.
                </div>
            @endif
        </div>
    </div>

    <!-- Processing Status -->
    @if($stockArrivalAdministration->incomingRecords->count() > 0)
    <div class="documents-section">
        <div class="documents-title">Stock Reception Status</div>
        <div class="documents-list">
            <div class="document-item" style="background-color: #d4edda; border-color: #c3e6cb;">
                <strong style="color: #155724;">✓ Stock Reception Completed</strong><br>
                <span style="color: #155724;">This shipment has been processed and stock items have been received.</span>
                <br><small style="color: #155724;">Processed on: {{ $stockArrivalAdministration->incomingRecords->first()->date_reception->format('d/m/Y H:i') }}</small>
            </div>
        </div>
    </div>
    @else
    <div class="documents-section">
        <div class="documents-title">Stock Reception Status</div>
        <div class="documents-list">
            <div class="document-item" style="background-color: #fff3cd; border-color: #ffeaa7;">
                <strong style="color: #856404;">⚠ Awaiting Stock Reception</strong><br>
                <span style="color: #856404;">This shipment has been registered but stock reception is pending.</span>
            </div>
        </div>
    </div>
    @endif

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line"></div>
            <div class="signature-label">Administrative Reception<br>{{ $stockArrivalAdministration->administrationStaff->name ?? 'N/A' }}</div>
        </div>
        <div class="signature-box">
            <div class="signature-line"></div>
            <div class="signature-label">Stock Reception<br>Stock Manager</div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>
            Document generated automatically by AIRID Stock Management System<br>
            Date: {{ now()->format('d/m/Y H:i:s') }} | Report ID: {{ $stockArrivalAdministration->id }}
        </p>
    </div>
</body>
</html>
