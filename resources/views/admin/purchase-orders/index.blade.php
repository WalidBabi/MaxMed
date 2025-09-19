@extends('admin.layouts.app')

@section('title', 'Purchase Orders')

@push('styles')
<style>
/* Hide scrollbar for the table */
.overflow-x-auto::-webkit-scrollbar {
    display: none;
}

.overflow-x-auto {
    -ms-overflow-style: none;  /* IE and Edge */
    scrollbar-width: none;  /* Firefox */
}

/* Ensure table fits without horizontal scroll */
.overflow-x-auto {
    overflow-x: hidden;
}
</style>
@endpush

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
            
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200" style="min-width: 1000px;">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">PO Number</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Source</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Supplier</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Amount</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">Status</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">Payment</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Attachments</th>
                            <th scope="col" class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($purchaseOrders as $po)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-3 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="text-xs font-medium text-gray-900">{{ $po->po_number }}</div>
                                            <div class="text-xs text-gray-500">{{ formatDubaiDate($po->po_date, 'M d, Y') }}</div>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-3 py-3 whitespace-nowrap">
                                    @if($po->hasCustomerOrder())
                                        <div class="text-xs font-medium text-gray-900">
                                            <a href="{{ route('admin.orders.show', $po->order) }}" class="text-indigo-600 hover:text-indigo-700">
                                                {{ $po->order->order_number }}
                                            </a>
                                        </div>
                                        <div class="text-xs text-gray-500">{{ $po->order->getCustomerName() }}</div>
                                        <div class="text-xs text-gray-400">Customer Order</div>
                                    @elseif($po->isFromSupplierInquiry())
                                        <div class="text-xs font-medium text-gray-900">Inquiry #{{ $po->supplier_quotation_id }}</div>
                                        @if($po->supplierQuotation && $po->supplierQuotation->product)
                                            <div class="text-xs text-gray-500">{{ $po->supplierQuotation->product->name }}</div>
                                        @endif
                                        <div class="text-xs text-gray-400">Supplier Inquiry</div>
                                    @else
                                        <div class="text-xs font-medium text-gray-900">Direct Purchase</div>
                                        <div class="text-xs text-gray-400">Internal Purchase</div>
                                    @endif
                                </td>
                                
                                <td class="px-3 py-3 whitespace-nowrap">
                                    <div class="text-xs font-medium text-gray-900">{{ $po->supplier_name }}</div>
                                    @if($po->supplier_email)
                                        <div class="text-xs text-gray-500">{{ $po->supplier_email }}</div>
                                    @endif
                                </td>
                                
                                <td class="px-3 py-3 whitespace-nowrap">
                                    <div class="text-xs font-medium text-gray-900">{{ $po->currency }} {{ $po->formatted_total }}</div>
                                    @if($po->paid_amount > 0)
                                        <div class="text-xs text-gray-500">Paid: {{ $po->currency }} {{ $po->formatted_paid_amount }}</div>
                                    @endif
                                </td>
                                
                                <td class="px-3 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $po->status_badge_class }}">
                                        {{ \App\Models\PurchaseOrder::$statuses[$po->status] ?? ucfirst($po->status) }}
                                    </span>
                                </td>
                                
                                <td class="px-3 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $po->payment_status_badge_class }}">
                                        {{ \App\Models\PurchaseOrder::$paymentStatuses[$po->payment_status] ?? ucfirst($po->payment_status) }}
                                    </span>
                                </td>
                                
                                <td class="px-3 py-3 whitespace-nowrap">
                                    @php
                                        $attachments = is_array($po->attachments) ? $po->attachments : [];
                                        $attachmentCount = count($attachments);
                                    @endphp
                                    @if($attachmentCount > 0)
                                        <button onclick="openAttachmentModal('{{ $po->po_number }}', {{ json_encode($attachments) }})"
                                                class="inline-flex items-center px-3 py-1 rounded-md text-xs font-medium bg-blue-100 text-blue-800 hover:bg-blue-200 transition-colors">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                            </svg>
                                            {{ $attachmentCount }} file{{ $attachmentCount > 1 ? 's' : '' }}
                                        </button>
                                    @else
                                        <span class="text-sm text-gray-400">No attachments</span>
                                    @endif
                                </td>
                                
                                <td class="px-3 py-3 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-1">
                                        <a href="{{ route('admin.purchase-orders.show', $po) }}" class="text-indigo-600 hover:text-indigo-900" title="View Purchase Order">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                        
                                        @if($po->canBeEdited())
                                            <a href="{{ route('admin.purchase-orders.edit', $po) }}" class="text-green-600 hover:text-green-900" title="Edit Purchase Order{{ $po->status !== 'draft' ? ' (Sent)' : '' }}">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                @if($po->status !== 'draft')
                                                    <span class="sr-only">(Sent)</span>
                                                @endif
                                            </a>
                                        @endif
                                        
                                        <button class="text-blue-600 hover:text-blue-900 send-email-btn" 
                                                data-po-id="{{ $po->id }}"
                                                data-supplier-name="{{ $po->supplier_name }}"
                                                data-po-number="{{ $po->po_number }}"
                                                data-supplier-email="{{ $po->supplier_email ?? '' }}"
                                                data-attachments="{{ is_array($po->attachments) ? json_encode($po->attachments) : '[]' }}"
                                                title="Send Email">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                            </svg>
                                        </button>
                                        
                                        <a href="{{ route('admin.purchase-orders.pdf', $po) }}" class="text-red-600 hover:text-red-900" title="Download PDF">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </a>
                                        
                                        @if($po->payment_status === 'pending' || $po->payment_status === 'partial')
                                            <a href="{{ route('admin.purchase-orders.show', $po) }}#payment" class="text-green-600 hover:text-green-900" title="Record Payment">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                </svg>
                                            </a>
                                        @endif
                                        
                                        @if($po->canBeEdited())
                                            <form action="{{ route('admin.purchase-orders.destroy', $po) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this purchase order?{{ $po->status !== 'draft' ? ' Note: This purchase order has already been sent to the supplier.' : '' }} This action cannot be undone.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" title="Delete Purchase Order{{ $po->status !== 'draft' ? ' (Sent)' : '' }}">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
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

