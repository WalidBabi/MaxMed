@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <style>
        /* Enhanced Product Detail Page Styling with Brand Colors */
        :root {
            --main-color: #171e60;
            --auxiliary-color: #0a5694;
            --white: #ffffff;
            --light-gray: #f0f0f0;
            --medium-gray: #e0e0e0;
        }

        .breadcrumb {
            background-color: transparent;
            padding: 0.75rem 0;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
            border-bottom: 1px solid var(--light-gray);
        }

        .breadcrumb-item a {
            color: var(--auxiliary-color);
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .breadcrumb-item a:hover {
            color: var(--main-color);
        }

        .breadcrumb-item.active {
            color: var(--main-color);
            font-weight: 600;
        }

        /* Product Header */
        .product-header {
            margin-bottom: 2rem;
            position: relative;
            border-left: 4px solid var(--main-color);
            padding-left: 15px;
        }

        .product-title {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--main-color);
            margin-bottom: 0.5rem;
            transition: color 0.3s;
        }

        /* Product Images */
        .product-image-container {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            background-color: var(--white);
            height: 450px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--light-gray);
        }

        .product-image {
            height: auto;
            max-height: 400px;
            max-width: 100%;
            object-fit: contain;
            transition: transform 0.5s ease;
        }

        .product-image:hover {
            transform: scale(1.05);
        }

        .small-img-group {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 15px;
        }

        .small-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            cursor: pointer;
            border-radius: 6px;
            border: 2px solid var(--light-gray);
            padding: 3px;
            transition: all 0.3s;
        }

        .small-img:hover {
            transform: translateY(-3px);
        }

        .small-img.active {
            border-color: var(--main-color);
            box-shadow: 0 4px 8px rgba(23, 30, 96, 0.25);
        }

        /* Product Info Section */
        .product-info-section {
            border-radius: 8px;
            background-color: var(--white);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            border: 1px solid var(--light-gray);
            padding: 25px;
            height: 100%;
        }

        /* Quotation Button */
        .btn-quotation {
            background-color: var(--main-color);
            border-color: var(--main-color);
            color: var(--white);
            padding: 12px 25px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            font-size: 1rem;
            border-radius: 8px;
            transition: all 0.35s;
        }

        .btn-quotation:hover {
            background-color: var(--auxiliary-color);
            border-color: var(--auxiliary-color);
            color: var(--white);
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(10, 86, 148, 0.3);
        }

        /* Product Description */
        .product-description {
            background-color: rgba(240, 240, 240, 0.3);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 2rem;
            border: 1px solid var(--light-gray);
        }

        .product-description h5 {
            color: var(--main-color);
            border-bottom: 2px solid var(--auxiliary-color);
            padding-bottom: 10px;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .product-description p {
            color: #333;
            line-height: 1.7;
        }

        /* Technical Specifications */
        .product-parameters {
            background-color: var(--white);
            border-radius: 8px;
            padding: 20px;
            border: 1px solid var(--light-gray);
        }

        .product-parameters h5 {
            color: var(--main-color);
            border-bottom: 2px solid var(--auxiliary-color);
            padding-bottom: 10px;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .product-parameters .table {
            margin-bottom: 0;
        }

        .product-parameters .table td.fw-medium {
            color: var(--main-color);
            font-weight: 600;
        }

        .product-parameters .table-striped > tbody > tr:nth-of-type(odd) {
            background-color: rgba(10, 86, 148, 0.05);
        }

        /* Specification Image */
        .product-specifications-image {
            background-color: var(--white);
            border-radius: 8px;
            padding: 20px;
            border: 1px solid var(--light-gray);
        }

        .product-specifications-image h5 {
            color: var(--main-color);
            border-bottom: 2px solid var(--auxiliary-color);
            padding-bottom: 10px;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .specification-img {
            max-height: 500px;
            transition: all 0.3s;
        }

        .specification-img:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1) !important;
            transform: scale(1.01);
        }

        /* Specification Card */
        .specification-card {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s;
        }
        
        .specification-card:hover {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }
        
        .specification-card .card-header {
            border-bottom: 2px solid var(--main-color);
            padding: 15px 20px;
        }
        
        .specification-card .text-main {
            color: var(--main-color);
            font-weight: 600;
        }
        
        .specification-img-large {
            padding: 10px;
            background-color: white;
            transition: all 0.3s;
        }
        
        .specification-card .card-footer {
            padding: 12px;
            border-top: 1px solid var(--light-gray);
        }
        
        .specification-card .btn-outline-primary {
            color: var(--auxiliary-color);
            border-color: var(--auxiliary-color);
        }
        
        .specification-card .btn-outline-primary:hover {
            background-color: var(--auxiliary-color);
            color: white;
        }

        /* Size Options */
        .size-option {
            min-width: 80px;
            transition: all 0.2s ease;
            cursor: pointer;
            border-radius: 6px;
            font-weight: 500;
        }

        .size-option:hover {
            background-color: rgba(23, 30, 96, 0.1);
            transform: translateY(-2px);
        }

        .size-option.active {
            background-color: var(--main-color) !important;
            color: white;
            box-shadow: 0 4px 8px rgba(23, 30, 96, 0.15);
            border-color: var(--main-color);
        }

        /* Responsive Adjustments */
        @media (max-width: 992px) {
            .product-title {
                font-size: 1.8rem;
            }
        }

        @media (max-width: 768px) {
            .product-image-container {
                height: 350px;
                margin-bottom: 2rem;
            }

            .small-img {
                width: 60px;
                height: 60px;
            }
            
            .action-buttons {
                flex-direction: column;
            }

            .btn-quotation {
                width: 100%;
            }
        }

        /* Image Magnifier Glass */
        #img-magnifier-glass {
            position: absolute;
            border: 3px solid var(--main-color);
            border-radius: 0;
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
            overflow: hidden;
            position: fixed;
            top: 42%;
            left: 85%;
            transform: translate(-50%, -50%);
            background-repeat: no-repeat;
            background-color: var(--white);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            display: none;
            z-index: 1000;
        }
    </style>

    <!-- Floating magnifier panel -->
    <div class="magnifier-panel" id="magnifier-panel"></div>

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

    <div class="row">
        <div class="col-md-12">
            <div class="product-header">
                <h1 class="product-title">{{ $product->name }}</h1>
            </div>

            <div class="row">
                <div class="col-lg-7 mb-4">
                    <div class="product-image-container">
                        <img src="{{ $product->image_url }}" class="product-image" id="product-image" alt="{{ $product->name }}">
                        <div id="img-magnifier-glass"></div>
                    </div>
                    @if($product->images && $product->images->count() > 0)
                    <div class="small-img-group">
                        <!-- Always include the main product image in thumbnails -->
                        <img src="{{ $product->image_url }}" class="small-img active" 
                             alt="{{ $product->name }}" onclick="changeImage('{{ $product->image_url }}')">
                             
                        <!-- Display additional non-primary images, excluding specification images -->
                        @foreach($product->images->where('is_primary', false)->filter(function($image) {
                            return empty($image->specification_image_url);
                        }) as $image)
                        <img src="{{ $image->image_url }}" class="small-img" 
                             alt="{{ $product->name }}" onclick="changeImage('{{ $image->image_url }}')">
                        @endforeach
                    </div>
                    @endif
                </div>

                <div class="col-lg-5">
                    <div class="product-info-section">
                        <div class="d-grid action-buttons mb-4">
                            <a href="{{ route('quotation.form', ['product' => $product->id]) }}" class="btn btn-quotation w-100" id="quotation-btn">
                                <i class="fas fa-file-invoice me-2"></i> Request Quotation
                            </a>
                        </div>
                        
                        @if($product->has_size_options)
                        <div class="mb-4">
                            <h5 class="mb-3">Size</h5>
                            <div class="d-flex flex-wrap gap-2">
                                @php
                                    $sizeOptions = $product->size_options ?? [];
                                    if (!is_array($sizeOptions) && !empty($sizeOptions)) {
                                        // Convert JSON string to array if needed
                                        $sizeOptions = json_decode($sizeOptions, true) ?? [];
                                    }
                                @endphp
                                
                                @foreach($sizeOptions as $option)
                                    <label class="btn btn-outline-secondary size-option">
                                        <input type="radio" name="size" value="{{ $option }}" class="d-none"> {{ $option }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        
                        <div class="product-description">
                            <h5>Description</h5>
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

                        @if(isset($parameters) && count($parameters) > 0)
                        <div class="product-parameters">
                            <h5>Technical Specifications</h5>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <tbody>
                                        @foreach($parameters as $key => $value)
                                        <tr>
                                            <td class="fw-medium" style="width: 40%;">{{ $key }}</td>
                                            <td>{{ $value }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif

                        @php
                            $specImage = $product->images->first(function($image) {
                                return !empty($image->specification_image_url);
                            });
                        @endphp
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(isset($specImage) && $specImage)
    <div class="row mt-5">
        <div class="col-12">
            <div class="card specification-card">
                <div class="card-header bg-light">
                    <h3 class="text-main m-0"><i class="fas fa-file-alt me-2"></i>Product Specifications</h3>
                </div>
                <div class="card-body p-0">
                    <img src="{{ $specImage->specification_image_url }}" 
                         alt="Product specifications" 
                         class="img-fluid w-100 specification-img-large" 
                         style="cursor: pointer; max-height: 800px; object-fit: contain;"
                         onclick="window.open(this.src, '_blank')">
                </div>
                <div class="card-footer text-center bg-light">
                    <button class="btn btn-sm btn-outline-primary" onclick="window.open('{{ $specImage->specification_image_url }}', '_blank')">
                        <i class="fas fa-search-plus me-1"></i> View Full Size
                    </button>
                </div>
            </div>
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
                        o.classList.remove('active', 'btn-primary');
                        o.classList.add('btn-outline-secondary');
                    });
                    
                    // Add active class to selected option
                    this.classList.remove('btn-outline-secondary');
                    this.classList.add('active', 'btn-primary');
                    
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

        const smallImgs = document.querySelectorAll('.small-img');
        smallImgs.forEach(img => {
            img.addEventListener('click', function() {
                changeImage(this.src);
            });
        });

        // Add fade-in effect
        const productContainer = document.querySelector('.product-image-container');
        const productInfo = document.querySelector('.product-info-section');

        if (productContainer && productInfo) {
            [productContainer, productInfo].forEach((el, index) => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(20px)';
                el.style.transition = 'opacity 0.4s ease, transform 0.5s ease';

                setTimeout(() => {
                    el.style.opacity = '1';
                    el.style.transform = 'translateY(0)';
                }, 100 + (index * 100));
            });
        }
    });
</script>
@endsection