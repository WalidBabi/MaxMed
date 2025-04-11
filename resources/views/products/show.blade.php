@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <style>
        /* Enhanced Product Detail Page Styling */
        .breadcrumb {
            background-color: transparent;
            padding: 0.75rem 0;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
            border-bottom: 1px solid #f0f0f0;
        }

        .breadcrumb-item a {
            color: #6c757d;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .breadcrumb-item a:hover {
            color: #28a745;
        }

        .breadcrumb-item.active {
            color: #333;
            font-weight: 600;
        }

        /* Product Header */
        .product-header {
            margin-bottom: 2rem;
            position: relative;
            border-left: 4px solid #28a745;
            padding-left: 15px;
        }

        .product-title {
            font-size: 2rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 0.5rem;
            transition: color 0.3s;
        }

        /* Product Images */
        .product-image-container {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            background-color: #fff;
            height: 450px;
            display: flex;
            align-items: center;
            justify-content: center;
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
            border-radius: 8px;
            border: 2px solid #f0f0f0;
            padding: 3px;
            transition: all 0.3s;
        }

        .small-img:hover {
            transform: translateY(-3px);
        }

        .small-img.active {
            border-color: #28a745;
            box-shadow: 0 4px 8px rgba(40, 167, 69, 0.25);
        }

        /* Product Info Card */
        .product-info-card {
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            border: none;
            height: 100%;
        }

        .product-info-card .card-body {
            padding: 25px;
        }

        /* Price Display */
        .price-container {
            margin-bottom: 1.5rem;
        }

        .price-main {
            font-size: 2.2rem;
            font-weight: 700;
            color: #28a745;
        }

        .price-secondary {
            font-size: 1.2rem;
            color: #6c757d;
            margin-left: 10px;
        }

        /* Stock Badge */
        .stock-badge {
            font-size: 0.9rem;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 600;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .stock-badge.in-stock {
            background-color: #28a745;
            color: white;
            box-shadow: 0 3px 6px rgba(40, 167, 69, 0.3);
        }

        .stock-badge.out-of-stock {
            background-color: #dc3545;
            color: white;
            box-shadow: 0 3px 6px rgba(220, 53, 69, 0.3);
        }

        /* Description Section */
        .product-description {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 2rem;
        }

        .product-description h5 {
            color: #333;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .product-description p {
            color: #495057;
            line-height: 1.7;
        }

        /* Enhanced Quantity Control */
        .quantity-controls {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .quantity-controls .counter-text {
            display: flex;
            align-items: baseline;
            gap: 10px;
            margin-right: 20px;
        }

        .quantity-controls .counter-number {
            font-size: 1.8rem;
            font-weight: 700;
            color: #28a745;
            line-height: 1.2;
            min-width: 40px;
            text-align: center;
            animation: countPulse 2s ease-in-out;
        }

        @keyframes countPulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        .quantity-controls .counter-label {
            color: #555;
            font-size: 1.15rem;
            font-weight: 500;
        }

        .quantity-input-group {
            display: flex;
            align-items: center;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .quantity-input-group .btn {
            background-color: #f8f9fa;
            border: none;
            color: #495057;
            font-weight: bold;
            padding: 10px 15px;
            transition: all 0.2s;
        }

        .quantity-input-group .btn:hover {
            background-color: #e9ecef;
            color: #28a745;
        }

        .quantity-input-group input {
            width: 60px;
            text-align: center;
            border: none;
            border-left: 1px solid #e9ecef;
            border-right: 1px solid #e9ecef;
            font-weight: 600;
            padding: 10px 0;
        }

        .quantity-input-group input:focus {
            outline: none;
            box-shadow: none;
        }

        .availability-info {
            color: #6c757d;
            font-size: 0.9rem;
            margin-left: 15px;
        }

        /* Action Buttons */
        .action-buttons {
            gap: 15px;
            margin-top: 25px;
        }

        .btn-add-to-cart {
            background-color: #28a745;
            border-color: #28a745;
            padding: 12px 25px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            font-size: 1rem;
            border-radius: 8px;
            transition: all 0.35s;
            position: relative;
            overflow: hidden;
        }

        .btn-add-to-cart:hover {
            background-color: #218838;
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(33, 136, 56, 0.4);
        }

        .btn-add-to-cart:after {
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

        .btn-add-to-cart:hover:after {
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

        .btn-quotation {
            background-color: #17a2b8;
            border-color: #17a2b8;
            color:rgb(255, 255, 255);
            padding: 12px 25px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            font-size: 1rem;
            border-radius: 8px;
            transition: all 0.35s;
        }

        .btn-quotation:hover {
            background-color:rgb(255, 255, 255);
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        /* Product Details Card */
        .product-details-card {
            margin-top: 3rem;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            border: none;
        }

        .product-details-card .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            padding: 15px 25px;
        }

        .product-details-card .card-header h4 {
            font-weight: 700;
            color: #333;
            margin: 0;
        }

        .product-details-card .card-body {
            padding: 25px;
        }

        .product-details-card p {
            margin-bottom: 0.8rem;
            color: #495057;
        }

        .product-details-card strong {
            color: #333;
        }

        /* Responsive Adjustments */
        @media (max-width: 992px) {
            .product-title {
                font-size: 1.7rem;
            }

            .price-main {
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

            .quantity-controls {
                flex-direction: column;
                align-items: flex-start;
                gap: 20px;
            }

            .quantity-controls .counter-text {
                margin-right: 0;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn-add-to-cart,
            .btn-quotation {
                width: 100%;
            }
        }

        /* Image Magnifier Glass */
        #img-magnifier-glass {
            position: absolute;
            border: 3px solid #28a745;
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
            border: 3px solid #28a745;
            overflow: hidden;
            position: fixed;
            top: 42%;
            left:85%;
            transform: translate(-50%, -50%);
            background-repeat: no-repeat;
            background-color: #fff;
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
                <div class="col-lg-12 mb-4">
                    <div class="product-image-container">
                        <img src="{{ $product->image_url }}" class="product-image" id="product-image" alt="{{ $product->name }}">
                        <div id="img-magnifier-glass"></div>
                    </div>
                    @if($product->images && $product->images->count() > 0)
                    <div class="small-img-group">
                        <!-- Always include the main product image in thumbnails -->
                        <img src="{{ $product->image_url }}" class="small-img active" 
                             alt="{{ $product->name }}" onclick="changeImage('{{ $product->image_url }}')">
                             
                        <!-- Display additional non-primary images -->
                        @foreach($product->images->where('is_primary', false) as $image)
                        <img src="{{ $image->image_url }}" class="small-img" 
                             alt="{{ $product->name }}" onclick="changeImage('{{ $image->image_url }}')">
                        @endforeach
                    </div>
                    @endif
                </div>

                <div class="col-lg-12">
                    <div class="card product-info-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <!-- <div class="price-container">
                                    <span class="price-main">${{ number_format($product->price, 2) }}</span>
                                    <span class="price-secondary">AED {{ number_format($product->price_aed, 2) }}</span>
                                </div> -->

                                <!-- @if($product->inventory && $product->inventory->quantity > 0)
                                <span class="stock-badge in-stock">
                                    <i class="fas fa-check"></i> In Stock
                                </span>
                                @else
                                <span class="stock-badge out-of-stock">
                                    <i class="fas fa-times"></i> Out of Stock
                                </span>
                                @endif -->
                            </div>
                            <div class="d-grid action-buttons mb-3">
                                <!-- <form action="{{ route('cart.add', $product) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="quantity" id="form-quantity" value="1">
                                        <button type="submit" class="btn btn-add-to-cart w-100">
                                            <i class="fas fa-shopping-cart me-2"></i> Add to Cart
                                        </button>
                                    </form> -->
                                <a href="{{ route('quotation.form', $product) }}" class="btn btn-quotation w-100">
                                    <i class="fas fa-file-invoice me-2"></i> Request Quotation
                                </a>
                            </div>
                            <div class="product-description">
                                <h5 class="fw-bold">Description</h5>
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
                            <div class="product-parameters mt-4">
                                <h5 class="fw-bold">Technical Specifications</h5>
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

                              <!-- <div class="quantity-controls">
                                <div class="counter-text">
                                    <span class="counter-number" id="counter-display">1</span>
                                    <span class="counter-label">item</span>
                                </div>

                                <div class="d-flex align-items-center">
                                    <div class="quantity-input-group">
                                        <button class="btn" type="button" id="decrease-qty">
                                            <span>-</span>
                                        </button>
                                        <input type="number" class="form-control" id="quantity" value="1" min="1" max="{{ $product->inventory->quantity }}">
                                        <button class="btn" type="button" id="increase-qty">
                                            <span>+</span>
                                        </button>
                                    </div>
                                    <div class="availability-info">
                                        <span><i class="fas fa-box me-1"></i>{{ $product->inventory->quantity }} available</span>
                                    </div>
                                </div>
                            </div> -->
                           
                       
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const quantityInput = document.getElementById('quantity');
        const formQuantityInput = document.getElementById('form-quantity');
        const counterDisplay = document.getElementById('counter-display');
        const decreaseBtn = document.getElementById('decrease-qty');
        const increaseBtn = document.getElementById('increase-qty');

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

        if (quantityInput && formQuantityInput) {
            // Initial sync
            formQuantityInput.value = quantityInput.value;

            // Update counter display
            function updateCounter() {
                if (counterDisplay) {
                    counterDisplay.textContent = quantityInput.value;

                    // Update the label (singular/plural)
                    const counterLabel = document.querySelector('.counter-label');
                    if (counterLabel) {
                        counterLabel.textContent = parseInt(quantityInput.value) === 1 ? 'item' : 'items';
                    }

                    // Trigger animation
                    counterDisplay.style.animation = 'none';
                    setTimeout(() => {
                        counterDisplay.style.animation = 'countPulse 2s ease-in-out';
                    }, 10);
                }

                // Sync with form input
                formQuantityInput.value = quantityInput.value;
            }

            // Handle input changes
            quantityInput.addEventListener('input', function() {
                const max = parseInt(this.max, 10);
                const min = parseInt(this.min, 10);
                let value = parseInt(this.value, 10);

                if (isNaN(value) || value < min) {
                    this.value = min;
                } else if (value > max) {
                    alert('The requested quantity exceeds the available stock.');
                    this.value = max;
                }

                updateCounter();
            });

            // Handle increase button
            increaseBtn.addEventListener('click', function() {
                const max = parseInt(quantityInput.max, 10);
                let value = parseInt(quantityInput.value, 10);

                if (value < max) {
                    quantityInput.value = value + 1;
                    updateCounter();
                } else {
                    alert('The requested quantity exceeds the available stock.');
                }
            });

            // Handle decrease button
            decreaseBtn.addEventListener('click', function() {
                const min = parseInt(quantityInput.min, 10);
                let value = parseInt(quantityInput.value, 10);

                if (value > min) {
                    quantityInput.value = value - 1;
                    updateCounter();
                }
            });
        }

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
        const mainProductImage = document.querySelector('.product-image');

        smallImgs.forEach(img => {
            img.addEventListener('click', function() {
                changeImage(this.src);
            });
        });

        // Add fade-in effect
        const productContainer = document.querySelector('.product-image-container');
        const productInfo = document.querySelector('.product-info-card');

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