@extends('admin.layouts.app')

@section('title', 'Create Order')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white shadow">
        <div class="px-4 py-6 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold leading-tight text-gray-900">Create New Order</h1>
                    <p class="mt-2 text-sm text-gray-600">Add products and customer information to create a new order</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Orders
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.orders.store') }}" method="POST" id="orderForm">
        @csrf
        
        <!-- Main Content Area -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 xl:grid-cols-4 gap-8">
                <!-- Customer & Settings Section -->
                <div class="xl:col-span-3 space-y-6">
                    <!-- Customer Information Card -->
                    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-medium text-gray-900">Customer Information</h3>
                                    <p class="text-sm text-gray-500">Select the customer for this order</p>
                                </div>
                            </div>
                        </div>
                        <div class="px-6 py-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-2">
                                        Customer <span class="text-red-500">*</span>
                                    </label>
                                    <select name="customer_id" id="customer_id" required 
                                            class="block w-full rounded-md border-0 py-2.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                        <option value="">Choose a customer...</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->name }}{{ $customer->company_name ? ' - ' . $customer->company_name : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('customer_id')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div class="md:col-span-2">
                                    <div class="flex items-center space-x-3">
                                        <input type="checkbox" name="requires_quotation" id="requires_quotation" value="1" 
                                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        <label for="requires_quotation" class="text-sm font-medium text-gray-700">
                                            Requires supplier quotations
                                        </label>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-500">Check if this order needs quotations from suppliers before processing</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Summary Sidebar -->
                <div class="xl:col-span-1">
                    <div class="bg-white shadow-sm rounded-lg border border-gray-200 sticky top-6">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-blue-50">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-medium text-gray-900">Order Summary</h3>
                                    <p class="text-sm text-gray-500">Live order totals</p>
                                </div>
                            </div>
                        </div>
                        <div class="px-6 py-6">
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-600">Selected Items</span>
                                    <span id="selectedCount" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">0</span>
                                </div>
                                
                                <div class="border-t border-gray-200 pt-4">
                                    <div class="flex justify-between items-center">
                                        <span class="text-base font-medium text-gray-900">Total Amount</span>
                                        <span id="orderTotal" class="text-lg font-bold text-indigo-600">AED 0.00</span>
                                    </div>
                                </div>
                                
                                <div class="pt-4 border-t border-gray-200">
                                    <button type="submit" id="submitBtn" 
                                            class="w-full flex justify-center items-center px-4 py-3 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        Create Order
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Item Table - Full Width -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v1a2 2 0 002 2h2m0-4v4m0-4a2 2 0 012-2h1a2 2 0 012 2v1a2 2 0 01-2 2h-1m-2-4v4"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900">Order Items</h3>
                                <p class="text-sm text-gray-500">Add products to this order</p>
                            </div>
                        </div>
                        <button type="button" id="addItem" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            Add Item
                        </button>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="table-container">
                        <table class="w-full divide-y divide-gray-200 items-table">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 60px;">Drag</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 300px;">Item Details</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 200px;">Specifications</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 120px;">Quantity</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 120px;">Rate</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 120px;">Discount</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 120px;">Amount</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 80px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="itemsTable" class="bg-white divide-y divide-gray-200">
                                <!-- Items will be added dynamically -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Totals Section -->
                    <div class="mt-8 flex justify-end">
                        <div class="w-full max-w-md">
                            <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="font-medium text-gray-600">Subtotal:</span>
                                        <span id="subTotal" class="font-semibold text-gray-900">AED 0.00</span>
                                    </div>
                                    <div class="border-t border-gray-300 pt-3">
                                        <div class="flex justify-between items-center">
                                            <span class="text-lg font-semibold text-gray-900">Total:</span>
                                            <span id="totalAmount" class="text-xl font-bold text-indigo-600">AED 0.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Error Messages -->
                    @error('items')
                        <div class="mt-4 bg-red-50 border border-red-200 rounded-md p-4">
                            <div class="flex">
                                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                                <div class="ml-3">
                                    <p class="text-sm text-red-800">{{ $message }}</p>
                                </div>
                            </div>
                        </div>
                    @enderror
                    
                    @if($errors->any())
                        <div class="mt-4 bg-red-50 border border-red-200 rounded-md p-4">
                            <div class="flex">
                                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                                <div class="ml-3">
                                    <p class="text-sm text-red-800 font-medium">Please fix the following errors:</p>
                                    <ul class="mt-1 text-sm text-red-700 list-disc list-inside">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
    /* Table Layout Styles */
    .items-table {
        table-layout: fixed;
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        position: relative;
    }
    
    .items-table th,
    .items-table td {
        padding: 12px 8px;
        vertical-align: top;
        border-bottom: 1px solid #e5e7eb;
        word-wrap: break-word;
        overflow-wrap: break-word;
        position: relative;
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
        z-index: 100;
    }
    
    .items-table td {
        background-color: #ffffff;
    }
    
    .items-table tr:hover td {
        background-color: #f9fafb;
    }

    /* Product dropdown styles */
    .product-dropdown-container {
        position: relative;
        width: 100%;
    }
    
    .product-dropdown-list {
        position: absolute;
        left: 0;
        right: 0;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        max-height: 300px;
        overflow-y: auto;
        background: white;
        z-index: 9999;
        min-width: 320px;
        margin-top: 2px;
    }
    
    /* Dynamic positioning classes */
    .dropdown-below {
        top: 100%;
    }
    
    .dropdown-above {
        bottom: 100%;
        margin-top: 0;
        margin-bottom: 2px;
    }
    
    .dropdown-item:hover {
        background-color: #f3f4f6 !important;
    }
    
    .specifications-dropdown-container {
        position: relative;
    }
    
    .specifications-dropdown-list {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        z-index: 9998;
        max-height: 200px;
        overflow-y: auto;
        margin-top: 4px;
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

    .amount-display {
        font-weight: 600;
        color: #374151;
    }

    /* Table container improvements */
    .table-container {
        position: relative;
        overflow: visible;
    }
    
    .overflow-x-auto {
        overflow: visible !important;
    }
    
    /* Ensure dropdowns are always on top */
    .product-dropdown-list {
        z-index: 10000 !important;
    }
    
    /* Ensure table rows don't interfere with dropdowns */
    .items-table tbody tr {
        position: relative;
    }
    
    /* Fix any potential overflow issues */
    body {
        overflow-x: auto;
    }
</style>
@endpush

@push('scripts')
<script>
let itemCounter = 0;

document.addEventListener('DOMContentLoaded', function() {
    const addItemBtn = document.getElementById('addItem');
    const orderForm = document.getElementById('orderForm');
    
    if (addItemBtn) {
        addItemBtn.addEventListener('click', addItem);
    }
    
    // Form validation before submission
    if (orderForm) {
        orderForm.addEventListener('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
                return false;
            }
        });
    }
    
    // Add initial item - but don't require it for validation
    // addItem();
    
    // Initialize totals
    calculateTotals();
    
    // Handle window resize to reposition dropdowns
    window.addEventListener('resize', function() {
        const visibleDropdowns = document.querySelectorAll('.product-dropdown-list:not(.hidden)');
        visibleDropdowns.forEach(dropdown => {
            const container = dropdown.closest('.product-dropdown-container');
            const input = container.querySelector('.product-search-input');
            if (input) {
                positionDropdown(dropdown, input);
            }
        });
    });
    
    // Handle scroll to reposition dropdowns
    window.addEventListener('scroll', function() {
        const visibleDropdowns = document.querySelectorAll('.product-dropdown-list:not(.hidden)');
        visibleDropdowns.forEach(dropdown => {
            const container = dropdown.closest('.product-dropdown-container');
            const input = container.querySelector('.product-search-input');
            if (input) {
                positionDropdown(dropdown, input);
            }
        });
    });
});

