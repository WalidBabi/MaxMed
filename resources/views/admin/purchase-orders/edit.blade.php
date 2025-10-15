@extends('admin.layouts.app')

@section('title', 'Edit Purchase Order')

@push('styles')
<style>
    /* Table Layout Styles */
    .items-table {
        table-layout: fixed;
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .items-table th,
    .items-table td {
        padding: 12px 8px;
        vertical-align: top;
        border-bottom: 1px solid #e5e7eb;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }
    
    .items-table th {
        background-color: #f9fafb;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        color: #6b7280;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    
    .items-table td {
        background-color: #ffffff;
    }
    
    .items-table tr:hover td {
        background-color: #f9fafb;
    }
    
    /* Column Widths */
    .items-table th:nth-child(1),
    .items-table td:nth-child(1) { width: 60px; } /* Drag */
    
    .items-table th:nth-child(2),
    .items-table td:nth-child(2) { width: 300px; } /* Item Details */
    
    .items-table th:nth-child(3),
    .items-table td:nth-child(3) { width: 200px; } /* Specifications */
    
    .items-table th:nth-child(4),
    .items-table td:nth-child(4) { width: 120px; } /* Quantity */
    
    .items-table th:nth-child(5),
    .items-table td:nth-child(5) { width: 150px; } /* Price Type & Rate */
    
    .items-table th:nth-child(6),
    .items-table td:nth-child(6) { width: 120px; } /* Discount */
    
    .items-table th:nth-child(7),
    .items-table td:nth-child(7) { width: 120px; } /* Amount */
    
    .items-table th:nth-child(8),
    .items-table td:nth-child(8) { width: 80px; } /* Action */
    
    /* Custom Dropdown Styles */
    .product-dropdown-container {
        position: relative;
        width: 100%;
    }
    
    .product-dropdown-list {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        max-height: 300px;
        overflow-y: auto;
        background: white;
        z-index: 999999;
        margin-top: 4px;
        min-width: 280px;
    }

    /* Specifications Dropdown Styles */
    .specifications-dropdown-container {
        position: relative;
        width: 100%;
    }
    
    .specifications-dropdown-list {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        max-height: 200px;
        overflow-y: auto;
        background: white;
        z-index: 999999;
        margin-top: 4px;
        min-width: 180px;
    }
    
    /* Specifications Checkbox Styles */
    .specifications-dropdown-list .spec-checkbox,
    .specifications-dropdown-list .select-all-checkbox {
        accent-color: #6366f1;
    }
    
    .specifications-dropdown-list .spec-checkbox:checked,
    .specifications-dropdown-list .select-all-checkbox:checked {
        background-color: #6366f1;
        border-color: #6366f1;
    }
    
    .specifications-dropdown-list .spec-checkbox:focus,
    .specifications-dropdown-list .select-all-checkbox:focus {
        ring: 2px;
        ring-color: #6366f1;
        ring-offset: 2px;
    }
    
    /* Select All section styling */
    .specifications-dropdown-list .bg-indigo-50 {
        background-color: #eef2ff;
    }
    
    .specifications-dropdown-list .bg-indigo-50:hover {
        background-color: #e0e7ff;
    }

    .dropdown-item {
        transition: background-color 0.15s ease-in-out;
        cursor: pointer;
        padding: 8px 12px;
        border-bottom: 1px solid #f3f4f6;
    }
    
    .dropdown-item:last-child {
        border-bottom: none;
    }
    
    /* Ensure table cells don't interfere with dropdown positioning */
    .items-table td {
        position: relative;
        z-index: 1;
    }
    
    .items-table .product-dropdown-container {
        z-index: 10;
    }
    
    .items-table .product-dropdown-list {
        z-index: 999999;
    }
    
    .items-table .specifications-dropdown-container {
        z-index: 10;
    }
    
    .items-table .specifications-dropdown-list {
        z-index: 999999;
    }
</style>
@endpush

@section('content')
<div class="-mx-4 sm:-mx-6 lg:-mx-8">
    <div class="mb-8 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit Purchase Order</h1>
                <p class="text-gray-600 mt-2">Update purchase order {{ $purchaseOrder->po_number }}</p>
                
                <!-- Date Summary -->
                @if($purchaseOrder->po_date || $purchaseOrder->delivery_date_requested)
                    <div class="mt-3 flex flex-wrap gap-4 text-sm">
                        @if($purchaseOrder->po_date)
                            <div class="flex items-center text-gray-600">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span>Created: {{ \Carbon\Carbon::parse($purchaseOrder->po_date)->format('M d, Y') }}</span>
                            </div>
                        @endif
                        
                        @if($purchaseOrder->delivery_date_requested)
                            @php
                                $deliveryDate = \Carbon\Carbon::parse($purchaseOrder->delivery_date_requested);
                                $isOverdue = $deliveryDate->isPast();
                                $daysDiff = \Carbon\Carbon::now()->diffInDays($deliveryDate, false);
                            @endphp
                            <div class="flex items-center {{ $isOverdue ? 'text-red-600' : 'text-gray-600' }}">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                <span>
                                    Delivery: {{ $deliveryDate->format('M d, Y') }}
                                    @if($isOverdue)
                                        ({{ abs($daysDiff) }} days overdue)
                                    @elseif($daysDiff == 0)
                                        (Today)
                                    @elseif($daysDiff == 1)
                                        (Tomorrow)
                                    @else
                                        ({{ $daysDiff }} days remaining)
                                    @endif
                                </span>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.purchase-orders.show', $purchaseOrder) }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                    </svg>
                    Back to Purchase Order
                </a>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="px-4 sm:px-6 lg:px-8 mb-6">
            <div class="bg-red-50 border border-red-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Warning for Sent Purchase Orders -->
    @if($purchaseOrder->status !== 'draft')
        <div class="px-4 sm:px-6 lg:px-8 mb-6">
            <div class="bg-amber-50 border border-amber-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-amber-800">Editing Sent Purchase Order</h3>
                        <div class="mt-2 text-sm text-amber-700">
                            <p>⚠️ <strong>Warning:</strong> This purchase order has already been sent to the supplier (Status: {{ \App\Models\PurchaseOrder::$statuses[$purchaseOrder->status] }}). Any changes you make will be logged for audit purposes and you must provide a reason for the edit.</p>
                            @if($purchaseOrder->sent_to_supplier_at)
                                <p class="mt-1">📧 Originally sent on: {{ $purchaseOrder->sent_to_supplier_at->format('M d, Y \a\t H:i') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Form -->
    <form action="{{ route('admin.purchase-orders.update', $purchaseOrder) }}" method="POST" enctype="multipart/form-data" onsubmit="return validateAndPrepareForm()">
        @csrf
        @method('PUT')
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="space-y-8">
                <!-- Basic Information -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25M8.25 9h2.25m-2.25 4.5h2.25m-2.25 4.5h2.25M10.5 21h4.5" />
                            </svg>
                            Purchase Order Information
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="order_id" class="block text-sm font-medium text-gray-700 mb-2">Customer Order (Optional)</label>
                                <select name="order_id" id="order_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">Select a customer order (optional)</option>
                                    @if(isset($availableOrders))
                                        @foreach($availableOrders as $order)
                                            <option value="{{ $order->id }}" {{ old('order_id', $purchaseOrder->order_id) == $order->id ? 'selected' : '' }}>
                                                {{ $order->order_number }} - {{ $order->getCustomerName() }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <p class="mt-1 text-sm text-gray-500">Link to an existing customer order or create a standalone purchase order</p>
                            </div>
                            <div>
                                <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
                                <select id="currency" name="currency" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="AED" {{ old('currency', $purchaseOrder->currency) == 'AED' ? 'selected' : '' }}>AED (UAE Dirham)</option>
                                    <option value="USD" {{ old('currency', $purchaseOrder->currency) == 'USD' ? 'selected' : '' }}>USD (US Dollar)</option>
                                    <option value="CNY" {{ old('currency', $purchaseOrder->currency) == 'CNY' ? 'selected' : '' }}>CNY (Chinese Yuan)</option>
                                    <option value="HKD" {{ old('currency', $purchaseOrder->currency) == 'HKD' ? 'selected' : '' }}>HKD (Hong Kong Dollar)</option>
                                </select>
                            </div>
                            <div>
                                <label for="po_number" class="block text-sm font-medium text-gray-700 mb-2">PO Number</label>
                                <input type="text" id="po_number" value="{{ $purchaseOrder->po_number }}" readonly
                                       class="block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm sm:text-sm">
                            </div>
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <input type="text" id="status" value="{{ \App\Models\PurchaseOrder::$statuses[$purchaseOrder->status] ?? ucfirst($purchaseOrder->status) }}" readonly
                                       class="block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm sm:text-sm">
                            </div>
                            <div>
                                <label for="po_date" class="block text-sm font-medium text-gray-700 mb-2">PO Date</label>
                                <input type="date" id="po_date" value="{{ $purchaseOrder->po_date ? \Carbon\Carbon::parse($purchaseOrder->po_date)->format('Y-m-d') : '' }}" readonly
                                       class="block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm sm:text-sm">
                                @if($purchaseOrder->po_date)
                                    <p class="mt-1 text-sm text-gray-500">Created on {{ \Carbon\Carbon::parse($purchaseOrder->po_date)->format('M d, Y') }}</p>
                                @endif
                            </div>
                            <div>
                                <label for="delivery_date_requested" class="block text-sm font-medium text-gray-700 mb-2">Requested Delivery Date *</label>
                                <input type="date" id="delivery_date_requested" name="delivery_date_requested" 
                                       value="{{ old('delivery_date_requested', $purchaseOrder->delivery_date_requested ? \Carbon\Carbon::parse($purchaseOrder->delivery_date_requested)->format('Y-m-d') : '') }}" required
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                @if($purchaseOrder->delivery_date_requested)
                                    @php
                                        $deliveryDate = \Carbon\Carbon::parse($purchaseOrder->delivery_date_requested);
                                        $today = \Carbon\Carbon::now();
                                        $isOverdue = $deliveryDate->isPast();
                                        $daysDiff = $today->diffInDays($deliveryDate, false);
                                    @endphp
                                    <p class="mt-1 text-sm {{ $isOverdue ? 'text-red-600' : 'text-gray-500' }}">
                                        Currently requested for {{ $deliveryDate->format('M d, Y') }}
                                        @if($isOverdue)
                                            ({{ abs($daysDiff) }} days overdue)
                                        @elseif($daysDiff == 0)
                                            (Today)
                                        @elseif($daysDiff == 1)
                                            (Tomorrow)
                                        @else
                                            ({{ $daysDiff }} days from now)
                                        @endif
                                    </p>
                                @endif
                            </div>
                            <div>
                                <label for="payment_terms" class="block text-sm font-medium text-gray-700 mb-2">Payment Terms</label>
                                <select name="payment_terms" id="payment_terms" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">Select payment terms</option>
                                    <option value="Net 30" {{ old('payment_terms', $purchaseOrder->payment_terms) == 'Net 30' ? 'selected' : '' }}>Net 30</option>
                                    <option value="Net 60" {{ old('payment_terms', $purchaseOrder->payment_terms) == 'Net 60' ? 'selected' : '' }}>Net 60</option>
                                    <option value="Net 90" {{ old('payment_terms', $purchaseOrder->payment_terms) == 'Net 90' ? 'selected' : '' }}>Net 90</option>
                                    <option value="Due on Receipt" {{ old('payment_terms', $purchaseOrder->payment_terms) == 'Due on Receipt' ? 'selected' : '' }}>Due on Receipt</option>
                                    <option value="Cash on Delivery" {{ old('payment_terms', $purchaseOrder->payment_terms) == 'Cash on Delivery' ? 'selected' : '' }}>Cash on Delivery</option>
                                    <option value="50% Advance, 50% on Delivery" {{ old('payment_terms', $purchaseOrder->payment_terms) == '50% Advance, 50% on Delivery' ? 'selected' : '' }}>50% Advance, 50% on Delivery</option>
                                    <option value="Advance Payment" {{ old('payment_terms', $purchaseOrder->payment_terms) == 'Advance Payment' ? 'selected' : '' }}>Advance Payment</option>
                                    <option value="Custom" {{ old('payment_terms', $purchaseOrder->payment_terms) == 'Custom' ? 'selected' : '' }}>Custom</option>
                                </select>
                            </div>
                            <div>
                                <label for="shipping_method" class="block text-sm font-medium text-gray-700 mb-2">Shipping Method</label>
                                <select name="shipping_method" id="shipping_method" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">Select shipping method</option>
                                    <option value="Standard Shipping" {{ old('shipping_method', $purchaseOrder->shipping_method) == 'Standard Shipping' ? 'selected' : '' }}>Standard Shipping</option>
                                    <option value="Express Shipping" {{ old('shipping_method', $purchaseOrder->shipping_method) == 'Express Shipping' ? 'selected' : '' }}>Express Shipping</option>
                                    <option value="Overnight Shipping" {{ old('shipping_method', $purchaseOrder->shipping_method) == 'Overnight Shipping' ? 'selected' : '' }}>Overnight Shipping</option>
                                    <option value="Local Pickup" {{ old('shipping_method', $purchaseOrder->shipping_method) == 'Local Pickup' ? 'selected' : '' }}>Local Pickup</option>
                                    <option value="Supplier Delivery" {{ old('shipping_method', $purchaseOrder->shipping_method) == 'Supplier Delivery' ? 'selected' : '' }}>Supplier Delivery</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Reason (for sent purchase orders) -->
                @if($purchaseOrder->status !== 'draft')
                <div class="bg-yellow-50 rounded-lg shadow-sm border border-yellow-200">
                    <div class="px-6 py-4 border-b border-yellow-200 bg-yellow-100">
                        <h3 class="text-lg font-semibold text-yellow-800 flex items-center">
                            <svg class="h-5 w-5 text-yellow-600 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                            Edit Reason Required
                        </h3>
                        <p class="text-sm text-yellow-700 mt-1">This purchase order has been sent to the supplier. Please provide a reason for making changes.</p>
                    </div>
                    <div class="p-6">
                        <div>
                            <label for="edit_reason" class="block text-sm font-medium text-yellow-800 mb-2">Reason for Edit *</label>
                            <textarea id="edit_reason" name="edit_reason" rows="3" required
                                      class="block w-full rounded-md border-yellow-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 sm:text-sm"
                                      placeholder="Please explain why you need to edit this sent purchase order...">{{ old('edit_reason') }}</textarea>
                            @error('edit_reason')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                @endif

                <!-- Supplier Information -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                            </svg>
                            Supplier Information
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="supplier_name" class="block text-sm font-medium text-gray-700 mb-2">Supplier Name *</label>
                                <input type="text" id="supplier_name" name="supplier_name" 
                                       value="{{ old('supplier_name', $purchaseOrder->supplier_name) }}" required
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="supplier_email" class="block text-sm font-medium text-gray-700 mb-2">Supplier Email</label>
                                <input type="email" id="supplier_email" name="supplier_email" 
                                       value="{{ old('supplier_email', $purchaseOrder->supplier_email) }}"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="supplier_phone" class="block text-sm font-medium text-gray-700 mb-2">Supplier Phone</label>
                                <input type="text" id="supplier_phone" name="supplier_phone" 
                                       value="{{ old('supplier_phone', $purchaseOrder->supplier_phone) }}"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="supplier_contact_person" class="block text-sm font-medium text-gray-700 mb-2">Contact Person</label>
                                <input type="text" id="supplier_contact_person" name="supplier_contact_person" 
                                       value="{{ old('supplier_contact_person', $purchaseOrder->supplier_contact_person) }}"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="supplier_address" class="block text-sm font-medium text-gray-700 mb-2">Supplier Address</label>
                                <textarea id="supplier_address" name="supplier_address" rows="3"
                                          class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('supplier_address', $purchaseOrder->supplier_address) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Editable Items Table -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v1a2 2 0 002 2h2m0-4v4m0-4a2 2 0 012-2h1a2 2 0 012 2v1a2 2 0 01-2 2h-1m-2-4v4"></path>
                            </svg>
                            Purchase Order Items
                        </h3>
                        <button type="button" id="addItem" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                            </svg>
                            Add Item
                        </button>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto w-full">
                            <table class="w-full divide-y divide-gray-200 items-table">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 60px;">Drag</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 300px;">Item Details</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 200px;">Specifications</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 120px;">Quantity</th>
                                                                                 <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 150px;">Price Type & Rate</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 120px;">Discount</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 120px;">Amount</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 80px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsTable" class="bg-white divide-y divide-gray-200">
                                    <!-- Items will be loaded here by JavaScript -->
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Totals Section -->
                        <div class="mt-6 flex justify-end">
                            <div class="w-80">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="flex justify-between py-2">
                                        <span class="text-sm font-medium text-gray-600">Subtotal:</span>
                                        <span class="text-sm font-bold text-gray-900"><span id="currencyDisplaySub">{{ $purchaseOrder->currency }}</span> <span id="subTotal">0.00</span></span>
                                    </div>
                                    <div class="border-t border-gray-200 pt-2 mt-2">
                                        <div class="flex justify-between">
                                            <span class="text-base font-bold text-gray-900">Total:</span>
                                            <span class="text-base font-bold text-gray-900"><span id="currencyDisplay">{{ $purchaseOrder->currency }}</span> <span id="totalAmount">0.00</span></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Hidden inputs for totals -->
                        <input type="hidden" name="sub_total" id="sub_total_hidden" value="0">
                        <input type="hidden" name="total_amount" id="total_amount_hidden" value="0">
                    </div>
                </div>

                <!-- Description and Terms -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25M8.25 9h2.25m-2.25 4.5h2.25m-2.25 4.5h2.25" />
                            </svg>
                            Description and Terms
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-6">
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                <textarea id="description" name="description" rows="3"
                                          placeholder="Enter purchase order description"
                                          class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('description', $purchaseOrder->description) }}</textarea>
                            </div>
                            <div>
                                <label for="terms_conditions" class="block text-sm font-medium text-gray-700 mb-2">Terms & Conditions</label>
                                <textarea id="terms_conditions" name="terms_conditions" rows="4"
                                          placeholder="Enter terms and conditions"
                                          class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('terms_conditions', $purchaseOrder->terms_conditions) }}</textarea>
                            </div>
                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                                <textarea id="notes" name="notes" rows="3"
                                          placeholder="Enter any additional notes"
                                          class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('notes', $purchaseOrder->notes) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attachments Section -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                            </svg>
                            Attachments
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">Upload files related to this purchase order (proforma invoices, quotes, specifications, etc.)</p>
                    </div>
                    <div class="p-6">
                        <!-- File Upload Area -->
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors duration-200" 
                             id="fileUploadArea">
                            <div class="space-y-4">
                                <div class="flex justify-center">
                                    <svg class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                </div>
                                <div>
                                    <label for="attachments" class="cursor-pointer">
                                        <span class="text-lg font-medium text-gray-900">Upload Files</span>
                                        <span class="block text-sm text-gray-500 mt-1">Click to browse or drag and drop files here</span>
                                        <span class="block text-xs text-gray-400 mt-2">PDF, DOC, DOCX, XLS, XLSX, JPG, PNG, GIF (Max 10MB each)</span>
                                    </label>
                                    <input type="file" id="attachments" name="attachments[]" multiple 
                                           accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif"
                                           class="hidden" 
                                           onchange="handleFileSelection(this)">
                                </div>
                            </div>
                        </div>

                        <!-- File Preview Area -->
                        <div id="filePreviewArea" class="mt-6 space-y-3 hidden">
                            <h4 class="text-sm font-medium text-gray-900">Selected Files:</h4>
                            <div id="fileList" class="space-y-2">
                                <!-- Files will be displayed here -->
                            </div>
                        </div>

                        <!-- Existing Attachments -->
                        @php
                            $existingAttachments = is_array($purchaseOrder->attachments) ? $purchaseOrder->attachments : [];
                        @endphp
                        @if(count($existingAttachments) > 0)
                            <div class="mt-6">
                                <h4 class="text-sm font-medium text-gray-900 mb-3">Current Attachments:</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                    @foreach($existingAttachments as $index => $attachment)
                                        <div class="border border-gray-200 rounded-lg p-3 bg-gray-50">
                                            <div class="flex items-start space-x-3">
                                                <div class="flex-shrink-0">
                                                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                                        @php
                                                            $filename = $attachment['filename'] ?? 'attachment';
                                                            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                                                        @endphp
                                                        
                                                        @switch($extension)
                                                            @case('pdf')
                                                                <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                                                </svg>
                                                                @break
                                                            @case('doc')
                                                            @case('docx')
                                                                <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                                                </svg>
                                                                @break
                                                            @case('xls')
                                                            @case('xlsx')
                                                                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                                                </svg>
                                                                @break
                                                            @case('jpg')
                                                            @case('jpeg')
                                                            @case('png')
                                                            @case('gif')
                                                                <svg class="w-4 h-4 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                                                </svg>
                                                                @break
                                                            @default
                                                                <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                                                </svg>
                                                        @endswitch
                                                    </div>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    @if(isset($attachment['path']))
                                                        <a href="{{ asset('storage/' . $attachment['path']) }}" target="_blank" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 truncate block" title="{{ $attachment['filename'] ?? 'Attachment' }}">
                                                            {{ $attachment['filename'] ?? 'Attachment' }}
                                                        </a>
                                                    @else
                                                        <h5 class="text-sm font-medium text-gray-900 truncate" title="{{ $attachment['filename'] ?? 'Attachment' }}">
                                                            {{ $attachment['filename'] ?? 'Attachment' }}
                                                        </h5>
                                                    @endif
                                                    <div class="mt-1 flex items-center space-x-2 text-xs text-gray-500">
                                                        @if(isset($attachment['size']))
                                                            <span>{{ number_format($attachment['size'] / 1024, 1) }} KB</span>
                                                        @endif
                                                        @if(isset($attachment['type']))
                                                            <span>•</span>
                                                            <span>{{ $attachment['type'] }}</span>
                                                        @endif
                                                        @if(isset($attachment['uploaded_at']))
                                                            <span>•</span>
                                                            <span>{{ \Carbon\Carbon::parse($attachment['uploaded_at'])->format('M j, Y') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="flex-shrink-0 flex items-center space-x-1">
                                                    @if(isset($attachment['path']))
                                                        <a href="{{ asset('storage/' . $attachment['path']) }}" target="_blank" 
                                                           class="text-indigo-600 hover:text-indigo-800 p-1"
                                                           title="View attachment">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                            </svg>
                                                        </a>
                                                    @endif
                                                    <button type="button" 
                                                            data-index="{{ $index }}"
                                                            class="remove-existing-attachment text-red-600 hover:text-red-800 p-1"
                                                            title="Remove attachment"
                                                            onclick="if(confirm('Are you sure you want to remove this attachment?')) { removeExistingAttachment({{ $index }}); }">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <!-- Hidden input to track removed attachments -->
                                <input type="hidden" name="removed_attachments" id="removed_attachments" value="">
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('admin.purchase-orders.show', $purchaseOrder) }}" 
                       class="inline-flex items-center rounded-md bg-white px-6 py-3 text-base font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center rounded-md bg-indigo-600 px-6 py-3 text-base font-semibold text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        <svg class="-ml-0.5 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.75V16.5L12 14.25 7.5 16.5V3.75m9 0H18A2.25 2.25 0 0120.25 6v12A2.25 2.25 0 0118 20.25H6A2.25 2.25 0 013.75 18V6A2.25 2.25 0 016 3.75h9z" />
                        </svg>
                        Update Purchase Order
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Existing items data for JavaScript -->
@php
$itemsData = $purchaseOrder->items->map(function($item) {
    $itemDetails = $item->item_description;
    if (!$itemDetails && $item->product) {
        $itemDetails = $item->product->name;
    }
    if (!$itemDetails) {
        $itemDetails = 'Unknown Item';
    }
    
    return [
        'item_details' => $itemDetails,
        'product_id' => $item->product_id ? (string) $item->product_id : '',
        'specifications' => $item->specifications ?? '',
        'size' => $item->size ?? '',
        'quantity' => (float) $item->quantity,
        'rate' => (float) $item->unit_price,
        'price_type' => $item->price_type ?? '',
        'discount' => (float) ($item->discount_percentage ?? 0),
        'amount' => (float) $item->line_total
    ];
});

@endphp

<script type="application/json" id="existingItemsData">
{!! json_encode($itemsData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!}
</script>

<script>
let itemCounter = 0;

// Existing items data from PHP
let existingItems = [];
try {
    const dataScript = document.getElementById('existingItemsData');
    if (dataScript) {
        existingItems = JSON.parse(dataScript.textContent);
    }
} catch (e) {
    console.error('Error parsing existing items data:', e);
    existingItems = [];
}

function addItem(itemData = null) {
    const tbody = document.getElementById('itemsTable');
    const row = document.createElement('tr');
    row.className = 'item-row bg-white hover:bg-gray-50';
    
    const data = itemData || {
        item_details: '',
        product_id: '',
        specifications: '',
        size: '',
        quantity: 1.00,
        rate: 0.00,
        price_type: '',
        discount: 0,
        amount: 0.00
    };
    
    row.innerHTML = `
        <td class="px-3 py-4 text-center">
            <svg class="w-4 h-4 text-gray-400 cursor-pointer drag-handle mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
            </svg>
        </td>
        <td class="px-3 py-4">
            <div class="relative product-dropdown-container">
                <input type="text" 
                       class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 product-search-input" 
                       placeholder="Search products..." 
                       autocomplete="off"
                       value="${data.item_details}">
                <input type="hidden" name="items[${itemCounter}][product_id]" class="product-id-input" value="${data.product_id || ''}">
                <input type="hidden" name="items[${itemCounter}][item_description]" class="item-details-hidden" value="${data.item_details}">
                
                <!-- Dropdown List -->
                <div class="product-dropdown-list hidden">
                    <div class="p-2 text-sm text-gray-500 dropdown-loading hidden">Searching...</div>
                    <div class="dropdown-items">
                        @foreach(($products ?? \Illuminate\Support\Collection::make([])) as $product)
                            <div class="dropdown-item cursor-pointer p-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0" 
                                 data-id="{{ $product->id }}"
                                 data-name="{{ $product->name }}"
                                 data-description="{{ $product->description }}"
                                 data-price-aed="{{ $product->price_aed ?? $product->price }}"
                                 data-price-usd="{{ $product->price ?? 0 }}"
                                 data-procurement-price-aed="{{ $product->procurement_price_aed ?? $product->price_aed ?? $product->price }}"
                                 data-procurement-price-usd="{{ $product->procurement_price_usd ?? $product->price ?? 0 }}"
                                 data-specifications="{{ $product->specifications ? json_encode($product->specifications->map(function($spec) { return $spec->display_name . ': ' . $spec->formatted_value; })->toArray()) : '[]' }}"
                                 data-has-size-options="{{ $product->has_size_options ? 'true' : 'false' }}"
                                 data-size-options="{{ is_array($product->size_options) ? json_encode($product->size_options) : ($product->size_options ?: '[]') }}"
                                 data-search-text="{{ strtolower($product->name . ' ' . ($product->brand ? $product->brand->name : '') . ' ' . $product->description) }}">
                                <div class="font-medium text-gray-900">{{ $product->name }}{{ $product->brand ? ' - ' . $product->brand->name : '' }}</div>
                                @if($product->description)
                                    <div class="text-gray-600 text-xs mt-1">{{ Str::limit($product->description, 80) }}</div>
                                @endif
                                @if($product->price_aed ?? $product->price)
                                    <div class="text-indigo-600 text-sm font-medium mt-1">AED {{ number_format($product->price_aed ?? $product->price, 2) }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <div class="p-3 text-sm text-gray-500 text-center dropdown-no-results hidden">No products found</div>
                </div>
            </div>
        </td>
        <td class="px-3 py-4">
            <div class="relative specifications-dropdown-container">
                <input type="text" 
                       class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 specifications-search-input" 
                       placeholder="Select specifications..." 
                       autocomplete="off"
                       readonly
                       value="">
                 <input type="hidden" name="items[${itemCounter}][specifications]" class="specifications-hidden" value="${data.specifications || ''}">
                
                <!-- Size Options Dropdown -->
                <div class="mt-2">
                    <select name="items[${itemCounter}][size]" class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 size-options-select" data-selected-size="${data.size || ''}">
                        <option value="">Select Size (if applicable)</option>
                        ${data.size ? `<option value="${data.size}" selected>${data.size}</option>` : ''}
                    </select>
                </div>
                
                <!-- Specifications Dropdown List -->
                <div class="specifications-dropdown-list hidden">
                    <div class="p-2 text-sm text-gray-500">No specifications available</div>
                </div>
            </div>
        </td>
        <td class="px-3 py-4">
            <input type="number" step="0.01" name="items[${itemCounter}][quantity]" value="${data.quantity}" required
                   class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 quantity-input">
        </td>
        <td class="px-3 py-4">
            <div class="space-y-2">
                <select name="items[${itemCounter}][price_type]" class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 price-type-select">
                    <option value="">Select Price Type</option>
                    <option value="aed" ${(data.price_type || '') === 'aed' ? 'selected' : ''}>Procurement Price AED</option>
                    <option value="usd" ${(data.price_type || '') === 'usd' ? 'selected' : ''}>Procurement Price USD</option>
                    <option value="cny" ${(data.price_type || '') === 'cny' ? 'selected' : ''}>Procurement Price CNY</option>
                    <option value="hkd" ${(data.price_type || '') === 'hkd' ? 'selected' : ''}>Procurement Price HKD</option>
                </select>
                <input type="number" step="0.01" name="items[${itemCounter}][unit_price]" value="${data.rate}" required
                       class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rate-input">
            </div>
        </td>
        <td class="px-3 py-4">
            <div class="flex">
                <input type="number" step="0.01" name="items[${itemCounter}][discount_percentage]" value="${data.discount}" min="0" max="100"
                       class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-l-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 discount-input">
                <span class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm rounded-r-md">%</span>
            </div>
        </td>
        <td class="px-3 py-4 text-right">
            <span class="amount-display font-medium text-gray-900">${data.amount.toFixed(2)}</span>
        </td>
        <td class="px-3 py-4 text-center">
            <button type="button" onclick="removeItem(this)" class="inline-flex items-center p-1 border border-transparent rounded-full text-red-600 hover:bg-red-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </td>
    `;
    
    tbody.appendChild(row);
    itemCounter++;
    
    // Add event listeners for calculation
    const quantityInput = row.querySelector('.quantity-input');
    const rateInput = row.querySelector('.rate-input');
    const discountInput = row.querySelector('.discount-input');
    const priceTypeSelect = row.querySelector('.price-type-select');
    const productSearchInput = row.querySelector('.product-search-input');
    const productIdInput = row.querySelector('.product-id-input');
    const itemDetailsHidden = row.querySelector('.item-details-hidden');
    const dropdownList = row.querySelector('.product-dropdown-list');
    const dropdownItems = row.querySelector('.dropdown-items');
    const dropdownNoResults = row.querySelector('.dropdown-no-results');
    const specificationsInput = row.querySelector('.specifications-search-input');
    const specificationsHidden = row.querySelector('.specifications-hidden');
    const specificationsDropdown = row.querySelector('.specifications-dropdown-list');
    const sizeSelect = row.querySelector('.size-options-select');
    
    [quantityInput, rateInput, discountInput].forEach(input => {
        input.addEventListener('input', calculateRowAmount);
    });
    
    // Add event listener for price type change
    priceTypeSelect.addEventListener('change', function() {
        updateRateFromPriceType(this);
    });
    
    // Initialize custom dropdown functionality
    initializeCustomDropdown(productSearchInput, productIdInput, itemDetailsHidden, dropdownList, dropdownItems, dropdownNoResults, rateInput, specificationsInput, specificationsHidden, specificationsDropdown, sizeSelect);
    
    // Add specifications dropdown functionality
    specificationsInput.addEventListener('click', function() {
        if (specificationsHidden.value && specificationsHidden.value !== '[]') {
            specificationsDropdown.classList.toggle('hidden');
        }
    });
    
    // Hide specifications dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!specificationsInput.contains(e.target) && !specificationsDropdown.contains(e.target)) {
            specificationsDropdown.classList.add('hidden');
        }
    });
    
    // Prevent dropdown from closing when clicking on checkboxes
    specificationsDropdown.addEventListener('click', function(e) {
        if (e.target.type === 'checkbox' || e.target.tagName === 'LABEL') {
            e.stopPropagation();
        }
    });
    
    calculateTotals();
    
    // If this is an existing item with a product_id, initialize specifications dropdown and product name
    if (data.product_id && data.product_id !== '') {
        // Set the product search input to show the product name
        const dropdownItem = row.querySelector(`[data-id="${data.product_id}"]`);
        if (dropdownItem) {
            const productName = dropdownItem.getAttribute('data-name');
            if (productName) {
                productSearchInput.value = productName;
            }
        }
        
        initializeExistingItemSpecifications(row, data);
        
        // If there's a price type but no saved rate, update the rate based on it
        // For existing items with saved rates, don't override the rate
        if (data.price_type && priceTypeSelect && (!data.rate || data.rate == 0)) {
            setTimeout(() => {
                updateRateFromPriceType(priceTypeSelect);
            }, 100);
        }
    } else {
        // Even if there's no product_id, display saved specifications if they exist
        if (data.specifications) {
            let displayValue = '';
            try {
                if (data.specifications.startsWith('[') && data.specifications.endsWith(']')) {
                    const specsArray = JSON.parse(data.specifications);
                    displayValue = specsArray.join(', ');
                } else {
                    displayValue = data.specifications;
                }
            } catch (e) {
                displayValue = data.specifications;
            }
            specificationsInput.value = displayValue;
        }
    }
}

