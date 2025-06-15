<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="user-authenticated" content="{{ auth()->check() ? 'true' : 'false' }}">

        @include('layouts.meta')
        <title>@yield('title', 'MaxMed UAE - Medical & Laboratory Equipment Supplier')</title>

        <!-- Page Transition System - Prevents flashing -->
        <style>
            /* Page transition overlay */
            #page-transition-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: #fff;
                z-index: 9999;
                opacity: 0;
                visibility: hidden;
                transition: opacity 0.25s ease-in-out;
                pointer-events: none;
                display: flex;
                justify-content: center;
                align-items: center;
            }
            
            /* Lab-themed loading icon */
            .lab-loader {
                width: 120px;
                height: 120px;
                position: relative;
                opacity: 0;
                transform: scale(0.8);
                transition: opacity 0.3s ease, transform 0.3s ease;
            }
            
            /* When navigating, show overlay */
            body.navigating #page-transition-overlay {
                opacity: 1;
                visibility: visible;
            }
            
            /* When navigating, show the loader */
            body.navigating .lab-loader {
                opacity: 1;
                transform: scale(1);
            }
            
            /* Test tube and flask container */
            .lab-loader-container {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 15px;
                position: relative;
            }
            
            /* Test tube styles */
            .test-tube {
                width: 22px;
                height: 70px;
                background: linear-gradient(to bottom, rgba(255,255,255,0) 20%, #0a5694 20%, #0a5694 100%);
                border-radius: 0 0 11px 11px;
                border: 2px solid #171e60;
                position: relative;
                transform-origin: center top;
                animation: shake 3s ease-in-out infinite;
            }
            
            .test-tube::before {
                content: '';
                position: absolute;
                top: -8px;
                left: -2px;
                width: 22px;
                height: 8px;
                background-color: #171e60;
                border-radius: 5px 5px 0 0;
            }
            
            /* Flask styles */
            .flask {
                width: 40px;
                height: 70px;
                position: relative;
                animation: bubble 4s ease-in-out infinite;
            }
            
            .flask-top {
                width: 20px;
                height: 15px;
                background-color: #171e60;
                border-radius: 5px 5px 0 0;
                margin: 0 auto;
            }
            
            .flask-body {
                width: 40px;
                height: 45px;
                background: linear-gradient(to bottom, rgba(255,255,255,0) 30%, #171e60 30%, #00a9e0 100%);
                border: 2px solid #171e60;
                border-radius: 0 0 20px 20px;
                position: relative;
                overflow: hidden;
            }
            
            .flask-bubble {
                position: absolute;
                background-color: rgba(255, 255, 255, 0.6);
                border-radius: 50%;
                animation: rise 2s ease-in-out infinite;
            }
            
            .flask-bubble:nth-child(1) {
                width: 8px;
                height: 8px;
                left: 7px;
                bottom: 0;
                animation-delay: 0.2s;
            }
            
            .flask-bubble:nth-child(2) {
                width: 6px;
                height: 6px;
                left: 22px;
                bottom: 0;
                animation-delay: 0.8s;
            }
            
            .flask-bubble:nth-child(3) {
                width: 4px;
                height: 4px;
                left: 15px;
                bottom: 0;
                animation-delay: 1.5s;
            }
            
            /* DNA helix around the containers */
            .dna-strand {
                position: absolute;
                width: 100px;
                height: 100px;
                border: 2px dashed rgba(23, 30, 96, 0.3);
                border-radius: 50%;
                animation: rotate 8s linear infinite;
            }
            
            .dna-strand:nth-child(1) {
                border-color: rgba(23, 30, 96, 0.2);
                animation-duration: 10s;
            }
            
            .dna-strand:nth-child(2) {
                width: 85px;
                height: 85px;
                border-color: rgba(10, 86, 148, 0.2);
                animation-direction: reverse;
                animation-duration: 7s;
            }
            
            /* Animations */
            @keyframes shake {
                0%, 100% { transform: rotate(0deg); }
                10% { transform: rotate(5deg); }
                20% { transform: rotate(-5deg); }
                30% { transform: rotate(3deg); }
                40% { transform: rotate(-3deg); }
                50% { transform: rotate(0deg); }
            }
            
            @keyframes bubble {
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-5px); }
            }
            
            @keyframes rise {
                from { transform: translateY(0); opacity: 1; }
                to { transform: translateY(-30px); opacity: 0; }
            }
            
            @keyframes rotate {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
            
            /* Override any transitions that might cause flashing */
            body.navigating * {
                transition: none !important;
            }
            
            /* But keep our loader transitions */
            body.navigating .lab-loader,
            body.navigating .test-tube,
            body.navigating .flask,
            body.navigating .flask-bubble,
            body.navigating .dna-strand {
                transition: all 0.3s ease !important;
            }
        </style>
        
        <!-- Page Transition Script - Prevents flashing -->
        <script>
            // Simple function to remove the overlay
            function hideOverlay() {
                document.body.classList.remove('navigating');
            }
            
            // Simple function to show the overlay with lab loader
            function showOverlay() {
                document.body.classList.add('navigating');
            }
            
            // Create page transition system that prevents flashing
            document.addEventListener('DOMContentLoaded', function() {
                // Create overlay if it doesn't exist (should already be in HTML)
                const overlay = document.getElementById('page-transition-overlay');
                if (!overlay) {
                    console.warn('Page transition overlay not found in DOM');
                }
                
                // Add event listeners to all internal links
                document.querySelectorAll('a').forEach(link => {
                    // Only handle links to the same domain
                    if (link.hostname === window.location.hostname) {
                        link.addEventListener('click', function(e) {
                            // Skip if using modifier keys or it's a download
                            if (e.ctrlKey || e.metaKey || e.shiftKey || link.hasAttribute('download')) {
                                return;
                            }
                            
                            // Skip for special links
                            if (link.getAttribute('href').startsWith('#') || 
                                link.getAttribute('href').startsWith('mailto:') || 
                                link.getAttribute('href').startsWith('tel:') ||
                                link.getAttribute('target') === '_blank' ||
                                link.hasAttribute('data-no-transition')) {
                                return;
                            }
                            
                            // Start transition with lab loader
                            showOverlay();
                        });
                    }
                });
                
                // Handle browser back/forward navigation
                window.addEventListener('popstate', hideOverlay);
                
                // Handle page showing from cache
                window.addEventListener('pageshow', function(e) {
                    if (e.persisted) {
                        // Page was loaded from back-forward cache
                        hideOverlay();
                    }
                });
                
                // Mark page as loaded
                document.body.classList.add('page-loaded');
                
                // Always ensure overlay is hidden after load
                hideOverlay();
            });
            
            // Hide overlay when page is fully loaded
            window.addEventListener('load', hideOverlay);
        </script>
        
        <!-- Preconnect to external domains -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link rel="preconnect" href="https://www.googletagmanager.com">
        <link rel="preconnect" href="https://cdn.jsdelivr.net">
    
        <!-- Immediate script to prevent flashing of Alpine elements -->
        <script>
            // Hide Alpine components until Alpine is loaded
            document.addEventListener('DOMContentLoaded', function() {
                // Add a class to the body to indicate JS is available
                document.body.classList.add('js-enabled');
                
                // Preserve Alpine state during navigation
                document.addEventListener('alpine:initialized', () => {
                    document.body.classList.add('alpine-ready');
                });
            });
            
            // Preserve Alpine state during page transitions
            window.addEventListener('beforeunload', function(e) {
                // Check if this is actually a page navigation and not a browser back/forward
                if (e.currentTarget.performance && e.currentTarget.performance.navigation) {
                    const navType = e.currentTarget.performance.navigation.type;
                    // Only apply for normal navigation, not for reload (1) or back/forward (2)
                    if (navType !== 1 && navType !== 2) {
                        if (window.Alpine) {
                            // Don't destroy Alpine components during navigation
                            document.body.classList.add('navigating');
                        }
                    }
                } else {
                    // For browsers that don't support the above
                    document.body.classList.add('navigating');
                }
            });
        </script>
    
        <!-- Schema.org structured data -->
        @include('layouts.schema')

        <!-- Script to prevent navbar flashing during navigation -->
        <script>
            // Apply immediate styles to the navbar to prevent flashing
            document.addEventListener('DOMContentLoaded', function() {
                // Mark that Alpine is ready
                const navbar = document.querySelector('nav');
                if (navbar) {
                    // Force the navbar to display block to avoid flashing
                    navbar.classList.add('initialized');
                }
                
                // Force sidebars to be visible
                document.querySelectorAll('.sidebar, .sidebar-column').forEach(el => {
                    el.style.opacity = '1';
                    el.style.visibility = 'visible';
                });
            });
            
            // Preserve navbar state during page navigation
            if (window.history && window.history.pushState) {
                window.addEventListener('beforeunload', function() {
                    // Keep navbar visible during page transitions
                    const navbar = document.querySelector('nav');
                    if (navbar) {
                        navbar.style.opacity = '1';
                        navbar.style.transition = 'none';
                    }
                });
            }
        </script>

        <!-- Critical CSS inline -->
        <style>
            /* Critical path CSS only */
            :root {
                --brand-main: #171e60;
                --brand-auxiliary: #0a5694;
                --brand-white: #ffffff;
                --page-transition-duration: 200ms;
            }
            
            /* Improve page transitions */
            html, body {
                scroll-behavior: smooth;
            }
            
            /* Body state management */
            body {
                margin: 0;
                padding: 0;
                font-family: system-ui, -apple-system, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
                overflow-x: hidden;
                width: 100%;
                opacity: 1;
                transition: opacity var(--page-transition-duration) ease-in-out;
            }
            
            /* Show initial content once page is loaded */
            body.page-loaded .container,
            body.page-loaded main,
            body.page-loaded .row {
                animation: none !important;
            }
            
            /* Hide Alpine elements until Alpine is fully loaded */
            .js-enabled [x-cloak] {
                display: none !important;
            }
            
            /* Make sure sidebar is always visible */
            .sidebar-column,
            .sidebar {
                opacity: 1 !important;
                visibility: visible !important;
            }
            
            /* Force fixed elements like navigation to stay visible */
            nav {
                opacity: 1 !important;
                visibility: visible !important;
                position: relative;
                z-index: 1030;
                margin-top: 0 !important;
                padding-top: 0 !important;
                top: 0 !important;
            }
            
            /* Remove any styles that might be hiding elements */
            body:not(.js-enabled) .sidebar-column, 
            body:not(.page-loaded) .sidebar-column {
                opacity: 1 !important; /* Always show sidebar */
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
                transform: translateZ(0);
                backface-visibility: hidden; /* Prevent flickering during transitions */
            }
            
            /* Prevent sidebar flashing during navigation */
            .sidebar {
                opacity: 1;
                transition: opacity 0.3s ease;
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
        <!-- Page transition overlay -->
        <div id="page-transition-overlay">
            <div class="lab-loader">
                <div class="dna-strand"></div>
                <div class="dna-strand"></div>
                <div class="lab-loader-container">
                    <div class="test-tube"></div>
                    <div class="flask">
                        <div class="flask-top"></div>
                        <div class="flask-body">
                            <div class="flask-bubble"></div>
                            <div class="flask-bubble"></div>
                            <div class="flask-bubble"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="min-h-screen bg-gray-100 mt-0 pt-0">
            @include('layouts.navigation')

            <!-- Flash Messages -->
            <div class="container">
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
                <header class="bg-white shadow">
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
                // Remove navigation class to hide overlay
                document.body.classList.remove('navigating');
                
                const loader = document.getElementById('loader');
                if (loader) {
                    loader.style.display = 'none';
                }
                
                // Ensure smooth page transitions and prevent flickering
                document.body.classList.add('page-loaded');
                
                // Make sure navigation is fully visible and properly initialized
                const navbar = document.querySelector('nav');
                if (navbar) {
                    navbar.classList.add('initialized');
                    navbar.style.opacity = '1';
                    navbar.style.visibility = 'visible';
                }
                
                // Make sure all sidebars are visible
                document.querySelectorAll('.sidebar-column, .sidebar').forEach(el => {
                    el.style.opacity = '1';
                    el.style.visibility = 'visible';
                });
                
                // Add transition handlers to internal links for smooth navigation
                const internalLinks = document.querySelectorAll('a[href^="/"]:not([target]), a[href^="{{ url("/") }}"]:not([target])');
                internalLinks.forEach(link => {
                    if (!link.hasAttribute('data-no-transition')) {
                        link.addEventListener('click', function(e) {
                            // Skip if using modifier keys
                            if (e.ctrlKey || e.metaKey || e.shiftKey) return;
                            
                            // Skip for specific links
                            if (link.getAttribute('href').startsWith('#') || 
                                link.getAttribute('href').startsWith('mailto:') || 
                                link.getAttribute('href').startsWith('tel:')) {
                                return;
                            }
                            
                            // Start transition
                            document.body.classList.add('navigating');
                        });
                    }
                });
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

        <!-- Extra script to handle browser back button overlay removal -->
        <script>
            // Ensure overlay is removed on back navigation
            (function() {
                // Forcibly remove overlay when page loads from back button
                window.addEventListener('pageshow', function(e) {
                    document.body.classList.remove('navigating');
                    
                    // Force all related styles to be cleared
                    const overlay = document.getElementById('page-transition-overlay');
                    if (overlay) {
                        overlay.style.opacity = "0";
                        overlay.style.visibility = "hidden";
                    }
                });
                
                // Also handle popstate events
                window.addEventListener('popstate', function() {
                    document.body.classList.remove('navigating');
                    
                    // Force all related styles to be cleared
                    const overlay = document.getElementById('page-transition-overlay');
                    if (overlay) {
                        overlay.style.opacity = "0";
                        overlay.style.visibility = "hidden";
                    }
                });
                
                // Add a small delay for showing loader during navigation
                // This prevents the loader from showing for very fast page loads
                window.addEventListener('beforeunload', function(e) {
                    // Don't show loader immediately - only after a tiny delay
                    // This prevents flashing for quick navigations
                    setTimeout(function() {
                        document.body.classList.add('navigating');
                    }, 50);
                });
            })();
        </script>
        
        <!-- Google One Tap Sign-in -->
        @include('components.google-one-tap')
        @include('components.google-one-tap-styles')
        
        <!-- Google One Tap Script -->
        <script src="https://accounts.google.com/gsi/client" async defer></script>
        <script src="{{ asset('js/google-one-tap.js') }}"></script>
        
        <!-- Footer -->
        @include('layouts.footer')
        @include('components.cookie-consent')
    </body>
</html>
