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
            
            // Initialize immediately if DOM is ready, otherwise wait
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initializeNavigation);
            } else {
                initializeNavigation();
            }
        </script>
        
        <!-- Preconnect to external domains -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link rel="preconnect" href="https://www.googletagmanager.com">
        <link rel="preconnect" href="https://cdn.jsdelivr.net">
    
        <!-- Immediate script to prevent flashing of Alpine elements -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.body.classList.add('js-enabled');
                
                // Initialize Alpine components
                document.addEventListener('alpine:initialized', () => {
                    document.body.classList.add('alpine-ready');
                });
                
                // Initialize navbar
                const navbar = document.querySelector('nav');
                if (navbar) {
                    navbar.classList.add('initialized');
                }
                
                // Initialize sidebars
                document.querySelectorAll('.sidebar, .sidebar-column').forEach(el => {
                    el.style.opacity = '1';
                    el.style.visibility = 'visible';
                });
            });
        </script>
    
        <!-- Schema.org structured data -->
        @include('layouts.schema')

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
            <main>
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
                document.querySelectorAll('.sidebar-column, .sidebar').forEach(el => {
                    el.style.opacity = '1';
                    el.style.visibility = 'visible';
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
        
        <!-- Google One Tap Sign-in -->
        @include('components.google-one-tap')
        @include('components.google-one-tap-styles')
        
        <!-- Error Handler Script (load first) -->
        <script src="{{ asset('js/error-handler.js') }}"></script>
        
        <!-- Google One Tap Script -->
        <script src="https://accounts.google.com/gsi/client" async defer></script>
        <script src="{{ asset('js/google-one-tap.js') }}"></script>
        
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
        
        <!-- Scripts -->
        <script src="{{ asset('js/back-button-protection.js') }}"></script>
    </body>
</html>