function removeItem(button) {
    button.closest('tr').remove();
    calculateTotals();
}

function calculateRowAmount(event) {
    const row = event.target.closest('tr');
    const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
    const rate = parseFloat(row.querySelector('.rate-input').value) || 0;
    const discount = parseFloat(row.querySelector('.discount-input').value) || 0;
    
    const subtotal = quantity * rate;
    const discountAmount = (subtotal * discount) / 100;
    const amount = subtotal - discountAmount;
    
    row.querySelector('.amount-display').textContent = amount.toFixed(2);
    calculateTotals();
}

function calculateTotals() {
    const amounts = document.querySelectorAll('.amount-display');
    let total = 0;
    
    amounts.forEach(amount => {
        total += parseFloat(amount.textContent) || 0;
    });
    
    document.getElementById('subTotal').textContent = total.toFixed(2);
    document.getElementById('totalAmount').textContent = total.toFixed(2);
    
    // Update hidden inputs for form submission
    document.getElementById('sub_total_hidden').value = total.toFixed(2);
    document.getElementById('total_amount_hidden').value = total.toFixed(2);
}

// Initialize custom dropdown functionality
function initializeCustomDropdown(searchInput, productIdInput, itemDetailsHidden, dropdownList, dropdownItems, dropdownNoResults, rateInput, specificationsInput, specificationsHidden, specificationsDropdown, sizeSelect) {
    const allDropdownItems = dropdownItems.querySelectorAll('.dropdown-item');
    let selectedIndex = -1;
    
    // Show dropdown when input is focused
    searchInput.addEventListener('focus', function() {
        dropdownList.classList.remove('hidden');
        filterDropdownItems('');
    });
    
    // Filter items as user types
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        filterDropdownItems(searchTerm);
        selectedIndex = -1;
        dropdownList.classList.remove('hidden');
    });
    
    // Handle keyboard navigation
    searchInput.addEventListener('keydown', function(e) {
        const visibleItems = dropdownItems.querySelectorAll('.dropdown-item:not(.hidden)');
        
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            selectedIndex = Math.min(selectedIndex + 1, visibleItems.length - 1);
            updateSelection(visibleItems);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            selectedIndex = Math.max(selectedIndex - 1, -1);
            updateSelection(visibleItems);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (selectedIndex >= 0 && visibleItems[selectedIndex]) {
                selectProduct(visibleItems[selectedIndex]);
            }
        } else if (e.key === 'Escape') {
            dropdownList.classList.add('hidden');
            selectedIndex = -1;
        }
    });
    
    // Handle item clicks
    allDropdownItems.forEach(item => {
        item.addEventListener('click', function() {
            selectProduct(this);
        });
    });
    
    // Hide dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !dropdownList.contains(e.target)) {
            dropdownList.classList.add('hidden');
            selectedIndex = -1;
        }
    });
    
    function filterDropdownItems(searchTerm) {
        const items = dropdownItems.querySelectorAll('.dropdown-item');
        let visibleCount = 0;
        
        items.forEach(item => {
            const searchText = item.getAttribute('data-search-text') || '';
            if (searchText.includes(searchTerm.toLowerCase())) {
                item.classList.remove('hidden');
                visibleCount++;
            } else {
                item.classList.add('hidden');
            }
        });
        
        if (visibleCount === 0) {
            dropdownNoResults.classList.remove('hidden');
        } else {
            dropdownNoResults.classList.add('hidden');
        }
    }
    
    function updateSelection(visibleItems) {
        visibleItems.forEach((item, index) => {
            if (index === selectedIndex) {
                item.style.backgroundColor = '#f3f4f6';
            } else {
                item.style.backgroundColor = '';
            }
        });
    }
    
    function selectProduct(item) {
        const productId = item.getAttribute('data-id');
        const productName = item.getAttribute('data-name');
        const specifications = item.getAttribute('data-specifications');
        const hasSizeOptions = item.getAttribute('data-has-size-options') === 'true';
        const sizeOptions = item.getAttribute('data-size-options');
        
        searchInput.value = productName;
        productIdInput.value = productId;
        itemDetailsHidden.value = productName;
        
        // Reset price type selection and rate input
        const currentRow = searchInput.closest('tr');
        const priceTypeSelect = currentRow.querySelector('.price-type-select');
        priceTypeSelect.value = '';
        rateInput.value = 0;
        
        // Handle specifications
        if (specifications && specifications !== '[]') {
            try {
                const specsArray = JSON.parse(specifications);
                if (specsArray.length > 0) {
                    specificationsInput.value = 'Click to select specifications...';
                    specificationsHidden.value = JSON.stringify(specsArray);
                    
                    // Create checkboxes for specifications
                    const rowIndex = Array.from(document.querySelectorAll('.specifications-search-input')).indexOf(specificationsInput);
                    createSpecificationDropdown(specificationsDropdown, specsArray, rowIndex);
                } else {
                    specificationsInput.value = '';
                    specificationsHidden.value = '';
                    specificationsDropdown.innerHTML = '<div class="p-2 text-sm text-gray-500">No specifications available</div>';
                }
            } catch (e) {
                console.error('Error parsing specifications:', e);
                specificationsInput.value = '';
                specificationsHidden.value = '';
                specificationsDropdown.innerHTML = '<div class="p-2 text-sm text-gray-500">No specifications available</div>';
            }
        } else {
            specificationsInput.value = '';
            specificationsHidden.value = '';
            specificationsDropdown.innerHTML = '<div class="p-2 text-sm text-gray-500">No specifications available</div>';
        }
        
        // Handle size options
        if (hasSizeOptions && sizeOptions && sizeOptions !== '[]') {
            try {
                const sizesArray = JSON.parse(sizeOptions);
                sizeSelect.innerHTML = '<option value="">Select Size (if applicable)</option>';
                sizesArray.forEach(size => {
                    const option = document.createElement('option');
                    option.value = size;
                    option.textContent = size;
                    sizeSelect.appendChild(option);
                });
            } catch (e) {
                console.error('Error parsing size options:', e);
            }
        } else {
            sizeSelect.innerHTML = '<option value="">Select Size (if applicable)</option>';
        }
        
        // Hide dropdown
        dropdownList.classList.add('hidden');
        selectedIndex = -1;
        
        // Trigger calculation
        calculateRowAmount({ target: rateInput });
    }
}