function validateForm() {
    const rows = document.querySelectorAll('.item-row');
    let validItems = 0;
    let errors = [];
    
    rows.forEach((row, index) => {
        const productId = row.querySelector('.product-id-input');
        const quantity = row.querySelector('.quantity-input');
        const rate = row.querySelector('.rate-input');
        
        if (productId && productId.value && 
            quantity && parseFloat(quantity.value) > 0 && 
            rate && parseFloat(rate.value) >= 0) {
            validItems++;
        } else if (productId && productId.value) {
            // Item has product but missing other fields
            errors.push(`Item ${index + 1}: Please enter valid quantity and rate`);
        }
    });
    
    if (validItems === 0) {
        alert('Please add at least one item with valid product, quantity, and rate.');
        return false;
    }
    
    if (errors.length > 0) {
        alert('Please fix the following errors:\n' + errors.join('\n'));
        return false;
    }
    
    return true;
}

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
                       autocomplete="off"
                       required>
                <input type="hidden" name="items[${itemCounter}][product_id]" class="product-id-input" required>
                <input type="hidden" name="items[${itemCounter}][item_details]" class="item-details-hidden" required>
                
                <!-- Dropdown List -->
                <div class="product-dropdown-list dropdown-below hidden">
                    <div class="p-2 text-sm text-gray-500 dropdown-loading hidden">Searching...</div>
                    <div class="dropdown-items">
                        @foreach($products as $product)
                            <div class="dropdown-item cursor-pointer p-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0" 
                                 data-id="{{ $product->id }}"
                                 data-name="{{ $product->name }}"
                                 data-description="{{ $product->description }}"
                                 data-price="{{ $product->price_aed ?? $product->price }}"
                                 data-search-text="{{ strtolower($product->name . ' ' . ($product->brand ? $product->brand->name : '') . ' ' . $product->description) }}">
                                <div class="font-medium text-gray-900">{{ $product->name }}{{ $product->brand ? ' - ' . $product->brand->name : '' }}</div>
                                @if($product->description)
                                    <div class="text-gray-600 text-xs mt-1">{{ Str::limit($product->description, 80) }}</div>
                                @endif
                                @if($product->price_aed ?? $product->price)
                                    <div class="text-indigo-600 text-sm font-medium mt-1">
                                        AED {{ number_format($product->price_aed ?? $product->price, 2) }}
                                    </div>
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
                       readonly>
                <input type="hidden" name="items[${itemCounter}][specifications]" class="specifications-hidden">
                
                <!-- Size Options Dropdown -->
                <div class="mt-2">
                    <select name="items[${itemCounter}][size]" class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 size-options-select">
                        <option value="">Select Size (if applicable)</option>
                    </select>
                </div>
            </div>
        </td>
        <td class="px-3 py-4">
            <input type="number" step="0.01" name="items[${itemCounter}][quantity]" value="1.00" min="0.01" required
                   class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 quantity-input">
        </td>
        <td class="px-3 py-4">
            <input type="number" step="0.01" name="items[${itemCounter}][rate]" value="0.00" min="0" required
                   class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rate-input">
        </td>
        <td class="px-3 py-4">
            <div class="flex">
                <input type="number" step="0.01" name="items[${itemCounter}][discount]" value="0" min="0" max="100"
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
    
    // Product search functionality
    productSearchInput.addEventListener('focus', function() {
        dropdownList.classList.remove('hidden');
        // Small delay to ensure dropdown is rendered before positioning
        setTimeout(() => {
            positionDropdown(dropdownList, this);
        }, 10);
    });
    
    productSearchInput.addEventListener('input', function() {
        const searchText = this.value.toLowerCase();
        const items = dropdownItems.querySelectorAll('.dropdown-item');
        let hasResults = false;
        
        items.forEach(item => {
            const searchableText = item.getAttribute('data-search-text') || '';
            if (searchableText.includes(searchText)) {
                item.style.display = 'block';
                hasResults = true;
            } else {
                item.style.display = 'none';
            }
        });
        
        const noResults = dropdownList.querySelector('.dropdown-no-results');
        if (hasResults) {
            noResults.classList.add('hidden');
        } else {
            noResults.classList.remove('hidden');
        }
        
        dropdownList.classList.remove('hidden');
        // Position after content update
        setTimeout(() => {
            positionDropdown(dropdownList, this);
        }, 10);
    });
    
    // Product selection
    dropdownItems.addEventListener('click', function(e) {
        const item = e.target.closest('.dropdown-item');
        if (item) {
            const productId = item.getAttribute('data-id');
            const productName = item.getAttribute('data-name');
            const productPrice = item.getAttribute('data-price');
            
            productSearchInput.value = productName;
            productIdInput.value = productId;
            itemDetailsHidden.value = productName;
            rateInput.value = productPrice;
            
            dropdownList.classList.add('hidden');
            calculateRowAmount(row);
        }
    });
    
    // Hide dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!row.contains(e.target)) {
            dropdownList.classList.add('hidden');
        }
    });
    
    // Calculation event listeners
    [quantityInput, rateInput, discountInput].forEach(input => {
        input.addEventListener('input', function() {
            calculateRowAmount(row);
        });
    });
    
    // Focus on the product search input for new items
    productSearchInput.focus();
    
    // Update totals after adding item
    calculateTotals();
}

