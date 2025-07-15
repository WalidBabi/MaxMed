<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'MaxMed Supplier') - Supplier Management System</title>

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('img/favicon/favicon-16x16.png') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <meta name="theme-color" content="#171e60">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Custom CSS - Load after Vite assets -->
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/mobile.css') }}">

    <!-- Additional Styling -->
    <style>
        /* Supplier-style sidebar styling */
        .supplier-sidebar {
            background: white;
            color: #374151;
            min-height: 100vh;
            position: relative;
            z-index: 30;
            border-right: 1px solid #e5e7eb;
        }

        .supplier-sidebar .menu-item a {
            transition: all 0.3s;
            color: #6b7280;
        }

        .supplier-sidebar .menu-item:hover a {
            background-color: #f3f4f6;
            color: #0a5694;
        }

        .supplier-sidebar .menu-item.active a,
        .sidebar-active {
            background: linear-gradient(135deg, #0a5694 0%, #171e60 100%);
            color: white !important;
        }

        /* Card hover effects */
        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .metric-card {
            background: linear-gradient(135deg, #0a5694 0%, #171e60 100%);
        }

        .success-card {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .warning-card {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        .danger-card {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        /* Fix layout structure */
        body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        .min-h-full {
            position: relative;
            display: flex;
            min-height: 100vh;
        }

        /* Prevent sidebar flashing during navigation */
        .supplier-sidebar,
        .sidebar-container {
            opacity: 1 !important;
            visibility: visible !important;
            transition: opacity 0.3s ease, visibility 0.3s ease;
            transform: translateZ(0);
            backface-visibility: hidden;
        }
        
        /* Ensure sidebars are visible during page load */
        .sidebar-initialized {
            opacity: 1 !important;
            visibility: visible !important;
        }
        
        /* Prevent Alpine components from flashing */
        [x-data] {
            opacity: 1 !important;
            visibility: visible !important;
            transition: opacity 0.2s ease, visibility 0.2s ease;
        }
        
        /* Only hide x-cloak elements until Alpine is ready */
        [x-cloak] {
            display: none !important;
        }
        
        .alpine-ready [x-cloak] {
            display: block !important;
        }

        /* Desktop sidebar positioning */
        @media (min-width: 1024px) {
            .sidebar-container {
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                width: 18rem;
                z-index: 50;
                display: flex;
                flex-direction: column;
                transform: translateX(0);
                transition: transform 0.3s ease-in-out;
            }

            .sidebar-container.hidden {
                transform: translateX(-100%);
            }

            .main-content-container {
                margin-left: 18rem;
                min-height: 100vh;
                width: calc(100% - 18rem);
                position: relative;
                z-index: 10;
                transition: margin-left 0.3s ease-in-out;
            }

            .main-content-container.expanded {
                margin-left: 0;
                width: 100%;
            }
        }

        /* Mobile responsiveness */
        @media (max-width: 1023px) {
            .sidebar-container {
                display: none;
            }

            .sidebar-container.mobile-open {
                display: flex;
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                width: 18rem;
                z-index: 50;
            }

            .main-content-container {
                margin-left: 0;
                width: 100%;
            }
        }

        /* Override any conflicting Tailwind classes */
        .lg\:pl-72 {
            padding-left: 0 !important;
        }

        .lg\:fixed {
            position: static !important;
        }

        /* Ensure Tailwind classes take precedence */
        .tailwind-override {
            all: revert;
        }
        
        /* Fix for potential Bootstrap conflicts */
        .container, .row, .col {
            all: revert;
        }
        
        /* Ensure proper z-index stacking */
        .z-50 { z-index: 50 !important; }
        .z-40 { z-index: 40 !important; }
        .z-30 { z-index: 30 !important; }
        .z-20 { z-index: 20 !important; }
        .z-10 { z-index: 10 !important; }
    </style>
</head>

<body class="h-full font-inter" x-data="{ sidebarOpen: false, sidebarHidden: false }">
    <div class="min-h-full">
        <!-- Mobile menu backdrop -->
        <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/80 lg:hidden" style="display: none;" @click="sidebarOpen = false"></div>

        <!-- Desktop sidebar -->
        <div class="sidebar-container" :class="{ 'hidden': sidebarHidden, 'mobile-open': sidebarOpen }">
            @include('supplier.partials.sidebar')
        </div>

        <!-- Main content area -->
        <div class="main-content-container" :class="{ 'expanded': sidebarHidden }">
            <!-- Top navigation -->
            <div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-white px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
                
                <!-- Mobile menu button -->
                <button type="button" class="lg:hidden -m-2.5 p-2.5 text-gray-700" @click="sidebarOpen = !sidebarOpen">
                    <span class="sr-only">Open sidebar</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H8.25" />
                    </svg>
                </button>

                <!-- Desktop sidebar toggle -->
                <button type="button" class="hidden lg:block -m-2.5 p-2.5 text-gray-700" @click="sidebarHidden = !sidebarHidden">
                    <span class="sr-only">Toggle sidebar</span>
                    <svg x-show="!sidebarHidden" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H8.25" />
                    </svg>
                    <svg x-show="sidebarHidden" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>

                <!-- Separator -->
                <div class="h-6 w-px bg-gray-900/10"></div>

                <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
                    <!-- Spacer to push items to the right -->
                    <div class="flex-1"></div>

                    <div class="flex items-center gap-x-4 lg:gap-x-6">
                        <!-- View Customer Site Button -->
                        <a href="{{ route('welcome') }}" target="_blank" class="-m-2.5 p-2.5 text-gray-400 hover:text-gray-500 flex items-center gap-x-2">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                            </svg>
                            <span class="hidden sm:block text-sm font-medium">View Site</span>
                        </a>

                        <!-- Notifications -->
                        @include('components.supplier.notification-dropdown')

                        <!-- User menu -->
                        <div class="relative" x-data="{ open: false }">
                            <button type="button" class="-m-1.5 flex items-center p-1.5" @click="open = !open">
                                <span class="sr-only">Open user menu</span>
                                @if(Auth::user()->profile_photo)
                                    <img class="h-8 w-8 rounded-full bg-gray-50 object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}">
                                @else
                                    <div class="h-8 w-8 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center text-white text-sm font-bold">
                                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}
                                    </div>
                                @endif
                                <span class="hidden lg:flex lg:items-center">
                                    <span class="ml-4 text-sm font-semibold leading-6 text-gray-900">{{ Auth::user()->name ?? 'Supplier' }}</span>
                                    <svg class="ml-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </button>

                            <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 z-10 mt-2.5 w-48 origin-top-right rounded-md bg-white py-2 shadow-lg ring-1 ring-gray-900/5" style="display: none;">
                                @if(Auth::user()->is_admin)
                                <a href="{{ route('admin.dashboard') }}" class="block px-3 py-1 text-sm leading-6 text-gray-900 hover:bg-gray-50">Admin Dashboard</a>
                                <a href="{{ route('crm.dashboard') }}" class="block px-3 py-1 text-sm leading-6 text-gray-900 hover:bg-gray-50">CRM Dashboard</a>
                                <div class="border-t border-gray-100 my-1"></div>
                                @endif
                                <a href="{{ route('profile.show') }}" class="block px-3 py-1 text-sm leading-6 text-gray-900 hover:bg-gray-50">Your profile</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-3 py-1 text-sm leading-6 text-gray-900 hover:bg-gray-50">Sign out</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <main class="py-10">
                <div class="px-4 sm:px-6 lg:px-8">
                    @if(session('success'))
                        <div class="mb-6 rounded-xl bg-gradient-to-r from-green-50 to-emerald-50 p-6 border border-green-200 shadow-lg" x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                        <svg class="h-6 w-6 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.236 4.53L7.53 10.53a.75.75 0 00-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4 flex-1">
                                    <h3 class="text-lg font-semibold text-green-800 mb-1">Success!</h3>
                                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <button @click="show = false" class="text-green-400 hover:text-green-600 transition-colors">
                                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-6 rounded-xl bg-gradient-to-r from-red-50 to-pink-50 p-6 border border-red-200 shadow-lg" x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                        <svg class="h-6 w-6 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4 flex-1">
                                    <h3 class="text-lg font-semibold text-red-800 mb-1">Error</h3>
                                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <button @click="show = false" class="text-red-400 hover:text-red-600 transition-colors">
                                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Initialize sidebars immediately to prevent flashing -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize supplier sidebar immediately
            document.querySelectorAll('.supplier-sidebar, .sidebar-container').forEach(el => {
                el.style.opacity = '1';
                el.style.visibility = 'visible';
                el.classList.add('sidebar-initialized');
            });
            
            // Initialize Alpine components
            document.addEventListener('alpine:initialized', () => {
                document.body.classList.add('alpine-ready');
                
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
            });
        });
    </script>

    @stack('scripts')
</body>
</html> 