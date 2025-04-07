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

            img {
                max-width: 100%;
                height: auto;
            }
            
            /* Improve touch targets for mobile */
            button, a, input[type="submit"], input[type="button"] {
                min-height: 44px;
                min-width: 44px;
            }
            
            /* Prevent horizontal overflow */
            body {
                overflow-x: hidden;
                width: 100%;
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

        <!-- Add Mobile CSS -->
        <link rel="stylesheet" href="{{ asset('css/mobile.css') }}">

        @stack('head')
    </head>
    <body class="font-sans antialiased bg-gray-50 relative">

        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Flash Messages -->
            <div class="container mt-3">
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

        <!-- WhatsApp Floating Widget -->
        <div class="fixed bottom-6 right-6 z-50">
            <a href="https://wa.me/971554602500" target="_blank" class="flex items-center justify-center w-14 h-14 bg-green-500 rounded-full shadow-lg hover:bg-green-600 transition-all duration-300 transform hover:scale-110 hover:-translate-y-1 group">
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
