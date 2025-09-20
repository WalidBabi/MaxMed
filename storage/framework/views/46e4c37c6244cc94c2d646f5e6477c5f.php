<?php
    $currentUrl = url()->current();
    $preferredDomain = 'https://maxmedme.com';
    
    // Initialize canonical URL
    $canonicalUrl = $currentUrl;
    
    // Always use non-www version for canonical URLs
    $canonicalUrl = str_replace('https://www.maxmedme.com', $preferredDomain, $canonicalUrl);
    $canonicalUrl = str_replace('http://www.maxmedme.com', $preferredDomain, $canonicalUrl);
    $canonicalUrl = str_replace('http://maxmedme.com', $preferredDomain, $canonicalUrl);
    
    // Handle product pages - ensure consistent URL structure
    if (request()->route() && request()->route()->getName() === 'product.show') {
        $product = request()->route('product');
        if ($product && is_object($product) && method_exists($product, 'getSlug')) {
            $canonicalUrl = $preferredDomain . '/products/' . $product->getSlug();
        }
    }
    
    // Handle category pages - ensure consistent URL structure
    if (request()->route() && strpos(request()->route()->getName(), 'categories.') === 0) {
        $category = request()->route('category');
        if ($category && is_object($category) && method_exists($category, 'getSlug')) {
            $canonicalUrl = $preferredDomain . '/categories/' . $category->getSlug();
        }
    }
    
    // Handle news pages
    if (request()->route() && request()->route()->getName() === 'news.show') {
        $news = request()->route('news');
        if ($news && is_object($news) && method_exists($news, 'getSlug')) {
            $canonicalUrl = $preferredDomain . '/news/' . $news->getSlug();
        }
    }
    
    // Remove query parameters from canonical URLs (except essential ones)
    $parsedUrl = parse_url($canonicalUrl);
    if (isset($parsedUrl['query'])) {
        parse_str($parsedUrl['query'], $queryParams);
        
        // Keep only essential query parameters
        $essentialParams = ['utm_source', 'utm_medium', 'utm_campaign'];
        $filteredParams = array_intersect_key($queryParams, array_flip($essentialParams));
        
        if (empty($filteredParams)) {
            // Remove query string entirely if no essential params
            $canonicalUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . $parsedUrl['path'];
        } else {
            // Rebuild URL with only essential params
            $canonicalUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . $parsedUrl['path'] . '?' . http_build_query($filteredParams);
        }
    }
    
    // Remove trailing slash for consistency (except for homepage)
    if ($canonicalUrl !== $preferredDomain && substr($canonicalUrl, -1) === '/') {
        $canonicalUrl = rtrim($canonicalUrl, '/');
    }
    
    // Ensure homepage canonical is exactly the preferred domain
    if ($canonicalUrl === $preferredDomain . '/') {
        $canonicalUrl = $preferredDomain;
    }
?>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, shrink-to-fit=no, viewport-fit=cover">
<meta name="description" content="<?php echo $__env->yieldContent('meta_description', '🔬 MaxMed UAE - Leading lab equipment supplier in Dubai! PCR machines, centrifuges, fume hoods, dental supplies & more ✅ Same-day quotes ☎️ +971 55 460 2500 🚚 Fast delivery'); ?>">
<meta name="keywords" content="<?php echo $__env->yieldContent('meta_keywords', 'laboratory equipment Dubai, lab instruments UAE, medical equipment supplier, fume hood suppliers UAE, dental consumables, PCR machine suppliers UAE, centrifuge suppliers, benchtop autoclave, dental supplies UAE, veterinary diagnostics UAE, point of care testing equipment, contact MaxMed, MaxMed phone number'); ?>">
<meta name="author" content="MaxMed UAE">
<meta name="robots" content="<?php echo $__env->yieldContent('meta_robots', 'index, follow'); ?>">

<!-- Enhanced Meta Tags for Better CTR -->
<meta name="application-name" content="MaxMed UAE">
<meta name="apple-mobile-web-app-title" content="MaxMed UAE">
<meta name="msapplication-TileColor" content="#171e60">
<meta name="theme-color" content="#171e60">

<!-- Contact Information for Search Engines -->
<meta name="contact:phone" content="+971 55 460 2500">
<meta name="contact:email" content="sales@maxmedme.com">

<!-- Enhanced Social Media Meta Tags -->
<meta property="og:site_name" content="MaxMed UAE">
<meta property="og:type" content="website">
<meta property="og:locale" content="en_US">
<meta property="og:locale:alternate" content="ar_AE">

<!-- Enhanced Twitter Cards -->
<meta name="twitter:site" content="@maxmeduae">
<meta name="twitter:creator" content="@maxmeduae">
<meta name="twitter:card" content="summary_large_image">

<!-- Business Information for Rich Snippets -->
<meta name="business:hours" content="Monday-Friday 09:00-18:00">
<meta name="business:hours:day" content="monday tuesday wednesday thursday friday">
<meta name="business:hours:time" content="09:00-18:00">