function createSpecificationDropdown(specificationsDropdown, specsArray, rowIndex) {
    // Create select all option
    let checkboxesHtml = `
        <div class="p-2 bg-indigo-50 border-b border-gray-200">
            <label class="flex items-center cursor-pointer">
                <input type="checkbox" class="select-all-checkbox h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                <span class="ml-2 text-sm font-medium text-indigo-900">Select All</span>
            </label>
        </div>
    `;
    
    // Create individual specification checkboxes
    specsArray.forEach((spec, index) => {
        checkboxesHtml += `
            <div class="flex items-center p-2 hover:bg-gray-50">
                <input type="checkbox" class="spec-checkbox h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" 
                       data-spec="${spec}" id="spec_${rowIndex}_${index}">
                <label for="spec_${rowIndex}_${index}" class="flex-1 cursor-pointer ml-2 text-sm text-gray-700">${spec}</label>
            </div>
        `;
    });
    
    specificationsDropdown.innerHTML = checkboxesHtml;
    
    // Add event listeners for checkboxes
    const checkboxes = specificationsDropdown.querySelectorAll('.spec-checkbox');
    const selectAllCheckbox = specificationsDropdown.querySelector('.select-all-checkbox');
    
    // Select All functionality
    selectAllCheckbox.addEventListener('change', function() {
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelectedSpecificationsForRow(rowIndex);
    });
    
    // Individual checkbox functionality
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectedSpecificationsForRow(rowIndex);
            // Update select all checkbox
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            const someChecked = Array.from(checkboxes).some(cb => cb.checked);
            selectAllCheckbox.checked = allChecked;
            selectAllCheckbox.indeterminate = someChecked && !allChecked;
        });
    });
}

