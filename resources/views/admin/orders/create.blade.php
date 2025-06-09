@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Create New Order</h1>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to Orders
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.orders.store') }}" method="POST" id="orderForm">
                @csrf
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="user_id">Customer *</label>
                            <select name="customer_id" id="customer_id" class="form-control @error('customer_id') is-invalid @enderror" required>
                                <option value="">Select Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }} ({{ $customer->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <h5>Order Items</h5>
                    <div id="orderItems">
                        <!-- Order items will be added here by JavaScript -->
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="addItem">
                        <i class="fas fa-plus"></i> Add Item
                    </button>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Item Template (Hidden) -->
<template id="itemTemplate">
    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-5">
                    <div class="form-group">
                        <label>Product</label>
                        <select name="items[__INDEX__][product_id]" class="form-control product-select" required>
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                                    {{ $product->name }} - ${{ number_format($product->price, 2) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Quantity</label>
                        <input type="number" name="items[__INDEX__][quantity]" class="form-control quantity" value="1" min="1" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Price</label>
                        <input type="text" class="form-control price" readonly>
                    </div>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-item">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let itemIndex = 0;
        const itemsContainer = document.getElementById('orderItems');
        const addItemBtn = document.getElementById('addItem');
        
        // Add first item by default
        addNewItem();
        
        // Add new item
        addItemBtn.addEventListener('click', addNewItem);
        
        // Handle remove item
        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-item')) {
                e.preventDefault();
                const itemRow = e.target.closest('.item-row');
                if (itemRow) {
                    itemRow.remove();
                    updateIndexes();
                }
            }
        });
        
        // Handle product/quantity changes
        document.addEventListener('change', function(e) {
            const target = e.target;
            if (target.matches('.product-select, .quantity')) {
                updateItemPrice(target.closest('.item-row'));
            }
        });
        
        function addNewItem() {
            const template = document.getElementById('itemTemplate').innerHTML;
            const newItem = document.createElement('div');
            newItem.classList.add('item-row');
            newItem.innerHTML = template.replace(/__INDEX__/g, itemIndex);
            itemsContainer.appendChild(newItem);
            
            // Initialize price for the new item
            updateItemPrice(newItem);
            
            itemIndex++;
        }
        
        function updateItemPrice(itemRow) {
            if (!itemRow) return;
            
            const productSelect = itemRow.querySelector('.product-select');
            const quantityInput = itemRow.querySelector('.quantity');
            const priceInput = itemRow.querySelector('.price');
            
            if (productSelect && productSelect.value && quantityInput && quantityInput.value) {
                const price = parseFloat(productSelect.options[productSelect.selectedIndex].dataset.price) || 0;
                const quantity = parseFloat(quantityInput.value) || 0;
                priceInput.value = '$' + (price * quantity).toFixed(2);
            } else {
                priceInput.value = '';
            }
        }
        
        function updateIndexes() {
            const items = itemsContainer.querySelectorAll('.item-row');
            items.forEach(function(item, index) {
                item.querySelectorAll('[name^="items["]').forEach(function(input) {
                    const name = input.getAttribute('name');
                    const newName = name.replace(/items\[\d+\]/, 'items[' + index + ']');
                    input.setAttribute('name', newName);
                });
            });
        }
    });
</script>
@endpush

<style>
.item-row {
    position: relative;
}

.remove-item {
    position: absolute;
    top: 10px;
    right: 10px;
}

.price {
    background-color: #f8f9fa;
}
</style>
@endsection