<!-- Send Email Modal -->
<div x-data="{ show: false, isLoading: false }" 
     x-on:open-modal.window="console.log('Modal event received:', $event.detail); $event.detail == 'send-po-email' ? show = true : null" 
     x-on:close-modal.window="$event.detail == 'send-po-email' ? show = false : null" 
     x-show="show" 
     class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50" 
     style="display: none;">
    <div x-show="show" class="fixed inset-0 transform transition-all" x-on:click="show = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="absolute inset-0 bg-gray-900 bg-opacity-75 backdrop-blur-sm"></div>
    </div>

    <div x-show="show" class="mb-6 bg-white rounded-2xl overflow-hidden shadow-2xl transform transition-all sm:w-full sm:max-w-2xl sm:mx-auto border border-gray-200" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
        <form id="sendEmailForm" method="POST" action="" x-data="{ submitting: false }" @submit.prevent="submitEmailForm($event)">
            @csrf
            
            <!-- Header with gradient background -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white bg-opacity-20 backdrop-blur-sm">
                        <svg class="h-7 w-7 text-emerald-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1 text-white">
                        <h3 class="text-xl font-semibold">Send Purchase Order Email</h3>
                        <p class="text-sm mt-1">Send the purchase order directly to your supplier's email</p>
                    </div>
                    <button type="button" x-on:click="show = false" class="rounded-full p-2 hover:bg-white hover:bg-opacity-20 transition-colors duration-200 text-white">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Content -->
            <div class="px-6 py-6">
                <div class="space-y-6">
                    <!-- Purchase Order Information Card -->
                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-gray-900" id="emailModalPoNumber">Purchase Order #</h4>
                                <p class="text-sm text-gray-600" id="emailModalSupplierName">Supplier Name</p>
                            </div>
                        </div>
                    </div>

                    <!-- Email Fields -->
                    <div class="space-y-5">
                        <div>
                            <label for="supplier_email" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                </svg>
                                Supplier Email Address
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative">
                                <input type="email" 
                                       id="supplier_email" 
                                       name="supplier_email" 
                                       required
                                       class="block w-full px-4 py-3 pr-10 text-gray-900 placeholder-gray-500 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200" 
                                       placeholder="Enter supplier email address">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <div id="emailLoadingSpinner" class="hidden">
                                        <svg class="animate-spin h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                    <svg id="emailFoundIcon" class="hidden h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            </div>
                            <p id="emailStatus" class="mt-2 text-sm text-gray-600 hidden"></p>
                        </div>
                        
                        <div>
                            <label for="cc_emails" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                CC Email Addresses
                            </label>
                            <input type="text" 
                                   id="cc_emails" 
                                   name="cc_emails" 
                                   value="sales@maxmedme.com" 
                                   class="block w-full px-4 py-3 text-gray-900 placeholder-gray-500 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200"
                                   placeholder="Additional email addresses (comma separated)">
                            <p class="mt-2 text-xs text-gray-500 flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                                Separate multiple email addresses with commas
                            </p>
                        </div>
                        
                        <!-- Enhanced Info Box -->
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3 flex-1">
                                    <h4 class="text-sm font-medium text-blue-800">Email will include:</h4>
                                    <ul class="mt-2 text-sm text-blue-700 space-y-1">
                                        <li class="flex items-center">
                                            <svg class="w-3 h-3 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            Professional purchase order PDF attachment
                                        </li>
                                        <li class="flex items-center">
                                            <svg class="w-3 h-3 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            All uploaded attachments (proforma invoices, quotes, etc.)
                                        </li>
                                        <li class="flex items-center">
                                            <svg class="w-3 h-3 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            Personalized email message
                                        </li>
                                        <li class="flex items-center">
                                            <svg class="w-3 h-3 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            Company branding and contact information
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Attachments Preview -->
                        <div id="attachmentsPreview" class="hidden">
                            <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                                <div class="flex items-center space-x-3 mb-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-sm font-semibold text-gray-900">Attachments to be sent:</h4>
                                        <p class="text-xs text-gray-600">Files uploaded with this purchase order</p>
                                    </div>
                                </div>
                                <div id="attachmentsList" class="space-y-2">
                                    <!-- Attachments will be populated here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Footer -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 rounded-b-2xl">
                <div class="flex items-center justify-between">
                    <button type="button" 
                            x-on:click="show = false" 
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Cancel
                    </button>
                    <button type="submit" 
                            x-bind:disabled="submitting"
                            class="inline-flex items-center px-6 py-2 text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-blue-600 border border-transparent rounded-lg shadow-lg hover:from-indigo-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 transform hover:scale-105">
                        <span x-show="!submitting" class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            Send Email
                        </span>
                        <span x-show="submitting" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Sending...
                        </span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let currentPoId = null; // Store current PO ID for status updates

