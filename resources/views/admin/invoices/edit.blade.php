@extends('admin.layouts.app')

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
    .items-table td:nth-child(5) { width: 120px; } /* Rate */
    
    .items-table th:nth-child(6),
    .items-table td:nth-child(6) { width: 120px; } /* Discount */
    
    .items-table th:nth-child(7),
    .items-table td:nth-child(7) { width: 120px; } /* Amount */
    
    .items-table th:nth-child(8),
    .items-table td:nth-child(8) { width: 80px; } /* Action */
    
    /* Input Styles */
    .items-table input,
    .items-table select {
        min-width: 0;
        box-sizing: border-box;
    }
    
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
    
    /* Force dropdown to appear above everything */
    .product-dropdown-list:not(.hidden) {
        position: absolute !important;
        z-index: 999999 !important;
    }
    
    .specifications-dropdown-list:not(.hidden) {
        position: absolute !important;
        z-index: 999999 !important;
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
    
    .dropdown-item:hover {
        background-color: #f8fafc;
    }
    
    .dropdown-item.bg-indigo-50 {
        background-color: #eef2ff;
    }
    
    .product-search-input {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z'/%3e%3c/svg%3e");
        background-position: right 0.5rem center;
        background-repeat: no-repeat;
        background-size: 1.5em 1.5em;
        padding-right: 2.5rem;
    }
    
    .hidden {
        display: none;
    }
    
    /* Table Container Styles */
    .overflow-x-auto {
        overflow-x: auto;
        overflow-y: visible;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        background: white;
    }
    
    /* Additional fixes for table overflow */
    .overflow-x-auto {
        overflow: visible;
    }
    
    /* Ensure the table container doesn't clip the dropdown */
    .items-table {
        overflow: visible;
    }
    
    .items-table tbody {
        overflow: visible;
    }
    
    .items-table tr {
        overflow: visible;
    }
    
    /* Scrollbar Styles */
    .overflow-x-auto::-webkit-scrollbar {
        height: 8px;
    }
    
    .overflow-x-auto::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }
    
    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    
    .overflow-x-auto::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    
    /* Amount Display */
    .amount-display {
        font-weight: 600;
        color: #374151;
    }
    
    /* Action Button */
    .items-table .action-button {
        padding: 4px;
        border-radius: 50%;
        transition: all 0.2s ease-in-out;
    }
    
    .items-table .action-button:hover {
        background-color: #fef2f2;
        transform: scale(1.1);
    }
</style>
@endpush

@section('title', 'Edit Invoice')

