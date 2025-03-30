<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, shrink-to-fit=no, viewport-fit=cover">
<meta name="description" content="@yield('meta_description', 'MaxMed UAE - Leading supplier of laboratory equipment and medical supplies in Dubai. High-quality scientific instruments, diagnostic tools, and lab technology for healthcare and research facilities throughout UAE.')">
<meta name="keywords" content="@yield('meta_keywords', 'laboratory equipment Dubai, lab instruments UAE, medical equipment supplier, scientific equipment Abu Dhabi, diagnostic tools, research lab supplies, laboratory technology, microscope supplier, centrifuge machine UAE, hospital supplies, laboratory analysis equipment, medical grade refrigerator')">
<meta name="author" content="MaxMed UAE">
<meta name="robots" content="index, follow">

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

<!-- Canonical URL -->
<link rel="canonical" href="{{ url()->current() }}" />

<!-- Mobile Optimization -->
<meta name="theme-color" content="#171e60">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="format-detection" content="telephone=no">
<link rel="manifest" href="{{ asset('manifest.json') }}">

<!-- Preconnect to External Resources -->
<link rel="preconnect" href="https://fonts.bunny.net">
<link rel="preconnect" href="https://cdn.tailwindcss.com"> 