<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">
        <link rel="apple-touch-icon" href="{{ asset('favicon.ico') }}">

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
        </style>

        <!-- Include Bootstrap CSS or any other stylesheets -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

        <link href="{{ asset('css/custom.css') }}" rel="stylesheet">

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
