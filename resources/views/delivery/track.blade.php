<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Your Delivery - MaxMed</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .brand-gradient {
            background: linear-gradient(135deg, #171e60 0%, #0a5694 100%);
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <div class="brand-gradient shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center">
                        <img src="{{ asset('Images/logo.png') }}" alt="MaxMed" class="h-10 w-auto">
                        <span class="ml-3 text-white text-xl font-bold">MaxMed</span>
                    </div>
                    <div class="text-white text-sm">
                        Delivery Tracking
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <!-- Track Delivery Form -->
            @if(!$delivery)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                            <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553-2.276A1 1 0 0020 14.618V3.382a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                            </svg>
                            Track Your Delivery
                        </h2>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-600 mb-6">Enter your tracking number to see the status of your delivery.</p>
                        
                        <form action="{{ route('delivery.track') }}" method="GET" class="flex space-x-4">
                            <div class="flex-1">
                                <input type="text" 
                                       name="tracking" 
                                       placeholder="Enter tracking number (e.g., TRK123456)" 
                                       value="{{ $trackingNumber }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <button type="submit" 
                                    class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 font-medium">
                                Track
                            </button>
                        </form>
                        
                        @if($trackingNumber && !$delivery)
                            <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                                <div class="flex">
                                    <svg class="h-5 w-5 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                    <p class="text-red-800">
                                        <strong>Tracking number not found.</strong> Please check your tracking number and try again.
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Delivery Information -->
            @if($delivery)
                <div class="space-y-8">
                    <!-- Status Overview -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <div class="flex items-center justify-between">
                                <h2 class="text-xl font-semibold text-gray-900">Delivery Status</h2>
                                @php
                                    $statusClasses = [
                                        'pending' => 'bg-orange-100 text-orange-800',
                                        'processing' => 'bg-blue-100 text-blue-800',
                                        'in_transit' => 'bg-purple-100 text-purple-800',
                                        'delivered' => 'bg-green-100 text-green-800',
                                        'cancelled' => 'bg-red-100 text-red-800'
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusClasses[$delivery->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst(str_replace('_', ' ', $delivery->status)) }}
                                </span>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tracking Number</label>
                                    <p class="text-gray-900 bg-gray-50 px-3 py-2 rounded-md font-mono">{{ $delivery->tracking_number }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Order Number</label>
                                    <p class="text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ $delivery->order->order_number }}</p>
                                </div>
                                @if($delivery->carrier && $delivery->carrier !== 'TBD')
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Carrier</label>
                                        <p class="text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ $delivery->carrier }}</p>
                                    </div>
                                @endif
                                @if($delivery->shipped_at)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Shipped Date</label>
                                        <p class="text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ formatDubaiDate($delivery->shipped_at, 'M d, Y H:i') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Progress Timeline -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <h3 class="text-lg font-semibold text-gray-900">Delivery Progress</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-8">
                                <!-- Step 1: Order Created -->
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                            <svg class="h-6 w-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4 min-w-0 flex-1">
                                        <p class="text-sm font-medium text-gray-900">Order Created</p>
                                        <p class="text-sm text-gray-500">{{ $delivery->order->created_at->format('M d, Y H:i') }}</p>
                                        <p class="text-xs text-gray-400 mt-1">Your order has been received and is being prepared</p>
                                    </div>
                                </div>

                                <!-- Step 2: Processing -->
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        @if(in_array($delivery->status, ['processing', 'in_transit', 'delivered']))
                                            <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                                <svg class="h-6 w-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                            </div>
                                        @elseif($delivery->status === 'pending')
                                            <div class="h-10 w-10 rounded-full bg-orange-100 flex items-center justify-center">
                                                <div class="h-3 w-3 rounded-full bg-orange-600"></div>
                                            </div>
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                <div class="h-3 w-3 rounded-full bg-gray-400"></div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4 min-w-0 flex-1">
                                        <p class="text-sm font-medium text-gray-900">Processing</p>
                                        @if($delivery->processed_by_supplier_at)
                                            <p class="text-sm text-gray-500">{{ formatDubaiDate($delivery->processed_by_supplier_at, 'M d, Y H:i') }}</p>
                                        @else
                                            <p class="text-sm text-gray-500">Pending</p>
                                        @endif
                                        <p class="text-xs text-gray-400 mt-1">Your order is being prepared by our supplier</p>
                                    </div>
                                </div>

                                <!-- Step 3: In Transit -->
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        @if(in_array($delivery->status, ['in_transit', 'delivered']))
                                            <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                                <svg class="h-6 w-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                            </div>
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                <div class="h-3 w-3 rounded-full bg-gray-400"></div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4 min-w-0 flex-1">
                                        <p class="text-sm font-medium text-gray-900">In Transit</p>
                                        @if($delivery->shipped_at)
                                            <p class="text-sm text-gray-500">{{ formatDubaiDate($delivery->shipped_at, 'M d, Y H:i') }}</p>
                                        @else
                                            <p class="text-sm text-gray-500">Pending</p>
                                        @endif
                                        <p class="text-xs text-gray-400 mt-1">Your order is on its way to you</p>
                                    </div>
                                </div>

                                <!-- Step 4: Delivered -->
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        @if($delivery->status === 'delivered')
                                            <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                                <svg class="h-6 w-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                            </div>
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                <div class="h-3 w-3 rounded-full bg-gray-400"></div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4 min-w-0 flex-1">
                                        <p class="text-sm font-medium text-gray-900">Delivered</p>
                                        @if($delivery->delivered_at)
                                            <p class="text-sm text-gray-500">{{ formatDubaiDate($delivery->delivered_at, 'M d, Y H:i') }}</p>
                                        @else
                                            <p class="text-sm text-gray-500">Pending</p>
                                        @endif
                                        <p class="text-xs text-gray-400 mt-1">Your order has been delivered</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Signature Required -->
                    @if($delivery->status === 'in_transit')
                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-6">
                            <div class="flex">
                                <svg class="h-6 w-6 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                </svg>
                                <div>
                                    <h3 class="text-lg font-medium text-purple-900">Ready for Signature</h3>
                                    <p class="text-purple-700 mt-2">Your delivery is ready! Please sign to confirm receipt.</p>
                                    <div class="mt-4">
                                        <a href="{{ route('delivery.signature', $delivery->tracking_number) }}" 
                                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                            </svg>
                                            Sign for Delivery
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Delivery Complete -->
                    @if($delivery->status === 'delivered')
                        <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                            <div class="flex">
                                <svg class="h-6 w-6 text-green-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <div>
                                    <h3 class="text-lg font-medium text-green-900">Delivery Complete!</h3>
                                    <p class="text-green-700 mt-2">Your order has been successfully delivered and signed for.</p>
                                    @if($delivery->signed_at)
                                        <p class="text-sm text-green-600 mt-1">Signed on {{ formatDubaiDate($delivery->signed_at, 'M d, Y \a\t H:i') }}</p>
                                    @endif
                                    <div class="mt-4">
                                        <a href="{{ route('delivery.receipt', $delivery->tracking_number) }}" 
                                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-green-800 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            Download Receipt
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Order Items -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <h3 class="text-lg font-semibold text-gray-900">Order Items</h3>
                        </div>
                        <div class="overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($delivery->order->items as $item)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $item->product ? $item->product->name : 'Product #' . $item->product_id }}
                                                </div>
                                                @if($item->variation)
                                                    <div class="text-sm text-gray-500">{{ $item->variation }}</div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->quantity }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($item->price, 2) }} AED</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td colspan="2" class="px-6 py-4 text-right text-sm font-medium text-gray-900">Total:</td>
                                        <td class="px-6 py-4 text-sm font-bold text-gray-900">{{ number_format($delivery->order->total_amount, 2) }} AED</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</body>
</html> 