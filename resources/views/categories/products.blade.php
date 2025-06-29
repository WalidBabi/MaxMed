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

@if(isset($emptyCategory))
    @section('meta_robots', 'noindex, follow')
@endif

@section('content')
<div class="container-fluid py-4">
    <style>
        /* Page Layout */
        .category-header {
            margin-bottom: 2rem;
            position: relative;
            border-left: 4px solid #171e60;
            padding-left: 15px;
        }
        .category-title {
            font-size: 1.5rem;
            color: #171e60;
            margin-bottom: 0.75rem;
            font-weight: 600;
        }
        .category-description {
            color: #7f8c8d;
            margin-bottom: 1rem;
            font-size: 0.95rem;
            max-width: 800px;
            line-height: 1.6;
        }
        
        /* Professional grid layout */
        .masonry-layout {
            margin-top: 30px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
        }
        
        /* Professional card styling */
        .masonry-item {
            display: block;
            width: 100%;
            margin-bottom: 0;
            height: 100%;
        }
        
        .product-item {
            position: relative;
            border-radius: 4px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            transition: all 0.2s ease;
            background-color: #fff;
            height: 100%;
            display: flex;
            flex-direction: column;
            border: 1px solid #eaeaea;
        }
        
        .product-item:hover {
            transform: none;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        
        .product-image-container {
            position: relative;
            overflow: hidden;
            width: 100%;
            padding-bottom: 75%; /* 4:3 aspect ratio */
            background: #f8f8f8;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .product-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: contain;
            transition: transform 0.3s ease;
        }
        
        .product-item:hover .product-image {
            transform: scale(1.03);
        }
        
        .product-content {
            padding: 20px;
            display: flex;
            flex-direction: column;
            background-color: #fff;
            flex-grow: 1;
        }
        
        .product-title {
            font-size: 0.95rem;
            font-weight: 500;
            margin: 0 0 20px 0;
            color: #333;
            line-height: 1.5;
            text-align: left;
            height: 45px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
        
        .product-title a {
            color: #2c3e50;
            text-decoration: none;
        }
        
        .product-title a:hover {
            color: #3498db;
        }
        
        .product-actions {
            display: flex;
            gap: 8px;
            width: 100%;
            margin-top: auto;
        }
        
        .btn-action {
            padding: 8px 12px;
            border-radius: 3px;
            font-size: 0.8rem;
            font-weight: 500;
            transition: all 0.2s;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        
        .btn-view {
            background-color: #f8f9fa;
            color: #171e60;
            border: 1px solid #dee2e6;
            flex: 1;
        }
        
        .btn-view:hover {
            background-color: #e9ecef;
            color: #0a5694;
        }
        
        .btn-quote {
            background-color: #0a5694;
            color: white;
            flex: 1;
        }
        
        .btn-quote:hover {
            background-color: #171e60;
        }
        
        /* Featured badge styling */
        .product-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            background: rgba(255, 255, 255, 0.95);
            color: #171e60;
            padding: 5px 10px;
            border-radius: 2px;
            font-size: 0.7rem;
            font-weight: 500;
            letter-spacing: 0.5px;
            z-index: 2;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        /* Responsive styles */
        @media (max-width: 1200px) {
            .masonry-layout {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        
        @media (max-width: 992px) {
            .masonry-layout {
                grid-template-columns: repeat(2, 1fr);
                gap: 16px;
            }
        }
        
        @media (max-width: 768px) {
            .masonry-layout {
                grid-template-columns: repeat(2, 1fr);
                gap: 16px;
            }
            .product-content {
                padding: 15px;
            }
        }
        
        @media (max-width: 576px) {
            .masonry-layout {
                grid-template-columns: 1fr;
            }
        }
        
        /* Professional filters styling */
        .filters-row {
            background-color: #f8f9fa;
            padding: 18px 25px;
            border-radius: 4px;
            margin-bottom: 30px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            border-left: 4px solid #0a5694;
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
        
        /* Pagination tip styling */
        .pagination-tip {
            border-left: 4px solid #17a2b8;
            border-radius: 8px;
            padding: 15px 20px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
            animation: fadeInDown 0.5s ease-out;
        }
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
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
                <div class="masonry-layout">
                    @foreach($products as $index => $product)
                        @php
                            // Determine if this is a featured product (first or every 7th)
                            $isFeatured = ($index === 0 || $index % 7 === 0);
                        @endphp
                        
                        <div class="masonry-item">
                            <div class="product-item">
                                @if($isFeatured)
                                    <div class="product-badge">Featured</div>
                                @endif
                                
                                <div class="product-image-container">
                                    <img src="{{ $product->image_url }}" class="product-image" alt="{{ $product->name }}">
                                </div>
                                
                                <div class="product-content">
                                    <h3 class="product-title">
                                        <a href="{{ route('product.show', $product) }}">{{ $product->name }}</a>
                                    </h3>
                                    
                                    <div class="product-actions">
                                        <a href="{{ route('product.show', $product) }}" class="btn-action btn-view">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <a href="{{ route('quotation.form', ['product' => $product->slug]) }}" class="btn-action btn-quote">
                                            <i class="fas fa-file-invoice"></i> Quote
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
            
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
        // Add staggered animation to product items
        const masonryItems = document.querySelectorAll('.masonry-item');
        masonryItems.forEach((item, index) => {
            item.style.opacity = '0';
            item.style.transform = 'translateY(30px)';
            item.style.transition = 'opacity 0.5s ease, transform 0.6s ease';
            
            setTimeout(() => {
                item.style.opacity = '1';
                item.style.transform = 'translateY(0)';
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
    });
</script>
@endsection