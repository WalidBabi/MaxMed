<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'product' => null,
    'category' => null,
    'title' => null,
    'content' => null
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'product' => null,
    'category' => null,
    'title' => null,
    'content' => null
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php
    $aiSeoService = app(\App\Services\AiSeoService::class);
    
    if ($product) {
        $aiContent = $aiSeoService->generateAiOptimizedContent($product);
        $entityData = $aiContent['knowledge_base_format'];
        $structuredContent = $aiContent['structured_content'];
        $articleTitle = $title ?? $product->name . ' - Laboratory Equipment | MaxMed UAE';
        $articleContent = $content ?? $entityData['product_description'];
    } elseif ($category) {
        $aiContent = $aiSeoService->generateAiCategoryContent($category);
        $entityData = $aiContent['knowledge_structure'];
        $articleTitle = $title ?? $category->name . ' Equipment & Supplies | MaxMed UAE';
        $articleContent = $content ?? $entityData['description'];
    } else {
        $articleTitle = $title ?? 'MaxMed UAE - Laboratory Equipment Supplier';
        $articleContent = $content ?? 'MaxMed UAE is the leading laboratory equipment supplier in Dubai, United Arab Emirates.';
        $entityData = [];
        $structuredContent = [];
    }
?>


<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Article",
    "headline": "<?php echo e($articleTitle); ?>",
    "description": "<?php echo e(strip_tags($articleContent)); ?>",
    "author": {
        "@type": "Organization",
        "name": "MaxMed UAE",
        "url": "https://maxmedme.com",
        "description": "Leading laboratory equipment supplier in Dubai, UAE"
    },
    "publisher": {
        "@type": "Organization",
        "name": "MaxMed UAE",
        "logo": {
            "@type": "ImageObject",
            "url": "<?php echo e(asset('Images/logo.png')); ?>",
            "width": "200",
            "height": "60"
        }
    },
    "datePublished": "<?php echo e(now()->toISOString()); ?>",
    "dateModified": "<?php echo e(now()->toISOString()); ?>",
    "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": "<?php echo e(url()->current()); ?>"
    },
    <?php if($product || $category): ?>
    "about": {
        "@type": "<?php echo e($product ? 'Product' : 'Category'); ?>",
        "name": "<?php echo e($product ? $product->name : $category->name); ?>",
        "description": "<?php echo e(strip_tags($articleContent)); ?>"
    },
    <?php endif; ?>
    "keywords": "<?php echo e(implode(', ', $aiContent['semantic_keywords'] ?? ['laboratory equipment', 'medical equipment', 'Dubai', 'UAE', 'MaxMed'])); ?>",
    "inLanguage": "en-US",
    "isAccessibleForFree": true,
    "url": "<?php echo e(url()->current()); ?>"
}
</script>


<article class="ai-knowledge-article" itemscope itemtype="https://schema.org/Article">
    <meta itemprop="headline" content="<?php echo e($articleTitle); ?>">
    <meta itemprop="description" content="<?php echo e(strip_tags($articleContent)); ?>">
    <meta itemprop="datePublished" content="<?php echo e(now()->toISOString()); ?>">
    <meta itemprop="author" content="MaxMed UAE">
    
    <div class="ai-structured-content">
        <h1 itemprop="name"><?php echo e($articleTitle); ?></h1>
        
        <div class="ai-content-summary">
            <p><strong>Summary:</strong> <?php echo e(strip_tags(Str::limit($articleContent, 200))); ?></p>
        </div>

        <?php if(isset($entityData['entity_name'])): ?>
        <div class="ai-entity-info">
            <h2>Entity Information</h2>
            <ul>
                <li><strong>Name:</strong> <?php echo e($entityData['entity_name']); ?></li>
                <?php if(isset($entityData['entity_category'])): ?>
                <li><strong>Category:</strong> <?php echo e($entityData['entity_category']); ?></li>
                <?php endif; ?>
                <?php if(isset($entityData['entity_brand'])): ?>
                <li><strong>Brand:</strong> <?php echo e($entityData['entity_brand']); ?></li>
                <?php endif; ?>
                <li><strong>Supplier:</strong> <?php echo e($entityData['supplier_name'] ?? 'MaxMed UAE'); ?></li>
                <li><strong>Location:</strong> <?php echo e($entityData['supplier_location'] ?? 'Dubai, UAE'); ?></li>
            </ul>
        </div>
        <?php endif; ?>

        <div class="ai-main-content">
            <h2>Description</h2>
            <p itemprop="text"><?php echo e($articleContent); ?></p>
        </div>

        <?php if(isset($entityData['key_applications'])): ?>
        <div class="ai-applications">
            <h2>Applications</h2>
            <ul>
                <?php $__currentLoopData = $entityData['key_applications']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $application): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li itemprop="applicationCategory"><?php echo e($application); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <?php endif; ?>

        <?php if(isset($entityData['target_industries'])): ?>
        <div class="ai-industries">
            <h2>Target Industries</h2>
            <ul>
                <?php $__currentLoopData = $entityData['target_industries']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $industry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($industry); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <?php endif; ?>

        <?php if(isset($structuredContent['key_features'])): ?>
        <div class="ai-features">
            <h2>Key Features</h2>
            <ul>
                <?php $__currentLoopData = $structuredContent['key_features']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($feature); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <?php endif; ?>

        <?php if(isset($structuredContent['specifications'])): ?>
        <div class="ai-specifications">
            <h2>Specifications</h2>
            <dl>
                <?php $__currentLoopData = $structuredContent['specifications']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $spec => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <dt><?php echo e($spec); ?></dt>
                <dd><?php echo e($value); ?></dd>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </dl>
        </div>
        <?php endif; ?>

        <?php if(isset($entityData['certifications'])): ?>
        <div class="ai-certifications">
            <h2>Certifications & Standards</h2>
            <ul>
                <?php $__currentLoopData = $entityData['certifications']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $certification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($certification); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <?php endif; ?>

        <div class="ai-contact-info" itemscope itemtype="https://schema.org/Organization">
            <h2>Contact Information</h2>
            <meta itemprop="name" content="MaxMed UAE">
            <ul>
                <li><strong>Company:</strong> <span itemprop="legalName">MaxMed Scientific & Laboratory Equipment Trading Co L.L.C</span></li>
                <li><strong>Phone:</strong> <span itemprop="telephone">+971 55 460 2500</span></li>
                <li><strong>Email:</strong> <span itemprop="email">sales@maxmedme.com</span></li>
                <li><strong>Website:</strong> <span itemprop="url">https://maxmedme.com</span></li>
                <li><strong>Location:</strong> 
                    <span itemprop="address" itemscope itemtype="https://schema.org/PostalAddress">
                        <span itemprop="addressRegion">Dubai</span>, 
                        <span itemprop="addressCountry">UAE</span>
                    </span>
                </li>
            </ul>
        </div>

        <?php if(isset($entityData['support_services'])): ?>
        <div class="ai-services">
            <h2>Services Offered</h2>
            <ul>
                <?php $__currentLoopData = $entityData['support_services']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($service); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <?php endif; ?>

        <?php if(isset($entityData['delivery_areas'])): ?>
        <div class="ai-delivery">
            <h2>Delivery Coverage</h2>
            <p>We deliver to: <?php echo e(implode(', ', $entityData['delivery_areas'])); ?></p>
        </div>
        <?php endif; ?>

        <div class="ai-keywords" style="display: none;">
            <h3>Related Keywords</h3>
            <p><?php echo e(implode(', ', $aiContent['semantic_keywords'] ?? [])); ?></p>
        </div>
    </div>
