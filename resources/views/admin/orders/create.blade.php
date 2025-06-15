@extends('admin.layouts.app')

@section('title', 'Create Order')

@section('content')
<div class="-mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Create New Order</h1>
                <p class="text-gray-600 mt-2">Create a new order for customers</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                    </svg>
                    Back to Orders
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.orders.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-8">
                <!-- Customer Selection -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Customer Information
                        </h3>
                    </div>
                    <div class="p-6">
                        <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-2">Select Customer <span class="text-red-500">*</span></label>
                        <select name="customer_id" id="customer_id" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('customer_id') border-red-300 @enderror">
                            <option value="">Choose a customer...</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }} 
                                    @if($customer->email)
                                        ({{ $customer->email }})
                                    @elseif($customer->user && $customer->user->email)
                                        ({{ $customer->user->email }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Products -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4l8-4M4 7v10l8 4"></path>
                            </svg>
                            Select Products and Quantities
                        </h3>
                    </div>
                    <div class="p-6">
                        <!-- Search Box -->
                        <div class="mb-6">
                            <label for="searchInput" class="block text-sm font-medium text-gray-700 mb-2">Search Products</label>
                            <input type="text" 
                                   id="searchInput" 
                                   placeholder="Type to search products..."
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Quantity</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($products as $product)
                                        <tr class="product-row hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900 product-name">{{ $product->name }}</div>
                                                @if($product->description)
                                                    <div class="text-sm text-gray-500">{{ Str::limit($product->description, 50) }}</div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">AED {{ number_format($product->price_aed, 2) }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <button type="button" 
                                                            class="decrease-btn inline-flex items-center p-1 border border-gray-300 rounded-l-md bg-gray-50 text-gray-600 hover:bg-gray-100" 
                                                            data-product-id="{{ $product->id }}">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                                        </svg>
                                                    </button>
                                                    <input type="number" 
                                                           name="quantities[{{ $product->id }}]" 
                                                           id="quantity-{{ $product->id }}"
                                                           class="quantity-input block w-20 px-3 py-2 text-center border-t border-b border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                                           value="0"
                                                           min="0"
                                                           data-product-id="{{ $product->id }}"
                                                           data-price="{{ $product->price_aed}}">
                                                    <button type="button" 
                                                            class="increase-btn inline-flex items-center p-1 border border-gray-300 rounded-r-md bg-gray-50 text-gray-600 hover:bg-gray-100" 
                                                            data-product-id="{{ $product->id }}">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    AED <span id="total-{{ $product->id }}">0.00</span>
                                                </div>
                                                <input type="hidden" name="selected_products[]" value="{{ $product->id }}">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @error('selected_products')
                            <div class="mt-4 bg-red-50 border border-red-200 rounded-md p-4">
                                <div class="text-sm text-red-800">{{ $message }}</div>
                            </div>
                        @enderror

                        @error('quantities')
                            <div class="mt-4 bg-red-50 border border-red-200 rounded-md p-4">
                                <div class="text-sm text-red-800">{{ $message }}</div>
                            </div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-8">
                <!-- Order Summary -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            Order Summary
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-700">Selected Products:</span>
                                <span id="selectedCount" class="text-sm font-semibold text-gray-900">0</span>
                            </div>
                            <div class="border-t border-gray-200 pt-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-base font-semibold text-gray-900">Total Amount:</span>
                                    <span id="orderTotal" class="text-base font-bold text-indigo-600">AED 0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6">
                        <div class="space-y-3">
                            <button type="submit" id="submitBtn" class="w-full inline-flex justify-center items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                                Create Order
                            </button>
                            <a href="{{ route('admin.orders.index') }}" class="w-full inline-flex justify-center items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing order form...');
    
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchText = this.value.toLowerCase();
            const rows = document.getElementsByClassName('product-row');
            
            for (let row of rows) {
                const productName = row.querySelector('.product-name');
                if (productName) {
                    const name = productName.textContent.toLowerCase();
                    row.style.display = name.includes(searchText) ? '' : 'none';
                }
            }
        });
    }

    // Quantity increase buttons
    const increaseButtons = document.querySelectorAll('.increase-btn');
    increaseButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const input = document.getElementById('quantity-' + productId);
            if (input) {
                const currentValue = parseInt(input.value) || 0;
                input.value = currentValue + 1;
                updateProductTotal(productId);
            }
        });
    });

    // Quantity decrease buttons
    const decreaseButtons = document.querySelectorAll('.decrease-btn');
    decreaseButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const input = document.getElementById('quantity-' + productId);
            if (input) {
                const currentValue = parseInt(input.value) || 0;
                if (currentValue > 0) {
                    input.value = currentValue - 1;
                    updateProductTotal(productId);
                }
            }
        });
    });

    // Quantity input changes
    const quantityInputs = document.querySelectorAll('.quantity-input');
    quantityInputs.forEach(function(input) {
        input.addEventListener('change', function() {
            const value = parseInt(this.value) || 0;
            if (value < 0) {
                this.value = 0;
            }
            const productId = this.getAttribute('data-product-id');
            updateProductTotal(productId);
        });
    });

    function updateProductTotal(productId) {
        const input = document.getElementById('quantity-' + productId);
        const totalSpan = document.getElementById('total-' + productId);
        
        if (input && totalSpan) {
            const quantity = parseInt(input.value) || 0;
            const price = parseFloat(input.getAttribute('data-price')) || 0;
            const total = quantity * price;
            totalSpan.textContent = total.toFixed(2);
        }
        
        updateOrderSummary();
    }

    function updateOrderSummary() {
        let totalAmount = 0;
        let selectedProducts = 0;
        
        quantityInputs.forEach(function(input) {
            const quantity = parseInt(input.value) || 0;
            if (quantity > 0) {
                selectedProducts++;
                const price = parseFloat(input.getAttribute('data-price')) || 0;
                totalAmount += quantity * price;
            }
        });
        
        const selectedCountEl = document.getElementById('selectedCount');
        const orderTotalEl = document.getElementById('orderTotal');
        const submitBtn = document.getElementById('submitBtn');
        
        if (selectedCountEl) selectedCountEl.textContent = selectedProducts;
        if (orderTotalEl) orderTotalEl.textContent = 'AED ' + totalAmount.toFixed(2);
        if (submitBtn) submitBtn.disabled = selectedProducts === 0;
    }

    // Initial update
    updateOrderSummary();
    
    console.log('Order form initialized successfully');
});
</script>
@endpush
