<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, shrink-to-fit=no, viewport-fit=cover">
<meta name="description" content="@yield('meta_description', 'MaxMed UAE - Leading supplier of laboratory equipment and medical supplies in Dubai. High-quality scientific instruments, diagnostic tools, and lab technology for healthcare and research facilities throughout UAE.')">
<meta name="keywords" content="@yield('meta_keywords', 'laboratory equipment Dubai, lab instruments UAE, medical equipment supplier, scientific equipment Abu Dhabi, diagnostic tools, research lab supplies, laboratory technology, microscope supplier, centrifuge machine UAE, hospital supplies, laboratory analysis equipment, medical grade refrigerator')">
<meta name="author" content="MaxMed UAE">
<meta name="robots" content="@yield('meta_robots', 'index, follow')">

<!-- Favicon -->
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/favicon/apple-touch-icon.png') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/favicon/favicon-32x32.png') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('img/favicon/favicon-16x16.png') }}">
<link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
<link rel="manifest" href="{{ asset('site.webmanifest') }}">
<link rel="mask-icon" href="{{ asset('img/favicon/safari-pinned-tab.svg') }}" color="#171e60">
<meta name="msapplication-TileColor" content="#171e60">
<meta name="theme-color" content="#171e60">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:title" content="@yield('title', 'MaxMed UAE - Premium Laboratory & Medical Equipment Supplier in Dubai')">
<meta property="og:description" content="@yield('meta_description')">
<meta property="og:image" content="@yield('og_image', asset('Images/banner2.jpeg'))">
<meta property="og:locale" content="en_US">
<meta property="og:site_name" content="MaxMed UAE">

<!-- Twitter -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:url" content="{{ url()->current() }}">
<meta name="twitter:title" content="@yield('title')">
<meta name="twitter:description" content="@yield('meta_description')">
<meta name="twitter:image" content="@yield('og_image', asset('Images/banner2.jpeg'))">

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