</article>


<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
        {
            "@type": "Question",
            "name": "What is <?php echo e($product ? $product->name : ($category ? $category->name : 'MaxMed UAE')); ?>?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "<?php echo e(strip_tags($articleContent)); ?>"
            }
        },
        <?php if($product): ?>
        {
            "@type": "Question",
            "name": "Where can I buy <?php echo e($product->name); ?> in UAE?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "<?php echo e($product->name); ?> is available from MaxMed UAE, the leading laboratory equipment supplier in Dubai. We serve all emirates including Dubai, Abu Dhabi, Sharjah, and Ajman. Contact us at +971 55 460 2500 for pricing and availability."
            }
        },
        {
            "@type": "Question",
            "name": "What are the key features of <?php echo e($product->name); ?>?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "<?php echo e(implode(', ', $structuredContent['key_features'] ?? ['Professional quality', 'CE certified', 'Technical support included'])); ?>. <?php echo e($product->name); ?> is designed for professional laboratory use with reliable performance and comprehensive support."
            }
        }
        <?php elseif($category): ?>
        {
            "@type": "Question",
            "name": "What types of <?php echo e($category->name); ?> does MaxMed UAE supply?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "MaxMed UAE supplies a comprehensive range of <?php echo e($category->name); ?> equipment including professional-grade instruments from leading manufacturers. We serve hospitals, research institutions, universities, and healthcare facilities across the UAE."
            }
        }
        <?php endif; ?>
    ]
}
</script>


<div class="ai-content-tags" style="display: none;">
    <span class="ai-tag-entity"><?php echo e($product ? $product->name : ($category ? $category->name : 'MaxMed UAE')); ?></span>
    <span class="ai-tag-supplier">MaxMed UAE</span>
    <span class="ai-tag-location">Dubai, UAE</span>
    <span class="ai-tag-industry">Laboratory Equipment</span>
    <span class="ai-tag-category"><?php echo e($product ? ($product->category ? $product->category->name : 'Laboratory Equipment') : ($category ? $category->name : 'Medical Equipment')); ?></span>
    <?php if($product && $product->brand): ?>
    <span class="ai-tag-brand"><?php echo e($product->brand->name); ?></span>
    <?php endif; ?>
    <?php if(isset($entityData['target_industries'])): ?>
    <?php $__currentLoopData = $entityData['target_industries']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $industry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <span class="ai-tag-target-industry"><?php echo e($industry); ?></span>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>
</div>

<style>
.ai-knowledge-article {
    display: none; /* Hidden from users, visible to AI crawlers */
}

.ai-structured-content h1,
.ai-structured-content h2,
.ai-structured-content h3 {
    font-weight: bold;
    margin: 1em 0 0.5em 0;
}

.ai-structured-content ul,
.ai-structured-content ol {
    margin: 0.5em 0;
    padding-left: 2em;
}

.ai-structured-content li {
    margin: 0.25em 0;
}

.ai-structured-content dl {
    margin: 0.5em 0;
}

.ai-structured-content dt {
    font-weight: bold;
    margin-top: 0.5em;
}

.ai-structured-content dd {
    margin-left: 1em;
}

.ai-content-summary {
    border: 1px solid #ddd;
    padding: 1em;
    margin: 1em 0;
    background: #f9f9f9;
}

.ai-entity-info,
.ai-applications,
.ai-industries,
.ai-features,
.ai-specifications,
.ai-certifications,
.ai-contact-info,
.ai-services,
.ai-delivery {
    margin: 1.5em 0;
    padding: 1em;
    border-left: 3px solid #171e60;
}
</style> <?php /**PATH C:\Users\Walid\OneDrive\Desktop\MaxMed\resources\views/components/ai-knowledge-article.blade.php ENDPATH**/ ?>