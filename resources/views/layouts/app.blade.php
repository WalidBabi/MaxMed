<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-5JRSRT4MLZ"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            gtag('config', 'G-5JRSRT4MLZ');
        </script>

        @include('layouts.meta')
        <title>@yield('title', 'MaxMed UAE - Medical & Laboratory Equipment Supplier')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('android-chrome-192x192.png') }}">
        <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('android-chrome-512x512.png') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
        <link rel="manifest" href="{{ asset('site.webmanifest') }}">
        <meta name="theme-color" content="#171e60">

        <!-- Add Tailwind CSS CDN for quick styling -->
        <script src="https://cdn.tailwindcss.com"></script>
        
        <!-- Add Alpine.js -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <!-- Add custom styles -->
        <style>
            .hero-section {
                background: linear-gradient(rgba(23, 30, 96, 0.6), rgba(23, 30, 96, 0.6)),
                            url('/Images/banner.png');
                background-size: cover;
                background-position: center;
            }

            /* Carousel fade animation */
            .carousel-item {
                transition: opacity 0.6s ease-in-out;
            }

            /* Update primary colors */
            :root {
                --brand-main: #171e60;
                --brand-auxiliary: #0a5694;
                --brand-white: #ffffff;
            }

            .bg-primary {
                background-color: var(--brand-main) !important;
            }

            .text-primary {
                color: var(--brand-main) !important;
            }

            .btn-primary {
                background-color: var(--brand-main) !important;
                border-color: var(--brand-main) !important;
            }

            .btn-primary:hover {
                background-color: var(--brand-auxiliary) !important;
                border-color: var(--brand-auxiliary) !important;
            }

            .border-primary {
                border-color: var(--brand-main) !important;
            }

            /* Lazy Loading Styles */
            .lazy-image {
                opacity: 0;
                transition: opacity 0.3s ease-in;
            }
            
            .lazy-image.loaded {
                opacity: 1;
            }
        </style>

        <!-- Include Bootstrap CSS or any other stylesheets -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

        <link href="{{ asset('css/custom.css') }}" rel="stylesheet">

        <!-- Image Loading Optimization -->
        <link rel="preload" as="image" href="{{ asset('Images/banner.png') }}">

        <!-- Image Loading Script -->
        <script>
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
        
        <!-- Browser Caching Headers -->
        @php
            header('Cache-Control: public, max-age=31536000');
            header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + 31536000));
        @endphp

        @stack('head')
    </head>
    <body class="font-sans antialiased bg-gray-50 relative">

        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

      

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

        <!-- Include Bootstrap JS or any other scripts -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        <script>
            // Wait until the entire page is loaded, then hide the loader.
            window.addEventListener('load', function(){
                const loader = document.getElementById('loader');
                if (loader) {
                    loader.style.display = 'none';
                }
            });

            window.onpageshow = function(event) {
                if (event.persisted) {
                    window.location.reload();
                }
            };
            
            // Disable back button
            window.history.pushState(null, null, window.location.href);
            window.onpopstate = function () {
                window.history.pushState(null, null, window.location.href);
            };
        </script>

   
    </body>
</html>
