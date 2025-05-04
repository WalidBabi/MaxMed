@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <style>
        /* Modern Product Detail Page Styling */
        :root {
            --main-color: #171e60;
            --auxiliary-color: #0a5694;
            --accent-color: #28a745;
            --white: #ffffff;
            --light-gray: #f7f7f7;
            --medium-gray: #e0e0e0;
            --dark-gray: #333333;
            --box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        /* Breadcrumb Navigation */
        .breadcrumb-container {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            position: relative;
        }
        
        .breadcrumb-container::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 1px;
            background: linear-gradient(to right, var(--light-gray), var(--medium-gray), var(--light-gray));
        }
        
        .breadcrumb {
            background-color: transparent;
            padding: 0;
            margin: 0;
            font-size: 0.95rem;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
        }

        .breadcrumb-item a {
            color: var(--auxiliary-color);
            text-decoration: none;
            transition: var(--transition);
            position: relative;
        }

        .breadcrumb-item a:hover {
            color: var(--main-color);
        }
        
        .breadcrumb-item a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -3px;
            left: 0;
            background-color: var(--main-color);
            transition: width 0.3s;
        }
        
        .breadcrumb-item a:hover::after {
            width: 100%;
        }

        .breadcrumb-item.active {
            color: var(--main-color);
            font-weight: 600;
        }
        
        .breadcrumb-item + .breadcrumb-item::before {
            color: var(--medium-gray);
        }

        /* Product Header */
        .product-header {
            margin-bottom: 2.5rem;
            position: relative;
            border-left: 4px solid var(--main-color);
            padding-left: 15px;
            animation: fadeIn 0.8s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .product-title {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--main-color);
            margin-bottom: 0.5rem;
            transition: var(--transition);
        }

        /* Product Container */
        .product-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2.5rem;
            margin-bottom: 3rem;
        }
        
        @media (max-width: 992px) {
            .product-container {
                grid-template-columns: 1fr;
            }
        }

        /* Image Gallery Section */
        .gallery-section {
            position: relative;
            background-color: var(--white);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--box-shadow);
            transition: var(--transition);
        }
        
        .gallery-section:hover {
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .product-image-container {
            position: relative;
            padding-bottom: 75%;
            overflow: hidden;
            background-color: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: contain;
            transition: transform 0.6s ease;
            padding: 20px;
        }

        .product-image:hover {
            transform: scale(1.03);
        }

        .small-img-container {
            position: relative;
            padding: 20px;
            background-color: var(--light-gray);
            border-top: 1px solid var(--medium-gray);
        }

        .small-img-group {
            display: flex;
            gap: 12px;
            overflow-x: auto;
            padding: 5px;
            scrollbar-width: thin;
            scrollbar-color: var(--auxiliary-color) var(--light-gray);
            -webkit-overflow-scrolling: touch;
        }

        .small-img-group::-webkit-scrollbar {
            height: 5px;
        }

        .small-img-group::-webkit-scrollbar-track {
            background: var(--light-gray);
            border-radius: 10px;
        }

        .small-img-group::-webkit-scrollbar-thumb {
            background: var(--auxiliary-color);
            border-radius: 10px;
        }

        .scroll-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background-color: var(--main-color);
            color: white;
            border: none;
            opacity: 0.9;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.15);
            transition: var(--transition);
        }

        .scroll-btn:hover {
            opacity: 1;
            transform: translateY(-50%) scale(1.1);
        }

        .scroll-btn.left {
            left: 10px;
        }

        .scroll-btn.right {
            right: 10px;
        }

        .small-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            cursor: pointer;
            border-radius: 8px;
            border: 2px solid transparent;
            padding: 3px;
            transition: var(--transition);
            flex-shrink: 0;
            background-color: var(--white);
        }

        .small-img:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        }

        .small-img.active {
            border-color: var(--main-color);
            box-shadow: 0 5px 10px rgba(23, 30, 96, 0.25);
        }

        /* Content Section */
        .content-section {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .section-card {
            background-color: var(--white);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--box-shadow);
            transition: var(--transition);
        }
        
        .section-card:hover {
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            transform: translateY(-5px);
        }

        .card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--light-gray);
            background-color: var(--white);
            position: relative;
        }
        
        .card-header h4 {
            margin: 0;
            color: var(--main-color);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .card-header h4::before {
            content: '';
            display: block;
            width: 4px;
            height: 20px;
            background-color: var(--auxiliary-color);
            border-radius: 2px;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Action Section */
        .action-section {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .btn-quotation {
            background: linear-gradient(135deg, var(--auxiliary-color), var(--main-color));
            border: none;
            color: var(--white);
            padding: 14px 25px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            font-size: 1rem;
            border-radius: 8px;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            z-index: 1;
        }

        .btn-quotation::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255,255,255,0.2), transparent);
            transition: transform 0.6s;
            z-index: -1;
        }

        .btn-quotation:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(23, 30, 96, 0.25);
            color: var(--white);
        }

        .btn-quotation:hover::before {
            transform: translateX(100%);
        }

        /* Size Options */
        .size-options {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .size-option {
            min-width: 60px;
            padding: 8px 15px;
            border: 2px solid var(--medium-gray);
            background-color: var(--white);
            transition: var(--transition);
            cursor: pointer;
            border-radius: 6px;
            font-weight: 500;
            text-align: center;
        }

        .size-option:hover {
            border-color: var(--auxiliary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .size-option.active {
            background-color: var(--main-color) !important;
            color: white;
            border-color: var(--main-color);
            box-shadow: 0 4px 8px rgba(23, 30, 96, 0.25);
        }

        /* Product Description */
        .product-description p {
            color: var(--dark-gray);
            line-height: 1.7;
            margin-bottom: 0;
        }

        /* Product Documentation */
        .pdf-documentation {
            background-color: rgba(23, 30, 96, 0.05);
            border-radius: 8px;
            padding: 15px;
            display: flex;
            align-items: center;
            gap: 15px;
            transition: var(--transition);
        }
        
        .pdf-documentation:hover {
            background-color: rgba(23, 30, 96, 0.08);
        }
        
        .pdf-icon {
            color: #e74c3c;
            font-size: 2rem;
            flex-shrink: 0;
        }
        
        .pdf-content {
            flex-grow: 1;
        }
        
        .pdf-content h6 {
            margin-bottom: 8px;
            color: var(--main-color);
            font-weight: 600;
        }
        
        .btn-pdf {
            background-color: transparent;
            color: var(--auxiliary-color);
            border: 1px solid var(--auxiliary-color);
            border-radius: 6px;
            padding: 6px 12px;
            font-size: 0.85rem;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .btn-pdf:hover {
            background-color: var(--auxiliary-color);
            color: var(--white);
            transform: translateY(-2px);
        }

        /* Technical Specifications */
        .specifications-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .specifications-table tr {
            transition: var(--transition);
        }
        
        .specifications-table tr:hover {
            background-color: rgba(10, 86, 148, 0.05);
        }
        
        .specifications-table td {
            padding: 12px 15px;
            border-bottom: 1px solid var(--light-gray);
        }
        
        .specifications-table td:first-child {
            font-weight: 600;
            color: var(--main-color);
            width: 40%;
        }
        
        .specifications-table tr:last-child td {
            border-bottom: none;
        }

        /* Specification Image */
        .specifications-image {
            width: 100%;
            border-radius: 8px;
            transition: var(--transition);
            cursor: pointer;
        }
        
        .specifications-image:hover {
            transform: scale(1.01);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        /* Image Magnifier Glass */
        #img-magnifier-glass {
            position: absolute;
            border: 3px solid var(--main-color);
            border-radius: 50%;
            cursor: none;
            width: 100px;
            height: 100px;
            display: none;
            z-index: 100;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        
        /* Magnifier Display Panel */
        .magnifier-panel {
            width: 400px;
            height: 400px;
            border: 3px solid var(--main-color);
            border-radius: 8px;
            overflow: hidden;
            position: fixed;
            top: 42%;
            left: 85%;
            transform: translate(-50%, -50%);
            background-repeat: no-repeat;
            background-color: var(--white);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            display: none;
            z-index: 1000;
        }

        /* Responsive Adjustments */
        @media (max-width: 992px) {
            .product-title {
                font-size: 1.8rem;
            }
            
            .product-container {
                gap: 2rem;
            }
        }

        @media (max-width: 768px) {
            .product-image-container {
                padding-bottom: 75%;
            }

            .small-img {
                width: 60px;
                height: 60px;
            }
            
            .btn-quotation {
                padding: 12px 20px;
            }
        }

        @media (max-width: 576px) {
            .product-header {
                margin-bottom: 1.5rem;
            }
            
            .product-title {
                font-size: 1.5rem;
            }
            
            .product-container {
                gap: 1.5rem;
            }
            
            .card-body {
                padding: 1.25rem;
            }
        }
    </style>

    <!-- Floating magnifier panel -->
    <div class="magnifier-panel" id="magnifier-panel"></div>

    <div class="breadcrumb-container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('welcome') }}"><i class="fas fa-home me-1"></i>Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
                @if($product->category)
                <li class="breadcrumb-item"><a href="{{ route('categories.show', $product->category) }}">{{ $product->category->name }}</a></li>
                @endif
                <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
            </ol>
        </nav>
    </div>

    <div class="product-header">
        <h1 class="product-title">{{ $product->name }}</h1>
    </div>

    <div class="product-container">
        <!-- Image Gallery Section -->
        <div class="gallery-section">
            <div class="product-image-container">
                <img src="{{ $product->image_url }}" class="product-image" id="product-image" alt="{{ $product->name }}">
                <div id="img-magnifier-glass"></div>
            </div>
            
            @if($product->images && $product->images->count() > 0)
            <div class="small-img-container">
                <button class="scroll-btn left" id="scroll-left" aria-label="Scroll left">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <div class="small-img-group" id="img-gallery">
                    <!-- Main product image in thumbnails -->
                    <img src="{{ $product->image_url }}" class="small-img active" 
                         alt="{{ $product->name }}" onclick="changeImage('{{ $product->image_url }}')">
                         
                    <!-- Additional non-primary images -->
                    @foreach($product->images->where('is_primary', false)->filter(function($image) {
                        return empty($image->specification_image_url);
                    }) as $image)
                    <img src="{{ $image->image_url }}" class="small-img" 
                         alt="{{ $product->name }}" onclick="changeImage('{{ $image->image_url }}')">
                    @endforeach
                </div>
                <button class="scroll-btn right" id="scroll-right" aria-label="Scroll right">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            @endif
        </div>

        <!-- Content Section -->
        <div class="content-section">
            <!-- Action Section -->
            <div class="section-card">
                <div class="card-body action-section">
                    <a href="{{ route('quotation.form', ['product' => $product->id]) }}" class="btn btn-quotation" id="quotation-btn">
                        <i class="fas fa-file-invoice"></i> Request Quotation
                    </a>
                    
                    @if($product->has_size_options)
                    <div class="mt-3">
                        <h5 class="mb-3">Select Size</h5>
                        <div class="size-options">
                            @php
                                $sizeOptions = $product->size_options ?? [];
                                if (!is_array($sizeOptions) && !empty($sizeOptions)) {
                                    $sizeOptions = json_decode($sizeOptions, true) ?? [];
                                }
                            @endphp
                            
                            @foreach($sizeOptions as $option)
                                <label class="size-option">
                                    <input type="radio" name="size" value="{{ $option }}" class="d-none"> {{ $option }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Description Section -->
            <div class="section-card">
                <div class="card-header">
                    <h4>Description</h4>
                </div>
                <div class="card-body product-description">
                    @php
                    // Check if the description contains PRODUCT PARAMETERS
                    $description = $product->description;
                    $hasParameters = strpos($description, 'PRODUCT PARAMETERS') !== false;
                    
                    if ($hasParameters) {
                        // Split the content
                        $parts = explode('PRODUCT PARAMETERS', $description, 2);
                        $mainDescription = trim($parts[0]);
                        $parametersSection = trim($parts[1]);
                        
                        // Parse parameters - handle alternating lines as key-value pairs
                        $lines = preg_split('/\r\n|\r|\n/', $parametersSection);
                        $parameters = [];
                        $currentKey = null;
                        
                        foreach ($lines as $i => $line) {
                            $line = trim($line);
                            if (empty($line)) continue;
                            
                            if ($i % 2 === 0) {
                                // Even lines are keys
                                $currentKey = $line;
                            } else {
                                // Odd lines are values
                                if ($currentKey !== null) {
                                    $parameters[$currentKey] = $line;
                                }
                            }
                        }
                    } else {
                        $mainDescription = $description;
                    }
                    @endphp
                    
                    <p>{!! nl2br(e($mainDescription ?? $description)) !!}</p>
                </div>
            </div>
            
            <!-- PDF Documentation -->
            @if($product->pdf_file)
            <div class="section-card">
                <div class="card-header">
                    <h4>Documentation</h4>
                </div>
                <div class="card-body">
                    <div class="pdf-documentation">
                        <div class="pdf-icon">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <div class="pdf-content">
                            <h6>Product Specification PDF</h6>
                            <a href="{{ Storage::url($product->pdf_file) }}" target="_blank" class="btn btn-pdf">
                                <i class="fas fa-download"></i> Download PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Technical Specifications -->
            @if(isset($parameters) && count($parameters) > 0)
            <div class="section-card">
                <div class="card-header">
                    <h4>Technical Specifications</h4>
                </div>
                <div class="card-body">
                    <table class="specifications-table">
                        <tbody>
                            @foreach($parameters as $key => $value)
                            <tr>
                                <td>{{ $key }}</td>
                                <td>{{ $value }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Specification Image (if available) -->
    @php
        $specImage = $product->images->first(function($image) {
            return !empty($image->specification_image_url);
        });
    @endphp
    
    @if(isset($specImage) && $specImage)
    <div class="section-card mb-4">
        <div class="card-header">
            <h4>Product Specifications Diagram</h4>
        </div>
        <div class="card-body p-3">
            <img src="{{ $specImage->specification_image_url }}" 
                 alt="Product specifications diagram" 
                 class="specifications-image" 
                 onclick="window.open(this.src, '_blank')">
        </div>
    </div>
    @endif

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Size option selection
        const sizeOptions = document.querySelectorAll('.size-option');
        if (sizeOptions.length > 0) {
            sizeOptions.forEach(option => {
                option.addEventListener('click', function() {
                    // Remove active class from all options
                    sizeOptions.forEach(o => {
                        o.classList.remove('active');
                    });
                    
                    // Add active class to selected option
                    this.classList.add('active');
                    
                    // Check the radio input
                    const radioInput = this.querySelector('input[type="radio"]');
                    radioInput.checked = true;
                    
                    // Store the selected size in form data or local storage if needed
                    const selectedSize = radioInput.value;
                    localStorage.setItem('selected_product_size', selectedSize);
                });
            });
            
            // Update quotation button URL to include selected size
            const quotationBtn = document.getElementById('quotation-btn');
            if (quotationBtn) {
                const baseHref = quotationBtn.getAttribute('href');
                quotationBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const selectedSize = document.querySelector('.size-option.active input[type="radio"]')?.value;
                    if (selectedSize) {
                        // Check if the baseHref already contains a query parameter
                        const separator = baseHref.includes('?') ? '&' : '?';
                        window.location.href = baseHref + separator + 'size=' + encodeURIComponent(selectedSize);
                    } else {
                        window.location.href = baseHref;
                    }
                });
            }
        }

        // Image Magnifier Glass functionality
        function magnify(imgID, zoom) {
            const img = document.getElementById(imgID);
            if (!img) return;
            
            const glass = document.getElementById("img-magnifier-glass");
            const panel = document.getElementById("magnifier-panel");
            if (!glass || !panel) return;
            
            // Set background properties for the magnifier glass and panel
            glass.style.backgroundImage = "url('" + img.src + "')";
            glass.style.backgroundRepeat = "no-repeat";
            panel.style.backgroundImage = "url('" + img.src + "')";
            
            // Calculate background size based on the image's natural dimensions
            img.onload = function() {
                const w = img.naturalWidth;
                const h = img.naturalHeight;
                glass.style.backgroundSize = (w * zoom) + "px " + (h * zoom) + "px";
                panel.style.backgroundSize = (w * (zoom * 1.5)) + "px " + (h * (zoom * 1.5)) + "px";
            };
            
            // If image is already loaded
            if (img.complete) {
                const w = img.naturalWidth;
                const h = img.naturalHeight;
                glass.style.backgroundSize = (w * zoom) + "px " + (h * zoom) + "px";
                panel.style.backgroundSize = (w * (zoom * 1.5)) + "px " + (h * (zoom * 1.5)) + "px";
            }
            
            // Mouse event handlers
            img.addEventListener("mouseenter", function() {
                glass.style.display = "block";
                panel.style.display = "block";
            });
            
            img.addEventListener("mouseleave", function() {
                glass.style.display = "none";
                panel.style.display = "none";
            });
            
            img.addEventListener("mousemove", moveMagnifier);
            
            function moveMagnifier(e) {
                e.preventDefault();
                
                // Get cursor position
                const rect = img.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                // Prevent the magnifier glass from being positioned outside the image
                if (x < 0 || x > rect.width || y < 0 || y > rect.height) {
                    glass.style.display = "none";
                    panel.style.display = "none";
                    return;
                }
                
                glass.style.display = "block";
                panel.style.display = "block";
                
                // Calculate the position of the magnifier
                const glassSize = glass.offsetWidth;
                glass.style.left = Math.min(Math.max(x - glassSize/2, 0), rect.width - glassSize) + "px";
                glass.style.top = Math.min(Math.max(y - glassSize/2, 0), rect.height - glassSize) + "px";
                
                // Calculate the ratio of the image dimensions to displayed dimensions
                const rx = (img.naturalWidth / rect.width);
                const ry = (img.naturalHeight / rect.height);
                
                // Set the background position for the glass
                const bgPosX = x * rx * zoom - glassSize/2;
                const bgPosY = y * ry * zoom - glassSize/2;
                glass.style.backgroundPosition = "-" + bgPosX + "px -" + bgPosY + "px";
                
                // Set the background position for the panel - center on the hovered point
                const panelCenterX = panel.offsetWidth / 2;
                const panelCenterY = panel.offsetHeight / 2;
                const panelZoom = zoom * 1.5; // Higher zoom for the panel
                const panelBgPosX = x * rx * panelZoom - panelCenterX;
                const panelBgPosY = y * ry * panelZoom - panelCenterY;
                panel.style.backgroundPosition = "-" + panelBgPosX + "px -" + panelBgPosY + "px";
            }
        }

        // Initialize magnifier
        setTimeout(function() {
            magnify("product-image", 0.4);
        }, 500);
        
        // Image gallery
        function changeImage(src) {
            const mainImage = document.getElementById('product-image');
            mainImage.src = src;
            
            // Remove event listeners from previous image to avoid duplicates
            const glass = document.getElementById("img-magnifier-glass");
            const panel = document.getElementById("magnifier-panel");
            if (glass) glass.style.display = "none";
            if (panel) panel.style.display = "none";
            
            // Wait for the image to load, then reinitialize magnifier
            setTimeout(function() {
                magnify("product-image", 0.4);
            }, 100);
            
            // Update active thumbnail
            document.querySelectorAll('.small-img').forEach(img => {
                img.classList.remove('active');
                if (img.src === src) {
                    img.classList.add('active');
                }
            });
        }
        
        // Make changeImage function globally available
        window.changeImage = changeImage;

        // Add fade-in effect for sections
        const sections = document.querySelectorAll('.section-card');
        sections.forEach((section, index) => {
            section.style.opacity = '0';
            section.style.transform = 'translateY(20px)';
            section.style.transition = 'opacity 0.4s ease, transform 0.5s ease';
            
            setTimeout(() => {
                section.style.opacity = '1';
                section.style.transform = 'translateY(0)';
            }, 100 + (index * 150));
        });
        
        // Handle image gallery scrolling
        const scrollLeftBtn = document.getElementById('scroll-left');
        const scrollRightBtn = document.getElementById('scroll-right');
        const imgGallery = document.getElementById('img-gallery');
        
        if (scrollLeftBtn && scrollRightBtn && imgGallery) {
            // Function to check if scroll buttons should be visible
            function updateScrollButtonsVisibility() {
                const isScrollable = imgGallery.scrollWidth > imgGallery.clientWidth;
                const atStart = imgGallery.scrollLeft <= 10;
                const atEnd = imgGallery.scrollLeft + imgGallery.clientWidth >= imgGallery.scrollWidth - 10;
                
                scrollLeftBtn.style.display = (!isScrollable || atStart) ? 'none' : 'flex';
                scrollRightBtn.style.display = (!isScrollable || atEnd) ? 'none' : 'flex';
            }
            
            // Initial check
            updateScrollButtonsVisibility();
            
            // Scroll amount for each button click
            const scrollAmount = 200;
            
            // Scroll left button
            scrollLeftBtn.addEventListener('click', () => {
                imgGallery.scrollBy({
                    left: -scrollAmount,
                    behavior: 'smooth'
                });
            });
            
            // Scroll right button
            scrollRightBtn.addEventListener('click', () => {
                imgGallery.scrollBy({
                    left: scrollAmount,
                    behavior: 'smooth'
                });
            });
            
            // Update button visibility on scroll
            imgGallery.addEventListener('scroll', updateScrollButtonsVisibility);
            
            // Update button visibility on window resize
            window.addEventListener('resize', updateScrollButtonsVisibility);
            
            // Scroll active image into view when page loads
            const activeImg = imgGallery.querySelector('.small-img.active');
            if (activeImg) {
                setTimeout(() => {
                    activeImg.scrollIntoView({
                        behavior: 'smooth',
                        inline: 'center',
                        block: 'nearest'
                    });
                    updateScrollButtonsVisibility();
                }, 500);
            }
        }
    });
</script>
@endsection