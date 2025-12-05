<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Acknowledgment - Shipment #{{ $shipment->id }}</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; font-size: 14px; }
        .container { max-width: 800px; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 20px; }
        .section { margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; }
        td, th { padding: 6px; border: 1px solid #ccc; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Acknowledgment of Receipt</h1>
            <p>Shipment #: {{ $shipment->shipment_number ?? $shipment->id }}</p>
        </div>

        <div class="section">
            <strong>Received at:</strong> {{ optional($shipment->received_at)->format('Y-m-d H:i') }}<br>
            <strong>Received by:</strong> {{ optional($shipment->receivedBy)->name ?? '—' }}<br>
            <strong>To location:</strong> {{ optional($shipment->toLocation)->name ?? '—' }}<br>
            <strong>Project:</strong> {{ optional($shipment->project)->name ?? '—' }}
        </div>

        <div class="section">
            <strong>Colis count:</strong> {{ $shipment->colis_count }}
        </div>

        <div class="section">
            <strong>Sender:</strong> {{ $shipment->sender ?? '—' }}
        </div>

        <div class="section">
            <strong>Admin notes:</strong>
            <p>{{ $shipment->admin_notes ?? '—' }}</p>
        </div>

        <div class="section">
            <p>Signature: ____________________________</p>
            <p>Date: _________________________________</p>
        </div>
    </div>
</body>
</html>
