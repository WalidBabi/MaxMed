@extends('supplier.layouts.app')

@section('title', 'Order Management')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Order Management</h1>
                <p class="text-gray-600 mt-2">Process and manage customer orders</p>
            </div>
            <div class="flex items-center space-x-3">
                <span class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.75 2a.75.75 0 01.75.75V4h7V2.75a.75.75 0 011.5 0V4h.25A2.25 2.25 0 0117.5 6.25v8.5A2.25 2.25 0 0115.25 17H4.75A2.25 2.25 0 012.5 14.75v-8.5A2.25 2.25 0 014.75 4H5V2.75A.75.75 0 015.75 2zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75z" clip-rule="evenodd" />
                    </svg>
                    {{ nowDubai('l, F j, Y') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="mb-6">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <a href="{{ route('supplier.orders.index', ['status' => 'all']) }}" 
                   class="@if($status === 'all') border-indigo-500 text-indigo-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                    All Orders
                    <span class="@if($status === 'all') bg-indigo-100 text-indigo-600 @else bg-gray-100 text-gray-900 @endif ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $statusCounts['all'] }}</span>
                </a>
                
                <a href="{{ route('supplier.orders.index', ['status' => 'pending']) }}" 
                   class="@if($status === 'pending') border-orange-500 text-orange-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                    Pending
                    <span class="@if($status === 'pending') bg-orange-100 text-orange-600 @else bg-gray-100 text-gray-900 @endif ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $statusCounts['pending'] }}</span>
                </a>
                
                <a href="{{ route('supplier.orders.index', ['status' => 'processing']) }}" 
                   class="@if($status === 'processing') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                    Processing
                    <span class="@if($status === 'processing') bg-blue-100 text-blue-600 @else bg-gray-100 text-gray-900 @endif ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $statusCounts['processing'] }}</span>
                </a>
                
                <a href="{{ route('supplier.orders.index', ['status' => 'in_transit']) }}" 
                   class="@if($status === 'in_transit') border-purple-500 text-purple-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                    Shipped to MaxMed
                    <span class="@if($status === 'in_transit') bg-purple-100 text-purple-600 @else bg-gray-100 text-gray-900 @endif ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $statusCounts['in_transit'] }}</span>
                </a>
                
                <a href="{{ route('supplier.orders.index', ['status' => 'delivered']) }}" 
                   class="@if($status === 'delivered') border-green-500 text-green-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                    Delivered
                    <span class="@if($status === 'delivered') bg-green-100 text-green-600 @else bg-gray-100 text-gray-900 @endif ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $statusCounts['delivered'] }}</span>
                </a>
            </nav>
        </div>
    </div>

    <!-- Orders List -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        @if($orders->count() > 0)
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">
                    @if($status === 'all')
                        All Orders
                    @else
                        {{ ucfirst(str_replace('_', ' ', $status)) }} Orders
                    @endif
                    <span class="text-gray-500 font-normal">({{ $orders->total() }} total)</span>
                </h3>
            </div>
            
            <div class="overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Delivery Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($orders as $order)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                                <svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M8 11v6h8v-6M8 11H6a2 2 0 00-2 2v6a2 2 0 002 2h12a2 2 0 002-2v-6a2 2 0 00-2-2h-2" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $order->order_number }}</div>
                                            <div class="text-sm text-gray-500">{{ $order->items->count() }} items</div>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $order->items->count() }} items</div>
                                    <div class="text-sm text-gray-500">
                                        @foreach($order->items->take(2) as $item)
                                            {{ $item->product ? $item->product->name : 'Product #' . $item->product_id }}@if(!$loop->last), @endif
                                        @endforeach
                                        @if($order->items->count() > 2)
                                            <span class="text-gray-400">+{{ $order->items->count() - 2 }} more</span>
                                        @endif
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $delivery = $order->delivery;
                                        $statusClasses = [
                                            'pending' => 'bg-orange-100 text-orange-800',
                                            'processing' => 'bg-blue-100 text-blue-800',
                                            'in_transit' => 'bg-purple-100 text-purple-800',
                                            'delivered' => 'bg-green-100 text-green-800',
                                            'cancelled' => 'bg-red-100 text-red-800'
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClasses[$delivery->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        @if($delivery->status === 'in_transit')
                                            Shipped to MaxMed
                                        @else
                                            {{ ucfirst(str_replace('_', ' ', $delivery->status)) }}
                                        @endif
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ formatDubaiDate($order->created_at, 'M d, Y') }}
                                    <br>
                                    <span class="text-xs">{{ formatDubaiDate($order->created_at, 'H:i') }}</span>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('supplier.orders.show', $order) }}" 
                                           class="text-indigo-600 hover:text-indigo-900 inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            View
                                        </a>
                                        
                                        @if($delivery->status === 'pending')
                                            <form action="{{ route('supplier.orders.mark-processing', $order) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="text-blue-600 hover:text-blue-900 inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-blue-700 bg-blue-100 hover:bg-blue-200"
                                                        onclick="return confirm('Mark this order as processing?')">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    Process
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($orders->hasPages())
                <div class="px-6 py-3 border-t border-gray-200">
                    {{ $orders->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m13-8l-3-3-3 3m6 0H7" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No orders found</h3>
                <p class="mt-1 text-sm text-gray-500">
                    @if($status === 'all')
                        No orders have been created yet.
                    @else
                        No orders with "{{ str_replace('_', ' ', $status) }}" status found.
                    @endif
                </p>
            </div>
        @endif
    </div>
</div>
@endsection 