function updateSelectedSpecificationsForRow(rowIndex) {
    const specificationsInputs = document.querySelectorAll('.specifications-search-input');
    const specificationsHiddens = document.querySelectorAll('.specifications-hidden');
    
    if (specificationsInputs[rowIndex] && specificationsHiddens[rowIndex]) {
        const specificationsDropdown = specificationsInputs[rowIndex].closest('td').querySelector('.specifications-dropdown-list');
        
        if (specificationsDropdown) {
            const checkboxes = specificationsDropdown.querySelectorAll('.spec-checkbox:checked');
            const selectedSpecs = Array.from(checkboxes).map(cb => cb.dataset.spec);
            
            if (selectedSpecs.length > 0) {
                specificationsInputs[rowIndex].value = selectedSpecs.join(', ');
                specificationsHiddens[rowIndex].value = JSON.stringify(selectedSpecs);
            } else {
                specificationsInputs[rowIndex].value = 'Click to select specifications...';
                specificationsHiddens[rowIndex].value = '';
            }
        }
    }
}

function updateRateFromPriceType(priceTypeSelect) {
    const row = priceTypeSelect.closest('tr');
    const rateInput = row.querySelector('.rate-input');
    const productIdInput = row.querySelector('.product-id-input');
    
    if (!productIdInput.value || !priceTypeSelect.value) {
        return;
    }
    
    // Find the product dropdown item to get the procurement prices
    const dropdownItem = row.querySelector(`[data-id="${productIdInput.value}"]`);
    if (dropdownItem) {
        let price = 0;
        if (priceTypeSelect.value === 'aed') {
            price = dropdownItem.getAttribute('data-procurement-price-aed') || 0;
        } else if (priceTypeSelect.value === 'usd') {
            price = dropdownItem.getAttribute('data-procurement-price-usd') || 0;
        }
        
        rateInput.value = price;
        calculateRowAmount({ target: rateInput });
    }
}

