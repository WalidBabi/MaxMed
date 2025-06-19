@extends('admin.layouts.app')

@section('title', 'Purchase Orders')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Purchase Orders</h1>
                <p class="text-gray-600 mt-2">Manage purchase orders sent to suppliers</p>
            </div>
            <div>
                <a href="{{ route('admin.purchase-orders.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Create Purchase Order
                </a>
            </div>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="border-b border-gray-200">
            <nav class="flex space-x-8 px-6" aria-label="Tabs">
                <a href="{{ route('admin.purchase-orders.index', ['status' => 'all']) }}" 
                   class="@if($status === 'all') border-indigo-500 text-indigo-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                    All Orders
                    <span class="@if($status === 'all') bg-indigo-100 text-indigo-600 @else bg-gray-100 text-gray-900 @endif ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $statusCounts['all'] }}</span>
                </a>
                
                <a href="{{ route('admin.purchase-orders.index', ['status' => 'draft']) }}" 
                   class="@if($status === 'draft') border-gray-500 text-gray-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                    Draft
                    <span class="@if($status === 'draft') bg-gray-100 text-gray-600 @else bg-gray-100 text-gray-900 @endif ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $statusCounts['draft'] }}</span>
                </a>
                
                <a href="{{ route('admin.purchase-orders.index', ['status' => 'sent_to_supplier']) }}" 
                   class="@if($status === 'sent_to_supplier') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                    Sent to Supplier
                    <span class="@if($status === 'sent_to_supplier') bg-blue-100 text-blue-600 @else bg-gray-100 text-gray-900 @endif ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $statusCounts['sent_to_supplier'] }}</span>
                </a>
                
                <a href="{{ route('admin.purchase-orders.index', ['status' => 'acknowledged']) }}" 
                   class="@if($status === 'acknowledged') border-yellow-500 text-yellow-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                    Acknowledged
                    <span class="@if($status === 'acknowledged') bg-yellow-100 text-yellow-600 @else bg-gray-100 text-gray-900 @endif ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $statusCounts['acknowledged'] }}</span>
                </a>
                
                <a href="{{ route('admin.purchase-orders.index', ['status' => 'in_production']) }}" 
                   class="@if($status === 'in_production') border-purple-500 text-purple-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                    In Production
                    <span class="@if($status === 'in_production') bg-purple-100 text-purple-600 @else bg-gray-100 text-gray-900 @endif ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $statusCounts['in_production'] }}</span>
                </a>
                
                <a href="{{ route('admin.purchase-orders.index', ['status' => 'completed']) }}" 
                   class="@if($status === 'completed') border-green-500 text-green-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                    Completed
                    <span class="@if($status === 'completed') bg-green-100 text-green-600 @else bg-gray-100 text-gray-900 @endif ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $statusCounts['completed'] }}</span>
                </a>
            </nav>
        </div>
    </div>

    <!-- Purchase Orders List -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        @if($purchaseOrders->count() > 0)
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">
                    @if($status === 'all')
                        All Purchase Orders
                    @else
                        {{ ucfirst(str_replace('_', ' ', $status)) }} Purchase Orders
                    @endif
                    <span class="text-gray-500 font-normal">({{ $purchaseOrders->total() }} total)</span>
                </h3>
            </div>
            
            <div class="overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PO Number</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer Order</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($purchaseOrders as $po)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $po->po_number }}</div>
                                            <div class="text-sm text-gray-500">{{ formatDubaiDate($po->po_date, 'M d, Y') }}</div>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $po->order->order_number ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">{{ $po->order->getCustomerName() ?? 'N/A' }}</div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $po->supplier_name }}</div>
                                    @if($po->supplier_email)
                                        <div class="text-sm text-gray-500">{{ $po->supplier_email }}</div>
                                    @endif
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $po->currency }} {{ $po->formatted_total }}</div>
                                    @if($po->paid_amount > 0)
                                        <div class="text-sm text-gray-500">Paid: {{ $po->currency }} {{ $po->formatted_paid_amount }}</div>
                                    @endif
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $po->status_badge_class }}">
                                        {{ \App\Models\PurchaseOrder::$statuses[$po->status] ?? ucfirst($po->status) }}
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $po->payment_status_badge_class }}">
                                        {{ \App\Models\PurchaseOrder::$paymentStatuses[$po->payment_status] ?? ucfirst($po->payment_status) }}
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ formatDubaiDate($po->created_at, 'M d, Y') }}
                                    <br>
                                    <span class="text-xs">{{ formatDubaiDate($po->created_at, 'H:i') }}</span>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('admin.purchase-orders.show', $po) }}" 
                                           class="text-indigo-600 hover:text-indigo-900 inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            View
                                        </a>
                                        
                                        @if($po->status === 'draft')
                                            <a href="{{ route('admin.purchase-orders.edit', $po) }}" 
                                               class="text-gray-600 hover:text-gray-900 inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-gray-700 bg-gray-100 hover:bg-gray-200">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Edit
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($purchaseOrders->hasPages())
                <div class="px-6 py-3 border-t border-gray-200">
                    {{ $purchaseOrders->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No purchase orders found</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating a new purchase order.</p>
                <div class="mt-6">
                    <a href="{{ route('admin.purchase-orders.create') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Create Purchase Order
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection 