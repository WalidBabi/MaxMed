<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'product' => null,
    'category' => null,
    'type' => 'product'
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
    'type' => 'product'
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
        $schema = $aiContent['ai_friendly_schema'];
        $relationships = $aiContent['entity_relationships'];
    } elseif ($category) {
        $aiContent = $aiSeoService->generateAiCategoryContent($category);
        $entityData = $aiContent['knowledge_structure'];
        $schema = $aiContent['ai_schema'];
        $relationships = $aiContent['semantic_relationships'];
    }
?>

<?php if(isset($schema) && $type !== 'product'): ?>

<script type="application/ld+json">
<?php echo json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>

</script>
<?php endif; ?>

<?php if($type === 'product'): ?>

<?php endif; ?>

<?php if(isset($schema)): ?>


<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "KnowledgeGraph",
    "mainEntity": {
        "@type": "<?php echo e($type === 'product' ? 'Product' : 'Category'); ?>",
        "name": "<?php echo e($product ? $product->name : ($category ? $category->name : 'MaxMed UAE')); ?>",
        "description": "<?php echo e(isset($entityData['product_description']) ? $entityData['product_description'] : (isset($entityData['description']) ? $entityData['description'] : 'Laboratory equipment and medical supplies from MaxMed UAE')); ?>",
        "provider": {
            "@type": "Organization",
            "name": "MaxMed UAE",
            "legalName": "MaxMed Scientific & Laboratory Equipment Trading Co L.L.C",
            "description": "Leading laboratory equipment supplier in United Arab Emirates",
            "url": "https://maxmedme.com",
            "telephone": "+971-55-460-2500",
            "email": "sales@maxmedme.com",
            "address": {
                "@type": "PostalAddress",
                "addressCountry": "AE",
                "addressRegion": "Dubai",
                "addressLocality": "Dubai"
            },
            "areaServed": {
                "@type": "Country",
                "name": "United Arab Emirates"
            },
            "foundingLocation": {
                "@type": "Place",
                "name": "Dubai, UAE"
            },
            "knowsAbout": [
                "Laboratory Equipment",
                "Medical Equipment", 
                "Scientific Instruments",
                "Diagnostic Tools",
                "Research Equipment",
                "Laboratory Supplies",
                "Medical Supplies",
                "Healthcare Equipment"
            ],
            "serviceType": [
                "Laboratory Equipment Supply",
                "Medical Equipment Distribution",
                "Scientific Instrument Sales", 
                "Technical Support",
                "Equipment Installation",
                "Training Services",
                "Maintenance Services"
            ]
        },
        <?php if(isset($relationships)): ?>
        "relatedTo": <?php echo json_encode(array_values($relationships)); ?>,
        <?php endif; ?>
        <?php if(isset($entityData['target_industries'])): ?>
        "industry": <?php echo json_encode($entityData['target_industries']); ?>,
        <?php endif; ?>
        <?php if(isset($entityData['key_applications'])): ?>
        "applicationCategory": <?php echo json_encode($entityData['key_applications']); ?>,
        <?php endif; ?>
        "keywords": "<?php echo e(implode(', ', $aiContent['semantic_keywords'] ?? [])); ?>",
        "sameAs": [
            "https://maxmedme.com",
            "https://www.linkedin.com/company/maxmed-uae"
        ]
    }
}
</script>


