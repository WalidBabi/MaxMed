@extends('layouts.app')

@if(isset($seoData))
    @section('title', $seoData['title'])
    @section('meta_description', $seoData['meta_description'])
    @section('meta_keywords', $seoData['meta_keywords'])
@elseif(isset($category))
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

@php
    $currentCategory = $category ?? $subcategory ?? $subsubcategory ?? $subsubsubcategory ?? null;
    $isLabEssentials = $currentCategory && (str_contains($currentCategory->name, 'Lab Essentials') || str_contains($currentCategory->name, 'Tubes, Pipettes, Glassware'));
@endphp

{{-- Structured Data for Lab Essentials Category --}}
@if($isLabEssentials && isset($seoData))
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "CollectionPage",
  "name": "{{ $seoData['title'] }}",
  "description": "{{ $seoData['meta_description'] }}",
  "url": "{{ $seoData['canonical_url'] }}",
  "provider": {
    "@type": "Organization",
    "name": "MaxMed UAE",
    "url": "https://maxmedme.com",
    "telephone": "+971 55 460 2500",
    "address": {
      "@type": "PostalAddress",
      "addressCountry": "AE",
      "addressRegion": "Dubai"
    }
  },
  "mainEntity": {
    "@type": "ItemList",
    "name": "Laboratory Tubes, Pipettes & Glassware",
    "description": "Premium laboratory glassware, tubes, and pipettes for scientific research and analysis in Dubai UAE",
    "numberOfItems": {{ $products->total() ?? 0 }}
  }
}
</script>
@endif

