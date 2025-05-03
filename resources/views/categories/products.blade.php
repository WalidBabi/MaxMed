@extends('layouts.app')

@if(isset($category))
    @section('title', $category->name . ' Laboratory Equipment | MaxMed UAE')
    @section('meta_description', 'Browse ' . $category->name . ' laboratory equipment at MaxMed UAE. Find high-quality scientific instruments and lab supplies in Dubai.')
@elseif(isset($subcategory))
    @section('title', $subcategory->name . ' Laboratory Equipment | MaxMed UAE')
    @section('meta_description', 'Browse ' . $subcategory->name . ' laboratory equipment at MaxMed UAE. Find high-quality scientific instruments and lab supplies in Dubai.')
@elseif(isset($subsubcategory))
    @section('title', $subsubcategory->name . ' Laboratory Equipment | MaxMed UAE')
    @section('meta_description', 'Browse ' . $subsubcategory->name . ' laboratory equipment at MaxMed UAE. Find high-quality scientific instruments and lab supplies in Dubai.')
@elseif(isset($subsubsubcategory))
    @section('title', $subsubsubcategory->name . ' Laboratory Equipment | MaxMed UAE')
    @section('meta_description', 'Browse ' . $subsubsubcategory->name . ' laboratory equipment at MaxMed UAE. Find high-quality scientific instruments and lab supplies in Dubai.')
@endif

@if(isset($emptyCategory) || (isset($products) && $products->isEmpty()))
    @section('meta_robots', 'noindex, follow')
@endif

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
            font-size: 0.875rem;
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
            font-weight: 700;
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
            font-weight: 600;
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
        
        /* NEW PRODUCT CARD STYLING */
        /* Improved Product Grid Layout */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 25px;
            margin-top: 25px;
        }
        
        /* Product Card Wrapper */
        .product-card-wrapper {
            height: 100%;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        /* Product Card */
        .product-card {
            height: 100%;
            border-radius: 12px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            border: none !important;
            background-color: #fff;
            position: relative;
            transition: all 0.35s;
        }
        
        .product-card:hover {
            transform: translateY(-7px);
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.15);
        }
        
        /* Image Container with Fixed Aspect Ratio */
        .product-image-container {
            position: relative;
            width: 100%;
            padding-top: 100%; /* 1:1 Aspect Ratio */
            overflow: hidden;
        }
        
        .product-card img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: contain; /* Changed from cover to contain */
            background-color: #f9f9f9; /* Light background for image container */
            transition: transform 0.5s ease;
        }
        
        .product-card:hover img {
            transform: scale(1.08);
        }
        
        /* Card Body */
        .product-card .card-body {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 1.5rem;
            position: relative;
        }
        
        /* Card Title */
        .product-card .card-title {
            margin-bottom: 0.75rem;
            font-size: 1.1rem;
            font-weight: 600;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            height: 3rem; /* Fixed height for title (2 lines) */
        }
        
        .product-card .card-title a {
            text-decoration: none;
            color: #222;
            transition: color 0.2s;
        }
        
        .product-card .card-title a:hover {
            color: #28a745;
        }
        
        /* Card Footer */
        .product-card .card-footer {
            background-color: transparent;
            border-top: none;
            padding: 0;
            margin-top: auto; /* Push footer to bottom of card */
        }
        
        /* Button Group */
        .product-card .button-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
            width: 100%;
        }
        
        /* Action Buttons */
        .product-card .btn {
            border-radius: 8px;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            transition: all 0.35s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .product-card .btn i {
            font-size: 0.9rem;
        }
        
        /* View Details Button */
        .product-card .btn-view {
            background-color: #0a5694;
            color: white;
        }
        
        .product-card .btn-view:hover {
            background-color: #084476;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(10, 86, 148, 0.3);
        }
        
        /* Quote Button */
        .product-card .btn-quote {
            background-color: #28a745;
            color: white;
        }
        
        .product-card .btn-quote:hover {
            background-color: #218838;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
        }
        
        /* Responsive Adjustments */
        @media (max-width: 992px) {
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
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
        }
        
        @media (max-width: 576px) {
            .products-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }
            .product-card .card-title {
                font-size: 0.95rem;
            }
            .product-card .btn {
                padding: 0.4rem 0.6rem;
                font-size: 0.8rem;
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
        
        @media (max-width: 480px) {
            .products-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    
   
    
    <div class="row sidebar-content-container justify-content-center">
        <div class="col-md-3 sidebar-column transition-all duration-300">
            @include('layouts.sidebar')
        </div>
        <div class="col-md-9 mt-2 main-content-column transition-all duration-300">
            <div class="category-header">
                <h1 class="category-title">{{ isset($category) ? $category->name : 'All Products' }}</h1>
                <p class="category-description">{{ isset($category) && $category->description ? $category->description : 'Browse our selection of high-quality products' }}</p>
           
            </div>
            
            
            <div class="filters-row">
                <div class="product-counter">
                    <div class="counter-text">
                        <span class="counter-number">{{ $products->total() }}</span>
                        <span class="counter-label">{{ Str::plural('product', $products->total()) }} found</span>
                    </div>
                </div>
            </div>
            
            @if(session()->missing('pagination_message_shown') && $products->hasPages())
            <div class="alert alert-info mb-4 pagination-tip" role="alert">
                <i class="fas fa-info-circle mr-2"></i>
                Not all products are displayed on this page. Use the pagination controls at the bottom to navigate through more products.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {{ session(['pagination_message_shown' => true]) }}
            @endif
            
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
                            <a href="{{ route('product.show', $product) }}" class="product-image-container">
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
                            </a>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">
                                    <a href="{{ route('product.show', $product) }}">{{ $product->name }}</a>
                                </h5>
                                <div class="card-footer">
                                    <div class="button-group">
                                        <a href="{{ route('product.show', $product) }}" class="btn btn-view">
                                            <i class="fas fa-eye"></i> View Details
                                        </a>
                                        <a href="{{ route('quotation.form', ['product' => $product->id]) }}" class="btn btn-quote">
                                            <i class="fas fa-file-invoice"></i> Request Quote
                                        </a>
                                    </div>
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
        
        // Make alert dismissible
        const closeButton = document.querySelector('.pagination-tip .close');
        if (closeButton) {
            closeButton.addEventListener('click', function() {
                const alert = this.closest('.alert');
                if (alert) {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-20px)';
                    setTimeout(() => {
                        alert.remove();
                    }, 300);
                }
            });
        }
        
        // Sidebar toggle is now handled with CSS transitions in app.blade.php and sidebar.blade.php
    });
</script>
@endsection