function initializeExistingItemSpecifications(row, data) {
    const productId = data.product_id;
    const savedSpecifications = data.specifications;
    
    // Find the product dropdown item to get specifications
    const dropdownItem = row.querySelector(`[data-id="${productId}"]`);
    if (!dropdownItem) {
        return;
    }
    
    const specifications = dropdownItem.getAttribute('data-specifications');
    const hasSizeOptions = dropdownItem.getAttribute('data-has-size-options') === 'true';
    const sizeOptions = dropdownItem.getAttribute('data-size-options');
    
    const specificationsInput = row.querySelector('.specifications-search-input');
    const specificationsHidden = row.querySelector('.specifications-hidden');
    const specificationsDropdown = row.querySelector('.specifications-dropdown-list');
    const sizeSelect = row.querySelector('.size-options-select');
    
    // Initialize the hidden field with the saved data
    if (savedSpecifications) {
        specificationsHidden.value = savedSpecifications;
    }
    
    // Handle specifications
    if (specifications && specifications !== '[]') {
        try {
            const specsArray = JSON.parse(specifications);
            if (specsArray.length > 0) {
                const rowIndex = Array.from(document.querySelectorAll('.specifications-search-input')).indexOf(specificationsInput);
                createSpecificationDropdown(specificationsDropdown, specsArray, rowIndex);
                
                // Pre-select saved specifications
                if (savedSpecifications) {
                    let savedSpecsArray = [];
                    try {
                        if (savedSpecifications.startsWith('[') && savedSpecifications.endsWith(']')) {
                            savedSpecsArray = JSON.parse(savedSpecifications);
                        } else {
                            savedSpecsArray = savedSpecifications.split(',').map(s => s.trim());
                        }
                    } catch (e) {
                        savedSpecsArray = [savedSpecifications];
                    }
                    
                    // Check the saved specifications in the dropdown
                    setTimeout(() => {
                        savedSpecsArray.forEach(savedSpec => {
                            const checkbox = specificationsDropdown.querySelector(`input[data-spec="${savedSpec}"]`);
                            if (checkbox) {
                                checkbox.checked = true;
                            }
                        });
                        
                                                 // Update the display
                         updateSelectedSpecificationsForRow(rowIndex);
                         
                         // Update select all checkbox state
                         const checkboxes = specificationsDropdown.querySelectorAll('.spec-checkbox');
                         const selectAllCheckbox = specificationsDropdown.querySelector('.select-all-checkbox');
                         if (selectAllCheckbox && checkboxes.length > 0) {
                             const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                             const someChecked = Array.from(checkboxes).some(cb => cb.checked);
                             selectAllCheckbox.checked = allChecked;
                             selectAllCheckbox.indeterminate = someChecked && !allChecked;
                         }
                         
                         // Make sure the display shows the selected specifications
                         if (savedSpecsArray.length > 0) {
                             specificationsInput.value = savedSpecsArray.join(', ');
                         }
                    }, 100);
                                                  } else {
                     specificationsInput.value = 'Click to select specifications...';
                 }
             } else {
                 // Set up display for already formatted specifications when no product specs available
                 if (savedSpecifications) {
                     if (savedSpecifications.startsWith('[') && savedSpecifications.endsWith(']')) {
                         try {
                             const specsArray = JSON.parse(savedSpecifications);
                             specificationsInput.value = specsArray.join(', ');
                         } catch (e) {
                             specificationsInput.value = savedSpecifications;
                         }
                     } else {
                         specificationsInput.value = savedSpecifications;
                     }
                 } else {
                     specificationsInput.value = 'Click to select specifications...';
                 }
             }
        } catch (e) {
            console.error('Error parsing specifications for existing item:', e);
        }
    }
    
    // Handle size options
    if (hasSizeOptions && sizeOptions && sizeOptions !== '[]') {
        try {
            const sizesArray = JSON.parse(sizeOptions);
            sizeSelect.innerHTML = '<option value="">Select Size (if applicable)</option>';
            sizesArray.forEach(size => {
                const option = document.createElement('option');
                option.value = size;
                option.textContent = size;
                if (data.size === size) {
                    option.selected = true;
                }
                sizeSelect.appendChild(option);
            });
        } catch (e) {
            console.error('Error parsing size options for existing item:', e);
        }
    } else if (data.size) {
        // If no pre-defined size options but we have a saved size, add it as an option
        sizeSelect.innerHTML = '<option value="">Select Size (if applicable)</option>';
        const option = document.createElement('option');
        option.value = data.size;
        option.textContent = data.size;
        option.selected = true;
        sizeSelect.appendChild(option);
    }
}

