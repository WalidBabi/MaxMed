@extends('supplier.layouts.app')

@section('title', 'Supplier Dashboard')

@section('content')
    {{-- Dashboard Header --}}
    <x-dashboard-header 
        title="Welcome to MaxMed Supplier Portal"
        subtitle="Manage your product catalog and track your submissions"
    />

    {{-- Product Management Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <div class="lg:col-span-2">
            <x-stats-card
                title="Product Management"
                :icon="'<svg class=\"w-5 h-5\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                    <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4\"></path>
                </svg>'"
                :stats="[
                    ['value' => $totalProducts, 'label' => 'Total Products', 'color' => 'blue'],
                    ['value' => $activeProducts, 'label' => 'Active Products', 'color' => 'green']
                ]"
                description="Manage your product catalog and inventory"
                actionLabel="Manage Products"
                :actionUrl="route('supplier.products.index')"
                color="blue"
            />
        </div>

        <div>
            <x-quick-actions-card 
                title="Quick Actions"
                :actions="[
                    [
                        'label' => 'Add New Product',
                        'url' => route('supplier.products.create'),
                        'style' => 'border-transparent text-white bg-green-600',
                        'hover' => 'bg-green-700',
                        'icon' => '<svg class=\"w-4 h-4 mr-2\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                            <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M12 6v6m0 0v6m0-6h6m-6 0H6\"></path>
                        </svg>'
                    ],
                    [
                        'label' => 'View Orders',
                        'url' => route('supplier.orders.index'),
                        'icon' => '<svg class=\"w-4 h-4 mr-2\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                            <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z\"></path>
                        </svg>'
                    ],
                    [
                        'label' => 'Send Feedback',
                        'url' => route('supplier.feedback.create'),
                        'icon' => '<svg class=\"w-4 h-4 mr-2\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                            <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z\"></path>
                        </svg>'
                    ]
                ]"
            />
        </div>
    </div>

    {{-- Order Management Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <div class="lg:col-span-2">
            <x-stats-card
                title="Order Management"
                :icon="'<svg class=\"w-5 h-5\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                    <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z\"></path>
                </svg>'"
                :stats="[
                    ['value' => $orderStats['pending'], 'label' => 'Pending', 'color' => 'orange'],
                    ['value' => $orderStats['processing'], 'label' => 'Processing', 'color' => 'blue'],
                    ['value' => $orderStats['in_transit'], 'label' => 'In Transit', 'color' => 'purple'],
                    ['value' => $orderStats['delivered_today'], 'label' => 'Delivered Today', 'color' => 'green']
                ]"
                description="Process and manage customer orders"
                actionLabel="Manage Orders"
                :actionUrl="route('supplier.orders.index')"
                color="purple"
            />
        </div>

        <div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Orders Requiring Attention</h3>
                </div>
                <div class="p-6">
                    @if($recentOrders->count() > 0)
                        <div class="space-y-3">
                            @foreach($recentOrders as $order)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $order->order_number }}</div>
                                        <div class="text-xs text-gray-500">{{ $order->items->count() }} items</div>
                                    </div>
                                    <div class="text-right">
                                        @php
                                            $statusClasses = [
                                                'pending' => 'bg-orange-100 text-orange-800',
                                                'processing' => 'bg-blue-100 text-blue-800',
                                                'awaiting_quotations' => 'bg-yellow-100 text-yellow-800',
                                                'quotations_received' => 'bg-blue-100 text-blue-800',
                                            ];
                                            
                                            if ($order->delivery) {
                                                $currentStatus = $order->delivery->status;
                                                $statusClass = $statusClasses[$currentStatus] ?? 'bg-gray-100 text-gray-800';
                                                $statusText = ucfirst($currentStatus);
                                            } else {
                                                $currentStatus = $order->status;
                                                $statusClass = $statusClasses[$currentStatus] ?? 'bg-gray-100 text-gray-800';
                                                $statusText = $currentStatus === 'awaiting_quotations' ? 'Needs Quotation' : ucfirst(str_replace('_', ' ', $currentStatus));
                                            }
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                                            {{ $statusText }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('supplier.orders.index', ['status' => 'pending']) }}" 
                               class="text-sm text-purple-600 hover:text-purple-700 font-medium">
                                View all pending orders →
                            </a>
                        </div>
                    @else
                        <x-empty-state 
                            title="All caught up!"
                            message="No orders requiring immediate attention."
                            :icon="'<svg class=\"h-8 w-8 text-gray-400\" fill=\"none\" viewBox=\"0 0 24 24\" stroke=\"currentColor\">
                                <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z\"></path>
                            </svg>'"
                        />
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection 