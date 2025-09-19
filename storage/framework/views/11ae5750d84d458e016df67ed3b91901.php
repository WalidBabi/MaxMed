<?php $__env->startSection('title', $seoData['title'] ?? $product->name . ' | MaxMed UAE'); ?>
<?php $__env->startSection('meta_description', $seoData['meta_description'] ?? '🔬 Premium ' . $product->name . ' available at MaxMed UAE! ✅ Same-day quotes ☎️ +971 55 460 2500 🚚 Fast delivery across UAE. Leading laboratory equipment supplier in Dubai.'); ?>
<?php $__env->startSection('meta_keywords', $seoData['meta_keywords'] ?? 'laboratory equipment, medical supplies, UAE, Dubai, ' . strtolower($product->name) . ', lab instruments'); ?>
<?php $__env->startSection('og_title', $seoData['og_title'] ?? $seoData['title'] ?? $product->name . ' - MaxMed UAE'); ?>
<?php $__env->startSection('og_description', $seoData['og_description'] ?? $seoData['meta_description'] ?? 'Premium ' . $product->name . ' from MaxMed UAE. Leading laboratory equipment supplier in Dubai. Contact +971 55 460 2500.'); ?>
<?php $__env->startSection('og_image', $product->image_url ?? asset('Images/banner2.jpeg')); ?>


<?php $__env->startPush('head'); ?>
    <?php if (isset($component)) { $__componentOriginal0eb350b3c65b028b13e39fd2077668f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0eb350b3c65b028b13e39fd2077668f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ai-enhanced-schema','data' => ['product' => $product,'type' => 'product']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ai-enhanced-schema'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['product' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product),'type' => 'product']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0eb350b3c65b028b13e39fd2077668f6)): ?>
<?php $attributes = $__attributesOriginal0eb350b3c65b028b13e39fd2077668f6; ?>
<?php unset($__attributesOriginal0eb350b3c65b028b13e39fd2077668f6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0eb350b3c65b028b13e39fd2077668f6)): ?>
<?php $component = $__componentOriginal0eb350b3c65b028b13e39fd2077668f6; ?>
<?php unset($__componentOriginal0eb350b3c65b028b13e39fd2077668f6); ?>
<?php endif; ?>
    
    <!-- Enhanced Breadcrumb Schema -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [
            {
                "@type": "ListItem",
                "position": 1,
                "name": "Home",
                "item": "https://maxmedme.com"
            },
            {
                "@type": "ListItem",
                "position": 2,
                "name": "<?php echo e($product->category->name ?? 'Products'); ?>",
                "item": "<?php echo e(url('/categories/' . ($product->category->slug ?? 'products'))); ?>"
            },
            {
                "@type": "ListItem",
                "position": 3,
                "name": "<?php echo e($product->name); ?>",
                "item": "<?php echo e(url('/products/' . $product->slug)); ?>"
            }
        ]
    }
    </script>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>


<?php if (isset($component)) { $__componentOriginal5d81958b7882a39b0d77a97609344f89 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5d81958b7882a39b0d77a97609344f89 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ai-knowledge-article','data' => ['product' => $product]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ai-knowledge-article'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['product' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5d81958b7882a39b0d77a97609344f89)): ?>
<?php $attributes = $__attributesOriginal5d81958b7882a39b0d77a97609344f89; ?>
<?php unset($__attributesOriginal5d81958b7882a39b0d77a97609344f89); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5d81958b7882a39b0d77a97609344f89)): ?>
<?php $component = $__componentOriginal5d81958b7882a39b0d77a97609344f89; ?>
<?php unset($__componentOriginal5d81958b7882a39b0d77a97609344f89); ?>
<?php endif; ?>


<style>
    /* Only hide content during actual navigation transitions */
    body.navigating .container-fluid {
        opacity: 0.3;
        transition: opacity 0.2s ease-in-out;
    }
    
    .container-fluid {
        opacity: 1;
        transition: opacity 0.2s ease-in-out;
    }
</style>