<!-- RSS Feed Links for Content Discovery -->
<link rel="alternate" type="application/rss+xml" title="MaxMed UAE - Latest Updates" href="<?php echo e(url('/rss/feed.xml')); ?>">
<link rel="alternate" type="application/rss+xml" title="MaxMed UAE - Latest News" href="<?php echo e(url('/rss/news.xml')); ?>">
<link rel="alternate" type="application/rss+xml" title="MaxMed UAE - Latest Products" href="<?php echo e(url('/rss/products.xml')); ?>">

<!-- Enhanced Hreflang for International SEO -->
<link rel="alternate" hreflang="en" href="<?php echo e($canonicalUrl); ?>">
<link rel="alternate" hreflang="en-ae" href="<?php echo e($canonicalUrl); ?>">
<link rel="alternate" hreflang="en-sa" href="<?php echo e($canonicalUrl); ?>">
<link rel="alternate" hreflang="en-qa" href="<?php echo e($canonicalUrl); ?>">
<link rel="alternate" hreflang="en-kw" href="<?php echo e($canonicalUrl); ?>">
<link rel="alternate" hreflang="en-om" href="<?php echo e($canonicalUrl); ?>">
<link rel="alternate" hreflang="en-bh" href="<?php echo e($canonicalUrl); ?>">
<link rel="alternate" hreflang="en-cn" href="<?php echo e($canonicalUrl); ?>">
<link rel="alternate" hreflang="en-hk" href="<?php echo e($canonicalUrl); ?>">
<link rel="alternate" hreflang="en-in" href="<?php echo e($canonicalUrl); ?>">
<link rel="alternate" hreflang="en-za" href="<?php echo e($canonicalUrl); ?>">
<link rel="alternate" hreflang="zh-cn" href="<?php echo e($canonicalUrl); ?>">
<link rel="alternate" hreflang="zh-hk" href="<?php echo e($canonicalUrl); ?>">
<link rel="alternate" hreflang="zh-tw" href="<?php echo e($canonicalUrl); ?>">
<link rel="alternate" hreflang="ar-ae" href="<?php echo e($canonicalUrl); ?>">
<link rel="alternate" hreflang="x-default" href="<?php echo e($canonicalUrl); ?>">

<!-- Enhanced Favicon for Google Search Results -->
<link rel="icon" type="image/x-icon" href="<?php echo e(asset('favicon.ico')); ?>">
<link rel="icon" type="image/png" sizes="96x96" href="<?php echo e(asset('img/favicon/favicon-96x96.png')); ?>">
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo e(asset('img/favicon/favicon-32x32.png')); ?>">
<link rel="icon" type="image/png" sizes="16x16" href="<?php echo e(asset('img/favicon/favicon-16x16.png')); ?>">
<link rel="icon" type="image/svg+xml" href="<?php echo e(asset('img/favicon/favicon.svg')); ?>">
<link rel="shortcut icon" href="<?php echo e(asset('favicon.ico')); ?>">
<link rel="apple-touch-icon" sizes="180x180" href="<?php echo e(asset('img/favicon/apple-touch-icon.png')); ?>">
<link rel="manifest" href="<?php echo e(asset('site.webmanifest')); ?>">
<link rel="mask-icon" href="<?php echo e(asset('img/favicon/safari-pinned-tab.svg')); ?>" color="#171e60">
<meta name="msapplication-config" content="<?php echo e(asset('browserconfig.xml')); ?>">

<!-- Additional favicon formats for better browser compatibility -->
<link rel="icon" type="image/png" sizes="192x192" href="<?php echo e(asset('img/favicon/web-app-manifest-192x192.png')); ?>">
<link rel="icon" type="image/png" sizes="512x512" href="<?php echo e(asset('img/favicon/web-app-manifest-512x512.png')); ?>">

<!-- Google-specific favicon meta tags for better search results -->
<meta name="google-site-verification" content="your-google-verification-code">
<meta name="msapplication-TileImage" content="<?php echo e(asset('img/favicon/mstile-150x150.png')); ?>">
<meta name="msapplication-TileColor" content="#171e60">

<!-- Structured Data for Organization/Brand -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "MaxMed UAE",
  "alternateName": "MaxMed",
  "url": "<?php echo e($canonicalUrl); ?>",
  "logo": "<?php echo e(asset('img/favicon/favicon-96x96.png')); ?>",
  "image": "<?php echo e(asset('img/favicon/favicon-96x96.png')); ?>",
  "description": "Leading supplier of laboratory equipment and medical supplies in Dubai, UAE",
  "address": {
    "@type": "PostalAddress",
    "addressCountry": "AE",
    "addressRegion": "Dubai"
  },
  "contactPoint": {
    "@type": "ContactPoint",
    "telephone": "+971-55-460-2500",
    "contactType": "customer service",
    "availableLanguage": ["English", "Arabic"]
  },
  "sameAs": [
    "https://www.linkedin.com/company/maxmed-uae",
    "https://www.facebook.com/maxmeduae"
  ]
}
</script>

