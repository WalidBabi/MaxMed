@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <style>
        /* Enhanced Product Card Styling */
        .product-card {
            height: 100%;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-radius: 8px;
            overflow: hidden;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            border: 1px solid #eaeaea;
        }
        
        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .product-image-container {
            position: relative;
            padding-top: 75%;
            overflow: hidden;
            background-color: #f8f9fa;
        }
        
        .product-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 10px;
            transition: transform 0.5s ease;
        }
        
        .product-card:hover .product-image {
            transform: scale(1.05);
        }
        
        .product-title {
            font-size: 1rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            height: 2.5rem;
        }
        
        .product-description {
            color: #666;
            margin-bottom: 1rem;
            font-size: 0.875rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            height: 3.75rem;
        }
        
        .category-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
            background-color: #f0f0f0;
            color: #666;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
        }
        
        .industry-heading {
            position: relative;
            border-left: 4px solid #0064a8;
            padding: 20px;
            margin-bottom: 2rem;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .industry-title {
            font-size: 2rem;
            font-weight: 700;
            color: #0064a8;
            margin-bottom: 0.75rem;
        }
        
        .industry-subtitle {
            font-size: 1.1rem;
            color: #555;
            margin-bottom: 1.5rem;
        }
        
        .industry-description {
            font-size: 1rem;
            color: #666;
            margin-bottom: 2rem;
            max-width: 900px;
            line-height: 1.6;
        }
        
        .breadcrumb {
            background-color: transparent;
            padding: 0.75rem 0;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .breadcrumb-item a {
            color: #0064a8;
            text-decoration: none;
            transition: color 0.2s ease;
        }
        
        .breadcrumb-item a:hover {
            color: #00a9e0;
        }
        
        .breadcrumb-item.active {
            color: #333;
            font-weight: 600;
        }
    </style>
    
    <div class="row sidebar-content-container justify-content-center">
        <div class="col-md-3 sidebar-column transition-all duration-300">
            @include('layouts.sidebar')
        </div>
        <div class="col-md-9 mt-2 main-content-column transition-all duration-300">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('welcome') }}"><i class="fas fa-home me-1"></i>Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $industryCategory }}</li>
                </ol>
            </nav>
            
            <div class="industry-heading">
                <h1 class="industry-title">{{ $industryCategory }} Solutions</h1>
                <h2 class="industry-subtitle">Specialized Products for Your Industry Needs</h2>
                <p class="industry-description">
                    @php
                        $descriptions = [
                            // Healthcare & Medical Facilities subcategories
                            'clinics' => 'Tailored solutions for outpatient care facilities, diagnostic centers, and specialized medical practices. Our products help clinics deliver exceptional patient care with reliable equipment for diagnostics, treatment, and monitoring.',
                            'hospitals' => 'Comprehensive equipment and supplies for every department in hospital and acute care settings. From surgical instruments to patient monitoring systems, we support the critical work of hospital professionals.',
                            'veterinary' => 'Specialized equipment and supplies for animal healthcare and veterinary practices. Our veterinary solutions are designed to support veterinarians in delivering quality care to animals of all sizes.',
                            'medical-labs' => 'Advanced equipment and supplies for clinical diagnostic and medical testing laboratories. Our solutions support accurate and efficient testing for healthcare facilities of all sizes.',
                            
                            // Scientific & Research Institutions subcategories
                            'research-labs' => 'Comprehensive equipment and supplies for multi-disciplinary research facilities and dedicated laboratories. Our precision instruments support groundbreaking research across scientific disciplines.',
                            'academia' => 'Teaching and research equipment for academic institutions at all educational levels. From student-grade instruments to advanced research equipment, we support academic excellence.',
                            'biotech-pharma' => 'Specialized equipment for biotechnology research and pharmaceutical development. Our solutions support innovation in drug discovery, bioprocessing, and quality control.',
                            'forensic' => 'Precise equipment and supplies for forensic analysis and investigation. Our forensic tools support accurate evidence collection and analysis for law enforcement and legal proceedings.',
                            
                            // Specialized Testing & Diagnostics subcategories
                            'environment' => 'Precision instruments for water, soil, and air quality analysis. Our environmental testing equipment supports reliable monitoring and analysis of environmental parameters.',
                            'food' => 'Specialized tools for food composition, contaminant, and pathogen testing. Our food safety solutions ensure compliance with regulatory standards and protect consumer health.',
                            'material' => 'Equipment for analyzing material properties, composition, and performance. Our material testing solutions support quality control and research across industries.',
                            'cosmetic' => 'Specialized tools for cosmetic product testing and skin analysis. Our cosmetic testing equipment ensures product safety and efficacy in the beauty industry.',
                            
                            // Government & Regulatory Bodies subcategories
                            'public-health' => 'Equipment and supplies for public health monitoring, disease surveillance, and population health management. Our solutions support governments in protecting and improving public health.',
                            'military-defense' => 'Specialized equipment for military medical research, field applications, and defense healthcare systems. Our solutions support military healthcare and research initiatives.',
                            'regulatory' => 'Equipment and systems for healthcare regulation, standards enforcement, and quality assurance. Our regulatory solutions ensure compliance with healthcare standards.',
                            
                            // Emerging & AI-driven Healthcare subcategories
                            'telemedicine' => 'Advanced equipment and tools for remote patient monitoring, virtual consultations, and digital diagnostics. Our telemedicine solutions support healthcare delivery beyond traditional settings.',
                            'ai-medical' => 'Equipment and systems for developing and implementing AI algorithms in medical diagnosis and treatment. Our AI solutions support the future of healthcare technology.'
                        ];
                        echo $descriptions[$industryFor] ?? 'Explore our specialized collection of products tailored for your specific industry needs. MaxMed provides high-quality equipment and supplies that meet the unique requirements of your professional environment.';
                    @endphp
                </p>
            </div>
            
            <!-- Products Display -->
            <div class="row">
                @if($products->count() > 0)
                    @foreach($products as $product)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="product-card h-100">
                                <a href="{{ route('products.show', $product) }}" class="text-decoration-none">
                                    <div class="product-image-container">
                                        <img src="{{ $product->image_url }}" class="product-image" alt="{{ $product->name }}">
                                    </div>
                                    <div class="card-body">
                                        <h3 class="product-title">{{ $product->name }}</h3>
                                        <p class="product-description">{{ Str::limit(strip_tags($product->description), 100) }}</p>
                                        @if($product->category)
                                            <span class="category-badge">{{ $product->category->name }}</span>
                                        @endif
                                    </div>
                                </a>
                            </div>
                        </div>
                    @endforeach
                    
                    <div class="col-12 mt-4">
                        {{ $products->links() }}
                    </div>
                @else
                    <div class="col-12 text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-search fa-3x text-muted"></i>
                        </div>
                        <h3 class="h4 mb-3">No products found for this category</h3>
                        <p class="text-muted">We're constantly updating our inventory. Please check back soon or browse our other categories.</p>
                        <div class="mt-4">
                            <a href="{{ route('products.index') }}" class="btn btn-primary">View All Products</a>
                            <a href="{{ route('contact') }}" class="btn btn-outline-primary ms-2">Contact Us for Assistance</a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    // Animation for product cards
    document.addEventListener('DOMContentLoaded', function() {
        const productCards = document.querySelectorAll('.product-card');
        
        if (productCards.length > 0) {
            productCards.forEach((card, index) => {
                setTimeout(() => {
                    card.classList.add('show');
                }, 100 * index);
            });
        }
    });
</script>
@endsection 