document.addEventListener('DOMContentLoaded', function() {
    // Enhanced send email functionality
    document.querySelectorAll('.send-email-btn').forEach(button => {
        button.addEventListener('click', function() {
            console.log('Send email button clicked for purchase orders');
            const poId = this.getAttribute('data-po-id');
            const supplierName = this.getAttribute('data-supplier-name');
            const poNumber = this.getAttribute('data-po-number');
            const supplierEmail = this.getAttribute('data-supplier-email');
            const attachmentsData = this.getAttribute('data-attachments');
            
            // Store PO ID globally for status updates
            currentPoId = poId;
            
            console.log('PO data:', { poId, supplierName, poNumber, supplierEmail, attachmentsData });
            
            const sendEmailForm = document.getElementById('sendEmailForm');
            if (!sendEmailForm) {
                console.error('Send email form not found');
                return;
            }
            
            sendEmailForm.action = `/admin/purchase-orders/${poId}/send-email`;
            console.log('Form action set to:', sendEmailForm.action);
            
            // Update modal content
            const emailModalPoNumber = document.getElementById('emailModalPoNumber');
            const emailModalSupplierName = document.getElementById('emailModalSupplierName');
            
            if (emailModalPoNumber) emailModalPoNumber.textContent = `Purchase Order ${poNumber}`;
            if (emailModalSupplierName) emailModalSupplierName.textContent = supplierName;
            
            // Handle attachments preview
            populateAttachmentsPreview(attachmentsData);
            
            // Use existing email or populate field
            if (supplierEmail && supplierEmail.trim() !== '') {
                populateEmailField(supplierEmail, 'Supplier email loaded from purchase order');
            } else {
                // Clear email field if no email found
                const emailInput = document.getElementById('supplier_email');
                if (emailInput) {
                    emailInput.value = '';
                }
            }
            
            console.log('Dispatching open-modal event for send-po-email');
            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'send-po-email' }));
        });
    });

    // Helper function to populate email field
    function populateEmailField(email, message) {
        const emailInput = document.getElementById('supplier_email');
        const loadingSpinner = document.getElementById('emailLoadingSpinner');
        const emailFoundIcon = document.getElementById('emailFoundIcon');
        const emailStatus = document.getElementById('emailStatus');
        
        if (loadingSpinner) loadingSpinner.classList.add('hidden');
        if (emailInput) {
            emailInput.disabled = false;
            emailInput.value = email;
        }
        if (emailFoundIcon) emailFoundIcon.classList.remove('hidden');
        if (emailStatus) {
            emailStatus.className = 'mt-2 text-sm text-green-600 flex items-center';
            emailStatus.innerHTML = `
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                ${message}
            `;
            emailStatus.classList.remove('hidden');
        }
    }
});

