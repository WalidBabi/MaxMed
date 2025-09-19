<?php
// Get current route and determine appropriate schema
$route = request()->route();
$routeName = $route ? $route->getName() : '';
$organization = [
    "@context" => "https://schema.org",
    "@type" => "Organization",
    "name" => "MaxMed UAE",
    "alternateName" => "MaxMed",
    "url" => "https://maxmedme.com",
    "logo" => asset('Images/logo.png'),
    "contactPoint" => [
        "@type" => "ContactPoint",
        "telephone" => "+971-55-460-2500",
        "contactType" => "Sales",
        "email" => "sales@maxmedme.com",
        "areaServed" => ["AE", "SA", "QA", "KW", "OM", "BH"],
        "availableLanguage" => ["English", "Arabic"]
    ],
    "address" => [
        "@type" => "PostalAddress",
        "addressCountry" => "AE",
        "addressRegion" => "Dubai"
    ],
    "sameAs" => [
        "https://www.linkedin.com/company/maxmed-uae"
    ]
];
?>

<script type="application/ld+json">
<?php echo json_encode($organization, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>

</script>


<?php if(($routeName === 'product.show' && isset($product)) || (request()->route() && request()->route()->getName() === 'product.show')): ?>
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Product",
    "name": "<?php echo e(is_object($product) && isset($product->name) ? $product->name : 'Product'); ?>",
    "description": "<?php echo e(is_object($product) && isset($product->description) ? strip_tags(Str::limit($product->description, 300)) : 'Laboratory equipment and medical supplies'); ?>",
    "sku": "<?php echo e(is_object($product) && isset($product->sku) ? $product->sku : ''); ?>",
    "mpn": "<?php echo e(is_object($product) && isset($product->sku) ? $product->sku : ''); ?>",
    "brand": {
        "@type": "Brand",
        "name": "<?php echo e(is_object($product) && is_object($product->brand) && isset($product->brand->name) ? $product->brand->name : 'MaxMed'); ?>"
    },
    "manufacturer": {
        "@type": "Organization",
        "name": "<?php echo e(is_object($product) && is_object($product->brand) && isset($product->brand->name) ? $product->brand->name : 'MaxMed UAE'); ?>"
    },
    "image": [
        "<?php echo e(is_object($product) && isset($product->image_url) ? $product->image_url : asset('Images/logo.png')); ?>"
        <?php if(is_object($product) && isset($product->images) && $product->images && $product->images->count() > 0): ?>
            <?php $__currentLoopData = $product->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            ,"<?php echo e(is_object($image) && isset($image->image_url) ? $image->image_url : ''); ?>"
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
    ],
    "url": "<?php echo e(is_object($product) ? route('product.show', $product) : url()->current()); ?>",
    "category": "<?php echo e(is_object($product) && is_object($product->category) && isset($product->category->name) ? $product->category->name : 'Laboratory Equipment'); ?>",
    "offers": {
        "@type": "Offer",
        "url": "<?php echo e(is_object($product) ? route('product.show', $product) : url()->current()); ?>",
        "priceCurrency": "AED",
        "price": "<?php echo e(is_object($product) && isset($product->price_aed) ? $product->price_aed : (is_object($product) && isset($product->price) ? $product->price : '0')); ?>",
        "priceValidUntil": "<?php echo e(now()->addMonths(6)->format('Y-m-d')); ?>",
        "availability": "<?php echo e(is_object($product) && method_exists($product, 'inStock') ? ($product->inStock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock') : 'https://schema.org/InStock'); ?>",
        "itemCondition": "https://schema.org/NewCondition",
        "seller": {
            "@type": "Organization",
            "name": "MaxMed UAE",
            "url": "https://maxmedme.com"
        },
        "deliveryLeadTime": {
            "@type": "QuantitativeValue",
            "minValue": "2",
            "maxValue": "7",
            "unitCode": "DAY"
        },
        "shippingDetails": {
            "@type": "OfferShippingDetails",
            "shippingRate": {
                "@type": "MonetaryAmount",
                "value": "0",
                "currency": "AED"
            },
            "shippingDestination": [
                {
                    "@type": "DefinedRegion",
                    "addressCountry": "AE",
                    "addressRegion": ["Dubai", "Abu Dhabi", "Sharjah", "Ajman", "Ras Al Khaimah", "Fujairah", "Umm Al Quwain"]
                },
                {
                    "@type": "DefinedRegion",
                    "addressCountry": ["SA", "QA", "KW", "OM", "BH"]
                }
            ],
            "deliveryTime": {
                "@type": "ShippingDeliveryTime",
                "handlingTime": {
                    "@type": "QuantitativeValue",
                    "minValue": "1",
                    "maxValue": "2",
                    "unitCode": "DAY"
                },
                "transitTime": {
                    "@type": "QuantitativeValue", 
                    "minValue": "1",
                    "maxValue": "5", 
                    "unitCode": "DAY"
                }
            }
        },
        "hasMerchantReturnPolicy": {
            "@type": "MerchantReturnPolicy",
            "applicableCountry": "AE",
            "returnPolicyCategory": "https://schema.org/MerchantReturnFiniteReturnWindow",
            "merchantReturnDays": 30,
            "returnMethod": "https://schema.org/ReturnByMail",
            "returnFees": "https://schema.org/FreeReturn"
        }
    },
    "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "<?php echo e(is_object($product) && isset($product->average_rating) ? $product->average_rating : 4.8); ?>",
        "reviewCount": "<?php echo e(is_object($product) && isset($product->review_count) ? $product->review_count : 15); ?>",
        "bestRating": "5",
        "worstRating": "1"
    },
    "review": [
        {
            "@type": "Review",
            "reviewRating": {
                "@type": "Rating",
                "ratingValue": "5",
                "bestRating": "5"
            },
            "author": {
                "@type": "Person",
                "name": "Healthcare Professional"
            },
            "reviewBody": "Excellent quality <?php echo e(strtolower(is_object($product) && is_object($product->category) && isset($product->category->name) ? $product->category->name : 'laboratory equipment')); ?>. Fast delivery and great customer service from MaxMed UAE."
        }
    ]
}
</script>
<?php endif; ?>