<!-- Enhanced Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:url" content="<?php echo e($canonicalUrl); ?>">
<meta property="og:title" content="<?php echo $__env->yieldContent('og_title', 'MaxMed UAE - Premium Laboratory & Medical Equipment Supplier in Dubai'); ?>">
<meta property="og:description" content="<?php echo $__env->yieldContent('og_description', 'Leading supplier of laboratory equipment and medical supplies in Dubai, UAE. Contact us at +971 55 460 2500.'); ?>">
<?php
    $ogImage = null;
    // Dynamic OG image for products
    if (request()->route() && request()->route()->getName() === 'product.show') {
        $product = request()->route('product');
        if ($product && is_object($product) && method_exists($product, 'image_url')) {
            $ogImage = $product->image_url;
        }
    }
    // Dynamic OG image for categories
    elseif (request()->route() && strpos(request()->route()->getName(), 'categories.') === 0) {
        $category = request()->route('category');
        if ($category && is_object($category) && method_exists($category, 'image_url')) {
            $ogImage = $category->image_url;
        }
    }
    // Dynamic OG image for news
    elseif (request()->route() && request()->route()->getName() === 'news.show') {
        $news = request()->route('news');
        if ($news && is_object($news) && method_exists($news, 'image_url')) {
            $ogImage = $news->image_url;
        }
    }
    // Fallback to banner image
    if (!$ogImage) {
        $ogImage = asset('Images/banner2.jpeg');
    }
?>
<meta property="og:image" content="<?php echo e($ogImage); ?>">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:locale" content="en_US">
<meta property="og:site_name" content="MaxMed UAE">
<meta property="og:phone_number" content="+971 55 460 2500">
<meta property="og:email" content="sales@maxmedme.com">

<!-- Enhanced Twitter -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:url" content="<?php echo e($canonicalUrl); ?>">
<meta name="twitter:title" content="<?php echo $__env->yieldContent('title'); ?>">
<meta name="twitter:description" content="<?php echo $__env->yieldContent('meta_description'); ?>">
<meta name="twitter:image" content="<?php echo e($ogImage); ?>">

<!-- Enhanced SEO Meta Tags -->
<meta name="geo.region" content="AE-DU">
<meta name="geo.placename" content="Dubai">
<meta name="geo.position" content="25.2048;55.2708">
<meta name="ICBM" content="25.2048, 55.2708">

<!-- Enhanced Business Information -->
<meta name="business:hours" content="Monday-Friday 09:00-18:00">
<meta name="business:hours:day" content="monday tuesday wednesday thursday friday">
<meta name="business:hours:time" content="09:00-18:00">

<!-- Canonical URL -->
<link rel="canonical" href="<?php echo e($canonicalUrl); ?>" />

<!-- Enhanced Mobile Optimization -->
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="format-detection" content="telephone=no">
<meta name="apple-mobile-web-app-capable" content="yes">

<!-- Enhanced Preconnect to External Resources -->
<link rel="preconnect" href="https://fonts.bunny.net">
<link rel="preconnect" href="https://cdn.tailwindcss.com">
<link rel="preconnect" href="https://www.googletagmanager.com">
<link rel="preconnect" href="https://cdn.jsdelivr.net">

<!-- Enhanced Schema Markup -->
<?php echo $__env->make('components.schema-markup', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<!-- Enhanced Performance Meta Tags -->
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, shrink-to-fit=no, viewport-fit=cover">
<meta name="theme-color" content="#171e60">
<meta name="msapplication-TileColor" content="#171e60">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

<!-- Enhanced Security Meta Tags -->
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="referrer" content="strict-origin-when-cross-origin">

<!-- Enhanced Accessibility Meta Tags -->
<meta name="description" content="<?php echo $__env->yieldContent('meta_description', '🔬 MaxMed UAE - Leading lab equipment supplier in Dubai! PCR machines, centrifuges, fume hoods, dental supplies & more ✅ Same-day quotes ☎️ +971 55 460 2500 🚚 Fast delivery'); ?>">
<meta name="keywords" content="<?php echo $__env->yieldContent('meta_keywords', 'laboratory equipment Dubai, lab instruments UAE, medical equipment supplier, fume hood suppliers UAE, dental consumables, PCR machine suppliers UAE, centrifuge suppliers, benchtop autoclave, dental supplies UAE, veterinary diagnostics UAE, point of care testing equipment, contact MaxMed, MaxMed phone number'); ?>"> <?php /**PATH C:\Users\Walid\OneDrive\Desktop\MaxMed\resources\views/layouts/meta.blade.php ENDPATH**/ ?>