function validateAndPrepareForm() {
    // First, ensure all specifications are properly saved
    const itemRows = document.querySelectorAll('#itemsTable tr');
    itemRows.forEach((row, index) => {
        const specificationsInput = row.querySelector('.specifications-search-input');
        const specificationsHidden = row.querySelector('input[name*="[specifications]"]');
        
        if (specificationsInput && specificationsHidden) {
            const displayValue = specificationsInput.value;
            
            // If there's a display value but no hidden value, convert it
            if (displayValue && displayValue !== 'Click to select specifications...' && !specificationsHidden.value) {
                const specsArray = displayValue.split(',').map(s => s.trim()).filter(s => s);
                if (specsArray.length > 0) {
                    specificationsHidden.value = JSON.stringify(specsArray);
                }
            }
            
            // If hidden value exists but display is empty, update display
            if (specificationsHidden.value && (!displayValue || displayValue === 'Click to select specifications...')) {
                try {
                    const specsArray = JSON.parse(specificationsHidden.value);
                    if (Array.isArray(specsArray)) {
                        specificationsInput.value = specsArray.join(', ');
                    }
                } catch (e) {
                    // If it's not JSON, use as is
                    specificationsInput.value = specificationsHidden.value;
                }
            }
        }
    });
    
    return validateForm();
}