<?php if($routeName === 'products.index' || strpos($routeName, 'categories.') === 0): ?>
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "ItemList",
    "name": "Laboratory Equipment & Medical Supplies",
    "description": "Comprehensive collection of laboratory equipment and medical supplies from MaxMed UAE",
    "url": "<?php echo e(url()->current()); ?>"
}
</script>
<?php endif; ?>


<?php if($routeName === 'news.show' && isset($news)): ?>
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "NewsArticle",
    "headline": "<?php echo e(is_object($news) && isset($news->title) ? $news->title : 'News Article'); ?>",
    "description": "<?php echo e(is_object($news) && isset($news->content) ? strip_tags(Str::limit($news->content, 300)) : 'Latest news and updates from MaxMed UAE'); ?>",
    "author": {
        "@type": "Organization",
        "name": "MaxMed UAE"
    },
    "publisher": {
        "@type": "Organization",
        "name": "MaxMed UAE",
        "logo": {
            "@type": "ImageObject",
            "url": "<?php echo e(asset('Images/logo.png')); ?>"
        }
    },
    "datePublished": "<?php echo e(is_object($news) && isset($news->created_at) ? $news->created_at->toISOString() : now()->toISOString()); ?>",
    "dateModified": "<?php echo e(is_object($news) && isset($news->updated_at) ? $news->updated_at->toISOString() : now()->toISOString()); ?>",
    "url": "<?php echo e(is_object($news) ? route('news.show', $news) : url()->current()); ?>"
}
</script>
<?php endif; ?>


<?php if(isset($breadcrumbs) && count($breadcrumbs) > 1): ?>
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
        <?php $__currentLoopData = $breadcrumbs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $breadcrumb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        {
            "@type": "ListItem",
            "position": <?php echo e($index + 1); ?>,
            "name": "<?php echo e(is_array($breadcrumb) && isset($breadcrumb['name']) ? $breadcrumb['name'] : (is_string($breadcrumb) ? $breadcrumb : 'Page ' . ($index + 1))); ?>",
            "item": "<?php echo e(is_array($breadcrumb) && isset($breadcrumb['url']) ? $breadcrumb['url'] : url()->current()); ?>"
        }<?php if(!$loop->last): ?>,<?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    ]
}
</script>
<?php endif; ?>


<?php if($routeName === 'contact'): ?>
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "LocalBusiness",
    "name": "MaxMed UAE",
    "description": "Leading supplier of laboratory equipment and medical supplies in UAE",
    "image": "<?php echo e(asset('Images/logo.png')); ?>",
    "telephone": "+971-55-460-2500",
    "email": "sales@maxmedme.com",
    "url": "https://maxmedme.com",
    "address": {
        "@type": "PostalAddress",
        "addressCountry": "AE",
        "addressRegion": "Dubai"
    },
    "geo": {
        "@type": "GeoCoordinates",
        "latitude": 25.2048,
        "longitude": 55.2708
    },
    "openingHours": "Mo-Fr 09:00-18:00",
    "priceRange": "$$",
    "serviceArea": {
        "@type": "Country",
        "name": "United Arab Emirates"
    }
}
</script>
<?php endif; ?>


<?php if(isset($faqs) && count($faqs) > 0): ?>
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
        <?php $__currentLoopData = $faqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        {
            "@type": "Question",
            "name": "<?php echo e($faq['question']); ?>",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "<?php echo e($faq['answer']); ?>"
            }
        }<?php if(!$loop->last): ?>,<?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    ]
}
</script>
<?php endif; ?> <?php /**PATH C:\Users\Walid\OneDrive\Desktop\MaxMed\resources\views/components/schema-markup.blade.php ENDPATH**/ ?>