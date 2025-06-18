<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, shrink-to-fit=no, viewport-fit=cover">
<meta name="description" content="@yield('meta_description', 'MaxMed UAE - Leading supplier of laboratory equipment and medical supplies in Dubai. Contact us at +971 55 460 2500 or email sales@maxmedme.com. High-quality scientific instruments, diagnostic tools, and lab technology for healthcare and research facilities throughout UAE.')">
<meta name="keywords" content="@yield('meta_keywords', 'laboratory equipment Dubai, lab instruments UAE, medical equipment supplier, scientific equipment Abu Dhabi, diagnostic tools, research lab supplies, laboratory technology, microscope supplier, centrifuge machine UAE, hospital supplies, laboratory analysis equipment, medical grade refrigerator, contact MaxMed, MaxMed phone number')">
<meta name="author" content="MaxMed UAE">
<meta name="robots" content="@yield('meta_robots', 'index, follow')">

<!-- Contact Information for Search Engines -->
<meta name="contact:phone" content="+971 55 460 2500">
<meta name="contact:email" content="sales@maxmedme.com">

<!-- RSS Feed Links for Content Discovery -->
<link rel="alternate" type="application/rss+xml" title="MaxMed UAE - Latest Updates" href="{{ url('/rss/feed.xml') }}">
<link rel="alternate" type="application/rss+xml" title="MaxMed UAE - Latest News" href="{{ url('/rss/news.xml') }}">
<link rel="alternate" type="application/rss+xml" title="MaxMed UAE - Latest Products" href="{{ url('/rss/products.xml') }}">

<!-- Hreflang for International SEO -->
<link rel="alternate" hreflang="en" href="{{ url()->current() }}">
<link rel="alternate" hreflang="en-ae" href="{{ url()->current() }}">
<link rel="alternate" hreflang="en-sa" href="{{ url()->current() }}">
<link rel="alternate" hreflang="en-qa" href="{{ url()->current() }}">
<link rel="alternate" hreflang="en-kw" href="{{ url()->current() }}">
<link rel="alternate" hreflang="en-om" href="{{ url()->current() }}">
<link rel="alternate" hreflang="en-bh" href="{{ url()->current() }}">
<link rel="alternate" hreflang="x-default" href="{{ url()->current() }}">

<!-- Favicon -->
<link rel="icon" type="image/png" sizes="96x96" href="{{ asset('img/favicon/favicon-96x96.png') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/favicon/favicon-32x32.png') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('img/favicon/favicon-16x16.png') }}">
<link rel="icon" type="image/svg+xml" href="{{ asset('img/favicon/favicon.svg') }}">
<link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/favicon/apple-touch-icon.png') }}">
<meta name="apple-mobile-web-app-title" content="MaxMed UAE">
<link rel="manifest" href="{{ asset('site.webmanifest') }}">
<link rel="mask-icon" href="{{ asset('img/favicon/safari-pinned-tab.svg') }}" color="#171e60">
<meta name="msapplication-TileColor" content="#171e60">
<meta name="msapplication-config" content="{{ asset('browserconfig.xml') }}">
<meta name="theme-color" content="#171e60">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:title" content="@yield('og_title', 'MaxMed UAE - Premium Laboratory & Medical Equipment Supplier in Dubai')">
<meta property="og:description" content="@yield('og_description', 'Leading supplier of laboratory equipment and medical supplies in Dubai, UAE. Contact us at +971 55 460 2500.')">
@php
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
@endphp
<meta property="og:image" content="{{ $ogImage }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:locale" content="en_US">
<meta property="og:site_name" content="MaxMed UAE">
<meta property="og:phone_number" content="+971 55 460 2500">
<meta property="og:email" content="sales@maxmedme.com">

<!-- Twitter -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:url" content="{{ url()->current() }}">
<meta name="twitter:title" content="@yield('title')">
<meta name="twitter:description" content="@yield('meta_description')">
<meta name="twitter:image" content="{{ $ogImage }}">

<!-- Additional SEO Meta Tags -->
<meta name="geo.region" content="AE-DU">
<meta name="geo.placename" content="Dubai">
<meta name="geo.position" content="25.2048;55.2708">
<meta name="ICBM" content="25.2048, 55.2708">

<!-- Business Information -->
<meta name="business:hours" content="Monday-Friday 09:00-18:00">
<meta name="business:hours:day" content="monday tuesday wednesday thursday friday">
<meta name="business:hours:time" content="09:00-18:00">

<!-- Canonical URL with proper domain consistency -->
@php
    $currentUrl = url()->current();
    $preferredDomain = 'https://maxmedme.com';
    
    // Replace www with non-www in canonical URLs
    $canonicalUrl = str_replace('https://www.maxmedme.com', $preferredDomain, $currentUrl);
    
    // Handle product pages - ensure consistent URL structure
    if (request()->route() && request()->route()->getName() === 'product.show') {
        $product = request()->route('product');
        if ($product && is_object($product) && method_exists($product, 'getKey')) {
            $canonicalUrl = $preferredDomain . '/product/' . $product->getKey();
        }
    }
    
    // Handle category pages - ensure consistent URL structure
    if (request()->route() && strpos(request()->route()->getName(), 'categories.') === 0) {
        // Get all route parameters
        $routeParams = request()->route()->parameters();
        
        // For nested category pages, always use the deepest category as canonical
        if (isset($routeParams['category'])) {
            $category = $routeParams['category'];
            
            // Start with basic category URL
            $canonicalUrl = $preferredDomain . '/categories/' . $category->id;
            
            // Add subcategory if exists
            if (isset($routeParams['subcategory'])) {
                $subcategory = $routeParams['subcategory'];
                $canonicalUrl .= '/' . $subcategory->id;
                
                // Add subsubcategory if exists
                if (isset($routeParams['subsubcategory'])) {
                    $subsubcategory = $routeParams['subsubcategory'];
                    $canonicalUrl .= '/' . $subsubcategory->id;
                }
            }
        }
    }
    
    // Handle products index with query parameters
    if (request()->route() && request()->route()->getName() === 'products.index') {
        // Handle products with category query parameter - make them canonical to themselves
        // This ensures that product category pages can be indexed by search engines
        if (request()->has('category')) {
            $canonicalUrl = $preferredDomain . '/products?category=' . request()->get('category');
        }
    }
@endphp
<link rel="canonical" href="{{ $canonicalUrl }}" />

<!-- Mobile Optimization -->
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="format-detection" content="telephone=no">

<!-- Preconnect to External Resources -->
<link rel="preconnect" href="https://fonts.bunny.net">
<link rel="preconnect" href="https://cdn.tailwindcss.com">

<!-- Schema Markup -->
@include('components.schema-markup') 