<div class="container-fluid py-4">
    <style>
        /* Enhanced styles for Lab Essentials */
        .category-header {
            margin-bottom: 2rem;
            position: relative;
            border-left: 4px solid #171e60;
            padding-left: 15px;
        }
        
        @if($isLabEssentials)
        .lab-essentials-hero {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 8px;
            padding: 30px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }
        
        .lab-essentials-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="%23dee2e6" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>') repeat;
            opacity: 0.3;
            z-index: 1;
        }
        
        .lab-essentials-content {
            position: relative;
            z-index: 2;
        }
        
        .lab-essentials-title {
            font-size: 2rem;
            color: #171e60;
            margin-bottom: 1rem;
            font-weight: 600;
        }
        
        .lab-essentials-description {
            font-size: 1.1rem;
            color: #495057;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }
        
        .lab-essentials-features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            padding: 15px;
            background: white;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .feature-icon {
            width: 40px;
            height: 40px;
            background: #171e60;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            color: white;
            font-size: 18px;
        }
        @endif
        
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
            @if($isLabEssentials)
            <!-- Enhanced Lab Essentials Hero Section -->
            <div class="lab-essentials-hero">
                <div class="lab-essentials-content">
                    <h1 class="lab-essentials-title">Laboratory Tubes, Pipettes & Glassware in Dubai UAE</h1>
                    <p class="lab-essentials-description">
                        Premium laboratory essentials for scientific research and analysis. Our comprehensive range includes borosilicate glassware, precision pipettes, laboratory tubes, and essential consumables trusted by research institutions, hospitals, universities, and diagnostic centers across the UAE.
                    </p>
                    <div class="lab-essentials-features">
                        <div class="feature">
                            <span class="feature-icon">🔬</span>
                            <span>Borosilicate Glassware</span>
                        </div>
                        <div class="feature">
                            <span class="feature-icon">📏</span>
                            <span>Precision Pipettes</span>
                        </div>
                        <div class="feature">
                            <span class="feature-icon">🧪</span>
                            <span>Laboratory Tubes</span>
                        </div>
                        <div class="feature">
                            <span class="feature-icon">⚡</span>
                            <span>Fast Delivery</span>
                        </div>
                    </div>
                </div>
            </div>
            @elseif(isset($category) && $category->name === 'Molecular & Clinical Diagnostics')
            <!-- Enhanced Molecular & Clinical Diagnostics Hero Section -->
            <div class="category-hero molecular-diagnostics-hero">
                <div class="hero-content">
                    <h1 class="hero-title">Molecular & Clinical Diagnostic Equipment Dubai UAE</h1>
                    <p class="hero-description">
                        Advanced molecular and clinical diagnostic equipment for accurate medical testing and research applications. Professional PCR machines, real-time PCR systems, thermal cyclers, and molecular analysis instruments trusted by hospitals, research laboratories, and diagnostic centers across the Middle East.
                    </p>
                    <div class="hero-features">
                        <div class="feature">
                            <span class="feature-icon">🧬</span>
                            <span>PCR Machines</span>
                        </div>
                        <div class="feature">
                            <span class="feature-icon">⚡</span>
                            <span>Real-time PCR</span>
                        </div>
                        <div class="feature">
                            <span class="feature-icon">🔬</span>
                            <span>Molecular Analysis</span>
                        </div>
                        <div class="feature">
                            <span class="feature-icon">🏥</span>
                            <span>Clinical Diagnostics</span>
                        </div>
                    </div>
                </div>
            </div>
            @elseif(isset($category) && $category->name === 'Lab Equipment')
            <!-- Enhanced Lab Equipment Hero Section -->
            <div class="category-hero lab-equipment-hero">
                <div class="hero-content">
                    <h1 class="hero-title">Laboratory Equipment Dubai UAE | Scientific Instruments</h1>
                    <p class="hero-description">
                        Professional laboratory equipment and scientific instruments for analytical chemistry, materials testing, quality control, and research applications. Premium equipment from leading manufacturers for universities, research institutions, and industrial laboratories.
                    </p>
                    <div class="hero-features">
                        <div class="feature">
                            <span class="feature-icon">🔬</span>
                            <span>Analytical Equipment</span>
                        </div>
                        <div class="feature">
                            <span class="feature-icon">🧪</span>
                            <span>Research Instruments</span>
                        </div>
                        <div class="feature">
                            <span class="feature-icon">⚡</span>
                            <span>Quality Control</span>
                        </div>
                        <div class="feature">
                            <span class="feature-icon">🎓</span>
                            <span>University Grade</span>
                        </div>
                    </div>
                </div>
            </div>
            @elseif(isset($category) && $category->name === 'Medical Consumables')
            <!-- Enhanced Medical Consumables Hero Section -->
            <div class="category-hero medical-consumables-hero">
                <div class="hero-content">
                    <h1 class="hero-title">Medical Consumables Dubai UAE | Healthcare Supplies</h1>
                    <p class="hero-description">
                        High-quality medical consumables and healthcare supplies for healthcare facilities, hospitals, and medical centers. Sterile products, diagnostic kits, medical devices, and clinical supplies for patient care and clinical applications.
                    </p>
                    <div class="hero-features">
                        <div class="feature">
                            <span class="feature-icon">🏥</span>
                            <span>Sterile Products</span>
                        </div>
                        <div class="feature">
                            <span class="feature-icon">🔬</span>
                            <span>Diagnostic Kits</span>
                        </div>
                        <div class="feature">
                            <span class="feature-icon">⚡</span>
                            <span>Medical Devices</span>
                        </div>
                        <div class="feature">
                            <span class="feature-icon">🛡️</span>
                            <span>Clinical Supplies</span>
                        </div>
                    </div>
                </div>
            </div>
            @elseif(isset($category) && $category->name === 'Life Science & Research')
            <!-- Enhanced Life Science & Research Hero Section -->
            <div class="category-hero life-science-hero">
                <div class="hero-content">
                    <h1 class="hero-title">Life Science Research Equipment Dubai UAE</h1>
                    <p class="hero-description">
                        Comprehensive life science and research equipment for biological and chemical analysis, cell culture, protein research, and molecular biology applications. Professional research equipment for universities, biotechnology companies, and research institutions.
                    </p>
                    <div class="hero-features">
                        <div class="feature">
                            <span class="feature-icon">🧬</span>
                            <span>Biological Analysis</span>
                        </div>
                        <div class="feature">
                            <span class="feature-icon">🧪</span>
                            <span>Cell Culture</span>
                        </div>
                        <div class="feature">
                            <span class="feature-icon">⚡</span>
                            <span>Protein Research</span>
                        </div>
                        <div class="feature">
                            <span class="feature-icon">🔬</span>
                            <span>Molecular Biology</span>
                        </div>
                    </div>
                </div>
            </div>
            @elseif(isset($category) && $category->name === 'Technology & AI Solutions')
            <!-- Enhanced Technology & AI Solutions Hero Section -->
            <div class="category-hero technology-ai-hero">
                <div class="hero-content">
                    <h1 class="hero-title">Technology & AI Solutions Dubai UAE | Laboratory Automation</h1>
                    <p class="hero-description">
                        Cutting-edge technology and AI solutions for laboratory automation in Dubai UAE. Smart laboratory equipment, automated systems, and artificial intelligence applications for research and diagnostic laboratories.
                    </p>
                    <div class="hero-features">
                        <div class="feature">
                            <span class="feature-icon">🤖</span>
                            <span>AI Solutions</span>
                        </div>
                        <div class="feature">
                            <span class="feature-icon">⚡</span>
                            <span>Laboratory Automation</span>
                        </div>
                        <div class="feature">
                            <span class="feature-icon">🔬</span>
                            <span>Smart Equipment</span>
                        </div>
                        <div class="feature">
                            <span class="feature-icon">📊</span>
                            <span>Advanced Technology</span>
                        </div>
                    </div>
                </div>
            </div>
            @elseif(isset($category) && $category->name === 'Rapid Test Kits RDT')
            <!-- Enhanced Rapid Test Kits Hero Section -->
            <div class="category-hero rapid-tests-hero">
                <div class="hero-content">
                    <h1 class="hero-title">Rapid Test Kits Dubai UAE | Point of Care Testing</h1>
                    <p class="hero-description">
                        Rapid diagnostic test kits and point-of-care testing solutions for healthcare facilities and clinical laboratories. Professional rapid test kits for infectious diseases, cardiac markers, tumor markers, and various medical conditions.
                    </p>
                    <div class="hero-features">
                        <div class="feature">
                            <span class="feature-icon">⚡</span>
                            <span>Rapid Testing</span>
                        </div>
                        <div class="feature">
                            <span class="feature-icon">🏥</span>
                            <span>Point of Care</span>
                        </div>
                        <div class="feature">
                            <span class="feature-icon">🔬</span>
                            <span>Diagnostic Kits</span>
                        </div>
                        <div class="feature">
                            <span class="feature-icon">🩺</span>
                            <span>Clinical Testing</span>
                        </div>
                    </div>
                </div>
            </div>
            @elseif(isset($category) && $category->name === 'Analytical Instruments')
            <!-- Enhanced Analytical Instruments Hero Section -->
            <div class="category-hero analytical-instruments-hero">
                <div class="hero-content">
                    <h1 class="hero-title">Analytical Instruments Dubai UAE | Laboratory Analysis Equipment</h1>
                    <p class="hero-description">
                        Advanced analytical instruments and laboratory analysis equipment for chemical analysis, spectroscopy, chromatography, and quality control applications. Professional analytical equipment for research laboratories and industrial testing.
                    </p>
                    <div class="hero-features">
                        <div class="feature">
                            <span class="feature-icon">🔬</span>
                            <span>Chemical Analysis</span>
                        </div>
                        <div class="feature">
                            <span class="feature-icon">📊</span>
                            <span>Spectroscopy</span>
                        </div>
                        <div class="feature">
                            <span class="feature-icon">⚡</span>
                            <span>Chromatography</span>
                        </div>
                        <div class="feature">
                            <span class="feature-icon">🎯</span>
                            <span>Quality Control</span>
                        </div>
                    </div>
                </div>
            </div>
            @elseif(isset($category) && $category->name === 'Veterinary')
            <!-- Enhanced Veterinary Hero Section -->
            <div class="category-hero veterinary-hero">
                <div class="hero-content">
                    <h1 class="hero-title">Veterinary Equipment Dubai UAE | Animal Diagnostic Tools</h1>
                    <p class="hero-description">
                        Veterinary equipment and animal diagnostic tools for veterinary clinics and animal research facilities. Professional veterinary diagnostic equipment, animal testing kits, and veterinary supplies for animal healthcare and research.
                    </p>
                    <div class="hero-features">
                        <div class="feature">
                            <span class="feature-icon">🐾</span>
                            <span>Animal Diagnostics</span>
                        </div>
                        <div class="feature">
                            <span class="feature-icon">🏥</span>
                            <span>Veterinary Equipment</span>
                        </div>
                        <div class="feature">
                            <span class="feature-icon">🔬</span>
                            <span>Testing Kits</span>
                        </div>
                        <div class="feature">
                            <span class="feature-icon">⚡</span>
                            <span>Animal Healthcare</span>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <!-- Default Category Header -->
            <div class="category-header">
                <h1 class="category-title">{{ isset($category) ? $category->name : 'All Products' }}</h1>
                <p class="category-description">{{ isset($category) && $category->description ? $category->description : 'Browse our selection of high-quality products' }}</p>
            </div>
            @endif
            
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