function removeItem(button) {
    const row = button.closest('tr');
    row.remove();
    calculateTotals();
}

function positionDropdown(dropdown, input) {
    // Reset positioning classes
    dropdown.classList.remove('dropdown-above', 'dropdown-below');
    
    // Get the position of the input relative to the viewport
    const inputRect = input.getBoundingClientRect();
    const viewportHeight = window.innerHeight;
    const spaceBelow = viewportHeight - inputRect.bottom - 20; // Account for padding
    const spaceAbove = inputRect.top - 20; // Account for padding
    const dropdownHeight = Math.min(300, dropdown.scrollHeight || 300); // Max height or actual height
    
    // Prefer showing below unless there's significantly more space above
    if (spaceBelow >= dropdownHeight || (spaceBelow >= 150 && spaceBelow >= spaceAbove)) {
        dropdown.classList.add('dropdown-below');
    } else if (spaceAbove >= dropdownHeight || spaceAbove > spaceBelow) {
        dropdown.classList.add('dropdown-above');
    } else {
        // Default to below if both spaces are insufficient
        dropdown.classList.add('dropdown-below');
    }
}

function calculateRowAmount(row) {
    const quantityInput = row.querySelector('.quantity-input');
    const rateInput = row.querySelector('.rate-input');
    const discountInput = row.querySelector('.discount-input');
    const amountDisplay = row.querySelector('.amount-display');
    
    const quantity = parseFloat(quantityInput.value) || 0;
    const rate = parseFloat(rateInput.value) || 0;
    const discount = parseFloat(discountInput.value) || 0;
    
    const subtotal = quantity * rate;
    const discountAmount = (subtotal * discount) / 100;
    const amount = subtotal - discountAmount;
    
    amountDisplay.textContent = amount.toFixed(2);
    
    calculateTotals();
}

