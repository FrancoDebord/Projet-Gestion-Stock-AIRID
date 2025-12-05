@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Dashboard') }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-6">Stock Management System</h3>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @if(auth()->user()->hasPermission('view_stock'))
                        <a href="{{ route('stocks.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-6 px-4 rounded-lg text-center">
                            <div class="text-2xl mb-2">📦</div>
                            <div>Stock Items</div>
                        </a>
                    @endif

                    @if(auth()->user()->hasPermission('record_stock_in') || auth()->user()->hasPermission('record_stock_out'))
                        <a href="{{ route('movements.index') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-6 px-4 rounded-lg text-center">
                            <div class="text-2xl mb-2">🔄</div>
                            <div>Movements</div>
                        </a>
                    @endif

                    @if(auth()->user()->hasPermission('view_users'))
                        <a href="{{ route('users.index') }}" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-6 px-4 rounded-lg text-center">
                            <div class="text-2xl mb-2">👥</div>
                            <div>Users</div>
                        </a>
                    @endif

                    @if(auth()->user()->hasPermission('view_audit_log'))
                        <a href="javascript:void(0)" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-6 px-4 rounded-lg text-center cursor-not-allowed opacity-50">
                            <div class="text-2xl mb-2">📋</div>
                            <div>Audit Log</div>
                        </a>
                    @endif

                    <a href="{{ route('shipments.index') }}" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-6 px-4 rounded-lg text-center">
                        <div class="text-2xl mb-2">📥</div>
                        <div>Shipments</div>
                    </a>
                </div>
            </div>

            <!-- Additional Information Section -->
            <div class="mt-8 mb-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-6">Stock Locations</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    @php
                        $locations = \App\Models\StockLocation::withCount('items')->get();
                    @endphp

                    @forelse($locations as $loc)
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <div class="text-gray-900 dark:text-gray-100">
                                <h5 class="font-semibold text-lg mb-2">{{ $loc->name }}</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ $loc->code ?? '—' }}</p>
                                <p class="text-sm"><strong>Items:</strong> <span class="text-blue-600">{{ $loc->items_count }}</span></p>
                                <p class="text-xs text-gray-500 mt-2">{{ $loc->description }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="col-span-4 text-gray-500">No stock locations found.</p>
                    @endforelse
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- User Info Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h4 class="font-semibold text-lg mb-4">Your Information</h4>
                        <p class="mb-2"><strong>Name:</strong> {{ auth()->user()->name }}</p>
                        <p class="mb-4"><strong>Email:</strong> {{ auth()->user()->email }}</p>
                        <div class="mb-4">
                            <p class="font-semibold mb-2">Assigned Roles:</p>
                            <div class="flex flex-wrap gap-2">
                                @if(auth()->user()->roles->count())
                                    @foreach(auth()->user()->roles as $role)
                                        <span class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm">
                                            {{ ucfirst($role->name) }}
                                        </span>
                                    @endforeach
                                @else
                                    <span class="text-gray-500 italic">No roles assigned</span>
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="text-blue-500 hover:underline">Edit Profile →</a>
                    </div>
                </div>

                <!-- Quick Stats Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h4 class="font-semibold text-lg mb-4">Quick Stats</h4>
                        @if(auth()->user()->hasPermission('view_stock'))
                            <p class="mb-2">
                                <strong>Total Stock Items:</strong> 
                                <span class="text-blue-600">{{ \App\Models\StockItem::count() }}</span>
                            </p>
                            <p class="mb-2">
                                <strong>Low Stock Items:</strong> 
                                <span class="text-red-600">
                                    {{ \App\Models\StockItem::whereRaw('quantity <= min_quantity')->count() }}
                                </span>
                            </p>
                        @endif
                        @if(auth()->user()->hasPermission('view_movements'))
                            <p class="mb-2">
                                <strong>Total Movements:</strong> 
                                <span class="text-green-600">{{ \App\Models\StockMovement::count() }}</span>
                            </p>
                            <p class="mb-2">
                                <strong>Today's Movements:</strong> 
                                <span class="text-green-600">
                                    {{ \App\Models\StockMovement::whereDate('created_at', today())->count() }}
                                </span>
                            </p>
                        @endif
                        @if(auth()->user()->hasPermission('view_users'))
                            <p>
                                <strong>Total Users:</strong> 
                                <span class="text-purple-600">{{ \App\Models\User::count() }}</span>
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