function validateForm() {
    // Validate that we have at least one item
    const itemRows = document.querySelectorAll('#itemsTable tr');
    if (itemRows.length === 0) {
        alert('Please add at least one item before saving the purchase order.');
        return false;
    }
    
    // Validate that all item rows have required data and ensure specifications are properly set
    let hasEmptyItems = false;
    itemRows.forEach((row, index) => {
        const itemDescription = row.querySelector('input[name*="[item_description]"]');
        const quantity = row.querySelector('input[name*="[quantity]"]');
        const rate = row.querySelector('input[name*="[unit_price]"]');
        const specificationsHidden = row.querySelector('input[name*="[specifications]"]');
        
        if (!itemDescription || !itemDescription.value.trim()) {
            hasEmptyItems = true;
        }
        if (!quantity || parseFloat(quantity.value) <= 0) {
            hasEmptyItems = true;
        }
        if (!rate || parseFloat(rate.value) < 0) {
            hasEmptyItems = true;
        }
        
        // Ensure specifications hidden field is properly set
        if (specificationsHidden) {
            const specificationsInput = row.querySelector('.specifications-search-input');
            if (specificationsInput && specificationsInput.value && specificationsInput.value !== 'Click to select specifications...') {
                // If specifications are displayed but not in hidden field, try to fix it
                if (!specificationsHidden.value) {
                    const displayedSpecs = specificationsInput.value.split(', ').map(s => s.trim()).filter(s => s);
                    if (displayedSpecs.length > 0) {
                        specificationsHidden.value = JSON.stringify(displayedSpecs);
                    }
                }
            }
        }
    });
    
    if (hasEmptyItems) {
        alert('Please fill in all item descriptions, quantities and rates.');
        return false;
    }
    
    return true;
}