// Function to populate attachments preview
function populateAttachmentsPreview(attachmentsData) {
    const attachmentsPreview = document.getElementById('attachmentsPreview');
    const attachmentsList = document.getElementById('attachmentsList');
    
    if (!attachmentsPreview || !attachmentsList) {
        console.error('Attachments preview elements not found');
        return;
    }
    
    try {
        const attachments = JSON.parse(attachmentsData || '[]');
        
        if (attachments && attachments.length > 0) {
            // Show the attachments preview
            attachmentsPreview.classList.remove('hidden');
            
            // Clear existing content
            attachmentsList.innerHTML = '';
            
            // Add each attachment
            attachments.forEach(attachment => {
                const attachmentItem = document.createElement('div');
                attachmentItem.className = 'flex items-center space-x-3 p-2 bg-white rounded-lg border border-gray-200';
                
                // Get file icon based on type
                const fileIcon = getFileIcon(attachment.filename || attachment.path);
                
                attachmentItem.innerHTML = `
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                            ${fileIcon}
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">${attachment.filename || 'Attachment'}</p>
                        <p class="text-xs text-gray-500">${formatFileSize(attachment.size)} • ${attachment.type || 'File'}</p>
                    </div>
                    <div class="flex-shrink-0">
                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                `;
                
                attachmentsList.appendChild(attachmentItem);
            });
        } else {
            // Hide the attachments preview if no attachments
            attachmentsPreview.classList.add('hidden');
        }
    } catch (error) {
        console.error('Error parsing attachments data:', error);
        attachmentsPreview.classList.add('hidden');
    }
}

// Function to get file icon based on filename
function getFileIcon(filename) {
    if (!filename) return '<svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path></svg>';
    
    const extension = filename.split('.').pop().toLowerCase();
    
    switch (extension) {
        case 'pdf':
            return '<svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path></svg>';
        case 'doc':
        case 'docx':
            return '<svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path></svg>';
        case 'xls':
        case 'xlsx':
            return '<svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path></svg>';
        case 'jpg':
        case 'jpeg':
        case 'png':
            return '<svg class="w-4 h-4 text-purple-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path></svg>';
        default:
            return '<svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path></svg>';
    }
}

// Function to format file size
function formatFileSize(bytes) {
    if (!bytes) return 'Unknown size';
    
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    if (bytes === 0) return '0 Bytes';
    
    const i = Math.floor(Math.log(bytes) / Math.log(1024));
    return Math.round(bytes / Math.pow(1024, i) * 100) / 100 + ' ' + sizes[i];
}

// Form submission handler
function submitEmailForm(event) {
    const form = event.target;
    const formData = new FormData(form);
    
    // Set submitting state
    const Alpine = window.Alpine;
    if (Alpine) {
        Alpine.store('emailModal', { submitting: true });
    }
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Show success message
            alert(data.message || 'Purchase Order email sent successfully!');
            
            // Close modal
            window.dispatchEvent(new CustomEvent('close-modal', { detail: 'send-po-email' }));
            
            // Optionally reload page to update status
            if (data.reload) {
                location.reload();
            }
        } else {
            throw new Error(data.message || 'Failed to send email');
        }
    })
    .catch(error => {
        console.error('Error sending email:', error);
        alert('Failed to send email: ' + error.message);
    })
    .finally(() => {
        // Reset submitting state
        const Alpine = window.Alpine;
        if (Alpine) {
            Alpine.store('emailModal', { submitting: false });
        }
    });
}

