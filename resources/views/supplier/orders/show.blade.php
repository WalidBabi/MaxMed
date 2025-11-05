@extends('supplier.layouts.app')

@section('title', 'Order Details - ' . $order->order_number)

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('supplier.orders.index') }}" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <h1 class="text-3xl font-bold text-gray-900">Order {{ $order->order_number }}</h1>
                </div>
                <p class="text-gray-600 mt-2">Manage order processing and delivery</p>
            </div>
            <div class="flex items-center space-x-3">
                @if($order->requires_quotation && $order->status === 'awaiting_quotations')
                    @php
                        $existingQuotation = \App\Models\SupplierQuotation::where('order_id', $order->id)
                            ->where('supplier_id', auth()->id())
                            ->first();
                    @endphp

                    @if(!$existingQuotation)
                        <a href="{{ route('supplier.orders.quotation', $order) }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            Submit Quotation
                        </a>
                    @else
                        <div class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-gray-50">
                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Quotation Submitted
                        </div>
                    @endif
                @endif

                @php
                    $delivery = $order->delivery;
                    $statusClasses = [
                        'pending' => 'bg-orange-100 text-orange-800',
                        'processing' => 'bg-blue-100 text-blue-800',
                        'in_transit' => 'bg-purple-100 text-purple-800',
                        'delivered' => 'bg-green-100 text-green-800',
                        'cancelled' => 'bg-red-100 text-red-800'
                    ];
                    
                    // Determine status based on order and delivery
                    if ($delivery) {
                        $currentStatus = $delivery->status;
                        $statusClass = $statusClasses[$currentStatus] ?? 'bg-gray-100 text-gray-800';
                        $statusText = $currentStatus === 'in_transit' ? 'Shipped to MaxMed' : ucfirst(str_replace('_', ' ', $currentStatus));
                    } else {
                        // No delivery yet - show order status
                        $currentStatus = $order->status;
                        $statusClass = match($currentStatus) {
                            'awaiting_quotations' => 'bg-yellow-100 text-yellow-800',
                            'quotations_received' => 'bg-blue-100 text-blue-800',
                            'processing' => 'bg-indigo-100 text-indigo-800',
                            default => 'bg-gray-100 text-gray-800'
                        };
                        $statusText = match($currentStatus) {
                            'awaiting_quotations' => 'Needs Quotation',
                            'quotations_received' => 'Quotation Received',
                            'processing' => 'Processing',
                            default => ucfirst(str_replace('_', ' ', $currentStatus))
                        };
                    }
                @endphp
                <span class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium {{ $statusClass }}">
                    {{ $statusText }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Order Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Order Information
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Order Number</label>
                            <p class="text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ $order->order_number }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Total Items</label>
                            <p class="text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ $order->items->count() }} items</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Created At</label>
                            <p class="text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ formatDubaiDate($order->created_at, 'M d, Y H:i') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Order Status</label>
                            <p class="text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ ucfirst($order->status) }}</p>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Add after Order Information and before Order Items -->
            @if($order->requires_quotation)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            Quotation Status
                        </h3>
                    </div>
                    <div class="p-6">
                        @php
                            $myQuotation = \App\Models\SupplierQuotation::where('order_id', $order->id)
                                ->where('supplier_id', auth()->id())
                                ->first();
                        @endphp

                        @if($myQuotation)
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $myQuotation->status_badge_class }}">
                                            {{ $myQuotation->formatted_status }}
                                        </span>
                                        <p class="mt-1 text-sm text-gray-500">Submitted {{ $myQuotation->created_at->diffForHumans() }}</p>
                                    </div>
                                    <div class="text-xl font-bold text-gray-900">
                                        {{ $myQuotation->currency }} {{ number_format($myQuotation->total_amount, 2) }}
                                    </div>
                                    @if($myQuotation->shipping_cost)
                                        <div class="text-sm text-gray-500">
                                            + {{ number_format($myQuotation->shipping_cost, 2) }} {{ $myQuotation->currency }} shipping
                                        </div>
                                    @endif
                                </div>

                                @if($myQuotation->notes)
                                    <div class="mt-4">
                                        <h4 class="text-sm font-medium text-gray-700">Notes & Terms</h4>
                                        <p class="mt-1 text-sm text-gray-600 bg-gray-50 rounded-lg p-3">
                                            {{ $myQuotation->notes }}
                                        </p>
                                    </div>
                                @endif

                                @if($myQuotation->status === 'rejected' && $myQuotation->rejection_reason)
                                    <div class="mt-4 bg-red-50 border border-red-200 rounded-lg p-4">
                                        <h4 class="text-sm font-medium text-red-800">Rejection Reason:</h4>
                                        <p class="mt-1 text-sm text-red-700">{{ $myQuotation->rejection_reason }}</p>
                                    </div>
                                @endif

                                @if($myQuotation->status === 'approved')
                                    <div class="mt-4 bg-green-50 border border-green-200 rounded-lg p-4">
                                        <div class="flex">
                                            <svg class="h-5 w-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <div>
                                                <h4 class="text-sm font-medium text-green-800">Quotation Approved</h4>
                                                <p class="mt-1 text-sm text-green-700">
                                                    Your quotation has been approved. Please proceed with order processing.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="text-center py-6">
                                @if($order->status === 'awaiting_quotations')
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">No quotation submitted</h3>
                                    <p class="mt-1 text-sm text-gray-500">Submit your quotation to proceed with this order.</p>
                                    <div class="mt-6">
                                        <a href="{{ route('supplier.orders.quotation', $order) }}" 
                                           class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                            Submit Quotation
                                        </a>
                                    </div>
                                @else
                                    <p class="text-sm text-gray-500">This order is no longer accepting quotations.</p>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Order Items -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        Items to Prepare ({{ $order->items->count() }})
                    </h3>
                </div>
                <div class="overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Specifications</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($order->items as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if($item->product && $item->product->primaryImage)
                                                <img class="h-10 w-10 rounded-lg object-cover mr-4" src="{{ asset('storage/' . $item->product->primaryImage->image_path) }}" alt="{{ $item->product->name }}">
                                            @else
                                                <div class="h-10 w-10 rounded-lg bg-gray-200 flex items-center justify-center mr-4">
                                                    <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                    </svg>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $item->product ? $item->product->name : 'Product #' . $item->product_id }}
                                                </div>
                                                @if($item->product && $item->product->description)
                                                    <div class="text-sm text-gray-500">{{ Str::limit($item->product->description, 50) }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $item->quantity }} units
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($item->variation)
                                            {{ $item->variation }}
                                        @else
                                            <span class="text-gray-400">Standard</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Delivery Instructions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Delivery Instructions
                    </h3>
                </div>
                <div class="p-6">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex">
                            <svg class="h-5 w-5 text-yellow-400 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <div class="text-sm text-yellow-800">
                                <p class="font-medium">Important Delivery Guidelines:</p>
                                <ul class="mt-2 list-disc list-inside space-y-1">
                                    <li>Prepare all items listed above according to specifications</li>
                                    <li>Package items securely for safe transport</li>
                                    <li>Include packing list and commercial invoice when ready</li>
                                    <li>Ensure delivery address is accurate before shipping</li>
                                    <li>Update tracking information once handed to carrier</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Workflow Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 sticky top-6">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Delivery Workflow
                    </h3>
                </div>
                <div class="p-6">
                    <!-- Workflow Status -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm font-medium text-gray-700">Current Status</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                {{ $statusText }}
                            </span>
                        </div>
                        
                        <!-- Workflow Steps -->
                        <div class="space-y-3">
                            @php
                                $existingQuotation = \App\Models\SupplierQuotation::where('order_id', $order->id)
                                    ->where('supplier_id', auth()->id())
                                    ->first();
                            @endphp
                            @if($order->requires_quotation)
                                <!-- Step 1: Quotation -->
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        @php
                                            $quotationStatus = $existingQuotation ? $existingQuotation->status : 'pending';
                                        @endphp

                                        @if($quotationStatus === 'approved')
                                            <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                                                <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>
                                        @elseif($quotationStatus === 'rejected')
                                            <div class="h-8 w-8 rounded-full bg-red-100 flex items-center justify-center">
                                                <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </div>
                                        @elseif($existingQuotation)
                                            <div class="h-8 w-8 rounded-full bg-yellow-100 flex items-center justify-center">
                                                <svg class="h-5 w-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                        @else
                                            <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                                <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4 min-w-0 flex-1">
                                        <div class="text-sm font-medium text-gray-900">1. Submit Quotation</div>
                                        <div class="text-sm text-gray-500">
                                            @if($quotationStatus === 'approved')
                                                Approved {{ $existingQuotation->approved_at->diffForHumans() }}
                                                <div class="mt-1">
                                                    <span class="font-medium text-gray-900">{{ number_format($existingQuotation->total_amount, 2) }} {{ $existingQuotation->currency }}</span>
                                                    @if($existingQuotation->shipping_cost)
                                                        <div class="text-sm text-gray-500">
                                                            + {{ number_format($existingQuotation->shipping_cost, 2) }} {{ $existingQuotation->currency }} shipping
                                                        </div>
                                                    @endif
                                                </div>
                                            @elseif($quotationStatus === 'rejected')
                                                Rejected - Please contact admin
                                                @if($existingQuotation->rejection_reason)
                                                    <p class="mt-1 text-xs text-red-600">{{ $existingQuotation->rejection_reason }}</p>
                                                @endif
                                            @elseif($existingQuotation)
                                                Under review - Submitted {{ $existingQuotation->created_at->diffForHumans() }}
                                                <div class="mt-1">
                                                    <span class="font-medium text-gray-900">{{ number_format($existingQuotation->total_amount, 2) }} {{ $existingQuotation->currency }}</span>
                                                    @if($existingQuotation->shipping_cost)
                                                        <div class="text-sm text-gray-500">
                                                            + {{ number_format($existingQuotation->shipping_cost, 2) }} {{ $existingQuotation->currency }} shipping
                                                        </div>
                                                    @endif
                                                </div>
                                            @else
                                                Action required: Submit your quotation
                                                @if($order->status === 'awaiting_quotations')
                                                    <div class="mt-2">
                                                        <a href="{{ route('supplier.orders.quotation', $order) }}" 
                                                           class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                                            Submit Now
                                                        </a>
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Divider -->
                                <div class="relative py-2">
                                    <div class="absolute inset-0 flex items-center" aria-hidden="true">
                                        <div class="w-full border-t border-gray-200"></div>
                                    </div>
                                </div>
                            @endif

                            <!-- Processing Steps (only shown if no quotation required or quotation approved) -->
                            @if(!$order->requires_quotation || ($existingQuotation && $existingQuotation->status === 'approved'))
                                <!-- Step: Processing -->
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        @if(!$delivery)
                                            <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                                                <div class="h-2 w-2 rounded-full bg-gray-400"></div>
                                            </div>
                                        @elseif($delivery->status === 'pending')
                                            <div class="h-8 w-8 rounded-full bg-orange-100 flex items-center justify-center">
                                                <div class="h-2 w-2 rounded-full bg-orange-600"></div>
                                            </div>
                                        @elseif(in_array($delivery->status, ['processing', 'in_transit', 'delivered']))
                                            <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                                                <svg class="h-5 w-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                            </div>
                                        @else
                                            <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                                                <div class="h-2 w-2 rounded-full bg-gray-400"></div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4 min-w-0 flex-1">
                                        <div class="text-sm font-medium text-gray-900">{{ $order->requires_quotation ? '2' : '1' }}. Process Order</div>
                                        <div class="text-sm text-gray-500">
                                            @if(!$delivery)
                                                Waiting for delivery setup
                                            @elseif($delivery->status === 'pending')
                                                Action required: Start processing
                                                <div class="mt-2">
                                                    <form action="{{ route('supplier.orders.mark-processing', $order) }}" method="POST" class="inline" data-ajax="form" data-confirm="Start processing this order?" data-loading-text="Starting..." data-success-message="Order marked as processing.">
                                                        @csrf
                                                        <button type="submit" 
                                                                class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
                                                                data-loading-text="Starting...">
                                                            Start Processing
                                                        </button>
                                                    </form>
                                                </div>
                                            @elseif($delivery->status === 'processing')
                                                In progress - Started {{ $delivery->processed_by_supplier_at ? $delivery->processed_by_supplier_at->diffForHumans() : 'recently' }}
                                            @else
                                                Completed {{ $delivery->processed_by_supplier_at ? $delivery->processed_by_supplier_at->diffForHumans() : 'recently' }}
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Step: Shipping -->
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        @if(!$delivery)
                                            <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                                                <div class="h-2 w-2 rounded-full bg-gray-400"></div>
                                            </div>
                                        @elseif($delivery->status === 'in_transit')
                                            <div class="h-8 w-8 rounded-full bg-purple-100 flex items-center justify-center">
                                                <div class="h-2 w-2 rounded-full bg-purple-600"></div>
                                            </div>
                                        @elseif($delivery->status === 'delivered')
                                            <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                                                <svg class="h-5 w-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                            </div>
                                        @else
                                            <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                                                <div class="h-2 w-2 rounded-full bg-gray-400"></div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4 min-w-0 flex-1">
                                        <div class="text-sm font-medium text-gray-900">{{ $order->requires_quotation ? '3' : '2' }}. Ship to MaxMed</div>
                                        <div class="text-sm text-gray-500">
                                            @if(!$delivery)
                                                Waiting for delivery setup
                                            @elseif($delivery->status === 'processing')
                                                Ready to ship
                                                <div class="mt-2">
                                                    <button type="button" 
                                                            class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
                                                            onclick="document.getElementById('submit-documents-modal').style.display='block'">
                                                        Submit Shipping Details
                                                    </button>
                                                </div>
                                            @elseif($delivery->status === 'in_transit')
                                                Shipped {{ $delivery->shipped_at ? $delivery->shipped_at->diffForHumans() : 'recently' }}
                                                @if($delivery->carrier && $delivery->tracking_number)
                                                    <div class="mt-1 text-xs">
                                                        {{ $delivery->carrier }} - {{ $delivery->tracking_number }}
                                                    </div>
                                                @endif
                                            @elseif($delivery->status === 'delivered')
                                                Delivered {{ $delivery->delivered_at ? $delivery->delivered_at->diffForHumans() : 'recently' }}
                                            @else
                                                Waiting for processing to complete
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Step: Delivery -->
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        @if($delivery && $delivery->status === 'delivered')
                                            <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                                                <svg class="h-5 w-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                            </div>
                                        @else
                                            <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                                                <div class="h-2 w-2 rounded-full bg-gray-400"></div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4 min-w-0 flex-1">
                                        <div class="text-sm font-medium text-gray-900">{{ $order->requires_quotation ? '4' : '3' }}. Delivery Complete</div>
                                        <div class="text-sm text-gray-500">
                                            @if($delivery && $delivery->status === 'delivered')
                                                Completed {{ $delivery->delivered_at ? $delivery->delivered_at->diffForHumans() : 'recently' }}
                                            @else
                                                Waiting for delivery confirmation
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-4 text-sm text-gray-500">
                                    Order processing will be available after quotation approval
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-4">
                        @if($delivery && $delivery->status === 'pending')
                            <form action="{{ route('supplier.orders.mark-processing', $order) }}" method="POST">
                                @csrf
                                <button type="submit" 
                                        class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                        onclick="return confirm('Mark this order as processing?')">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Start Processing
                                </button>
                            </form>
                        @elseif($delivery && $delivery->status === 'processing')
                            <button type="button" 
                                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-md"
                                    onclick="document.getElementById('submit-documents-modal').style.display='block'">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Ship to MaxMed
                            </button>
                        @elseif(!$delivery)
                            <div class="text-center py-4">
                                <p class="text-sm text-gray-500">Delivery will be set up after quotation approval</p>
                            </div>
                        @endif
                        
                        <!-- Additional Status Controls -->
                        @if($delivery && $delivery->status === 'processing')
                            <div class="border-t pt-4">
                                <h4 class="text-sm font-medium text-gray-900 mb-3">Status Controls</h4>
                                <div class="space-y-2">
                                    <form action="{{ route('supplier.orders.mark-pending', $order) }}" method="POST">
                                        @csrf
                                        <button type="submit" 
                                                class="w-full inline-flex justify-center items-center px-3 py-2 border border-orange-300 text-sm font-medium rounded-md text-orange-700 bg-orange-50 hover:bg-orange-100"
                                                onclick="return confirm('Mark this order as pending again?')">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Back to Pending
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif

                        <!-- Document Downloads -->
                        @if($delivery && ($delivery->packing_list_file || $delivery->commercial_invoice_file))
                            <div class="border-t pt-4">
                                <h4 class="text-sm font-medium text-gray-900 mb-3">Documents</h4>
                                <div class="space-y-2">
                                    @if($delivery->packing_list_file)
                                        <a href="{{ route('supplier.orders.download-packing-list', $order) }}" 
                                           class="w-full inline-flex justify-center items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            Packing List
                                        </a>
                                    @endif
                                    @if($delivery->commercial_invoice_file)
                                        <a href="{{ route('supplier.orders.download-commercial-invoice', $order) }}" 
                                           class="w-full inline-flex justify-center items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            Commercial Invoice
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Timestamps -->
                    <div class="border-t pt-4 mt-6">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Timeline</h4>
                        <div class="space-y-2 text-xs text-gray-600">
                            <div>Created: {{ formatDubaiDate($order->created_at, 'M d, Y H:i') }}</div>
                            @if($delivery && $delivery->processed_by_supplier_at)
                                <div>Processing: {{ formatDubaiDate($delivery->processed_by_supplier_at, 'M d, Y H:i') }}</div>
                            @endif
                            @if($delivery && $delivery->status === 'in_transit')
                                <div>Shipped to MaxMed: {{ formatDubaiDate($delivery->updated_at, 'M d, Y H:i') }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Submit Documents Modal -->
<div id="submit-documents-modal" style="display: none;" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
    <div class="relative mx-auto w-full max-w-2xl bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">Ship Order to MaxMed</h3>
                        <p class="text-indigo-100 text-sm">Submit documents and send to carrier</p>
                    </div>
                </div>
                <button onclick="document.getElementById('submit-documents-modal').style.display='none'" 
                        class="hover:text-indigo-100 transition-colors duration-200 rounded-full p-1 hover:bg-white hover:bg-opacity-20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
            
        <!-- Modal Content -->
        <div class="px-6 py-6">
            <form id="submit-documents-form" action="{{ route('supplier.orders.submit-documents', $order) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-6">
                    <div class="space-y-4">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-blue-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                                <div class="text-sm text-blue-800">
                                    <p class="font-medium mb-1">Required Documents</p>
                                    <p>Please upload both packing list and commercial invoice to complete the shipping process.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="packing_list" class="block text-sm font-semibold text-gray-900">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Packing List *
                                </span>
                            </label>
                            <input type="file" id="packing_list" name="packing_list" required 
                                   accept=".pdf,.jpg,.jpeg,.png"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            <p class="text-xs text-gray-500 mt-1">PDF, JPG, PNG files up to 5MB</p>
                        </div>
                    
                        <div class="space-y-2">
                            <label for="commercial_invoice" class="block text-sm font-semibold text-gray-900">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Commercial Invoice *
                                </span>
                            </label>
                            <input type="file" id="commercial_invoice" name="commercial_invoice" required 
                                   accept=".pdf,.jpg,.jpeg,.png"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            <p class="text-xs text-gray-500 mt-1">PDF, JPG, PNG files up to 5MB</p>
                        </div>
                    </div>

                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-amber-500 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <div class="text-sm text-amber-800">
                                <h4 class="font-medium mb-1">Important: Customs Clearance Notice</h4>
                                <p class="text-xs">Please consider reducing the commercial invoice value to facilitate faster customs clearance, if legally permissible in your jurisdiction. This can help avoid delays and additional inspection fees.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="carrier" class="block text-sm font-semibold text-gray-900">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2v0a2 2 0 01-2-2v-4a2 2 0 00-2-2H8z"></path>
                                    </svg>
                                    Carrier *
                                </span>
                            </label>
                            <input type="text" id="carrier" name="carrier" required 
                                   placeholder="e.g., DHL, FedEx, Aramex"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200">
                        </div>
                        
                        <div class="space-y-2">
                            <label for="tracking_number" class="block text-sm font-semibold text-gray-900">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                                    </svg>
                                    Tracking Number *
                                </span>
                            </label>
                            <input type="text" id="tracking_number" name="tracking_number" required 
                                   placeholder="Enter tracking number"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200">
                        </div>
                    </div>
                    

                    
                    <div class="space-y-2">
                        <label for="supplier_notes" class="block text-sm font-semibold text-gray-900">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Supplier Notes
                            </span>
                        </label>
                        <textarea id="supplier_notes" name="supplier_notes" rows="4"
                                  placeholder="Any additional notes or instructions regarding shipment, customs clearance considerations, special handling requirements, etc."
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 resize-none"></textarea>
                        <p class="text-xs text-gray-500 mt-1">Include any relevant information about customs documentation, declared values, or special shipping instructions.</p>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Modal Footer -->
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="document.getElementById('submit-documents-modal').style.display='none'"
                        class="px-6 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                    Cancel
                </button>
                <button type="submit" form="submit-documents-form"
                        class="px-6 py-3 border border-transparent rounded-lg text-sm font-medium bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-lg transform hover:scale-105 transition-all duration-200">
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        Ship Order to MaxMed
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection 