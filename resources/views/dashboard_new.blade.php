{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.app_new')

@section('content')

  {{-- Section “boutons / cards” --}}
  <div class="row gy-4 mb-5">
        @php
            $buttons = [
                ['label' => 'Stock Locations', 'route' => 'stock-locations.index', 'icon' => 'fa-warehouse', 'color' => 'primary', 'permission' => 'view_stock'],
                ['label' => 'Stock Items Management', 'route' => 'stock-items.index', 'icon' => 'fa-boxes-stacked', 'color' => 'success', 'permission' => 'view_stock'],
                ['label' => 'Stock Requests', 'route' => 'stock-requests.index', 'icon' => 'fa-clipboard-list', 'color' => 'danger', 'permission' => 'view_stock'],
                ['label' => 'Facility Validation', 'route' => 'facility-manager.index', 'icon' => 'fa-user-check', 'color' => 'primary', 'permission' => 'approve_stock_requests_facility', 'badge' => $pendingFacilityRequests ?? 0],
                ['label' => 'Shipment Reception', 'route' => 'stock-arrivals-admin.index', 'icon' => 'fa-truck-loading', 'color' => 'info', 'permission' => 'record_stock_in'],
                ['label' => 'Stock Reception', 'route' => 'stock-receptions.index', 'icon' => 'fa-box-open', 'color' => 'primary', 'permission' => 'record_stock_in'],
                ['label' => 'Stock Movements', 'route' => 'movements.index', 'icon' => 'fa-exchange-alt', 'color' => 'secondary', 'permission' => 'view_movements'],
                ['label' => 'Stock by Project', 'route' => 'project-stocks.index', 'icon' => 'fa-chart-pie', 'color' => 'info', 'permission' => 'view_stock'],
                ['label' => 'Record Stock In', 'route' => 'movements.create-in', 'icon' => 'fa-plus-circle', 'color' => 'success', 'permission' => 'record_stock_in'],
                ['label' => 'Process Reception', 'route' => 'movements.process-reception', 'icon' => 'fa-boxes-packing', 'color' => 'info', 'permission' => 'record_stock_in'],
                ['label' => 'Record Stock Out', 'route' => 'movements.create-out', 'icon' => 'fa-minus-circle', 'color' => 'danger', 'permission' => 'record_stock_out', 'badge' => $approvedRequestsToFulfill ?? 0],
                ['label' => 'Fulfill Requests', 'route' => 'movements.fulfill-request', 'icon' => 'fa-clipboard-check', 'color' => 'success', 'permission' => 'record_stock_out', 'badge' => $approvedRequestsToFulfill ?? 0],
                ['label' => 'Adjust Stock', 'route' => 'movements.create-adjustment', 'icon' => 'fa-sliders-h', 'color' => 'warning', 'permission' => 'adjust_stock'],
                ['label' => 'Product Categories', 'route' => 'product-categories.index', 'icon' => 'fa-tags', 'color' => 'info', 'permission' => 'view_product_categories'],
                ['label' => 'Statistics', 'route' => 'statistics', 'icon' => 'fa-chart-line', 'color' => 'dark', 'permission' => 'view_reports'],
            ];
        @endphp

        @foreach ($buttons as $btn)
            @php
                $can = auth()->check() && (
                        empty($btn['permission']) || auth()->user()->hasPermission($btn['permission'])
                );
                $hasBadge = isset($btn['badge']) && $btn['badge'] > 0;
            @endphp

            @if(1)
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="{{ route($btn['route']) }}" class="card text-white bg-{{ $btn['color'] }} card-btn h-100 text-center p-3 d-flex flex-column justify-content-center align-items-center position-relative">
                        @if($hasBadge)
                            <span class="badge bg-danger position-absolute top-0 end-0 m-2" style="font-size: 0.75rem;">
                                <i class="fas fa-exclamation-triangle"></i>
                                {{ $btn['badge'] }}
                            </span>
                        @endif
                        <i class="fas {{ $btn['icon'] }} fa-2x mb-3"></i>
                        <span class="h5">{{ $btn['label'] }}</span>
                    </a>
                </div>
            @endif
        @endforeach
  </div>

  {{-- Section “Stock Locations disponibles” --}}

  {{-- Section “Stock Locations disponibles” --}}
<div class="card mb-4">
    <div class="card-header bg-white border-0 d-flex align-items-center justify-content-between">
        <h5 class="mb-0">Stock Locations disponibles</h5>
        @if(auth()->check() && (auth()->user()->hasPermission('create_stock') || auth()->user()->hasPermission('manage_settings')))
            <a href="{{ route('stock-locations.create') }}" class="btn btn-outline-primary btn-sm">Add new Stock Location</a>
        @endif
    </div>

    <div class="card-body">

        @if($locations->isEmpty())
            <p class="text-muted">Aucune location de stock trouvée.</p>
        @else

            @php
                // Palette de couleurs harmonieuses (Bootstrap + neutres modernes)
                $colors = [
                    'primary', 'success', 'info', 'warning', 'danger', 'secondary',
                    'dark'
                ];
            @endphp

            <div class="row gy-4">

                @foreach($locations as $index => $loc)
                    @php
                        // Attribution de couleur en boucle
                        $color = $colors[$index % count($colors)];
                    @endphp

                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm border-0 location-card">

                            <div class="card-body py-4 text-white bg-{{ $color }} rounded">
                                <h5 class="card-title fw-bold d-flex align-items-center justify-content-between">
                                    <a href="{{ route('stock-locations.show', $loc->id) }}" class="text-white text-decoration-none">{{ $loc->stock_name }}</a>
                                    @if(isset($pendingArrivals[$loc->id]) && $pendingArrivals[$loc->id] > 0)
                                        <span class="badge bg-danger ms-2" title="{{ $pendingArrivals[$loc->id] }} réception(s) en attente">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            {{ $pendingArrivals[$loc->id] }}
                                        </span>
                                    @endif
                                </h5>

                                @php
                                    $canManage = auth()->check() && (
                                        auth()->user()->hasPermission('edit_stock') ||
                                        auth()->id() == optional($loc)->principal_manager ||
                                        auth()->id() == optional($loc)->creator
                                    );
                                @endphp

                                @if($canManage)
                                                <a href="{{ route('stock-locations.show', $loc->id) }}" 
                                       class="btn btn-light btn-sm mt-3 fw-semibold">
                                        Manage this Stock
                                    </a>
                                @endif
                            </div>

                        </div>
                    </div>

                @endforeach

            </div>

        @endif

    </div>
</div>

  
@endsection