<div class="container-fluid py-4">
    <style>
        /* Professional Product Detail Page Styling */
        :root {
            --main-color: #171e60;
            --auxiliary-color: #0a5694;
            --accent-color: #0a5694;
            --white: #ffffff;
            --light-gray: #f8f9fa;
            --medium-gray: #e0e0e0;
            --dark-gray: #495057;
            --box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            --transition: all 0.2s ease;
        }

        /* Breadcrumb Navigation */
        .breadcrumb-container {
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--medium-gray);
        }
        
        .breadcrumb {
            background-color: transparent;
            padding: 0;
            margin: 0;
            font-size: 0.9rem;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
        }

        .breadcrumb-item a {
            color: var(--auxiliary-color);
            text-decoration: none;
            transition: var(--transition);
        }

        .breadcrumb-item a:hover {
            color: var(--main-color);
        }

        .breadcrumb-item.active {
            color: var(--main-color);
            font-weight: 500;
        }
        
        /* Product Header */
        .product-header {
            margin-bottom: 1.5rem;
            position: relative;
            border-left: 4px solid var(--main-color);
            padding-left: 15px;
        }

        .product-title {
            font-size: 1.75rem;
            font-weight: 600;
            color: var(--main-color);
            margin-bottom: 0.5rem;
        }

        /* Product Container */
        .product-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
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
            border-radius: 4px;
            overflow: hidden;
            box-shadow: var(--box-shadow);
            transition: var(--transition);
            border: 1px solid #eaeaea;
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
            transition: transform 0.3s ease;
            padding: 20px;
        }

        .product-image:hover {
            transform: scale(1.02);
        }

        .small-img-container {
            position: relative;
            padding: 15px;
            background-color: var(--light-gray);
            border-top: 1px solid #eaeaea;
        }

        .small-img-group {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            padding: 5px;
            scrollbar-width: thin;
            scrollbar-color: var(--medium-gray) var(--light-gray);
        }

        .small-img-group::-webkit-scrollbar {
            height: 4px;
        }

        .small-img-group::-webkit-scrollbar-track {
            background: var(--light-gray);
            border-radius: 4px;
        }

        .small-img-group::-webkit-scrollbar-thumb {
            background: var(--medium-gray);
            border-radius: 4px;
        }

        .small-img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            cursor: pointer;
            border-radius: 3px;
            border: 1px solid #eaeaea;
            padding: 3px;
            transition: var(--transition);
            flex-shrink: 0;
            background-color: var(--white);
        }

        .small-img:hover {
            border-color: var(--auxiliary-color);
        }

        .small-img.active {
            border-color: var(--main-color);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        /* Content Section */
        .content-section {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .section-card {
            background-color: var(--white);
            border-radius: 4px;
            overflow: hidden;
            box-shadow: var(--box-shadow);
            transition: var(--transition);
            border: 1px solid #eaeaea;
        }

        .card-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #eaeaea;
            background-color: var(--white);
            position: relative;
        }
        
        .card-header h4 {
            margin: 0;
            color: var(--main-color);
            font-weight: 600;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
        }

        .card-body {
            padding: 1.25rem;
        }

        /* Action Section */
        .action-section {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .btn-quotation {
            background-color: var(--auxiliary-color);
            border: none;
            color: var(--white);
            padding: 12px 20px;
            font-weight: 500;
            font-size: 0.95rem;
            border-radius: 3px;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-quotation:hover {
            background-color: var(--main-color);
            color: var(--white);
        }

        /* Size Options */
        .size-options {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .size-option {
            min-width: 60px;
            padding: 8px 12px;
            border: 1px solid var(--medium-gray);
            background-color: var(--white);
            transition: var(--transition);
            cursor: pointer;
            border-radius: 3px;
            font-weight: 400;
            text-align: center;
            font-size: 0.9rem;
        }

        .size-option:hover {
            border-color: var(--auxiliary-color);
        }

        .size-option.active {
            background-color: var(--main-color);
            color: white;
            border-color: var(--main-color);
        }

        /* Product Description */
        .product-description p {
            color: var(--dark-gray);
            line-height: 1.6;
            margin-bottom: 0;
            font-size: 0.95rem;
        }

        /* Product Documentation */
        .pdf-documentation {
            background-color: var(--light-gray);
            border-radius: 3px;
            padding: 15px;
            display: flex;
            align-items: center;
            gap: 15px;
            transition: var(--transition);
        }
        
        .pdf-icon {
            color: #e74c3c;
            font-size: 1.75rem;
            flex-shrink: 0;
        }
        
        .pdf-content {
            flex-grow: 1;
        }
        
        .pdf-content h6 {
            margin-bottom: 8px;
            color: var(--main-color);
            font-weight: 500;
            font-size: 0.95rem;
        }
        
        .btn-pdf {
            background-color: var(--white);
            color: var(--auxiliary-color);
            border: 1px solid #dee2e6;
            border-radius: 3px;
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
            border-color: var(--auxiliary-color);
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
            padding: 10px 15px;
            border-bottom: 1px solid var(--light-gray);
            font-size: 0.9rem;
        }
        
        .specifications-table td:first-child {
            font-weight: 500;
            color: var(--main-color);
            width: 40%;
        }
        
        .specifications-table tr:last-child td {
            border-bottom: none;
        }

        /* Specification Image */
        .specifications-image {
            width: 100%;
            border-radius: 3px;
            transition: var(--transition);
            cursor: pointer;
            border: 1px solid #eaeaea;
        }
        
        .specifications-image:hover {
            border-color: var(--auxiliary-color);
        }

        /* Image Magnifier Glass */
        #img-magnifier-glass {
            position: absolute;
            border: 2px solid var(--main-color);
            border-radius: 50%;
            cursor: none;
            width: 100px;
            height: 100px;
            display: none;
            z-index: 100;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            pointer-events: none;
            will-change: transform;
        }
        
        /* Magnifier Display Panel */
        .magnifier-panel {
            width: 350px;
            height: 350px;
            border: 1px solid #eaeaea;
            border-radius: 4px;
            overflow: hidden;
            position: fixed;
            top: 42%;
            left: 85%;
            transform: translate(-50%, -50%);
            background-repeat: no-repeat;
            background-color: var(--white);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            display: none;
            z-index: 1000;
            will-change: background-position;
        }

        /* Responsive Adjustments */
        @media (max-width: 992px) {
            .product-title {
                font-size: 1.5rem;
            }
            
            .product-container {
                gap: 1.5rem;
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
                padding: 10px 16px;
            }
        }

        @media (max-width: 576px) {
            .product-header {
                margin-bottom: 1rem;
            }
            
            .product-title {
                font-size: 1.3rem;
            }
            
            .product-container {
                gap: 1rem;
            }
            
            .card-body {
                padding: 1rem;
            }
        }
    </style>

    <!-- Floating magnifier panel -->
    <div class="magnifier-panel" id="magnifier-panel"></div>

    <div class="breadcrumb-container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo e(route('welcome')); ?>"><i class="fas fa-home me-1"></i>Home</a></li>
                <li class="breadcrumb-item"><a href="<?php echo e(route('products.index')); ?>">Products</a></li>
                <?php if($product->category): ?>
                <li class="breadcrumb-item"><a href="<?php echo e(route('categories.show', $product->category)); ?>"><?php echo e($product->category->name); ?></a></li>
                <?php endif; ?>
                <li class="breadcrumb-item active" aria-current="page"><?php echo e($product->name); ?></li>
            </ol>
        </nav>
    </div>

    <div class="product-header">
        <h1 class="product-title"><?php echo e($product->name); ?></h1>
    </div>

    <div class="product-container">
        <!-- Image Gallery Section -->
        <div class="gallery-section">
            <div class="product-image-container">
                <img src="<?php echo e($product->image_url); ?>" class="product-image" id="product-image" alt="<?php echo e($product->name); ?>">
                <div id="img-magnifier-glass"></div>
            </div>
            
            <?php if($product->images && $product->images->count() > 0): ?>
            <div class="small-img-container">
                <div class="small-img-group" id="img-gallery">
                    <!-- Main product image in thumbnails -->
                    <img src="<?php echo e($product->image_url); ?>" class="small-img active" 
                         alt="<?php echo e($product->name); ?>" onclick="changeImage('<?php echo e($product->image_url); ?>')">
                         
                    <!-- Additional non-primary images -->
                    <?php $__currentLoopData = $product->images->where('is_primary', false)->filter(function($image) {
                        return empty($image->specification_image_url);
                    }); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <img src="<?php echo e($image->image_url); ?>" class="small-img" 
                         alt="<?php echo e($product->name); ?>" onclick="changeImage('<?php echo e($image->image_url); ?>')">
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Content Section -->
        <div class="content-section">
            <!-- Action Section -->
            <div class="section-card">
                <div class="card-body action-section">
                    <a href="<?php echo e(route('quotation.form', ['product' => $product->slug])); ?>" class="btn btn-quotation" id="quotation-btn">
                        <i class="fas fa-file-invoice"></i> Request Quotation
                    </a>
                    
                    <?php if($product->has_size_options): ?>
                    <div class="mt-3">
                        <h5 class="mb-3">Select Size</h5>
                        <div class="size-options">
                            <?php
                                $sizeOptions = $product->size_options ?? [];
                                if (!is_array($sizeOptions) && !empty($sizeOptions)) {
                                    $sizeOptions = json_decode($sizeOptions, true) ?? [];
                                }
                            ?>
                            
                            <?php $__currentLoopData = $sizeOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="size-option">
                                    <input type="radio" name="size" value="<?php echo e($option); ?>" class="d-none"> <?php echo e($option); ?>

                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Description Section -->
            <div class="section-card">
                <div class="card-header">
                    <h4>Description</h4>
                </div>
                <div class="card-body product-description">
                    <?php
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
                    ?>
                    
                    <p><?php echo nl2br(e($mainDescription ?? $description)); ?></p>
                </div>
            </div>
            
            <!-- PDF Documentation -->
            <?php if($product->pdf_file): ?>
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
                            <a href="<?php echo e(Storage::url($product->pdf_file)); ?>" target="_blank" class="btn btn-pdf">
                                <i class="fas fa-download"></i> Download PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Technical Specifications -->
            <?php if(isset($parameters) && count($parameters) > 0): ?>
            <div class="section-card">
                <div class="card-header">
                    <h4>Technical Specifications</h4>
                </div>
                <div class="card-body">
                    <table class="specifications-table">
                        <tbody>
                            <?php $__currentLoopData = $parameters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($key); ?></td>
                                <td><?php echo e($value); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            <!-- Enhanced Product Specifications -->
            <?php if (isset($component)) { $__componentOriginal7f4dae5d614dc623697338c28d939bdf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7f4dae5d614dc623697338c28d939bdf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.product-specifications','data' => ['product' => $product]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('product-specifications'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['product' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7f4dae5d614dc623697338c28d939bdf)): ?>
<?php $attributes = $__attributesOriginal7f4dae5d614dc623697338c28d939bdf; ?>
<?php unset($__attributesOriginal7f4dae5d614dc623697338c28d939bdf); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7f4dae5d614dc623697338c28d939bdf)): ?>
<?php $component = $__componentOriginal7f4dae5d614dc623697338c28d939bdf; ?>
<?php unset($__componentOriginal7f4dae5d614dc623697338c28d939bdf); ?>
<?php endif; ?>
        </div>
    </div>

    <!-- Specification Image (if available) -->
    <?php
        $specImage = $product->images->first(function($image) {
            return !empty($image->specification_image_url);
        });
    ?>
    
    <?php if(isset($specImage) && $specImage): ?>
    <div class="section-card mb-4">
        <div class="card-header">
            <h4>Product Specifications Diagram</h4>
        </div>
        <div class="card-body p-3">
            <img src="<?php echo e($specImage->specification_image_url); ?>" 
                 alt="Product specifications diagram" 
                 class="specifications-image" 
                 onclick="window.open(this.src, '_blank')">
        </div>
    </div>
    <?php endif; ?>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ensure navigation state is cleared
        document.body.classList.remove('navigating');
        
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

        // Improved Image Magnifier Glass functionality
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
            
            // Pre-calculate image dimensions to avoid repeated calculations
            let w, h;
            const updateDimensions = () => {
                w = img.naturalWidth;
                h = img.naturalHeight;
                glass.style.backgroundSize = (w * zoom) + "px " + (h * zoom) + "px";
                panel.style.backgroundSize = (w * (zoom * 1.5)) + "px " + (h * (zoom * 1.5)) + "px";
            };
            
            // If image is already loaded
            if (img.complete) {
                updateDimensions();
            } else {
                // Wait for image to load
                img.onload = updateDimensions;
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
            
            // Use requestAnimationFrame for smoother performance
            let ticking = false;
            let lastX = 0, lastY = 0;
            let rect;
            
            img.addEventListener("mousemove", function(e) {
                lastX = e.clientX;
                lastY = e.clientY;
                
                if (!ticking) {
                    window.requestAnimationFrame(function() {
                        moveMagnifier(lastX, lastY);
                        ticking = false;
                    });
                    ticking = true;
                }
            });
            
            function moveMagnifier(clientX, clientY) {
                // Get current rect only when needed
                rect = img.getBoundingClientRect();
                
                // Get cursor position
                const x = clientX - rect.left;
                const y = clientY - rect.top;
                
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
                const rx = w / rect.width;
                const ry = h / rect.height;
                
                // Set the background position for the glass
                const bgPosX = x * rx * zoom - glassSize/2;
                const bgPosY = y * ry * zoom - glassSize/2;
                glass.style.backgroundPosition = `-${bgPosX}px -${bgPosY}px`;
                
                // Set the background position for the panel
                const panelCenterX = panel.offsetWidth / 2;
                const panelCenterY = panel.offsetHeight / 2;
                const panelZoom = zoom * 1.5;
                const panelBgPosX = x * rx * panelZoom - panelCenterX;
                const panelBgPosY = y * ry * panelZoom - panelCenterY;
                panel.style.backgroundPosition = `-${panelBgPosX}px -${panelBgPosY}px`;
            }
        }

        // Initialize magnifier
        setTimeout(function() {
            magnify("product-image", 0.4);
        }, 300);
        
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
        
        // Handle image gallery scrolling
        const imgGallery = document.getElementById('img-gallery');
        
        if (imgGallery) {
            // Scroll active image into view when page loads
            const activeImg = imgGallery.querySelector('.small-img.active');
            if (activeImg) {
                setTimeout(() => {
                    activeImg.scrollIntoView({
                        behavior: 'smooth',
                        inline: 'center',
                        block: 'nearest'
                    });
                }, 500);
            }
        }
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Walid\OneDrive\Desktop\MaxMed\resources\views/products/show.blade.php ENDPATH**/ ?>