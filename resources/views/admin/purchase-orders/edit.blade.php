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

    <!-- Form -->
    <form action="{{ route('admin.purchase-orders.update', $purchaseOrder) }}" method="POST" onsubmit="return validateAndPrepareForm()">
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
                        </div>
                    </div>
                </div>

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
                                        <span class="text-sm font-bold text-gray-900">AED <span id="subTotal">0.00</span></span>
                                    </div>
                                    <div class="border-t border-gray-200 pt-2 mt-2">
                                        <div class="flex justify-between">
                                            <span class="text-base font-bold text-gray-900">Total:</span>
                                            <span class="text-base font-bold text-gray-900">AED <span id="totalAmount">0.00</span></span>
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
        'product_id' => $item->product_id,
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
                 <input type="hidden" name="items[${itemCounter}][specifications]" class="specifications-hidden" value="">
                
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
        
        // If there's a price type, update the rate based on it
        if (data.price_type && priceTypeSelect) {
            setTimeout(() => {
                updateRateFromPriceType(priceTypeSelect);
            }, 100);
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
document.addEventListener('DOMContentLoaded', function() {
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
</script>
@endsection 