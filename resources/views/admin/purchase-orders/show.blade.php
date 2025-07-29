@extends('admin.layouts.app')

@section('title', 'Purchase Order #' . $purchaseOrder->po_number)

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Purchase Order #{{ $purchaseOrder->po_number }}</h1>
                <p class="text-gray-600 mt-2">Created {{ formatDubaiDate($purchaseOrder->created_at, 'M d, Y \a\t H:i') }}</p>
            </div>
            <div class="flex items-center space-x-3">
                @if($purchaseOrder->status === 'draft')
                    <a href="{{ route('admin.purchase-orders.edit', $purchaseOrder) }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </a>
                @endif
                
                <a href="{{ route('admin.purchase-orders.pdf', $purchaseOrder) }}" 
                   class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Download PDF
                </a>
                
                <a href="{{ route('admin.purchase-orders.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Purchase Orders
                </a>
            </div>
        </div>
    </div>

    <!-- Status & Actions Bar -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6 p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div>
                    <span class="text-sm font-medium text-gray-500">Status:</span>
                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $purchaseOrder->status_badge_class }}">
                        {{ \App\Models\PurchaseOrder::$statuses[$purchaseOrder->status] ?? ucfirst($purchaseOrder->status) }}
                    </span>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Payment:</span>
                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $purchaseOrder->payment_status_badge_class }}">
                        {{ \App\Models\PurchaseOrder::$paymentStatuses[$purchaseOrder->payment_status] ?? ucfirst($purchaseOrder->payment_status) }}
                    </span>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Total:</span>
                    <span class="ml-2 text-lg font-bold text-gray-900">{{ $purchaseOrder->currency }} {{ $purchaseOrder->formatted_total }}</span>
                </div>
            </div>
            
            <div class="flex items-center space-x-2">
                @if($purchaseOrder->status === 'draft')
                    <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 send-email-btn"
                            data-po-id="{{ $purchaseOrder->id }}"
                            data-supplier-name="{{ $purchaseOrder->supplier_name }}"
                            data-po-number="{{ $purchaseOrder->po_number }}"
                            data-supplier-email="{{ $purchaseOrder->supplier_email ?? '' }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Send Email to Supplier
                    </button>
                @endif
                
                @if($purchaseOrder->payment_status === 'unpaid')
                    <button onclick="openPaymentModal()" class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        Record Payment
                    </button>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- PO Details -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Purchase Order Details</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">PO Number</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $purchaseOrder->po_number }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">PO Date</label>
                            <p class="text-lg font-semibold text-gray-900">{{ formatDubaiDate($purchaseOrder->po_date, 'M d, Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Source</label>
                            @if($purchaseOrder->hasCustomerOrder())
                                <div>
                                    <span class="text-sm text-gray-500">Customer Order</span>
                                    <a href="{{ route('admin.orders.show', $purchaseOrder->order) }}" class="block text-lg font-semibold text-indigo-600 hover:text-indigo-700">
                                        {{ $purchaseOrder->order->order_number }}
                                    </a>
                                    <p class="text-sm text-gray-500">{{ $purchaseOrder->order->getCustomerName() }}</p>
                                </div>
                            @elseif($purchaseOrder->isFromSupplierInquiry())
                                <div>
                                    <span class="text-sm text-gray-500">Supplier Inquiry</span>
                                    <p class="text-lg font-semibold text-gray-900">Inquiry #{{ $purchaseOrder->supplier_quotation_id }}</p>
                                    @if($purchaseOrder->supplierQuotation)
                                        <p class="text-sm text-gray-500">{{ $purchaseOrder->supplierQuotation->product->name }}</p>
                                    @endif
                                </div>
                            @else
                                <div>
                                    <span class="text-sm text-gray-500">Internal Purchase</span>
                                    <p class="text-lg font-semibold text-gray-900">Direct Purchase</p>
                                </div>
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Requested Delivery</label>
                            <p class="text-lg font-semibold text-gray-900">
                                {{ $purchaseOrder->delivery_date_requested ? $purchaseOrder->delivery_date_requested->format('M d, Y') : 'Not specified' }}
                            </p>
                        </div>
                    </div>

                    @if($purchaseOrder->description)
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-500">Description</label>
                            <p class="text-gray-900">{{ $purchaseOrder->description }}</p>
                        </div>
                    @endif

                    @if($purchaseOrder->terms_conditions)
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-500">Terms & Conditions</label>
                            <p class="text-gray-900">{{ $purchaseOrder->terms_conditions }}</p>
                        </div>
                    @endif

                    @if($purchaseOrder->notes)
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-500">Internal Notes</label>
                            <p class="text-gray-900">{{ $purchaseOrder->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Financial Summary -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Financial Summary</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="text-gray-900">{{ $purchaseOrder->currency }} {{ $purchaseOrder->formatted_sub_total }}</span>
                        </div>
                        @if($purchaseOrder->tax_amount > 0)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tax:</span>
                                <span class="text-gray-900">{{ $purchaseOrder->currency }} {{ $purchaseOrder->formatted_tax_amount }}</span>
                            </div>
                        @endif
                        @if($purchaseOrder->shipping_cost > 0)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Shipping:</span>
                                <span class="text-gray-900">{{ $purchaseOrder->currency }} {{ $purchaseOrder->formatted_shipping_cost }}</span>
                            </div>
                        @endif
                        <div class="border-t border-gray-200 pt-3">
                            <div class="flex justify-between text-lg font-semibold">
                                <span class="text-gray-900">Total:</span>
                                <span class="text-gray-900">{{ $purchaseOrder->currency }} {{ $purchaseOrder->formatted_total }}</span>
                            </div>
                        </div>
                        @if($purchaseOrder->paid_amount > 0)
                            <div class="flex justify-between text-green-600">
                                <span>Paid:</span>
                                <span>{{ $purchaseOrder->currency }} {{ $purchaseOrder->formatted_paid_amount }}</span>
                            </div>
                            <div class="flex justify-between text-red-600">
                                <span>Outstanding:</span>
                                <span>{{ $purchaseOrder->currency }} {{ number_format($purchaseOrder->total_amount - $purchaseOrder->paid_amount, 2) }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Payments History -->
            @if($purchaseOrder->payments->count() > 0)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Payment History</h3>
                    </div>
                    <div class="overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment #</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($purchaseOrder->payments as $payment)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('admin.supplier-payments.show', $payment) }}" class="text-indigo-600 hover:text-indigo-700">
                                                {{ $payment->payment_number }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-900">
                                            {{ $payment->currency }} {{ $payment->formatted_amount }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-900">
                                            {{ ucfirst($payment->payment_method) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $payment->status_badge_class }}">
                                                {{ \App\Models\SupplierPayment::$statuses[$payment->status] ?? ucfirst($payment->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                            {{ formatDubaiDate($payment->payment_date, 'M d, Y') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Supplier Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Supplier Information</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Name</label>
                        <p class="text-gray-900">{{ $purchaseOrder->supplier_name }}</p>
                    </div>
                    @if($purchaseOrder->supplier_email)
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Email</label>
                            <a href="mailto:{{ $purchaseOrder->supplier_email }}" class="text-indigo-600 hover:text-indigo-700">
                                {{ $purchaseOrder->supplier_email }}
                            </a>
                        </div>
                    @endif
                    @if($purchaseOrder->supplier_phone)
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Phone</label>
                            <a href="tel:{{ $purchaseOrder->supplier_phone }}" class="text-indigo-600 hover:text-indigo-700">
                                {{ $purchaseOrder->supplier_phone }}
                            </a>
                        </div>
                    @endif
                    @if($purchaseOrder->supplier_address)
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Address</label>
                            <p class="text-gray-900">{{ $purchaseOrder->supplier_address }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Timeline -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Timeline</h3>
                </div>
                <div class="p-6">
                    <div class="flow-root">
                        <ul class="-mb-8">
                            <li>
                                <div class="relative pb-8">
                                    <div class="relative flex space-x-3">
                                        <div class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                            <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm text-gray-500">Created</p>
                                            <p class="text-xs text-gray-400">{{ formatDubaiDate($purchaseOrder->created_at, 'M d, Y H:i') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </li>

                            @if($purchaseOrder->sent_to_supplier_at)
                                <li>
                                    <div class="relative pb-8">
                                        <div class="relative flex space-x-3">
                                            <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                                </svg>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-sm text-gray-500">Sent to Supplier</p>
                                                <p class="text-xs text-gray-400">{{ formatDubaiDate($purchaseOrder->sent_to_supplier_at, 'M d, Y H:i') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endif

                            @if($purchaseOrder->acknowledged_at)
                                <li>
                                    <div class="relative">
                                        <div class="relative flex space-x-3">
                                            <div class="h-8 w-8 rounded-full bg-yellow-500 flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-sm text-gray-500">Acknowledged by Supplier</p>
                                                <p class="text-xs text-gray-400">{{ formatDubaiDate($purchaseOrder->acknowledged_at, 'M d, Y H:i') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div id="paymentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Record Payment to Supplier</h3>
            <form action="{{ route('admin.purchase-orders.create-payment', $purchaseOrder) }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="payment_amount" class="block text-sm font-medium text-gray-700">Amount *</label>
                        <input type="number" id="payment_amount" name="amount" step="0.01" min="0" 
                               max="{{ $purchaseOrder->total_amount - $purchaseOrder->paid_amount }}" 
                               value="{{ $purchaseOrder->total_amount - $purchaseOrder->paid_amount }}" required
                               class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment Method *</label>
                        <select id="payment_method" name="payment_method" required
                                class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="wire_transfer">Wire Transfer</option>
                            <option value="check">Check</option>
                            <option value="credit_card">Credit Card</option>
                            <option value="cash">Cash</option>
                        </select>
                    </div>
                    <div>
                        <label for="payment_date" class="block text-sm font-medium text-gray-700">Payment Date *</label>
                        <input type="date" id="payment_date" name="payment_date" value="{{ date('Y-m-d') }}" required
                               class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="reference_number" class="block text-sm font-medium text-gray-700">Reference Number</label>
                        <input type="text" id="reference_number" name="reference_number"
                               class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
                <div class="flex items-center justify-end space-x-3 mt-6">
                    <button type="button" onclick="closePaymentModal()" 
                            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                        Record Payment
                    </button>
                </div>
            </form>
        </div>
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

<script>
// Email functionality
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced send email functionality
    document.querySelectorAll('.send-email-btn').forEach(button => {
        button.addEventListener('click', function() {
            console.log('Send email button clicked for purchase orders');
            const poId = this.getAttribute('data-po-id');
            const supplierName = this.getAttribute('data-supplier-name');
            const poNumber = this.getAttribute('data-po-number');
            const supplierEmail = this.getAttribute('data-supplier-email');
            
            console.log('PO data:', { poId, supplierName, poNumber, supplierEmail });
            
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

// Form submission handler
function submitEmailForm(event) {
    const form = event.target;
    const formData = new FormData(form);
    
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
    });
}

// Payment modal functionality
function openPaymentModal() {
    document.getElementById('paymentModal').classList.remove('hidden');
}

function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('paymentModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePaymentModal();
    }
});
</script>
@endsection 