@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('welcome') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                <img src="{{ $product->image_url }}" class="card-img-top img-fluid product-image" alt="{{ $product->name }}">
                <div class="card-footer bg-white p-2">
                    <div class="d-flex justify-content-center">
                        <div class="small-img-group">
                            <img src="{{ $product->image_url }}" class="small-img active" alt="{{ $product->name }}">
                            @if($product->image_url)
                                <img src="{{ $product->image_url }}" class="small-img" alt="{{ $product->name }} view 2">
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-3 h-100">
                <div class="card-body p-4">
                    <h1 class="card-title fw-bold mb-3">{{ $product->name }}</h1>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="price-container">
                            <span class="fs-4 fw-bold text-primary">${{ number_format($product->price, 2) }}</span>
                            <span class="text-muted ms-2">/ AED {{ number_format($product->price_aed, 2) }}</span>
                        </div>
                        @if($product->inventory && $product->inventory->quantity > 0)
                            <span class="badge bg-success">In Stock</span>
                        @else
                            <span class="badge bg-danger">Out of Stock</span>
                        @endif
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="product-description mb-4">
                        <h5 class="fw-bold">Description</h5>
                        <p class="card-text">{{ $product->description }}</p>
                    </div>
                    
                    @if($product->inventory && $product->inventory->quantity > 0)
                        <div class="d-flex align-items-center mb-4">
                            <div class="input-group me-3" style="width: 130px;">
                                <button class="btn btn-outline-secondary" type="button" id="decrease-qty">-</button>
                                <input type="number" class="form-control text-center" id="quantity" value="1" min="1">
                                <button class="btn btn-outline-secondary" type="button" id="increase-qty">+</button>
                            </div>
                            <div class="availability-info">
                                <small class="text-muted">{{ $product->inventory->quantity }} items available</small>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <form action="{{ route('cart.add', $product) }}" method="POST">
                                @csrf
                                <input type="hidden" name="quantity" id="form-quantity" value="1">
                                <button type="submit" class="btn btn-primary btn-lg w-100">Add to Cart</button>
                            </form>
                            <a href="{{ route('quotation.form', $product) }}" class="btn btn-outline-secondary">Request Quotation</a>
                        </div>
                    @else
                        <div class="d-grid gap-2">
                            <button class="btn btn-secondary btn-lg" disabled>Out of Stock</button>
                            <a href="{{ route('quotation.form', $product) }}" class="btn btn-outline-primary">Request Quotation</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    @if($product->category)
    <div class="row mt-5">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white">
                    <h4 class="mb-0">Product Details</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Category:</strong> {{ $product->category->name }}</p>
                            @if($product->sku)
                                <p><strong>SKU:</strong> {{ $product->sku }}</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            @if($product->specifications)
                                <p><strong>Specifications:</strong> {{ $product->specifications }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
    .product-image {
        height: 400px;
        object-fit: contain;
    }
    .small-img-group {
        display: flex;
        justify-content: center;
    }
    .small-img {
        width: 70px;
        height: 70px;
        object-fit: cover;
        cursor: pointer;
        margin: 0 5px;
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 2px;
    }
    .small-img.active {
        border-color: #0d6efd;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Quantity buttons
        const quantityInput = document.getElementById('quantity');
        const decreaseBtn = document.getElementById('decrease-qty');
        const increaseBtn = document.getElementById('increase-qty');
        
        if (decreaseBtn && increaseBtn && quantityInput) {
            decreaseBtn.addEventListener('click', function() {
                if (quantityInput.value > 1) {
                    quantityInput.value = parseInt(quantityInput.value) - 1;
                }
            });
            
            increaseBtn.addEventListener('click', function() {
                quantityInput.value = parseInt(quantityInput.value) + 1;
            });
        }
        
        // Image gallery
        const smallImgs = document.querySelectorAll('.small-img');
        const productImage = document.querySelector('.product-image');
        
        smallImgs.forEach(img => {
            img.addEventListener('click', function() {
                productImage.src = this.src;
                smallImgs.forEach(i => i.classList.remove('active'));
                this.classList.add('active');
            });
        });
    });
</script>
@endsection