// Initialize page
// Currency handling in edit page
function updateCurrencyUI() {
    const currency = document.getElementById('currency').value;
    const currencyDisplay = document.getElementById('currencyDisplay');
    if (currencyDisplay) currencyDisplay.textContent = currency;
    const currencyDisplaySub = document.getElementById('currencyDisplaySub');
    if (currencyDisplaySub) currencyDisplaySub.textContent = currency;
    // Recalculate totals to reflect label changes
    calculateTotals();
    // Sync price type controls with selected currency
    updatePriceTypeControlsByCurrency();
}

// Enable/disable price type selects and auto-update rates based on currency
function updatePriceTypeControlsByCurrency() {
    const currency = document.getElementById('currency') ? document.getElementById('currency').value : '{{ $purchaseOrder->currency }}';
    const rows = document.querySelectorAll('#itemsTable tr');
    rows.forEach(row => {
        const priceTypeSelect = row.querySelector('.price-type-select');
        const rateInput = row.querySelector('.rate-input');
        const productIdInput = row.querySelector('.product-id-input');
        if (!priceTypeSelect) return;
        // Check if this is an existing item with saved values
        const hasExistingRate = rateInput && rateInput.value && parseFloat(rateInput.value) > 0;
        const hasExistingPriceType = priceTypeSelect.value && priceTypeSelect.value !== '';
        
        if (currency === 'AED') {
            priceTypeSelect.disabled = false;
            if (!priceTypeSelect.value) priceTypeSelect.value = 'aed';
            // Only update rate if it's a new item without existing values
            if (productIdInput && productIdInput.value && !hasExistingRate) {
                updateRateFromPriceType(priceTypeSelect);
            }
        } else if (currency === 'USD') {
            priceTypeSelect.disabled = false;
            if (!priceTypeSelect.value) priceTypeSelect.value = 'usd';
            if (productIdInput && productIdInput.value && !hasExistingRate) {
                updateRateFromPriceType(priceTypeSelect);
            }
        } else if (currency === 'CNY') {
            priceTypeSelect.disabled = false;
            if (!priceTypeSelect.value) priceTypeSelect.value = 'cny';
            if (productIdInput && productIdInput.value && !hasExistingRate) {
                updateRateFromPriceType(priceTypeSelect);
            }
        } else if (currency === 'HKD') {
            priceTypeSelect.disabled = false;
            if (!priceTypeSelect.value) priceTypeSelect.value = 'hkd';
            if (productIdInput && productIdInput.value && !hasExistingRate) {
                updateRateFromPriceType(priceTypeSelect);
            }
        } else {
            // For other currencies: disable price type selection and leave manual rate
            if (!hasExistingPriceType) {
                priceTypeSelect.value = '';
            }
            priceTypeSelect.disabled = true;
            // Do not auto-change rate; keep whatever user set
            if (rateInput && !rateInput.value) {
                rateInput.value = 0;
            }
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const currencySelect = document.getElementById('currency');
    if (currencySelect) {
        currencySelect.addEventListener('change', updateCurrencyUI);
    }
    // Initialize price type controls according to initial currency
    updatePriceTypeControlsByCurrency();
    // Ensure currency labels (Subtotal/Total) match current selection on load
    updateCurrencyUI();
    // Add event listener for add item button
    document.getElementById('addItem').addEventListener('click', function() {
        addItem();
    });
    
    // Load existing items
    if (existingItems && existingItems.length > 0) {
        existingItems.forEach(function(item) {
            addItem(item);
        });
    } else {
        addItem();
    }
});

// File upload and attachment management functions
let selectedFiles = [];
let removedAttachmentIndexes = [];

function handleFileSelection(input) {
    const files = Array.from(input.files);
    const maxFileSize = 10 * 1024 * 1024; // 10MB
    const allowedTypes = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/gif'
    ];
    
    // Validate files
    const validFiles = [];
    const errors = [];
    
    files.forEach(file => {
        if (file.size > maxFileSize) {
            errors.push(`${file.name} is too large. Maximum size is 10MB.`);
        } else if (!allowedTypes.includes(file.type)) {
            errors.push(`${file.name} is not a supported file type.`);
        } else {
            validFiles.push(file);
        }
    });
    
    if (errors.length > 0) {
        alert('File validation errors:\n' + errors.join('\n'));
    }
    
    // Add valid files to selected files
    validFiles.forEach(file => {
        if (!selectedFiles.find(f => f.name === file.name && f.size === file.size)) {
            selectedFiles.push(file);
        }
    });
    
    updateFilePreview();
}

function updateFilePreview() {
    const previewArea = document.getElementById('filePreviewArea');
    const fileList = document.getElementById('fileList');
    
    if (selectedFiles.length === 0) {
        previewArea.classList.add('hidden');
        return;
    }
    
    previewArea.classList.remove('hidden');
    fileList.innerHTML = '';
    
    selectedFiles.forEach((file, index) => {
        const fileItem = document.createElement('div');
        fileItem.className = 'flex items-center justify-between p-3 bg-gray-50 border border-gray-200 rounded-lg';
        
        const fileIcon = getFileIcon(file.name);
        const fileSize = formatFileSize(file.size);
        
        fileItem.innerHTML = `
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                    ${fileIcon}
                </div>
                <div>
                    <h5 class="text-sm font-medium text-gray-900">${file.name}</h5>
                    <p class="text-xs text-gray-500">${fileSize}</p>
                </div>
            </div>
            <button type="button" onclick="removeSelectedFile(${index})" class="text-red-600 hover:text-red-800 p-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        `;
        
        fileList.appendChild(fileItem);
    });
}

function removeSelectedFile(index) {
    selectedFiles.splice(index, 1);
    updateFilePreview();
    updateFileInput();
}

function removeExistingAttachment(index) {
    if (!removedAttachmentIndexes.includes(index)) {
        removedAttachmentIndexes.push(index);
    }
    
    // Hide the attachment element
    const attachmentElement = document.querySelector(`[data-index="${index}"]`).closest('.border');
    if (attachmentElement) {
        attachmentElement.style.display = 'none';
    }
    
    // Update hidden input
    document.getElementById('removed_attachments').value = removedAttachmentIndexes.join(',');
}

function getFileIcon(filename) {
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
        case 'gif':
            return '<svg class="w-4 h-4 text-purple-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path></svg>';
        default:
            return '<svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path></svg>';
    }
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function updateFileInput() {
    const fileInput = document.getElementById('attachments');
    const dataTransfer = new DataTransfer();
    
    selectedFiles.forEach(file => {
        dataTransfer.items.add(file);
    });
    
    fileInput.files = dataTransfer.files;
}

// Drag and drop functionality
document.addEventListener('DOMContentLoaded', function() {
    const uploadArea = document.getElementById('fileUploadArea');
    const fileInput = document.getElementById('attachments');
    
    // Drag and drop event listeners
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadArea.classList.add('border-indigo-500', 'bg-indigo-50');
    });
    
    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('border-indigo-500', 'bg-indigo-50');
    });
    
    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('border-indigo-500', 'bg-indigo-50');
        
        const files = Array.from(e.dataTransfer.files);
        const dataTransfer = new DataTransfer();
        files.forEach(file => dataTransfer.items.add(file));
        fileInput.files = dataTransfer.files;
        
        handleFileSelection(fileInput);
    });
    
    // Click to upload
    uploadArea.addEventListener('click', function(e) {
        if (e.target.tagName !== 'BUTTON') {
            fileInput.click();
        }
    });
    
    // Attachment removal is handled by individual onclick handlers
});
</script>
@endsection 