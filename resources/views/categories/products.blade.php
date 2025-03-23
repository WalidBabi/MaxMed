@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <style>
        /* Page Layout */
        .category-header {
            margin-bottom: 1.5rem;
            position: relative;
            border-left: 4px solid #28a745;
            padding-left: 15px;
        }
        .category-title {
            font-size: 2rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 0.5rem;
            transition: color 0.3s;
        }
        .category-description {
            color: #666;
            margin-bottom: 1rem;
            font-size: 1.1rem;
            max-width: 800px;
            line-height: 1.6;
        }
        .breadcrumb {
            background-color: transparent;
            padding: 0.5rem 0;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }
        .breadcrumb-item a {
            color: #6c757d;
            text-decoration: none;
        }
        .breadcrumb-item.active {
            color: #333;
            font-weight: 500;
        }
        
        /* Enhanced Filters Row */
        .filters-row {
            background-color: #f8f9fa;
            padding: 18px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            border-left: 4px solid #17a2b8;
        }
        
        /* Redesigned Counter without the circle */
        .product-counter {
            display: flex;
            align-items: center;
            padding: 8px 0;
        }
        .counter-text {
            display: flex;
            align-items: baseline;
            gap: 8px;
        }
        .counter-number {
            font-size: 1.6rem;
            font-weight: 700;
            color: #28a745;
            line-height: 1.2;
        }
        .counter-label {
            color: #666;
            font-size: 1.1rem;
        }
        
        /* Enhanced Empty State */
        .empty-state {
            background-color: #f8f9fa;
            border-radius: 15px;
            padding: 50px 30px;
            text-align: center;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.08);
            border: 1px dashed #dee2e6;
        }
        .empty-state-icon {
            font-size: 4rem;
            color: #6c757d;
            margin-bottom: 25px;
            opacity: 0.7;
        }
        .empty-state-message {
            font-size: 1.6rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: 20px;
        }
        
        /* Product Grid Layout Improvement */
        .products-grid {
            margin-top: 10px;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
        }
        
        /* Product Card Styling */
        .product-card {
            height: 100%;
            border-radius: 12px;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: none !important;
            background-color: #fff;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }
        .product-card img {
            height: 220px;
            object-fit: cover;
            transition: transform 0.5s;
        }
        .product-card:hover img {
            transform: scale(1.05);
        }
        .card-body {
            flex: 1 1 auto;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            padding: 18px;
            justify-content: space-between;
        }
        .card-title {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-weight: bold;
            font-size: 1.15em;
            margin-bottom: 8px;
        }
        .card-title a {
            text-decoration: none;
            color: #222;
            transition: color 0.2s;
        }
        .card-title a:hover {
            color: #28a745;
        }
        .card-text {
            color: #555;
            margin-bottom: 12px;
            font-size: 1.1em;
        }
        .price-aed {
            font-size: 0.9em;
            color: #777;
        }
        .badge {
            font-size: 0.85em;
            padding: 6px 10px;
            border-radius: 20px;
        }
        .badge.bg-success {
            background-color: #28a745 !important;
        }
        .badge.bg-danger {
            background-color: #dc3545 !important;
        }
        .btn {
            border-radius: 6px;
            margin-top: 5px;
            font-weight: 500;
            padding: 10px 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.85em;
            transition: all 0.3s;
        }
        .btn-primary {
            background-color: #28a745;
            border-color: #28a745;
            color: #fff;
        }
        .btn-primary:hover {
            background-color: #218838;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(33, 136, 56, 0.3);
        }
        .btn-secondary {
            background-color: #17a2b8;
            border-color: #17a2b8;
            color: #fff;
        }
        .btn-secondary:hover {
            background-color: #138496;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(19, 132, 150, 0.3);
        }
        .card-footer {
            background-color: transparent;
            padding: 0;
            border-top: none;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        .quantity-controls {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
            width: 100%;
        }
        .quantity-controls label {
            margin-bottom: 0;
            font-weight: 500;
            color: #555;
        }
        .quantity-controls input {
            width: 70px;
            text-align: center;
            border-radius: 4px;
            border: 1px solid #dee2e6;
            padding: 5px;
        }
        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 15px;
            width: 100%;
            flex-wrap: wrap;
        }
        
        /* Additional responsive improvements */
        @media (max-width: 768px) {
            .category-title {
                font-size: 1.6rem;
            }
            .counter-number {
                font-size: 1.3rem;
            }
            .button-group {
                flex-direction: column;
                gap: 10px;
            }
            .btn {
                width: 100%;
            }
        }
    </style>
    
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('welcome') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('categories.index') }}">Categories</a></li>
            <li class="breadcrumb-item active">{{ isset($category) ? $category->name : 'Products' }}</li>
        </ol>
    </nav>
    
    <div class="row">
        <div class="col-md-3">
            @include('layouts.sidebar')
        </div>
        <div class="col-md-9">
            <div class="category-header">
                <h1 class="category-title">{{ isset($category) ? $category->name : 'All Products' }}</h1>
                <p class="category-description">{{ isset($category) && $category->description ? $category->description : 'Browse our selection of high-quality products' }}</p>
            </div>
            
            <div class="filters-row">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="product-counter">
                            <div class="counter-text">
                                <span class="counter-number">{{ $products->count() }}</span>
                                <span class="counter-label">{{ Str::plural('product', $products->count()) }} found</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="products-grid">
                @if($products->isEmpty())
                    <div class="col-12">
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="fas fa-box-open"></i>
                            </div>
                            <h3 class="empty-state-message">Coming Soon</h3>
                            <p class="empty-state-description">We're working on adding products to this category. Please check back later or browse other categories.</p>
                        </div>
                    </div>
                @else
                    @foreach($products as $product)
                    <div class="product-card-wrapper">
                        <div class="card h-100 product-card">
                            <a href="{{ route('product.show', $product) }}" class="overflow-hidden">
                                <img src="{{ $product->image_url }}" class="card-img-top" alt="{{ $product->name }}">
                            </a>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">
                                    <a href="{{ route('product.show', $product) }}">{{ $product->name }}</a>
                                </h5>
                                <p class="card-text">
                                    <strong>${{ number_format($product->price, 2) }}</strong>
                                    <span class="price-aed d-block">AED {{ number_format($product->price_aed, 2) }}</span>
                                </p>
                                <div class="card-footer">
                                    @if($product->inventory->quantity > 0)
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="badge bg-success me-3">In Stock</span>
                                            <small class="text-muted">{{ $product->inventory->quantity }} available</small>
                                        </div>
                                        <form action="{{ route('cart.add', $product) }}" method="POST" class="w-100">
                                            @csrf
                                            <div class="quantity-controls">
                                                <label for="quantity-{{ $product->id }}" class="me-2">Quantity:</label>
                                                <input type="number" name="quantity" id="quantity-{{ $product->id }}" 
                                                    value="1" min="1" max="{{ $product->inventory->quantity }}" 
                                                    class="form-control">
                                            </div>
                                            <div class="button-group">
                                                <button type="submit" class="btn btn-primary w-100">
                                                    <i class="fas fa-shopping-cart me-2"></i> Add to Cart
                                                </button>
                                                <a href="{{ route('quotation.form', $product) }}" class="btn btn-secondary w-100">
                                                    <i class="fas fa-file-invoice me-2"></i> Request Quote
                                                </a>
                                            </div>
                                        </form>
                                    @else
                                        <div class="d-flex align-items-center mb-3">
                                            <span class="badge bg-danger me-2">Out of Stock</span>
                                        </div>
                                        <div class="button-group">
                                            <a href="{{ route('quotation.form', $product) }}" class="btn btn-secondary w-100">
                                                <i class="fas fa-file-invoice me-2"></i> Request Quote
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
            
            @if(isset($products) && method_exists($products, 'links'))
            <div class="pagination-container mt-4">
                {{ $products->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Quantity control validation
        document.querySelectorAll('.quantity-controls input').forEach(function(input) {
            input.addEventListener('input', function() {
                const max = parseInt(this.max, 10);
                const min = parseInt(this.min, 10);
                let value = parseInt(this.value, 10);

                if (isNaN(value) || value < min) {
                    this.value = min;
                } else if (value > max) {
                    alert('The requested quantity exceeds the available stock.');
                    this.value = max;
                }
            });
        });
    });
</script>
@endsection 