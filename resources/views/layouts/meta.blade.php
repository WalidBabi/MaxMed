<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="@yield('meta_description', 'MaxMed UAE - Leading supplier of medical and laboratory equipment in Dubai. High-quality products, expert service, and innovative solutions for healthcare facilities.')">
<meta name="keywords" content="@yield('meta_keywords', 'medical equipment, laboratory equipment, Dubai, UAE, healthcare supplies, medical supplies')">
<meta name="author" content="MaxMed UAE">
<meta name="robots" content="index, follow">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:title" content="@yield('title', 'MaxMed UAE - Medical & Laboratory Equipment Supplier')">
<meta property="og:description" content="@yield('meta_description')">
<meta property="og:image" content="@yield('og_image', asset('Images/banner2.jpeg'))">

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