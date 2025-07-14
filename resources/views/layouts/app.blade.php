<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="user-authenticated" content="{{ auth()->check() ? 'true' : 'false' }}">

        @include('layouts.meta')
        <title>@yield('title', 'MaxMed UAE - Medical & Laboratory Equipment Supplier')</title>

        <!-- Optimized Page Transition System - Prevents Text Flashing -->
        <style>
            /* Prevent text flashing during navigation */
            body {
                opacity: 1 !important;
                visibility: visible !important;
                transition: none !important;
            }
            
            /* Ensure content is always visible */
            .container, main, .row, .col, .card, .product-card, .sidebar, .navbar {
                opacity: 1 !important;
                visibility: visible !important;
                transition: none !important;
            }
            
            /* Prevent Alpine components from flashing */
            [x-data], [x-cloak] {
                opacity: 1 !important;
                visibility: visible !important;
                display: block !important;
                transition: none !important;
            }
            
            /* Disable page transition overlay to prevent flashing */
            #page-transition-overlay {
                display: none !important;
                opacity: 0 !important;
                visibility: hidden !important;
            }
            
            /* Ensure all text elements are always visible */
            h1, h2, h3, h4, h5, h6, p, span, div, a, button, input, textarea, select {
                opacity: 1 !important;
                visibility: visible !important;
                transition: none !important;
            }
            
            /* Prevent any CSS animations that might cause flashing */
            * {
                animation: none !important;
                transition: none !important;
            }
            
            /* Only allow smooth scrolling */
            html {
                scroll-behavior: smooth;
            }
            
            /* Ensure images don't cause layout shifts */
            img {
                opacity: 1 !important;
                visibility: visible !important;
                transition: none !important;
            }
            
            /* Prevent any Bootstrap transitions */
            .fade, .collapse, .collapsing {
                opacity: 1 !important;
                visibility: visible !important;
                display: block !important;
                transition: none !important;
            }
            
            /* Ensure navigation elements are always visible */
            .navbar, .navbar-nav, .nav-link, .dropdown-menu {
                opacity: 1 !important;
                visibility: visible !important;
                display: block !important;
                transition: none !important;
            }
            
            /* Prevent sidebar flashing */
            .sidebar, .sidebar-column, .crm-sidebar, .supplier-sidebar {
                opacity: 1 !important;
                visibility: visible !important;
                transition: none !important;
            }
            
            /* Ensure all components are immediately visible */
            .js-enabled, .alpine-ready, .page-loaded {
                opacity: 1 !important;
                visibility: visible !important;
            }
            
            /* Override any framework transitions */
            .transition, .transform, .animate {
                transition: none !important;
                animation: none !important;
            }
            
            /* Ensure content is visible during page load */
            body.loading, body.navigating {
                opacity: 1 !important;
                visibility: visible !important;
            }
            
            /* Prevent any remaining flashing */
            body * {
                opacity: 1 !important;
                visibility: visible !important;
                transition: none !important;
                animation: none !important;
            }
            body.navigating .dna-strand {
                transition: all 0.3s ease !important;
            }
        </style>
        
        <!-- Simplified Navigation - No Transitions to Prevent Flashing -->
        <script>
            // Simple navigation without transitions to prevent text flashing
            document.addEventListener('DOMContentLoaded', function() {
                // Mark page as loaded immediately
                document.body.classList.add('page-loaded');
                
                // Ensure all content is visible
                document.body.style.opacity = '1';
                document.body.style.visibility = 'visible';
                
                // Remove any transition classes that might cause flashing
                document.body.classList.remove('navigating', 'loading');
                
                // Ensure all elements are visible
                const allElements = document.querySelectorAll('*');
                allElements.forEach(el => {
                    el.style.opacity = '1';
                    el.style.visibility = 'visible';
                    el.style.transition = 'none';
                });
            });
            
            // Additional script to prevent any remaining flashing
            document.addEventListener('DOMContentLoaded', function() {
                // Force all Alpine components to be visible immediately
                document.querySelectorAll('[x-data], [x-cloak]').forEach(el => {
                    el.style.opacity = '1';
                    el.style.visibility = 'visible';
                    el.style.display = 'block';
                    el.style.transition = 'none';
                });
                
                // Mark Alpine as ready immediately
                document.body.classList.add('alpine-ready', 'js-enabled');
                
                // Ensure navbar and sidebars are visible
                document.querySelectorAll('nav, .sidebar, .sidebar-column, .crm-sidebar, .supplier-sidebar').forEach(el => {
                    el.style.opacity = '1';
                    el.style.visibility = 'visible';
                    el.style.transition = 'none';
                });
                
                // Prevent any CSS animations that might cause flashing
                const style = document.createElement('style');
                style.textContent = `
                    * {
                        animation: none !important;
                        transition: none !important;
                    }
                    body, .container, main, .row, .col, .card, .product-card, .sidebar, .navbar {
                        opacity: 1 !important;
                        visibility: visible !important;
                        transition: none !important;
                    }
                    [x-data], [x-cloak] {
                        opacity: 1 !important;
                        visibility: visible !important;
                        display: block !important;
                        transition: none !important;
                    }
                `;
                document.head.appendChild(style);
            });
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
        
        <!-- Mobile-specific styles for cookie consent and Google sign-in -->
        <style>
            /* Mobile cookie consent and Google sign-in positioning */
            @media (max-width: 768px) {
                /* Ensure proper spacing for mobile floating elements */
                .fixed.bottom-6.right-6 {
                    bottom: 140px; /* Space for cookie consent + Google sign-in */
                }
                
                /* When cookie consent is hidden, adjust WhatsApp position */
                body.cookie-consent-hidden .fixed.bottom-6.right-6 {
                    bottom: 100px; /* Space for Google sign-in only */
                }
                
                /* When both are hidden, restore original position */
                body.cookie-consent-hidden.google-signin-hidden .fixed.bottom-6.right-6 {
                    bottom: 24px;
                }
                
                /* Smooth transitions for all floating elements */
                .fixed.bottom-6.right-6,
                .mobile-google-signin,
                .mobile-google-signin-compact {
                    transition: bottom 0.3s ease;
                }
            }
            
            /* Ensure cookie consent is always on top */
            #cookieConsent {
                z-index: 1050 !important;
            }
            
            /* Google sign-in should be below cookie consent */
            .mobile-google-signin,
            .mobile-google-signin-compact {
                z-index: 1049 !important;
            }
            
            /* WhatsApp should be below Google sign-in */
            .fixed.bottom-6.right-6 {
                z-index: 1048 !important;
            }
        </style>
        
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
        <!-- Removed page transition overlay to prevent text flashing -->
        
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

        <!-- Enhanced Cookie Consent and Google Sign-in Management -->
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cookieConsent = document.getElementById('cookieConsent');
            const acceptBtn = document.getElementById('acceptCookies');
            const rejectBtn = document.getElementById('rejectCookies');
            const mobileGoogleSignin = document.getElementById('mobile-google-signin');
            const desktopGoogleSignin = document.getElementById('desktop-google-signin');

            // Function to update Google sign-in positioning
            function updateGoogleSigninPosition() {
                const isMobile = window.innerWidth <= 768;
                const cookieConsentVisible = !cookieConsent.classList.contains('hidden');
                const googleSigninVisible = mobileGoogleSignin && !mobileGoogleSignin.classList.contains('hidden');
                
                if (isMobile) {
                    if (cookieConsentVisible) {
                        // Add class to body for CSS positioning
                        document.body.classList.add('cookie-consent-visible');
                        document.body.classList.remove('cookie-consent-hidden');
                    } else {
                        document.body.classList.remove('cookie-consent-visible');
                        document.body.classList.add('cookie-consent-hidden');
                    }
                    
                    // Handle Google sign-in visibility
                    if (googleSigninVisible) {
                        document.body.classList.remove('google-signin-hidden');
                    } else {
                        document.body.classList.add('google-signin-hidden');
                    }
                } else {
                    // Desktop: remove all mobile-specific classes
                    document.body.classList.remove('cookie-consent-visible', 'cookie-consent-hidden', 'google-signin-hidden');
                }
            }

            // Check if user has already given consent
            const existingConsent = document.cookie.split('; ').find(row => row.startsWith('cookie_consent='));
            
            if (!existingConsent) {
                // Add a small delay to ensure smooth appearance
                setTimeout(() => {
                    cookieConsent.classList.remove('hidden');
                    cookieConsent.style.transform = 'translateY(0)';
                    updateGoogleSigninPosition();
                }, 500);
            } else {
                updateGoogleSigninPosition();
            }

            // Handle accept button
            acceptBtn.addEventListener('click', function() {
                document.cookie = 'cookie_consent=accepted; path=/; SameSite=Lax; max-age=' + (60 * 60 * 24 * 365);
                
                // Smooth hide animation
                cookieConsent.style.transform = 'translateY(100%)';
                setTimeout(() => {
                    cookieConsent.classList.add('hidden');
                    updateGoogleSigninPosition();
                }, 300);
                
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
                }).then(response => {
                    console.log('Consent tracking response:', response.status);
                }).catch(error => console.log('Consent tracking failed:', error));
                
                // Start tracking with a small delay to ensure cookie is set
                setTimeout(() => {
                    if (window.startUserBehaviorTracking) {
                        console.log('Starting user behavior tracking...');
                        window.startUserBehaviorTracking();
                    } else {
                        console.log('startUserBehaviorTracking function not found');
                    }
                }, 100);
            });

            // Handle reject button
            rejectBtn.addEventListener('click', function() {
                document.cookie = 'cookie_consent=denied; path=/; SameSite=Lax; max-age=' + (60 * 60 * 24 * 30);
                
                // Smooth hide animation
                cookieConsent.style.transform = 'translateY(100%)';
                setTimeout(() => {
                    cookieConsent.classList.add('hidden');
                    updateGoogleSigninPosition();
                }, 300);
                
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

            // Update positioning on window resize
            window.addEventListener('resize', updateGoogleSigninPosition);
            
            // Initial positioning update
            updateGoogleSigninPosition();
        });
        </script>

        <!-- User Behavior Tracker -->
        <script src="{{ asset('js/user-behavior-tracker.js') }}"></script>
        
        <!-- Mobile Optimization Script -->
        <script src="{{ asset('js/mobile-optimization.js') }}"></script>
        
        <!-- Scripts -->
        <script src="{{ asset('js/back-button-protection.js') }}"></script>
    </body>
</html>
