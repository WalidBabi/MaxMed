@extends('admin.layouts.app')

@section('title', 'Order Details')

@section('content')
<div class="-mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Order #{{ $order->order_number }}</h1>
                <p class="text-gray-600 mt-2">Order details and management</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Back to Orders
                </a>
            </div>
        </div>
    </div>

    <!-- Customer Information -->
    <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="h-5 w-5 text-blue-600 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
                Customer Information
            </h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Customer Name</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $order->getCustomerName() }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Email Address</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $order->getCustomerEmail() }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Order Date</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ formatDubaiDate($order->created_at, 'M d, Y \a\t H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Order Number</dt>
                    <dd class="mt-1 text-sm text-gray-900">#{{ $order->order_number }}</dd>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-wrap gap-8">
        <!-- Order Items -->
        <div class="w-full">
            <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="h-5 w-5 text-green-600 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                        </svg>
                        Order Items
                    </h3>
                </div>
                <div class="p-6">
                    <div class="w-full overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Discount</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($order->orderItems as $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $item->product ? $item->product->name : 'N/A' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $item->quantity }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">AED {{ number_format($item->price, 2) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($item->discount_percentage > 0)
                                                <div class="text-sm text-red-600">{{ number_format($item->discount_percentage, 2) }}%</div>
                                                @if($item->calculated_discount_amount > 0)
                                                    <div class="text-xs text-gray-500">(-AED {{ number_format($item->calculated_discount_amount, 2) }})</div>
                                                @endif
                                            @elseif($item->discount_amount > 0)
                                                <div class="text-sm text-red-600">-AED {{ number_format($item->discount_amount, 2) }}</div>
                                            @else
                                                <div class="text-sm text-gray-500">-</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">AED {{ number_format($item->line_total, 2) }}</div>
                                        </td>
                                    </tr>
                                @endforeach
                                @php
                                    $orderSubtotal = $order->orderItems->sum('line_subtotal');
                                    $totalDiscount = $order->orderItems->sum('calculated_discount_amount');
                                    $orderTotal = $order->orderItems->sum('line_total');
                                @endphp
                                @if($totalDiscount > 0)
                                    <tr class="bg-yellow-50">
                                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-right">
                                            <div class="text-sm font-medium text-gray-900">Subtotal</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">AED {{ number_format($orderSubtotal, 2) }}</div>
                                        </td>
                                    </tr>
                                    <tr class="bg-red-50">
                                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-right">
                                            <div class="text-sm font-medium text-red-600">Total Discount</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-red-600">-AED {{ number_format($totalDiscount, 2) }}</div>
                                        </td>
                                    </tr>
                                @endif
                                <tr class="bg-gray-50">
                                    <td colspan="4" class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="text-sm font-medium text-gray-900">Total</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">AED {{ number_format($orderTotal, 2) }}</div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Status -->
        <div class="w-full md:w-96">
            <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="h-5 w-5 text-purple-600 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Order Status
                    </h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('admin.orders.status.update', $order) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')
                        
                        <div>
                            <label for="status" class="block text-sm font-medium leading-6 text-gray-900">Current Status</label>
                            <div class="mt-2">
                                <select name="status" id="status" 
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                    @php
                                        $allowedTransitions = [
                                            'pending' => ['pending', 'processing', 'cancelled'],
                                            'processing' => ['processing', 'shipped', 'cancelled'],
                                            'shipped' => ['shipped'],
                                            'cancelled' => ['cancelled']
                                        ];
                                        $currentStatus = $order->status;
                                        $validTransitions = $allowedTransitions[$currentStatus] ?? [$currentStatus];

                                        $statusColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'processing' => 'bg-blue-100 text-blue-800',
                                            'shipped' => 'bg-green-100 text-green-800',
                                            'cancelled' => 'bg-red-100 text-red-800'
                                        ];
                                        $statusColor = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    
                                    @foreach($validTransitions as $status)
                                        <option value="{{ $status }}" {{ $order->status == $status ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <button type="submit" class="w-full inline-flex justify-center items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                            </svg>
                            Update Status
                        </button>
                    </form>
                </div>
            </div>

            <!-- Order Status Badge -->
            <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 mt-6">
                <div class="p-6 text-center">
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium {{ $statusColor }}">
                        {{ ucfirst($order->status) }}
                    </span>
                    
                    <p class="mt-2 text-sm text-gray-500">Current order status</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('modals')
@endpush

@push('scripts')
<script>
    // No rejection functionality
</script>
@endpush
@endsection