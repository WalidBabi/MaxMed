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
                    <form action="{{ route('admin.purchase-orders.send-to-supplier', $purchaseOrder) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            Send to Supplier
                        </button>
                    </form>
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
            @if($purchaseOrder->supplierPayments->count() > 0)
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
                                @foreach($purchaseOrder->supplierPayments as $payment)
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

<script>
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