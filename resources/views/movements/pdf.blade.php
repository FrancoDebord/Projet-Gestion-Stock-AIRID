<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Movement - {{ $movement->id }}</title>
    <style>
        @page { margin: 1cm; size: A4; }
        body { font-family: 'DejaVu Sans', 'Arial', sans-serif; font-size: 12px; line-height: 1.4; color: #333; margin: 0; padding: 0; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 3px solid #4b5563; padding-bottom: 20px; margin-bottom: 30px; }
        .logo-left, .logo-right { width: 80px; height: 80px; background-color: #f0f0f0; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #666; }
        .doc-info { text-align: center; flex: 1; }
        .doc-title { font-size: 24px; font-weight: bold; color: #111827; margin: 0; }
        .doc-subtitle { font-size: 14px; color: #666; margin: 5px 0 0 0; }
        .doc-number { font-size: 16px; font-weight: bold; }
        .status { display: inline-block; padding: 4px 10px; border-radius: 4px; font-size: 11px; color: #fff; }
        .status-in { background-color: #16a34a; }
        .status-out { background-color: #dc2626; }
        .status-adjustment { background-color: #f59e0b; }
        .section { margin-bottom: 20px; }
        .section-title { font-size: 14px; font-weight: bold; color: #374151; margin: 0 0 10px 0; }
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td { border: 1px solid #dee2e6; padding: 8px; vertical-align: top; }
        .info-label { background-color: #f8f9fa; width: 25%; font-weight: bold; color: #333; }
        .items-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .items-table th, .items-table td { border: 1px solid #dee2e6; padding: 8px; text-align: left; }
        .items-table th { background-color: #f8f9fa; font-weight: bold; }
        .footer { margin-top: 30px; border-top: 2px solid #4b5563; padding-top: 10px; font-size: 11px; color: #666; display: flex; justify-content: space-between; }
        .signature-section { display: flex; justify-content: space-between; margin-top: 25px; }
        .signature-box { width: 32%; border: 1px solid #ddd; padding: 15px; text-align: center; }
        .signature-line { height: 1px; background-color: #333; margin: 30px 0; }
        .muted { color: #6b7280; }
    </style>
    </head>
<body>
    <div class="header">
        <div class="logo-left">Logo</div>
        <div class="doc-info">
            <h1 class="doc-title">Stock Movement</h1>
            <p class="doc-subtitle">Stock Management System</p>
            <p class="doc-number">No. {{ $movement->id }}</p>
            <span class="status status-{{ $movement->type }}">{{ strtoupper($movement->type) }}</span>
        </div>
        <div class="logo-right">Logo</div>
    </div>

    <div class="section">
        <table class="info-table">
            <tr>
                <td class="info-label">Item</td>
                <td>{{ $movement->stockItem->name }}</td>
                <td class="info-label">Quantity</td>
                <td>{{ $movement->quantity }} {{ $movement->stockItem->unit ?? '' }}</td>
            </tr>
            <tr>
                <td class="info-label">Type</td>
                <td>{{ ucfirst($movement->type) }}</td>
                <td class="info-label">User</td>
                <td>{{ $movement->user->name }}</td>
            </tr>
            <tr>
                <td class="info-label">Movement Date</td>
                <td>{{ optional($movement->date_mouvement)->format('d/m/Y H:i') ?? $movement->created_at->format('d/m/Y H:i') }}</td>
                <td class="info-label">Reference</td>
                <td>{{ $movement->reference ?? '—' }}</td>
            </tr>
            <tr>
                <td class="info-label">Batch number</td>
                <td>{{ $movement->batch_number ?? '—' }}</td>
                <td class="info-label">Unit Price</td>
                <td>{{ $movement->stockItem->unit_price ? number_format($movement->stockItem->unit_price, 2) : '—' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Linked Records</div>
        <table class="info-table">
            <tr>
                <td class="info-label">Reception Detail</td>
                <td>
                    @if($movement->incomingDetail)
                        #{{ $movement->incomingDetail->id }} — Lot {{ $movement->incomingDetail->code_lot }} — Qty {{ $movement->incomingDetail->quantite_lot }}
                    @else
                        —
                    @endif
                </td>
                <td class="info-label">Usage Request</td>
                <td>
                    @if($movement->usageRequest)
                        #{{ $movement->usageRequest->id }} — {{ $movement->usageRequest->project->name ?? 'No Project' }}
                    @else
                        —
                    @endif
                </td>
            </tr>
        </table>
    </div>

    @if($movement->notes || $movement->reason)
    <div class="section">
        <div class="section-title">Notes and Reason</div>
        <table class="info-table">
            <tr>
                <td class="info-label">Reason</td>
                <td>{{ $movement->reason ?? '—' }}</td>
                <td class="info-label">Notes</td>
                <td>{{ $movement->notes ?? '—' }}</td>
            </tr>
        </table>
    </div>
    @endif

    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line"></div>
            <div class="muted">Recorded By<br>{{ $movement->user->name }}</div>
        </div>
        <div class="signature-box">
            <div class="signature-line"></div>
            <div class="muted">Stock Manager</div>
        </div>
        <div class="signature-box">
            <div class="signature-line"></div>
            <div class="muted">Data Manager</div>
        </div>
    </div>

    <div class="footer">
        <div>Document generated by AIRID Stock Management System</div>
        <div>Date: {{ now()->format('d/m/Y H:i:s') }}</div>
    </div>
</body>
</html>