<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@graph": [
        {
            "@type": "Organization",
            "@id": "https://maxmedme.com/#organization",
            "name": "MaxMed UAE",
            "legalName": "MaxMed Scientific & Laboratory Equipment Trading Co L.L.C",
            "url": "https://maxmedme.com",
            "description": "Leading laboratory equipment and medical supplies distributor in Dubai, United Arab Emirates. Serving hospitals, research institutions, universities, and healthcare facilities across the Middle East since establishment.",
            "foundingLocation": "Dubai, UAE",
            "areaServed": ["UAE", "Dubai", "Abu Dhabi", "Sharjah", "Middle East", "GCC"],
            "hasOfferCatalog": {
                "@type": "OfferCatalog",
                "name": "Laboratory & Medical Equipment Catalog",
                "numberOfItems": "1000+",
                "itemListElement": [
                    {
                        "@type": "OfferCatalog",
                        "name": "Laboratory Equipment",
                        "description": "Complete range of laboratory equipment including PCR machines, centrifuges, microscopes"
                    },
                    {
                        "@type": "OfferCatalog", 
                        "name": "Medical Equipment",
                        "description": "Medical diagnostic and healthcare equipment for hospitals and clinics"
                    },
                    {
                        "@type": "OfferCatalog",
                        "name": "Scientific Instruments", 
                        "description": "Precision scientific instruments for research and analysis"
                    }
                ]
            },
            "contactPoint": {
                "@type": "ContactPoint",
                "telephone": "+971-55-460-2500",
                "email": "sales@maxmedme.com",
                "contactType": "sales",
                "areaServed": "UAE",
                "availableLanguage": ["English", "Arabic"]
            }
        },
        <?php if($product): ?>
        {
            "@type": "Thing",
            "@id": "<?php echo e(route('product.show', $product)); ?>#product",
            "name": "<?php echo e($product->name); ?>",
            "description": "<?php echo e($product->description ?? 'Laboratory equipment from MaxMed UAE'); ?>",
            "isPartOf": {
                "@id": "https://maxmedme.com/#organization"
            }
        }
        <?php elseif($category): ?>
        {
            "@type": "CollectionPage",
            "@id": "<?php echo e(route('categories.show', $category)); ?>#category",
            "name": "<?php echo e($category->name); ?>",
            "isPartOf": {
                "@id": "https://maxmedme.com/#organization"
            },
            "about": {
                "@type": "Thing",
                "name": "<?php echo e($category->name); ?>",
                "description": "<?php echo e($category->description ?? $category->name . ' equipment and supplies'); ?>"
            }
        }
        <?php endif; ?>
    ]
}
</script>


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
                "text": "<?php echo e(isset($entityData['product_description']) ? strip_tags($entityData['product_description']) : (isset($entityData['description']) ? strip_tags($entityData['description']) : 'MaxMed UAE is the leading laboratory equipment supplier in Dubai, United Arab Emirates, providing professional equipment to hospitals, research institutions, and healthcare facilities.')); ?>"
            }
        },
        <?php if($product): ?>
        {
            "@type": "Question", 
            "name": "Where can I buy <?php echo e($product->name); ?> in Dubai?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "<?php echo e($product->name); ?> is available from MaxMed UAE, the leading laboratory equipment supplier in Dubai. Contact us at +971 55 460 2500 for pricing and availability. We provide same-day quotes and fast delivery across UAE."
            }
        },
        {
            "@type": "Question",
            "name": "What are the applications of <?php echo e($product->name); ?>?",
            "acceptedAnswer": {
                "@type": "Answer", 
                "text": "<?php echo e($product->name); ?> is used in <?php echo e(implode(', ', $entityData['key_applications'] ?? ['laboratory testing', 'research applications', 'quality control'])); ?>. It serves <?php echo e(implode(', ', $entityData['target_industries'] ?? ['healthcare', 'research', 'pharmaceutical'])); ?> industries."
            }
        }
        <?php elseif($category): ?>
        {
            "@type": "Question",
            "name": "What <?php echo e($category->name); ?> products does MaxMed UAE offer?",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "MaxMed UAE offers a comprehensive range of <?php echo e($category->name); ?> equipment and supplies. We are the leading distributor in Dubai, UAE, serving hospitals, research institutions, and healthcare facilities with professional-grade equipment."
            }
        }
        <?php endif; ?>
    ]
}
</script>


