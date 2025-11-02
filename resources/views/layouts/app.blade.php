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
            
            /* Only show loading during navigation transitions, not initial page load */
            body.navigating #page-transition-overlay {
                opacity: 1;
                visibility: visible;
            }
            
            body.navigating .lab-loader {
                opacity: 1;
                transform: scale(1);
            }
            
            /* Ensure content is always visible by default */
            .main-content {
                opacity: 1;
                visibility: visible;
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
        
        <!-- Page Transition System -->
        <script>
            // Single source of truth for navigation state
            const navigation = {
                isNavigating: false,
                preventNextClick: false,
                
                start() {
                    if (this.isNavigating || this.preventNextClick) return false;
                    this.isNavigating = true;
                    document.body.classList.add('navigating');
                    return true;
                },
                
                end() {
                    this.isNavigating = false;
                    this.preventNextClick = false;
                    document.body.classList.remove('navigating');
                    const overlay = document.getElementById('page-transition-overlay');
                    if (overlay) {
                        overlay.style.opacity = "0";
                        overlay.style.visibility = "hidden";
                    }
                },
                
                preventNext() {
                    this.preventNextClick = true;
                    setTimeout(() => this.preventNextClick = false, 300);
                }
            };

            // Initialize navigation system
            function initializeNavigation() {
                // Single click handler for all internal links
                document.addEventListener('click', function(e) {
                    const link = e.target.closest('a');
                    if (!link) return;
                    
                    // Only handle links to the same domain
                    if (link.hostname !== window.location.hostname) return;
                    
                    // Skip if using modifier keys or it's a special link
                    if (e.ctrlKey || e.metaKey || e.shiftKey || 
                        link.hasAttribute('download') ||
                        link.hasAttribute('data-no-transition') ||
                        link.getAttribute('href').startsWith('#') || 
                        link.getAttribute('href').startsWith('mailto:') || 
                        link.getAttribute('href').startsWith('tel:') ||
                        link.getAttribute('target') === '_blank') {
                        return;
                    }
                    
                    // Start navigation
                    if (!navigation.start()) {
                        e.preventDefault();
                        return;
                    }
                    
                    // Let the navigation proceed
                    navigation.preventNext();
                });
                
                // Handle page load and back/forward navigation
                window.addEventListener('pageshow', function(e) {
                    navigation.end();
                });
                
                window.addEventListener('popstate', function() {
                    navigation.end();
                });
                
                // Handle page unload
                window.addEventListener('beforeunload', function() {
                    if (!navigation.isNavigating) {
                        setTimeout(() => navigation.start(), 50);
                    }
                });
                
                // Mark page as loaded
                document.body.classList.add('page-loaded');
            }
            
            // Function to handle page ready state
            function markPageReady() {
                // Just end any navigation state
                navigation.end();
            }
            
            // Initialize immediately if DOM is ready, otherwise wait
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    initializeNavigation();
                    markPageReady();
                });
            } else {
                initializeNavigation();
                markPageReady();
            }
        </script>
        
        <!-- Enhanced Preconnect to External Resources -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link rel="preconnect" href="https://cdn.tailwindcss.com">
        <link rel="preconnect" href="https://www.googletagmanager.com">
        <link rel="preconnect" href="https://cdn.jsdelivr.net">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        
        <!-- Enhanced Performance Meta Tags -->
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, shrink-to-fit=no, viewport-fit=cover">
        <meta name="theme-color" content="#171e60">
        <meta name="msapplication-TileColor" content="#171e60">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        
        <!-- Comprehensive Favicon Implementation for Search Results -->
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
        <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('img/favicon/favicon-96x96.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/favicon/favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('img/favicon/favicon-16x16.png') }}">
        <link rel="icon" type="image/svg+xml" href="{{ asset('img/favicon/favicon.svg') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/favicon/apple-touch-icon.png') }}">
        <link rel="mask-icon" href="{{ asset('img/favicon/safari-pinned-tab.svg') }}" color="#171e60">
        <link rel="manifest" href="{{ asset('site.webmanifest') }}">
        
        <!-- Additional favicon formats for better browser compatibility -->
        <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('img/favicon/web-app-manifest-192x192.png') }}">
        <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('img/favicon/web-app-manifest-512x512.png') }}">
        
        <!-- Microsoft-specific favicon meta tags -->
        <meta name="msapplication-TileImage" content="{{ asset('img/favicon/mstile-150x150.png') }}">
        <meta name="msapplication-config" content="{{ asset('img/favicon/browserconfig.xml') }}">
        
        <!-- Organization Logo Structured Data for Search Results -->
        <script type="application/ld+json">
        {
          "@context": "https://schema.org",
          "@type": "Organization",
          "name": "MaxMed UAE",
          "url": "https://maxmedme.com",
          "logo": "{{ asset('img/favicon/favicon-96x96.png') }}",
          "image": "{{ asset('img/favicon/favicon-96x96.png') }}",
          "description": "Leading supplier of medical and laboratory equipment in Dubai, UAE. PCR machines, centrifuges, fume hoods, dental supplies and more.",
          "address": {
            "@type": "PostalAddress",
            "addressCountry": "AE",
            "addressLocality": "Dubai"
          },
          "contactPoint": {
            "@type": "ContactPoint",
            "telephone": "+971554602500",
            "contactType": "customer service"
          }
        }
        </script>
        
        <!-- Enhanced Security Meta Tags -->
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="referrer" content="strict-origin-when-cross-origin">
        
        <!-- Enhanced Accessibility Meta Tags -->
        <meta name="description" content="@yield('meta_description', '🔬 MaxMed UAE - Leading lab equipment supplier in Dubai! PCR machines, centrifuges, fume hoods, dental supplies & more ✅ Same-day quotes ☎️ +971 55 460 2500 🚚 Fast delivery')">
        <meta name="keywords" content="@yield('meta_keywords', 'laboratory equipment Dubai, lab instruments UAE, medical equipment supplier, fume hood suppliers UAE, dental consumables, PCR machine suppliers UAE, centrifuge suppliers, benchtop autoclave, dental supplies UAE, veterinary diagnostics UAE, point of care testing equipment, contact MaxMed, MaxMed phone number')">
        
        <!-- Performance Optimizations -->
        <link rel="dns-prefetch" href="//fonts.bunny.net">
        <link rel="dns-prefetch" href="//cdn.tailwindcss.com">
        <link rel="dns-prefetch" href="//www.googletagmanager.com">
        <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
        
        <!-- Critical CSS Inline for Above-the-fold Content -->
        <style>
            /* Critical CSS for immediate rendering */
            :root {
                --brand-main: #171e60;
                --brand-auxiliary: #0a5694;
                --brand-white: #ffffff;
            }
            
            body {
                margin: 0;
                padding: 0;
                font-family: system-ui, -apple-system, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
                line-height: 1.6;
                color: #333;
            }
            
            .container {
                max-width: 1200px;
                margin: 0 auto;
                padding: 0 15px;
            }
            
            .btn-primary {
                background-color: var(--brand-main);
                color: white;
                padding: 12px 24px;
                border: none;
                border-radius: 6px;
                text-decoration: none;
                display: inline-block;
                font-weight: 600;
                transition: all 0.3s ease;
            }
            
            .btn-primary:hover {
                background-color: var(--brand-auxiliary);
                transform: translateY(-2px);
            }
            
            /* Mobile-first responsive design */
            @media (max-width: 768px) {
                .container {
                    padding: 0 10px;
                }
                
                .btn-primary {
                    padding: 14px 28px;
                    font-size: 16px;
                }
            }
        </style>

        <!-- Preload LCP image -->
        <link rel="preload" as="image" fetchpriority="high" href="{{ asset('Images/optimized/banner-optimized.webp') }}" type="image/webp">

        <!-- CSS Files -->
        <link rel="stylesheet" href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('css/mobile.css') }}">

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
    <body class="font-sans antialiased bg-gray-50 relative no-select-content">
        <div id="consentOverlay" style="position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(255,255,255,0.7);z-index:40;backdrop-filter:blur(2px);display:none;"></div>
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
            <main class="main-content">
                @yield('content')
            </main>

        <!-- Dubai Date Formatting -->
        <script src="{{ asset('js/dubai-date.js') }}"></script>
        
        <!-- Notification trigger script for immediate sound feedback -->
        <script>
            // Notification trigger for immediate feedback after form submissions
            window.triggerNotificationCheck = function() {
                console.log('Manual notification check triggered'); // Debug log
                
                // Try to trigger CRM notification check
                const crmNotificationComponents = document.querySelectorAll('[x-data*="crmNotificationDropdown"]');
                console.log('Found CRM notification components:', crmNotificationComponents.length); // Debug log
                
                crmNotificationComponents.forEach(element => {
                    const alpineData = element._x_dataStack && element._x_dataStack[0];
                    if (alpineData && alpineData.checkForNewNotifications) {
                        console.log('Triggering CRM notification check'); // Debug log
                        alpineData.checkForNewNotifications();
                    }
                });
                
                // Try to trigger supplier notification check
                const supplierNotificationComponents = document.querySelectorAll('[x-data*="supplierNotificationDropdown"]');
                console.log('Found supplier notification components:', supplierNotificationComponents.length); // Debug log
                
                supplierNotificationComponents.forEach(element => {
                    const alpineData = element._x_dataStack && element._x_dataStack[0];
                    if (alpineData && alpineData.checkForNewNotifications) {
                        console.log('Triggering supplier notification check'); // Debug log
                        alpineData.checkForNewNotifications();
                    }
                });
                
                // Try to trigger admin notification check too
                const adminNotificationComponents = document.querySelectorAll('[x-data*="adminNotificationDropdown"]');
                console.log('Found admin notification components:', adminNotificationComponents.length); // Debug log
                
                adminNotificationComponents.forEach(element => {
                    const alpineData = element._x_dataStack && element._x_dataStack[0];
                    if (alpineData && alpineData.checkForNewNotifications) {
                        console.log('Triggering admin notification check'); // Debug log
                        alpineData.checkForNewNotifications();
                    }
                });
            };

                    // Also expose a function to play notification sound manually
        window.playNotificationSound = function() {
            console.log('Manual sound test triggered'); // Debug log
            
            // Create a simple audio element and play the notification sound
            const audio = new Audio('/audio/notification.mp3');
            audio.volume = 0.6;
            
            // Try to enable audio context if needed (for browsers that require user interaction)
            if (window.AudioContext && AudioContext.prototype.resume) {
                const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                if (audioContext.state === 'suspended') {
                    audioContext.resume().then(() => {
                        console.log('Audio context resumed'); // Debug log
                    });
                }
            }
            
            const playPromise = audio.play();
            
            if (playPromise !== undefined) {
                playPromise.then(() => {
                    console.log('Manual sound test played successfully'); // Debug log
                }).catch(error => {
                    console.warn('Could not play notification sound:', error);
                    console.log('This might be due to browser autoplay restrictions. Try clicking on the page first.'); // Debug log
                });
            }
            
            // Also try to trigger the notification components to play their sounds
            if (window.crmNotificationComponent && window.crmNotificationComponent.queueNotificationSound) {
                console.log('Triggering CRM component sound via global reference'); // Debug log
                window.crmNotificationComponent.queueNotificationSound();
            }
            
            const crmComponents = document.querySelectorAll('[x-data*="crmNotificationDropdown"]');
            crmComponents.forEach(element => {
                const alpineData = element._x_dataStack && element._x_dataStack[0];
                if (alpineData && alpineData.queueNotificationSound) {
                    console.log('Triggering CRM component sound'); // Debug log
                    alpineData.queueNotificationSound();
                }
            });
        };
        </script>
        
        <!-- Auto-trigger notification check only for authenticated admin/staff users -->
        @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSupplier() || (auth()->user()->role && in_array(auth()->user()->role->name, ['manager', 'sales-rep', 'content-editor']))))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Wait 2 seconds after page load to check for new notifications
                setTimeout(window.triggerNotificationCheck, 2000);
            });
        </script>
        @endif
        
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
                document.querySelectorAll('.sidebar-column, .sidebar, .crm-sidebar, .supplier-sidebar, .sidebar-container').forEach(el => {
                    el.style.opacity = '1';
                    el.style.visibility = 'visible';
                    el.classList.add('sidebar-initialized');
                });
                
                // Ensure all Alpine components are properly initialized
                document.querySelectorAll('[x-data]').forEach(el => {
                    if (el._x_dataStack) {
                        el.classList.add('alpine-initialized');
                    }
                });
                
                // Show all x-cloak elements
                document.querySelectorAll('[x-cloak]').forEach(el => {
                    el.style.display = '';
                });
                
                // Add final class to prevent any remaining flashing
                document.body.classList.add('fully-loaded');
            });
            
            // Additional script to prevent component flashing during navigation
            document.addEventListener('DOMContentLoaded', function() {
                // Immediately show all critical components
                const criticalComponents = document.querySelectorAll('nav, .sidebar, .sidebar-column, .crm-sidebar, .supplier-sidebar, .sidebar-container');
                criticalComponents.forEach(el => {
                    el.style.opacity = '1';
                    el.style.visibility = 'visible';
                    el.classList.add('immediately-visible');
                });
                
                // Prevent any CSS transitions from causing flashing
                document.body.style.setProperty('--page-transition-duration', '0ms');
                
                // Ensure Alpine components don't flash
                document.addEventListener('alpine:initialized', () => {
                    document.body.classList.add('alpine-ready');
                    
                    // Force all Alpine components to be visible
                    document.querySelectorAll('[x-data]').forEach(el => {
                        el.style.opacity = '1';
                        el.style.visibility = 'visible';
                    });
                    
                    // Show all x-cloak elements immediately
                    document.querySelectorAll('[x-cloak]').forEach(el => {
                        el.style.display = '';
                    });
                });
            });
        </script>

        <!-- WhatsApp Floating Widget -->
        <div class="fixed bottom-6 right-6 z-50 flex flex-col items-end space-y-3">
            <a href="https://wa.me/971554602500" target="_blank" class="flex items-center justify-center w-14 h-14 bg-green-500 rounded-full shadow-lg hover:bg-green-600 transition-all duration-300 transform hover:scale-110 hover:-translate-y-1 group" aria-label="Contact us on WhatsApp">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                </svg>
                <div class="absolute right-16 bg-white text-gray-700 px-4 py-2 rounded-lg shadow-lg opacity-0 invisible transform translate-x-2 group-hover:opacity-100 group-hover:visible group-hover:translate-x-0 transition-all duration-300 whitespace-nowrap">
                    Chat with us on WhatsApp
                </div>
            </a>
            <!-- Telegram Floating Widget -->
            <a href="https://t.me/MaxMedScientific" target="_blank" class="flex items-center justify-center w-14 h-14 bg-blue-500 rounded-full shadow-lg hover:bg-blue-600 transition-all duration-300 transform hover:scale-110 hover:-translate-y-1 group" aria-label="Contact us on Telegram">
                <img src="/Images/telegram-icon.webp" alt="Telegram" class="h-7 w-7" />
                <div class="absolute right-16 bg-white text-gray-700 px-4 py-2 rounded-lg shadow-lg opacity-0 invisible transform translate-x-2 group-hover:opacity-100 group-hover:visible group-hover:translate-x-0 transition-all duration-300 whitespace-nowrap">
                    Chat with us on Telegram
                </div>
            </a>
            <!-- WeChat Floating Widget -->
            <button type="button" onclick="document.getElementById('wechatModal').classList.remove('hidden');" class="flex items-center justify-center w-14 h-14 bg-green-600 rounded-full shadow-lg hover:bg-green-700 transition-all duration-300 transform hover:scale-110 hover:-translate-y-1 group" aria-label="Contact us on WeChat">
                <img src="/Images/wechat.png" alt="WeChat" class="h-7 w-7" />
                <div class="absolute right-16 bg-white text-gray-700 px-4 py-2 rounded-lg shadow-lg opacity-0 invisible transform translate-x-2 group-hover:opacity-100 group-hover:visible group-hover:translate-x-0 transition-all duration-300 whitespace-nowrap">
                    Chat with us on WeChat
                </div>
            </button>
        </div>
        <!-- WeChat Modal -->
        <div id="wechatModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60">
            <div class="bg-white rounded-lg shadow-lg p-6 max-w-xs w-full text-center relative">
                <button onclick="document.getElementById('wechatModal').classList.add('hidden');" class="absolute top-2 right-2 text-gray-400 hover:text-gray-700 text-2xl">&times;</button>
                <h3 class="text-lg font-semibold mb-4 text-gray-800">Scan to add us on WeChat</h3>
                <img src="/Images/wechatqrcode.jpg" alt="WeChat QR Code" class="mx-auto mb-2 w-40 h-40 rounded" />
                <p class="text-gray-600">Open WeChat and scan this QR code to connect with us.</p>
            </div>
        </div>
        
        <!-- Error Handler Script (load first) -->
        <script src="{{ asset('js/error-handler.js') }}"></script>
        
        <!-- Text Protection Script -->
        <script src="{{ asset('js/text-protection.js') }}"></script>
        
        <!-- Simple Notification System - Only for authenticated admin/staff users -->
        @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSupplier() || (auth()->user()->role && in_array(auth()->user()->role->name, ['manager', 'sales-rep', 'content-editor']))))
        <script src="{{ asset('js/simple-notifications.js') }}"></script>
        @endif
        
        <!-- Footer -->
        @include('layouts.footer')
        @include('components.cookie-consent')

        <!-- Cookie Consent Script (moved here for testing) -->
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cookieConsent = document.getElementById('cookieConsent');
            const acceptBtn = document.getElementById('acceptCookies');
            const rejectBtn = document.getElementById('rejectCookies');

            // Check if user has already given consent
            const existingConsent = document.cookie.split('; ').find(row => row.startsWith('cookie_consent='));
            console.log('Cookie consent check:', existingConsent);
            
            if (!existingConsent) {
                console.log('No consent found, showing banner');
                cookieConsent.classList.remove('hidden');
                document.getElementById('consentOverlay').style.display = 'block'; // Show overlay
            } else {
                console.log('Consent already given:', existingConsent);
                document.getElementById('consentOverlay').style.display = 'none'; // Hide overlay if consent is given
            }

            // Handle accept button
            acceptBtn.addEventListener('click', function() {
                document.cookie = 'cookie_consent=accepted; path=/; max-age=' + (60 * 60 * 24 * 365);
                cookieConsent.classList.add('hidden');
                document.getElementById('consentOverlay').style.display = 'none'; // Hide overlay on accept
                
                // Send consent to backend
                fetch('/api/user-behavior/track', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({
                        event_type: 'cookie_consent',
                        page_url: window.location.href,
                        event_data: {
                            consent: 'accepted',
                            timestamp: new Date().toISOString()
                        }
                    })
                }).catch(error => console.log('Consent tracking failed:', error));
            });

            // Handle reject button
            rejectBtn.addEventListener('click', function() {
                document.cookie = 'cookie_consent=denied; path=/; max-age=' + (60 * 60 * 24 * 30);
                cookieConsent.classList.add('hidden');
                document.getElementById('consentOverlay').style.display = 'none'; // Hide overlay on reject
                
                // Send consent to backend
                fetch('/api/user-behavior/track', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({
                        event_type: 'cookie_consent',
                        page_url: window.location.href,
                        event_data: {
                            consent: 'denied',
                            timestamp: new Date().toISOString()
                        }
                    })
                }).catch(error => console.log('Consent tracking failed:', error));
            });
        });
        </script>

        <!-- User Behavior Tracker -->
        <script src="{{ asset('js/user-behavior-tracker.js') }}"></script>
        
        <!-- Mobile Optimization Script -->
        <script src="{{ asset('js/mobile-optimization.js') }}"></script>
        
        <!-- Scripts -->
        <script src="{{ asset('js/back-button-protection.js') }}"></script>
        
        <!-- PWA Push registration -->
        <script>
        (function() {
            if (!('serviceWorker' in navigator) || !('PushManager' in window)) { return; }
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const getPublicKey = () => fetch('/push/public-key', { headers: { 'Accept': 'application/json' } })
                .then(r => r.json()).then(d => d.publicKey);
            const urlBase64ToUint8Array = (base64String) => {
                const padding = '='.repeat((4 - base64String.length % 4) % 4);
                const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
                const rawData = atob(base64);
                const outputArray = new Uint8Array(rawData.length);
                for (let i = 0; i < rawData.length; ++i) outputArray[i] = rawData.charCodeAt(i);
                return outputArray;
            };
            
            async function subscribeToPush() {
                try {
                    // Register service worker and wait for it to be ready
                    const registration = await navigator.serviceWorker.register('/service-worker.js');
                    await navigator.serviceWorker.ready;
                    
                    // Check current permission status first
                    let permission = Notification.permission;
                    
                    // Only request permission if it's not already been determined
                    // On mobile, requesting permission without user gesture may fail silently
                    if (permission === 'default') {
                        // Try to request permission, but don't fail if it doesn't work on mobile
                        try {
                            permission = await Notification.requestPermission();
                        } catch (e) {
                            console.warn('Permission request failed (may require user gesture on mobile):', e);
                            return;
                        }
                    }
                    
                    if (permission !== 'granted') {
                        console.log('Notification permission not granted:', permission);
                        return;
                    }
                    
                    // Get existing subscription first
                    let subscription = await registration.pushManager.getSubscription();
                    
                    // If no subscription, create a new one
                    if (!subscription) {
                        const publicKey = await getPublicKey();
                        subscription = await registration.pushManager.subscribe({
                            userVisibleOnly: true,
                            applicationServerKey: urlBase64ToUint8Array(publicKey)
                        });
                    }
                    
                    // Send subscription to server
                    // PushSubscription has a toJSON() method that formats it correctly
                    const response = await fetch('/push/subscribe', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrf
                        },
                        body: JSON.stringify(subscription)
                    });
                    
                    if (!response.ok) {
                        const error = await response.json().catch(() => ({ message: 'Unknown error' }));
                        console.error('Failed to save subscription:', error);
                        return;
                    }
                    
                    console.log('Push subscription successful');
                    
                    // If we're on the test page, refresh to update subscription count
                    if (window.location.pathname === '/push/test') {
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    }
                    
                    window.maxmedPush = {
                        unsubscribe: async function() {
                            const sub = await registration.pushManager.getSubscription();
                            if (!sub) return;
                            await fetch('/push/unsubscribe', {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': csrf
                                },
                                body: JSON.stringify({ endpoint: sub.endpoint })
                            });
                            await sub.unsubscribe();
                        }
                    };
                } catch (e) {
                    console.warn('Push registration failed:', e);
                }
            }
            
            let hasAttemptedSubscription = false;
            const interactionHandlers = new Map();
            
            // Try to subscribe on page load (may not work on mobile without user gesture)
            window.addEventListener('load', function() {
                subscribeToPush().catch(() => {
                    // If it fails, we'll try again on user interaction
                });
            });
            
            // Subscribe on user interaction (works on mobile)
            // Mobile browsers require user gesture for notification permission
            const interactionEvents = ['click', 'touchstart', 'scroll', 'keydown'];
            interactionEvents.forEach(eventType => {
                const handler = async function() {
                    if (!hasAttemptedSubscription) {
                        hasAttemptedSubscription = true;
                        await subscribeToPush();
                        // Remove all listeners after first attempt
                        interactionEvents.forEach(e => {
                            const h = interactionHandlers.get(e);
                            if (h) {
                                document.removeEventListener(e, h);
                            }
                        });
                    }
                };
                interactionHandlers.set(eventType, handler);
                document.addEventListener(eventType, handler, { once: true, passive: true });
            });
            
            // Also try on visibility change (when user returns to tab/app)
            // This helps on mobile when user grants permission and returns to the page
            document.addEventListener('visibilitychange', function() {
                if (!document.hidden && !hasAttemptedSubscription) {
                    setTimeout(() => {
                        subscribeToPush();
                    }, 1000);
                }
            });
        })();
        </script>
    </body>
</html>