@section('content')
<div class="main-content">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit Invoice</h1>
                <p class="text-gray-600 mt-2">Edit invoice {{ $invoice->invoice_number }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.invoices.show', $invoice) }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    View Invoice
                </a>
                <a href="{{ route('admin.invoices.index') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                    </svg>
                    Back to Invoices
                </a>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-6">
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
    @endif

    <!-- Form -->
    <form action="{{ route('admin.invoices.update', $invoice) }}" method="POST" id="invoiceForm">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Form -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Basic Information -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-900">Invoice Information</h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Invoice Type</label>
                                <input type="text" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-500" value="{{ ucfirst($invoice->type) }} Invoice" disabled>
                                <p class="mt-1 text-sm text-gray-500">Invoice type cannot be changed after creation.</p>
                            </div>
                            <div>
                                <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-2">Customer Name <span class="text-red-500">*</span></label>
                                <input type="text" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('customer_name') border-red-300 @enderror" id="customer_name" name="customer_name" 
                                       value="{{ old('customer_name', $invoice->customer_name) }}" required>
                                @error('customer_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="invoice_date" class="block text-sm font-medium text-gray-700 mb-2">Invoice Date <span class="text-red-500">*</span></label>
                                <input type="date" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('invoice_date') border-red-300 @enderror" id="invoice_date" name="invoice_date" 
                                       value="{{ old('invoice_date', $invoice->invoice_date->format('Y-m-d')) }}" required>
                                @error('invoice_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">Due Date <span class="text-red-500">*</span></label>
                                <input type="date" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('due_date') border-red-300 @enderror" id="due_date" name="due_date" 
                                       value="{{ old('due_date', $invoice->due_date->format('Y-m-d')) }}" required>
                                @error('due_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="reference_number" class="block text-sm font-medium text-gray-700 mb-2">Reference Number</label>
                                <input type="text" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('reference_number') border-red-300 @enderror" id="reference_number" name="reference_number" 
                                       value="{{ old('reference_number', $invoice->reference_number) }}">
                                @error('reference_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="po_number" class="block text-sm font-medium text-gray-700 mb-2">PO Number</label>
                                <input type="text" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('po_number') border-red-300 @enderror" id="po_number" name="po_number" 
                                       value="{{ old('po_number', $invoice->po_number) }}">
                                @error('po_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
                                <select name="currency" id="currency" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('currency') border-red-300 @enderror">
                                    <option value="AED" {{ old('currency', $invoice->currency ?? 'AED') == 'AED' ? 'selected' : '' }}>AED (UAE Dirham)</option>
                                    <option value="USD" {{ old('currency', $invoice->currency ?? 'AED') == 'USD' ? 'selected' : '' }}>USD (US Dollar)</option>
                                </select>
                                @error('currency')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Customer Information -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-900">Customer Information</h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label for="billing_address" class="block text-sm font-medium text-gray-700 mb-2">Billing Address</label>
                                <textarea class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" id="billing_address" name="billing_address" 
                                          rows="3" required>{{ old('billing_address', $invoice->billing_address) }}</textarea>
                            </div>
                            <div>
                                <label for="shipping_address" class="block text-sm font-medium text-gray-700 mb-2">Shipping Address</label>
                                <textarea class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" id="shipping_address" name="shipping_address" 
                                          rows="3">{{ old('shipping_address', $invoice->shipping_address) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Invoice Items -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v1a2 2 0 002 2h2m0-4v4m0-4a2 2 0 012-2h1a2 2 0 012 2v1a2 2 0 01-2 2h-1m-2-4v4"></path>
                            </svg>
                            Invoice Items
                        </h3>
                        <button type="button" id="addItem" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                            </svg>
                            Add Item
                        </button>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 items-table">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-10">Drag</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item Details</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Specifications</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Quantity</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-28">Rate</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-28">Discount</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-28">Amount</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsTable" class="bg-white divide-y divide-gray-200">
                                    @if($invoice->items->count() > 0)
                                        @foreach($invoice->items as $index => $item)
                                            <tr class="item-row bg-white hover:bg-gray-50" draggable="true">
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
                                                               value="{{ $item->item_description }}"
                                                               autocomplete="off">
                                                        <input type="hidden" name="items[{{ $index }}][product_id]" class="product-id-input" value="{{ $item->product_id ?? '' }}">
                                                        <input type="hidden" name="items[{{ $index }}][item_description]" class="item-details-hidden" value="{{ $item->item_description }}">
                                                        
                                                        <!-- Dropdown List -->
                                                        <div class="product-dropdown-list hidden">
                                                            <div class="p-2 text-sm text-gray-500 dropdown-loading hidden">Searching...</div>
                                                            <div class="dropdown-items">
                                                                @if(isset($products))
                                                                    @foreach($products as $product)
                                                                        <div class="dropdown-item cursor-pointer p-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0" 
                                                                             data-id="{{ $product->id }}"
                                                                             data-name="{{ $product->name }}"
                                                                             data-description="{{ $product->description }}"
                                                                             data-price-aed="{{ $product->price_aed ?? $product->price }}"
                                                                             data-price-usd="{{ $product->price }}"
                                                                             data-specifications="{{ $product->specifications ? json_encode($product->specifications->map(function($spec) { return $spec->display_name . ': ' . $spec->formatted_value; })->toArray()) : '[]' }}"
                                                                             data-has-size-options="{{ $product->has_size_options ? 'true' : 'false' }}"
                                                                             data-size-options="{{ is_array($product->size_options) ? json_encode($product->size_options) : ($product->size_options ?: '[]') }}"
                                                                             data-search-text="{{ strtolower($product->name . ' ' . ($product->brand ? $product->brand->name : '') . ' ' . $product->description) }}">
                                                                            <div class="font-medium text-gray-900">{{ $product->name }}{{ $product->brand ? ' - ' . $product->brand->name : '' }}</div>
                                                                            @if($product->description)
                                                                                <div class="text-gray-600 text-xs mt-1">{{ Str::limit($product->description, 80) }}</div>
                                                                            @endif
                                                                            @if($product->price_aed ?? $product->price)
                                                                                <div class="price-display-aed text-indigo-600 text-sm font-medium mt-1">AED {{ number_format($product->price_aed ?? $product->price, 2) }}</div>
                                                                                <div class="price-display-usd text-indigo-600 text-sm font-medium mt-1 hidden">USD {{ number_format($product->price, 2) }}</div>
                                                                            @endif
                                                                        </div>
                                                                    @endforeach
                                                                @endif
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
                                                               value="{{ $item->specifications || '' }}">
                                                        <input type="hidden" name="items[{{ $index }}][specifications]" class="specifications-hidden" value="{{ $item->specifications || '' }}">
                                                        
                                                        <!-- Size Options Dropdown -->
                                                        <div class="mt-2">
                                                            <select name="items[{{ $index }}][size]" class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 size-options-select" data-selected-size="{{ $item->size }}">
                                                                <option value="">Select Size (if applicable)</option>
                                                            </select>
                                                        </div>
                                                        
                                                        <!-- Specifications Dropdown List -->
                                                        <div class="specifications-dropdown-list hidden">
                                                            <div class="p-2 text-sm text-gray-500">No specifications available</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-3 py-4">
                                                    <input type="number" step="0.01" name="items[{{ $index }}][quantity]" value="{{ $item->quantity }}" required
                                                           class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 quantity-input">
                                                </td>
                                                <td class="px-3 py-4">
                                                    <input type="number" step="0.01" name="items[{{ $index }}][unit_price]" value="{{ $item->unit_price }}" required
                                                           class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rate-input">
                                                </td>
                                                <td class="px-3 py-4">
                                                    <div class="flex">
                                                        <input type="number" step="0.01" name="items[{{ $index }}][discount_percentage]" value="{{ $item->discount_percentage }}" min="0" max="100"
                                                               class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-l-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 discount-input">
                                                        <span class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm rounded-r-md">%</span>
                                                    </div>
                                                </td>
                                                <td class="px-3 py-4 text-right">
                                                    <span class="amount-display font-medium text-gray-900">{{ $item->formatted_line_total }}</span>
                                                </td>
                                                <td class="px-3 py-4 text-center">
                                                    <button type="button" onclick="removeItem(this)" class="inline-flex items-center p-1 border border-transparent rounded-full text-red-600 hover:bg-red-50">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-6 flex justify-end">
                            <div class="w-full max-w-sm">
                                <div class="rounded-lg bg-gray-50 p-4">
                                    <div class="flex justify-between py-2">
                                        <span class="text-sm font-medium text-gray-700">Sub Total:</span>
                                        <span id="subTotal" class="text-sm font-semibold text-gray-900">{{ number_format($invoice->sub_total, 2) }} <span id="subTotalCurrency">{{ $invoice->currency ?? 'AED' }}</span></span>
                                    </div>
                                    <div class="flex justify-between py-2">
                                        <span class="text-sm font-medium text-gray-700">Tax:</span>
                                        <span id="tax-amount" class="text-sm font-semibold text-gray-900">{{ number_format($invoice->tax_amount, 2) }} <span id="taxCurrency">{{ $invoice->currency ?? 'AED' }}</span></span>
                                    </div>
                                    <div class="border-t border-gray-200 pt-2">
                                        <div class="flex justify-between">
                                            <span class="text-base font-semibold text-gray-900">Total (<span id="totalCurrency">{{ $invoice->currency ?? 'AED' }}</span>):</span>
                                            <span id="totalAmount" class="text-base font-bold text-indigo-600">{{ number_format($invoice->total_amount, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2z"></path>
                            </svg>
                            Additional Information
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-6">
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                <textarea id="description" name="description" rows="3"
                                          class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('description') border-red-300 @enderror">{{ old('description', $invoice->description) }}</textarea>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="terms_conditions" class="block text-sm font-medium text-gray-700 mb-2">Terms & Conditions</label>
                                <textarea id="terms_conditions" name="terms_conditions" rows="3"
                                          class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('terms_conditions') border-red-300 @enderror">{{ old('terms_conditions', $invoice->terms_conditions) }}</textarea>
                                @error('terms_conditions')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                                <textarea id="notes" name="notes" rows="3"
                                          class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('notes') border-red-300 @enderror">{{ old('notes', $invoice->notes) }}</textarea>
                                @error('notes')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-8">
                <!-- Payment Terms -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-900">Payment Terms</h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="mb-4">
                            <label for="payment_terms" class="block text-sm font-medium text-gray-700 mb-2">Payment Terms</label>
                            <select name="payment_terms" id="payment_terms" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
                                <option value="advance_50" {{ old('payment_terms', $invoice->payment_terms) == 'advance_50' ? 'selected' : '' }}>50% Advance Payment</option>
                                <option value="advance_100" {{ old('payment_terms', $invoice->payment_terms) == 'advance_100' ? 'selected' : '' }}>100% Advance Payment</option>
                                <option value="on_delivery" {{ old('payment_terms', $invoice->payment_terms) == 'on_delivery' ? 'selected' : '' }}>Payment on Delivery</option>
                                <option value="net_30" {{ old('payment_terms', $invoice->payment_terms) == 'net_30' ? 'selected' : '' }}>Net 30 Days</option>
                                <option value="custom" {{ old('payment_terms', $invoice->payment_terms) == 'custom' ? 'selected' : '' }}>Custom Terms</option>
                            </select>
                        </div>
                        <div class="mb-4 {{ old('payment_terms', $invoice->payment_terms) == 'custom' ? '' : 'hidden' }}" id="custom-percentage">
                            <label for="advance_percentage" class="block text-sm font-medium text-gray-700 mb-2">Advance Percentage</label>
                            <input type="number" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" id="advance_percentage" 
                                   name="advance_percentage" min="0" max="100" step="0.01" 
                                   value="{{ old('advance_percentage', $invoice->advance_percentage) }}">
                        </div>
                    </div>
                </div>

                <!-- Invoice Status -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-900">Invoice Status</h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3 text-sm text-gray-600">
                            <div class="flex justify-between items-center">
                                <span class="font-medium text-gray-900">Current Status:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-800' : ($invoice->status === 'sent' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="font-medium text-gray-900">Created:</span>
                                <span>{{ formatDubaiDate($invoice->created_at, 'd M Y') }}</span>
                            </div>
                            @if($invoice->payments()->exists())
                            <div class="flex justify-between items-center">
                                <span class="font-medium text-gray-900">Paid Amount:</span>
                                <span>{{ number_format($invoice->paid_amount, 2) }} AED</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6">
                        <div class="space-y-3">
                            <button type="submit" class="w-full inline-flex justify-center items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                                <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z" />
                                </svg>
                                Update Invoice
                            </button>
                            <a href="{{ route('admin.invoices.show', $invoice) }}" class="w-full inline-flex justify-center items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Item count data -->
<script type="application/json" id="itemCountData">
    @json($invoice->items->count() > 0 ? $invoice->items->count() : 1)
</script>

<script>
let itemCounter = 0;
try {
    const dataScript = document.getElementById('itemCountData');
    if (dataScript) {
        itemCounter = JSON.parse(dataScript.textContent);
    }
} catch (e) {
    console.error('Error parsing item count data:', e);
    itemCounter = 1;
}

// Currency change handling
document.getElementById('currency').addEventListener('change', function() {
    const selectedCurrency = this.value;
    
    // Update total currency displays
    document.getElementById('subTotalCurrency').textContent = selectedCurrency;
    document.getElementById('taxCurrency').textContent = selectedCurrency;
    document.querySelectorAll('#totalCurrency').forEach(el => el.textContent = selectedCurrency);
    
    // Toggle price displays in product dropdowns
    const priceDisplaysAED = document.querySelectorAll('.price-display-aed');
    const priceDisplaysUSD = document.querySelectorAll('.price-display-usd');
    
    if (selectedCurrency === 'USD') {
        priceDisplaysAED.forEach(display => display.classList.add('hidden'));
        priceDisplaysUSD.forEach(display => display.classList.remove('hidden'));
    } else {
        priceDisplaysAED.forEach(display => display.classList.remove('hidden'));
        priceDisplaysUSD.forEach(display => display.classList.add('hidden'));
    }
    
    // Update existing item rates based on selected currency
    const existingRows = document.querySelectorAll('.item-row');
    existingRows.forEach(row => {
        const productIdInput = row.querySelector('.product-id-input');
        const rateInput = row.querySelector('.rate-input');
        
        if (productIdInput && productIdInput.value && rateInput) {
            // Find the product dropdown item to get the correct price
            const dropdownItem = row.querySelector(`[data-id="${productIdInput.value}"]`);
            if (dropdownItem) {
                const price = selectedCurrency === 'USD' ? 
                    dropdownItem.getAttribute('data-price-usd') : 
                    dropdownItem.getAttribute('data-price-aed');
                
                if (price) {
                    rateInput.value = price;
                    calculateRowAmount({ target: rateInput });
                }
            }
        }
    });
});

// Payment terms handling
document.addEventListener('DOMContentLoaded', function() {
    const paymentTermsSelect = document.getElementById('payment_terms');
    const customDiv = document.getElementById('custom-percentage');
    
    function toggleCustomPercentage() {
        if (paymentTermsSelect.value === 'custom') {
            customDiv.classList.remove('hidden');
        } else {
            customDiv.classList.add('hidden');
        }
    }
    
    // Initial state
    toggleCustomPercentage();
    
    // Handle changes
    paymentTermsSelect.addEventListener('change', toggleCustomPercentage);
});

function addItem() {
    const tbody = document.getElementById('itemsTable');
    const row = document.createElement('tr');
    row.className = 'item-row bg-white hover:bg-gray-50';
    row.draggable = true;
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
                       autocomplete="off">
                <input type="hidden" name="items[${itemCounter}][product_id]" class="product-id-input">
                <input type="hidden" name="items[${itemCounter}][item_description]" class="item-details-hidden">
                
                <!-- Dropdown List -->
                <div class="product-dropdown-list hidden">
                    <div class="p-2 text-sm text-gray-500 dropdown-loading hidden">Searching...</div>
                    <div class="dropdown-items">
                        @if(isset($products))
                            @foreach($products as $product)
                                <div class="dropdown-item cursor-pointer p-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0" 
                                     data-id="{{ $product->id }}"
                                     data-name="{{ $product->name }}"
                                     data-description="{{ $product->description }}"
                                     data-price-aed="{{ $product->price_aed ?? $product->price }}"
                                 data-price-usd="{{ $product->price }}"
                                     data-specifications="{{ $product->specifications ? json_encode($product->specifications->map(function($spec) { return $spec->display_name . ': ' . $spec->formatted_value; })->toArray()) : '[]' }}"
                                     data-has-size-options="{{ $product->has_size_options ? 'true' : 'false' }}"
                                     data-size-options="{{ is_array($product->size_options) ? json_encode($product->size_options) : ($product->size_options ?: '[]') }}"
                                     data-search-text="{{ strtolower($product->name . ' ' . ($product->brand ? $product->brand->name : '') . ' ' . $product->description) }}">
                                    <div class="font-medium text-gray-900">{{ $product->name }}{{ $product->brand ? ' - ' . $product->brand->name : '' }}</div>
                                    @if($product->description)
                                        <div class="text-gray-600 text-xs mt-1">{{ Str::limit($product->description, 80) }}</div>
                                    @endif
                                    @if($product->price_aed ?? $product->price)
                                        <div class="price-display-aed text-indigo-600 text-sm font-medium mt-1">AED {{ number_format($product->price_aed ?? $product->price, 2) }}</div>
                                        <div class="price-display-usd text-indigo-600 text-sm font-medium mt-1 hidden">USD {{ number_format($product->price, 2) }}</div>
                                    @endif
                                </div>
                            @endforeach
                        @endif
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
                       value="${data.specifications || ''}">
                <input type="hidden" name="items[${itemCounter}][specifications]" class="specifications-hidden" value="${data.specifications || ''}">
                
                <!-- Size Options Dropdown -->
                <div class="mt-2">
                    <select name="items[${itemCounter}][size]" class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 size-options-select" data-selected-size="${data.size}">
                        <option value="">Select Size (if applicable)</option>
                    </select>
                </div>
                
                <!-- Specifications Dropdown List -->
                <div class="specifications-dropdown-list hidden">
                    <div class="p-2 text-sm text-gray-500">No specifications available</div>
                </div>
            </div>
        </td>
        <td class="px-3 py-4">
            <input type="number" step="0.01" name="items[${itemCounter}][quantity]" value="1.00" required
                   class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 quantity-input">
        </td>
        <td class="px-3 py-4">
            <input type="number" step="0.01" name="items[${itemCounter}][unit_price]" value="0.00" required
                   class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rate-input">
        </td>
        <td class="px-3 py-4">
            <div class="flex">
                <input type="number" step="0.01" name="items[${itemCounter}][discount_percentage]" value="0" min="0" max="100"
                       class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-l-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 discount-input">
                <span class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm rounded-r-md">%</span>
            </div>
        </td>
        <td class="px-3 py-4 text-right">
            <span class="amount-display font-medium text-gray-900">0.00</span>
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
    
    // Initialize custom dropdown functionality
    initializeCustomDropdown(productSearchInput, productIdInput, itemDetailsHidden, dropdownList, dropdownItems, dropdownNoResults, rateInput, specificationsInput, specificationsHidden, specificationsDropdown);
    
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
    
    // Initialize size options if product is selected
    if (productIdInput.value) {
        const selectedSize = sizeSelect.getAttribute('data-selected-size') || '';
        populateSizeOptions(productIdInput.value, sizeSelect, selectedSize);
    }
    
    // Handle size options for existing items with selected size
    if (sizeSelect) {
        const selectedSize = sizeSelect.getAttribute('data-selected-size');
        if (selectedSize && productIdInput.value) {
            // Find the product and populate size options with selected size
            const product = products.find(p => p.id == productIdInput.value);
            if (product && product.has_size_options && product.size_options) {
                populateSizeOptionsFromData(sizeSelect, true, product.size_options, selectedSize);
            }
        }
    }
}

function removeItem(button) {
    const tbody = document.getElementById('itemsTable');
    if (tbody.children.length > 1) {
        button.closest('tr').remove();
        calculateTotals();
    } else {
        alert('You must have at least one item.');
    }
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
    
    const selectedCurrency = document.getElementById('currency').value;
    document.getElementById('subTotal').innerHTML = total.toFixed(2) + ' <span id="subTotalCurrency">' + selectedCurrency + '</span>';
    document.getElementById('totalAmount').textContent = total.toFixed(2);
}

function validateForm() {
    // Validate that we have at least one item
    const itemRows = document.querySelectorAll('#itemsTable tr');
    if (itemRows.length === 0) {
        alert('Please add at least one item before saving the invoice.');
        return false;
    }
    
    // Validate that all item rows have required data
    let hasEmptyItems = false;
    itemRows.forEach(row => {
        const productIdInput = row.querySelector('input[name*="[product_id]"]');
        const quantity = row.querySelector('input[name*="[quantity]"]');
        const rate = row.querySelector('input[name*="[unit_price]"]');
        
        if (!productIdInput || !productIdInput.value) {
            hasEmptyItems = true;
        }
        if (!quantity || parseFloat(quantity.value) <= 0) {
            hasEmptyItems = true;
        }
        if (!rate || parseFloat(rate.value) < 0) {
            hasEmptyItems = true;
        }
    });
    
    if (hasEmptyItems) {
        alert('Please select products and fill in all quantities and rates.');
        return false;
    }
    
    return true;
}

// Initialize custom dropdown functionality
function initializeCustomDropdown(searchInput, productIdInput, itemDetailsHidden, dropdownList, dropdownItems, dropdownNoResults, rateInput, specificationsInput, specificationsHidden, specificationsDropdown) {
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
        let visibleCount = 0;
        
        allDropdownItems.forEach(item => {
            const searchText = item.dataset.searchText || '';
            
            if (searchTerm === '' || searchText.includes(searchTerm)) {
                item.classList.remove('hidden');
                visibleCount++;
            } else {
                item.classList.add('hidden');
            }
        });
        
        // Show/hide no results message
        if (visibleCount === 0) {
            dropdownNoResults.classList.remove('hidden');
        } else {
            dropdownNoResults.classList.add('hidden');
        }
    }
    
    function updateSelection(visibleItems) {
        // Remove previous selection
        visibleItems.forEach(item => item.classList.remove('bg-indigo-50'));
        
        // Add selection to current item
        if (selectedIndex >= 0 && visibleItems[selectedIndex]) {
            visibleItems[selectedIndex].classList.add('bg-indigo-50');
        }
    }
    
    function selectProduct(item) {
        const productId = item.dataset.id;
        const productName = item.dataset.name;
        const selectedCurrency = document.getElementById('currency').value;
        const productPrice = selectedCurrency === 'USD' ? 
            item.dataset.priceUsd : 
            item.dataset.priceAed;
        const specifications = item.dataset.specifications;
        
        // Set values
        searchInput.value = productName;
        productIdInput.value = productId;
        itemDetailsHidden.value = productName;
        rateInput.value = productPrice || 0;
        
        // Handle specifications
        if (specifications && specifications !== '[]') {
            try {
                const specsArray = JSON.parse(specifications);
                if (specsArray.length > 0) {
                    specificationsInput.value = 'Click to select specifications...';
                    specificationsHidden.value = JSON.stringify(specsArray);
                    
                    // Create checkboxes for specifications
                    let checkboxesHtml = '';
                    specsArray.forEach(spec => {
                        checkboxesHtml += `
                            <label class="flex items-center p-2 hover:bg-gray-50">
                                <input type="checkbox" class="spec-checkbox h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" data-spec="${spec}">
                                <span class="ml-2 text-sm text-gray-700">${spec}</span>
                            </label>
                        `;
                    });
                    specificationsDropdown.innerHTML = checkboxesHtml;
                    
                    // Add event listeners to checkboxes
                    const checkboxes = specificationsDropdown.querySelectorAll('.spec-checkbox');
                    checkboxes.forEach(checkbox => {
                        checkbox.addEventListener('change', updateSelectedSpecifications);
                    });
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
        
        // Populate size options
        const row = searchInput.closest('tr');
        const sizeSelect = row.querySelector('.size-options-select');
        if (sizeSelect) {
            const hasSizeOptions = item.dataset.hasSizeOptions === 'true';
            const sizeOptions = item.dataset.sizeOptions ? JSON.parse(item.dataset.sizeOptions) : [];
            populateSizeOptionsFromData(sizeSelect, hasSizeOptions, sizeOptions);
        }
        
        // Hide dropdown
        dropdownList.classList.add('hidden');
        selectedIndex = -1;
        
        // Trigger calculation
        calculateRowAmount({ target: rateInput });
    }
    
    function updateSelectedSpecifications() {
        const checkboxes = specificationsDropdown.querySelectorAll('.spec-checkbox:checked');
        const selectedSpecs = Array.from(checkboxes).map(cb => cb.dataset.spec);
        
        if (selectedSpecs.length > 0) {
            specificationsInput.value = selectedSpecs.join(', ');
            specificationsHidden.value = JSON.stringify(selectedSpecs);
        } else {
            specificationsInput.value = 'Click to select specifications...';
            specificationsHidden.value = '';
        }
    }
    
    // Clear selection function
    searchInput.addEventListener('blur', function() {
        // Small delay to allow click events to process
        setTimeout(() => {
            if (!productIdInput.value && searchInput.value) {
                // If no product was selected but there's text, clear it
                searchInput.value = '';
                itemDetailsHidden.value = '';
                rateInput.value = 0;
                specificationsInput.value = '';
                specificationsHidden.value = '';
                specificationsDropdown.innerHTML = '<div class="p-2 text-sm text-gray-500">No specifications available</div>';
                calculateRowAmount({ target: rateInput });
            }
        }, 200);
    });
}

// Initialize with existing items
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('addItem').addEventListener('click', addItem);
    
    // Initialize existing items if any
    const existingItems = document.querySelectorAll('.item-row');
    existingItems.forEach(row => {
        const quantityInput = row.querySelector('.quantity-input');
        const rateInput = row.querySelector('.rate-input');
        const discountInput = row.querySelector('.discount-input');
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
        
        // Initialize custom dropdown functionality
        initializeCustomDropdown(productSearchInput, productIdInput, itemDetailsHidden, dropdownList, dropdownItems, dropdownNoResults, rateInput, specificationsInput, specificationsHidden, specificationsDropdown);
        
        // Initialize size options if product is selected
        if (productIdInput.value) {
            const selectedSize = sizeSelect.getAttribute('data-selected-size') || '';
            populateSizeOptions(productIdInput.value, sizeSelect, selectedSize);
        }
    });
    
    calculateTotals();
    
    // Trigger payment terms check
    document.getElementById('payment_terms').dispatchEvent(new Event('change'));
    
    // Initialize price displays based on selected currency
    const initialCurrency = document.getElementById('currency').value;
    if (initialCurrency === 'USD') {
        document.querySelectorAll('.price-display-aed').forEach(display => display.classList.add('hidden'));
        document.querySelectorAll('.price-display-usd').forEach(display => display.classList.remove('hidden'));
    }
});

// Function to populate size options from data attributes
function populateSizeOptionsFromData(sizeSelect, hasSizeOptions, sizeOptions, selectedSize = '') {
    if (!hasSizeOptions || !sizeOptions || sizeOptions.length === 0) {
        sizeSelect.innerHTML = '<option value="">Select Size (if applicable)</option>';
        return;
    }

    let options = '<option value="">Select Size (if applicable)</option>';
    sizeOptions.forEach(size => {
        const selected = size === selectedSize ? 'selected' : '';
        options += `<option value="${size}" ${selected}>${size}</option>`;
    });
    sizeSelect.innerHTML = options;
}

// Function to populate size options (kept for compatibility)
function populateSizeOptions(productId, sizeSelect, selectedSize = '') {
    if (!productId) {
        sizeSelect.innerHTML = '<option value="">Select Size (if applicable)</option>';
        return;
    }

    const product = products.find(p => p.id == productId);
    if (!product || !product.has_size_options || !product.size_options) {
        sizeSelect.innerHTML = '<option value="">Select Size (if applicable)</option>';
        return;
    }

    let options = '<option value="">Select Size (if applicable)</option>';
    product.size_options.forEach(size => {
        const selected = size === selectedSize ? 'selected' : '';
        options += `<option value="${size}" ${selected}>${size}</option>`;
    });
    sizeSelect.innerHTML = options;
}

// Update size options when product is selected
document.addEventListener('change', function(e) {
    if (e.target.matches('.product-select')) {
        const row = e.target.closest('tr');
        const sizeSelect = row.querySelector('.size-options-select');
        populateSizeOptions(e.target.value, sizeSelect);
    }
});

// Initialize size options for existing items
document.querySelectorAll('tr[data-item-id]').forEach(row => {
    const productId = row.querySelector('.product-select').value;
    const sizeSelect = row.querySelector('.size-options-select');
    const selectedSize = row.querySelector('input[name$="[size]"]').value;
    populateSizeOptions(productId, sizeSelect, selectedSize);
});

// Add product data to window for access
const products = <?php echo json_encode($products->map(function($product) {
    return [
        'id' => $product->id,
        'name' => $product->name,
        'has_size_options' => $product->has_size_options,
        'size_options' => $product->size_options,
        'price' => $product->price_aed ?? $product->price
    ];
})); ?>;
window.products = products;
</script>
@endsection