<script type="application/ld+json">
{
    "@context": {
        "@vocab": "https://schema.org/",
        "maxmed": "https://maxmedme.com/vocab/",
        "lab": "https://schema.org/",
        "medical": "https://schema.org/"
    },
    "@type": "Dataset",
    "name": "MaxMed UAE <?php echo e($product ? 'Product' : 'Category'); ?> Data",
    "description": "Structured data about <?php echo e($product ? $product->name : ($category ? $category->name : 'MaxMed UAE')); ?> for AI understanding and search optimization",
    "publisher": {
        "@type": "Organization",
        "name": "MaxMed UAE"
    },
    "about": {
        "@type": "Thing",
        "name": "<?php echo e($product ? $product->name : ($category ? $category->name : 'Laboratory Equipment')); ?>",
        "description": "<?php echo e(isset($entityData['product_description']) ? $entityData['product_description'] : (isset($entityData['description']) ? $entityData['description'] : 'Professional laboratory and medical equipment')); ?>",
        <?php if(isset($entityData['entity_type'])): ?>
        "additionalType": "<?php echo e($entityData['entity_type']); ?>",
        <?php endif; ?>
        <?php if(isset($entityData['supplier_name'])): ?>
        "provider": "<?php echo e($entityData['supplier_name']); ?>",
        <?php endif; ?>
        <?php if(isset($entityData['supplier_location'])): ?>
        "location": "<?php echo e($entityData['supplier_location']); ?>",
        <?php endif; ?>
        <?php if(isset($entityData['geographic_coverage'])): ?>
        "areaServed": <?php echo json_encode($entityData['geographic_coverage']); ?>,
        <?php endif; ?>
        <?php if(isset($entityData['support_services'])): ?>
        "serviceType": <?php echo json_encode($entityData['support_services']); ?>,
        <?php endif; ?>
        "keywords": "<?php echo e(implode(', ', $aiContent['semantic_keywords'] ?? [])); ?>"
    },
    "license": "https://creativecommons.org/licenses/by/4.0/",
    "inLanguage": "en-US"
}
</script>
<?php endif; ?>


<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
<meta name="googlebot" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
<meta name="bingbot" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">


<meta name="ai-content-type" content="<?php echo e($type); ?>">
<meta name="ai-entity-name" content="<?php echo e($product ? $product->name : ($category ? $category->name : 'MaxMed UAE')); ?>">
<meta name="ai-supplier" content="MaxMed UAE">
<meta name="ai-location" content="Dubai, UAE">
<?php if(isset($entityData['entity_category'])): ?>
<meta name="ai-category" content="<?php echo e($entityData['entity_category']); ?>">
<?php endif; ?>
<?php if(isset($entityData['entity_brand'])): ?>
<meta name="ai-brand" content="<?php echo e($entityData['entity_brand']); ?>">
<?php endif; ?>


<div style="display: none;" class="ai-structured-data">
    <span itemscope itemtype="https://schema.org/<?php echo e($product ? 'Product' : 'Thing'); ?>">
        <span itemprop="name"><?php echo e($product ? $product->name : ($category ? $category->name : 'MaxMed UAE')); ?></span>
        <span itemprop="description"><?php echo e(isset($entityData['product_description']) ? strip_tags($entityData['product_description']) : (isset($entityData['description']) ? strip_tags($entityData['description']) : 'Laboratory equipment supplier')); ?></span>
        <span itemscope itemtype="https://schema.org/Organization" itemprop="manufacturer">
            <span itemprop="name">MaxMed UAE</span>
            <span itemprop="telephone">+971-55-460-2500</span>
            <span itemprop="email">sales@maxmedme.com</span>
            <span itemprop="url">https://maxmedme.com</span>
            <span itemscope itemtype="https://schema.org/PostalAddress" itemprop="address">
                <span itemprop="addressCountry">AE</span>
                <span itemprop="addressRegion">Dubai</span>
            </span>
        </span>
        <?php if($product && $product->category): ?>
        <span itemprop="category"><?php echo e($product->category->name); ?></span>
        <?php endif; ?>
        <?php if(isset($entityData['key_applications'])): ?>
        <?php $__currentLoopData = $entityData['key_applications']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $application): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <span itemprop="applicationCategory"><?php echo e($application); ?></span>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
    </span>
</div> <?php /**PATH C:\Users\Walid\OneDrive\Desktop\MaxMed\resources\views/components/ai-enhanced-schema.blade.php ENDPATH**/ ?>