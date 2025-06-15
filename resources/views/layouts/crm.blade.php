<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'MaxMed CRM') - Professional Customer Relationship Management</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/heroicons@2.0.18/24/outline/style.css">
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Chart.js for Analytics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Additional Styling -->
    <style>
        /* CRM Sidebar styling */
        .crm-sidebar {
            background: #ffffff;
            min-height: 100vh;
            position: relative;
            border-right: 1px solid #e5e7eb;
            z-index: 30;
        }

        /* Card hover effects */
        .sidebar-active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .metric-card {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .success-card {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        .warning-card {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }
        .danger-card {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
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
    </style>
</head>

<body class="h-full font-inter" x-data="{ sidebarOpen: false, sidebarHidden: false }">
    <div class="min-h-full">
        <!-- Mobile menu backdrop -->
        <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/80 lg:hidden" style="display: none;" @click="sidebarOpen = false"></div>

        <!-- Desktop sidebar -->
        <div class="sidebar-container" :class="{ 'hidden': sidebarHidden, 'mobile-open': sidebarOpen }">
            @include('crm.partials.sidebar')
        </div>

        <!-- Main content area -->
        <div class="main-content-container" :class="{ 'expanded': sidebarHidden }">
            <!-- Top navigation -->
            <div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-white px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
             


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
                        <button type="button" class="-m-2.5 p-2.5 text-gray-400 hover:text-gray-500 relative">
                            <span class="sr-only">View notifications</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                            </svg>
                            <span class="absolute -top-1 -right-1 h-4 w-4 rounded-full bg-red-500 text-xs text-white flex items-center justify-center">3</span>
                        </button>

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
                                    <span class="ml-4 text-sm font-semibold leading-6 text-gray-900">{{ Auth::user()->name ?? 'User' }}</span>
                                    <svg class="ml-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </button>
                            
                            <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 z-10 mt-2.5 w-48 origin-top-right rounded-md bg-white py-2 shadow-lg ring-1 ring-gray-900/5" style="display: none;">
                                <a href="{{ route('admin.dashboard') }}" class="block px-3 py-1 text-sm leading-6 text-gray-900 hover:bg-gray-50">Admin Dashboard</a>
                                @if(Auth::user()->hasPermission('supplier.products.view'))
                                <a href="{{ route('supplier.dashboard') }}" class="block px-3 py-1 text-sm leading-6 text-gray-900 hover:bg-gray-50">Supplier Dashboard</a>
                                @endif
                                <div class="border-t border-gray-100 my-1"></div>
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
                        <div class="mb-6 rounded-md bg-green-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.236 4.53L7.53 10.53a.75.75 0 00-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-6 rounded-md bg-red-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
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
    
    @stack('scripts')
</body>
</html> 