// Attachment modal functionality (same as in show page)
function openAttachmentModal(poNumber, attachments) {
    console.log('Opening attachment modal for PO:', poNumber, attachments);
    
    // Create modal HTML
    const modalHTML = `
        <div id="attachmentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-10 mx-auto p-6 border w-full max-w-4xl shadow-lg rounded-lg bg-white">
                <div class="mt-3">
                    <!-- Header -->
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900">Purchase Order Attachments</h3>
                            <p class="text-sm text-gray-600 mt-1">PO #${poNumber} - ${attachments.length} file${attachments.length > 1 ? 's' : ''}</p>
                        </div>
                        <button onclick="closeAttachmentModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Attachments Grid -->
                    <div class="max-h-96 overflow-y-auto">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            ${attachments.map(attachment => {
                                const fileName = attachment.filename || attachment.original_name || 'Unknown file';
                                const filePath = attachment.path || attachment.file_path;
                                const extension = fileName.split('.').pop().toLowerCase();
                                const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(extension);
                                const isPdf = extension === 'pdf';
                                const fileUrl = '/storage/' + filePath;
                                
                                let iconColor = 'text-gray-600';
                                let bgColor = 'bg-gray-100';
                                let icon = 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z';
                                
                                if (isPdf) {
                                    iconColor = 'text-red-600';
                                    bgColor = 'bg-red-100';
                                    icon = 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z';
                                } else if (isImage) {
                                    iconColor = 'text-blue-600';
                                    bgColor = 'bg-blue-100';
                                    icon = 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z';
                                } else if (['doc', 'docx'].includes(extension)) {
                                    iconColor = 'text-blue-800';
                                    bgColor = 'bg-blue-100';
                                } else if (['xls', 'xlsx'].includes(extension)) {
                                    iconColor = 'text-green-600';
                                    bgColor = 'bg-green-100';
                                }
                                
                                return `
                                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                        <div class="flex flex-col items-center">
                                            ${isImage ? 
                                                `<div class="w-full h-32 mb-3 rounded-lg overflow-hidden bg-gray-100">
                                                    <img src="${fileUrl}" alt="${fileName}" 
                                                         class="w-full h-full object-cover cursor-pointer hover:scale-105 transition-transform"
                                                         onclick="openImagePreview('${fileUrl}', '${fileName}')">
                                                </div>` :
                                                `<div class="w-16 h-16 ${bgColor} rounded-lg flex items-center justify-center mb-3">
                                                    <svg class="w-8 h-8 ${iconColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${icon}"></path>
                                                    </svg>
                                                </div>`
                                            }
                                            <div class="text-center">
                                                <p class="text-sm font-medium text-gray-900 mb-2 truncate w-full" title="${fileName}">
                                                    ${fileName.length > 20 ? fileName.substring(0, 20) + '...' : fileName}
                                                </p>
                                                <div class="flex space-x-2">
                                                    ${isPdf ? 
                                                        `<button onclick="openPdfViewer('${fileUrl}', '${fileName}')" 
                                                                class="px-3 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700 transition-colors">
                                                            View PDF
                                                        </button>` :
                                                        `<a href="${fileUrl}" target="_blank" 
                                                           class="px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 transition-colors">
                                                            Open
                                                        </a>`
                                                    }
                                                    <a href="${fileUrl}" download="${fileName}"
                                                       class="px-3 py-1 bg-gray-600 text-white text-xs rounded hover:bg-gray-700 transition-colors">
                                                        Download
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            }).join('')}
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="flex justify-end mt-6 pt-4 border-t border-gray-200">
                        <button onclick="closeAttachmentModal()" 
                                class="px-6 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Add modal to page
    document.body.insertAdjacentHTML('beforeend', modalHTML);
}

function closeAttachmentModal() {
    const modal = document.getElementById('attachmentModal');
    if (modal) {
        modal.remove();
    }
}

function openImagePreview(imageUrl, imageName) {
    const previewHTML = `
        <div id="imagePreview" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-60" onclick="closeImagePreview()">
            <div class="relative max-w-4xl max-h-full p-4" onclick="event.stopPropagation()">
                <button onclick="closeImagePreview()" class="absolute top-2 right-2 text-white hover:text-gray-300 z-10">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                <img src="${imageUrl}" alt="${imageName}" class="max-w-full max-h-full object-contain rounded-lg">
                <div class="text-center mt-2">
                    <p class="text-white text-sm">${imageName}</p>
                </div>
            </div>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', previewHTML);
}

function closeImagePreview() {
    const preview = document.getElementById('imagePreview');
    if (preview) {
        preview.remove();
    }
}

function openPdfViewer(pdfUrl, pdfName) {
    const pdfViewerHTML = `
        <div id="pdfViewer" class="fixed inset-0 bg-white z-60">
            <div class="flex flex-col h-full">
                <div class="bg-gray-800 text-white p-4 flex items-center justify-between">
                    <h3 class="text-lg font-medium">${pdfName}</h3>
                    <div class="flex space-x-2">
                        <a href="${pdfUrl}" target="_blank" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">
                            Open in New Tab
                        </a>
                        <button onclick="closePdfViewer()" class="px-3 py-1 bg-gray-600 text-white rounded hover:bg-gray-700">
                            Close
                        </button>
                    </div>
                </div>
                <iframe src="${pdfUrl}" class="flex-1 w-full border-none" title="${pdfName}"></iframe>
            </div>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', pdfViewerHTML);
}

function closePdfViewer() {
    const viewer = document.getElementById('pdfViewer');
    if (viewer) {
        viewer.remove();
    }
}

// Close modals when clicking outside
document.addEventListener('click', function(e) {
    if (e.target.id === 'attachmentModal') {
        closeAttachmentModal();
    }
});
</script>
@endpush

@endsection 