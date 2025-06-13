@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Create New Order</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.orders.store') }}" method="POST">
                        @csrf
                        
                        <!-- Customer Selection -->
                        <div class="mb-4">
                            <label for="customer_id" class="form-label">Select Customer <span class="text-danger">*</span></label>
                            <select name="customer_id" id="customer_id" class="form-select @error('customer_id') is-invalid @enderror" required>
                                <option value="">Choose a customer...</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }} 
                                        @if($customer->user && $customer->user->email)
                                            ({{ $customer->user->email }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Products -->
                        <div class="mb-4">
                            <label class="form-label">Select Products and Quantities</label>
                            
                            <!-- Search Box -->
                            <div class="mb-3">
                                <input type="text" 
                                       class="form-control" 
                                       id="searchInput" 
                                       placeholder="Type to search products...">
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Product Name</th>
                                            <th>Price</th>
                                            <th width="150">Quantity</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($products as $product)
                                            <tr class="product-row">
                                                <td>
                                                    <span class="product-name">{{ $product->name }}</span>
                                                </td>
                                                <td>${{ number_format($product->price, 2) }}</td>
                                                <td>
                                                    <div class="input-group">
                                                        <button type="button" 
                                                                class="btn btn-outline-secondary decrease-btn" 
                                                                data-product-id="{{ $product->id }}">-</button>
                                                        <input type="number" 
                                                               name="quantities[{{ $product->id }}]" 
                                                               id="quantity-{{ $product->id }}"
                                                               class="form-control text-center quantity-input"
                                                               value="0"
                                                               min="0"
                                                               data-product-id="{{ $product->id }}"
                                                               data-price="{{ $product->price }}"
                                                               style="width: 60px">
                                                        <button type="button" 
                                                                class="btn btn-outline-secondary increase-btn" 
                                                                data-product-id="{{ $product->id }}">+</button>
                                                    </div>
                                                </td>
                                                <td>
                                                    $<span id="total-{{ $product->id }}">0.00</span>
                                                    <input type="hidden" name="selected_products[]" value="{{ $product->id }}">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Order Summary -->
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5>Selected Products: <span id="selectedCount">0</span></h5>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <h5>Total Amount: $<span id="orderTotal">0.00</span></h5>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @error('selected_products')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror

                        @error('quantities')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror

                        <div class="mt-4 text-end">
                            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">Create Order</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

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
        if (orderTotalEl) orderTotalEl.textContent = totalAmount.toFixed(2);
        if (submitBtn) submitBtn.disabled = selectedProducts === 0;
    }

    // Initial update
    updateOrderSummary();
    
    console.log('Order form initialized successfully');
});
</script>
@endpush
@endsection