function calculateTotals() {
    const rows = document.querySelectorAll('.item-row');
    let subTotal = 0;
    let itemCount = 0;
    
    rows.forEach(row => {
        const amountDisplay = row.querySelector('.amount-display');
        const quantityInput = row.querySelector('.quantity-input');
        const productIdInput = row.querySelector('.product-id-input');
        
        if (amountDisplay && quantityInput && productIdInput) {
            const amount = parseFloat(amountDisplay.textContent) || 0;
            const quantity = parseFloat(quantityInput.value) || 0;
            const productId = productIdInput.value;
            
            // Only count items that have a product selected and quantity > 0
            if (productId && quantity > 0) {
                subTotal += amount;
                itemCount++;
            }
        }
    });
    
    // Update item table totals
    const subTotalElement = document.getElementById('subTotal');
    const totalAmountElement = document.getElementById('totalAmount');
    
    if (subTotalElement) subTotalElement.textContent = 'AED ' + subTotal.toFixed(2);
    if (totalAmountElement) totalAmountElement.textContent = 'AED ' + subTotal.toFixed(2);
    
    // Update sidebar summary
    const selectedCountElement = document.getElementById('selectedCount');
    const orderTotalElement = document.getElementById('orderTotal');
    const submitBtn = document.getElementById('submitBtn');
    
    if (selectedCountElement) selectedCountElement.textContent = itemCount;
    if (orderTotalElement) orderTotalElement.textContent = 'AED ' + subTotal.toFixed(2);
    if (submitBtn) {
        if (itemCount === 0) {
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    }
}
</script>
@endpush
