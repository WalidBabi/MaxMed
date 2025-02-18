@extends('layouts.app')

@section('content')
<div class="container mt-5">
 
    <div class="row g-4">
        <div class="col-md-6">
            <div class="position-relative rounded-3 overflow-hidden shadow-sm">
                <img src="{{ asset($product->image_url) }}" alt="{{ $product->name }}" class="img-fluid w-100" style="height: 500px; object-fit: cover;">
                @if($product->category)
                    <span class="position-absolute top-0 end-0 bg-primary text-white m-3 px-3 py-2 rounded-pill">
                        {{ $product->category->name }}
                    </span>
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <div class="bg-white p-4 rounded-3 shadow-sm h-100">
                <h2 class="text-primary mb-4">{{ $product->name }}</h2>
                <div class="d-flex align-items-center mb-4">
                    <h3 class="text-success fw-bold mb-0">${{ number_format($product->price, 2) }}</h3>
                    @if($product->inStock())
                        <span class="badge bg-success ms-3">In Stock</span>
                    @else
                        <span class="badge bg-danger ms-3">Out of Stock</span>
                    @endif
                </div>
                @if(isset($product->description))
                    <div class="mb-4">
                        <h4 class="text-muted mb-3">Description</h4>
                        <p class="text-secondary">{{ $product->description }}</p>
                    </div>
                @endif
                <div class="d-grid gap-2">
                    <form action="{{ route('cart.add', $product->id) }}" method="POST">
                        @csrf
                        <div class="d-flex gap-2">
                            <input type="number" name="quantity" value="1" min="1" class="form-control" style="width: 100px;" {{ !$product->inStock() ? 'disabled' : '' }}>
                            <button type="submit" class="btn btn-primary flex-grow-1" {{ !$product->inStock() ? 'disabled' : '' }}>
                                <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection