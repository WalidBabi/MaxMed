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
            font-size: 1.25rem;
           
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
        
            
        /* Enhanced Filters Row */
        .filters-row {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.08);
            border-left: 4px solid #17a2b8;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        /* Redesigned Counter with animation */
        .product-counter {
            display: flex;
            align-items: center;
            padding: 8px 0;
        }
        .counter-text {
            display: flex;
            align-items: baseline;
            gap: 10px;
        }
        .counter-number {
            font-size: 1.8rem;
            : 700;
            color: #28a745;
            line-height: 1.2;
            animation: countPulse 2s ease-in-out;
        }
        @keyframes countPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        .counter-label {
            color: #555;
            font-size: 1.15rem;
        
        }
        
        /* Enhanced Empty State */
        .empty-state {
            background-color: #f9f9f9;
            border-radius: 15px;
            padding: 60px 30px;
            text-align: center;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.08);
            border: 1px dashed #e0e0e0;
            animation: fadeIn 0.6s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .empty-state-icon {
            font-size: 4.5rem;
            color: #6c757d;
            margin-bottom: 25px;
            opacity: 0.8;
        }
        .empty-state-message {
            font-size: 1.8rem;
            : 600;
            color: #495057;
            margin-bottom: 20px;
        }
        .empty-state-description {
            color: #666;
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }
        
        /* Improved Product Grid Layout */
        .products-grid {
            margin-top: 15px;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 28px;
        }
        
        /* Enhanced Product Card Styling */
        .product-card-wrapper {
            height: 100%;
            transition: transform 0.3s;
        }
        .product-card {
            height: 100%;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.35s;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            border: none !important;
            background-color: #fff;
            position: relative;
        }
        .product-card:hover {
            transform: translateY(-7px);
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.15);
        }
        .product-card img {
            height: 240px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .product-card:hover img {
            transform: scale(1.08);
        }
        .card-body {
            flex: 1 1 auto;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            padding: 22px;
            justify-content: space-between;
        }
        .card-title {
            white-space: normal; /* Changed from nowrap to normal */
            overflow: hidden; /* Added to hide overflow */
            text-overflow: ellipsis; /* Added to show ellipsis */
           
            font-size: 1.2em;
            margin-bottom: 10px;
        }
        .card-title a {
            text-decoration: none;
            color: #222;
            transition: color 0.2s;
            position: relative;
        }
        .card-title a:hover {
            color: #28a745;
        }
        .card-title a:after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -4px;
            left: 0;
            background-color: #28a745;
            transition: width 0.3s;
        }
        .card-title a:hover:after {
            width: 100%;
        }
        .card-text {
            color: #444;
            margin-bottom: 15px;
            font-size: 1.15em;
        }
        .price-aed {
            font-size: 0.9em;
            color: #777;
            margin-top: 3px;
            display: block;
        }
        
        /* Badge improvements */
        .badge {
            font-size: 0.85em;
            padding: 6px 12px;
            border-radius: 20px;
            letter-spacing: 0.5px;
            : 600;
        }
        .badge.bg-success {
            background-color: #28a745 !important;
            box-shadow: 0 2px 5px rgba(40, 167, 69, 0.3);
        }
        .badge.bg-danger {
            background-color: #dc3545 !important;
            box-shadow: 0 2px 5px rgba(220, 53, 69, 0.3);
        }
        
        /* Enhanced buttons */
        .btn {
            border-radius: 8px;
            margin-top: 5px;
         
            padding: 12px 20px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            font-size: 0.85em;
            transition: all 0.35s;
        }
        .btn-primary {
            background-color: #28a745;
            border-color: #28a745;
            color: #fff;
            position: relative;
            overflow: hidden;
        }
        .btn-primary:hover {
            background-color: #218838;
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(33, 136, 56, 0.4);
        }
        .btn-primary:after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 5px;
            height: 5px;
            background: rgba(255, 255, 255, 0.5);
            opacity: 0;
            border-radius: 100%;
            transform: scale(1, 1) translate(-50%);
            transform-origin: 50% 50%;
        }
        .btn-primary:hover:after {
            animation: ripple 1s ease-out;
        }
        @keyframes ripple {
            0% {
                transform: scale(0, 0);
                opacity: 0.5;
            }
            100% {
                transform: scale(20, 20);
                opacity: 0;
            }
        }
        .btn-secondary {
            background-color: #17a2b8;
            border-color: #17a2b8;
            color: #fff;
        }
        .btn-secondary:hover {
            background-color: #138496;
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(19, 132, 150, 0.4);
        }
        
        /* Card footer enhancements */
        .card-footer {
            background-color: transparent;
            padding: 5px 0 0;
            border-top: none;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        
        /* Enhanced quantity controls */
        .quantity-controls {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            width: 100%;
            position: relative;
        }
        .quantity-controls label {
            margin-bottom: 0;
          
            color: #444;
            margin-right: 10px;
        }
        .quantity-controls input {
            width: 70px;
            text-align: center;
            border-radius: 6px;
            border: 1px solid #dee2e6;
            padding: 8px 5px;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .quantity-controls input:focus {
            border-color: #28a745;
            box-shadow: inset 0 1px 3px rgba(40, 167, 69, 0.2);
            outline: none;
        }
        
        /* Button group improvements */
        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 15px;
            width: 100%;
            flex-wrap: wrap;
        }
        
        /* Pagination styling enhancement */
        .pagination-container {
            margin-top: 40px;
            display: flex;
            justify-content: center;
        }
        .pagination-container .pagination {
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
            border-radius: 30px;
            overflow: hidden;
        }
        .pagination-container .page-link {
            border: none;
            color: #555;
    
            padding: 12px 18px;
            transition: all 0.2s;
        }
        .pagination-container .page-link:hover {
            background-color: #f4f4f4;
            color: #28a745;
        }
        .pagination-container .page-item.active .page-link {
            background-color: #28a745;
            color: white;
        }
        
        /* Additional responsive improvements */
        @media (max-width: 992px) {
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 20px;
            }
        }
        
        @media (max-width: 768px) {
            .category-title {
                font-size: 1.25rem;
            }
            .counter-number {
                font-size: 1.5rem;
            }
            .filters-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 15px;
            }
            .button-group {
                flex-direction: column;
                gap: 10px;
            }
            .btn {
                width: 100%;
                padding: 10px 15px;
            }
        }
        
        @media (max-width: 576px) {
            .products-grid {
                grid-template-columns: 1fr;
            }
            .category-header {
                padding-left: 10px;
            }
            .empty-state {
                padding: 40px 20px;
            }
            .empty-state-message {
                font-size: 1.5rem;
            }
        }
    </style>
    
   
    
    <div class="row">
        <div class="col-md-3">
            @include('layouts.sidebar')
        </div>
        <div class="col-md-9 mt-2">
            <div class="category-header">
                <h1 class="category-title">{{ isset($category) ? $category->name : 'All Products' }}</h1>
                <p class="category-description">{{ isset($category) && $category->description ? $category->description : 'Browse our selection of high-quality products' }}</p>
           
            </div>
            
            
            <div class="filters-row">
                <div class="product-counter">
                    <div class="counter-text">
                        <span class="counter-number">{{ $products->count() }}</span>
                        <span class="counter-label">{{ Str::plural('product', $products->count()) }} found</span>
                    </div>
                </div>
                
                <!-- Placeholder for future filter options -->
                <div class="filter-options">
                    <!-- Future filter options would go here -->
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
                                <img src="{{ $product->image_url }}" class="card-img-top" alt="{{ $product->name }}" style="width: 100%; height: auto;">
                            </a>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">
                                    <a href="{{ route('product.show', $product) }}">{{ $product->name }}</a>
                                </h5>
                                <p class="card-text">
                                    <!-- <strong>${{ number_format($product->price, 2) }}</strong> -->
                                    <!-- <span class="price-aed d-block">AED {{ number_format($product->price_aed, 2) }}</span> -->
                                </p>
                                <div class="card-footer">
                         
                                        <div class="d-flex align-items-center mb-2">
                                            <!-- <span class="badge bg-success me-3"><i class="fas fa-check me-1"></i>In Stock</span>
                                            <small class="text-muted">{{ $product->inventory->quantity }} available</small> -->
                                        </div>
                                        <form action="{{ route('cart.add', $product) }}" method="POST" class="w-100">
                                            @csrf
                                            <!-- <div class="quantity-controls">
                                                <label for="quantity-{{ $product->id }}">Quantity:</label>
                                                <input type="number" name="quantity" id="quantity-{{ $product->id }}" 
                                                    value="1" min="1" max="{{ $product->inventory->quantity }}" 
                                                    class="form-control">
                                            </div> -->
                                            <div class="button-group" style="max-width: 200px; margin: 0 auto;">
                                                <a href="{{ route('product.show', $product) }}" class="btn w-100 mb-2" style="background-color: #0a5694; color: white; font-size: 0.875rem; padding: 0.5rem 0.75rem;">
                                                    <i class="fas fa-eye me-2"></i> View Details
                                                </a>
                                                <a href="{{ route('quotation.form', $product) }}" class="btn w-100" style="background-color: #0a5694; color: white; font-size: 0.875rem; padding: 0.5rem 0.75rem;">
                                                    <i class="fas fa-file-invoice me-2"></i> Request Quote
                                                </a>
                                            </div>
                                        </form>
                                  
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
        
        // Add smooth fade-in effect to product cards
        const productCards = document.querySelectorAll('.product-card-wrapper');
        productCards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.4s ease, transform 0.5s ease';
            
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100 + (index * 50));
        });
    });
</script>
@endsection 