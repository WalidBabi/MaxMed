<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @include('layouts.meta')
        <title>@yield('title', 'MaxMed UAE - Medical & Laboratory Equipment Supplier')</title>

        <!-- Preconnect to external domains -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link rel="preconnect" href="https://www.googletagmanager.com">
        <link rel="preconnect" href="https://cdn.jsdelivr.net">
        
        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

        <!-- Critical CSS inline -->
        <style>
            /* Critical path CSS only */
            :root {
                --brand-main: #171e60;
                --brand-auxiliary: #0a5694;
                --brand-white: #ffffff;
            }
            
            body {
                margin: 0;
                font-family: system-ui, -apple-system, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
                overflow-x: hidden;
                width: 100%;
            }
            
            img {
                max-width: 100%;
                height: auto;
            }
            
            .bg-primary { background-color: var(--brand-main) !important; }
            .text-primary { color: var(--brand-main) !important; }
            
            /* Lazy loading */
            .lazy-image {
                opacity: 0;
                transition: opacity 0.3s ease-in;
            }
            .lazy-image.loaded { opacity: 1; }
            
            /* Sidebar toggle transitions */
            .sidebar-column {
                transition: width 0.3s ease-in-out !important;
                will-change: width;
                overflow: hidden;
                position: relative;
                padding-left: 1rem !important; /* Add padding to prevent cutoff */
                padding-right: 0.5rem !important; /* Add padding to prevent right cutoff */
            }
            
            .main-content-column {
                transition: width 0.3s ease-in-out !important;
                will-change: width;
                transform: translateZ(0);
                padding-left: 1rem; /* Consistent padding to prevent layout shift */
            }
            
            .collapsed-sidebar-active {
                width: calc(100% - 65px) !important;
                margin-left: 0 !important; /* Remove left margin when collapsed */
                padding-left: 0.5rem !important; /* Reduce padding when collapsed */
            }
            
            .sidebar-content-container {
                transition: all 0.3s ease-in-out !important;
                overflow-x: hidden;
                transform: translateZ(0);
                padding-left: 0.5rem !important; /* Add consistent padding */
                padding-right: 0.5rem !important; /* Add consistent padding */
            }
            
            /* Prevent layout shifts */
            .category-container, .subcategory-container, .products-grid {
                transition: opacity 0.3s ease-in-out;
                transform: translateZ(0);
                backface-visibility: hidden;
                perspective: 1000px;
                contain: layout style paint; /* Modern browsers will use content-visibility */
            }
            
            /* Force hardware acceleration to reduce visual flickering */
            .row, .col-md-3, .col-md-9 {
                transform: translateZ(0);
                backface-visibility: hidden;
            }
        </style>

        <!-- Preload LCP image -->
        <link rel="preload" as="image" fetchpriority="high" href="{{ asset('Images/optimized/banner-optimized.webp') }}" type="image/webp">

        <!-- Deferred CSS -->
        <link rel="stylesheet" href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" media="print" onload="this.media='all'">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" media="print" onload="this.media='all'">
        <link href="{{ asset('css/custom.css') }}" rel="stylesheet" media="print" onload="this.media='all'">
        <link rel="stylesheet" href="{{ asset('css/mobile.css') }}" media="print" onload="this.media='all'">
        
        <!-- Fallbacks for browsers without JS -->
        <noscript>
            <link rel="stylesheet" href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap">
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
            <link rel="stylesheet" href="{{ asset('css/mobile.css') }}">
        </noscript>

        <!-- Vite assets with defer for JS -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Image Loading Script -->
        <script>
            // Inline critical JS for image loading
            document.addEventListener("DOMContentLoaded", function() {
                var lazyImages = [].slice.call(document.querySelectorAll("img.lazy-image"));
                if ("IntersectionObserver" in window) {
                    let lazyImageObserver = new IntersectionObserver(function(entries, observer) {
                        entries.forEach(function(entry) {
                            if (entry.isIntersecting) {
                                let lazyImage = entry.target;
                                lazyImage.src = lazyImage.dataset.src;
                                if(lazyImage.dataset.srcset) {
                                    lazyImage.srcset = lazyImage.dataset.srcset;
                                }
                                lazyImage.classList.add("loaded");
                                lazyImageObserver.unobserve(lazyImage);
                            }
                        });
                    });
                    lazyImages.forEach(function(lazyImage) {
                        lazyImageObserver.observe(lazyImage);
                    });
                }
            });
        </script>

        @stack('head')
        
        <!-- Defer non-critical JS -->
        <script defer src="https://www.googletagmanager.com/gtag/js?id=G-5JRSRT4MLZ"></script>
        <script>
            window.addEventListener('load', function() {
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());
                gtag('config', 'G-5JRSRT4MLZ');
            });
        </script>
    </head>
    <body class="font-sans antialiased bg-gray-50 relative">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Flash Messages -->
            <div class="container">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        {{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            </div>

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                @yield('content')
            </main>

        @stack('scripts')

        <!-- Defer non-essential JS -->
        <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        
        <script>
            // Wait until the entire page is loaded, then hide the loader.
            window.addEventListener('load', function(){
                const loader = document.getElementById('loader');
                if (loader) {
                    loader.style.display = 'none';
                }
            });
        </script>

        <!-- WhatsApp Floating Widget -->
        <div class="fixed bottom-6 right-6 z-50">
            <a href="https://wa.me/971554602500" target="_blank" class="flex items-center justify-center w-14 h-14 bg-green-500 rounded-full shadow-lg hover:bg-green-600 transition-all duration-300 transform hover:scale-110 hover:-translate-y-1 group" aria-label="Contact us on WhatsApp">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                </svg>
                <div class="absolute right-16 bg-white text-gray-700 px-4 py-2 rounded-lg shadow-lg opacity-0 invisible transform translate-x-2 group-hover:opacity-100 group-hover:visible group-hover:translate-x-0 transition-all duration-300 whitespace-nowrap">
                    Chat with us on WhatsApp
                </div>
            </a>
        </div>
    </body>
</html>
