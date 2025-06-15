@props([
    'title' => 'Dashboard',
    'type' => 'admin', // admin, supplier, crm
    'user' => null
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }} - {{ config('app.name', 'MaxMed') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Custom Styles -->
    <style>
        .card-hover {
            transition: all 0.2s ease-in-out;
        }
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .metric-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .success-card { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .warning-card { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
        .danger-card { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">
        <!-- Navigation -->
        @if($type === 'admin')
            @include('admin.partials.sidebar')
        @elseif($type === 'supplier')
            @include('supplier.partials.sidebar')
        @elseif($type === 'crm')
            @include('crm.partials.sidebar')
        @endif

        <!-- Page Content -->
        <main class="lg:pl-72">
            <div class="xl:pl-96">
                <!-- Header -->
                <div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-6 border-b border-white/5 bg-gray-900 px-4 shadow-sm sm:px-6 lg:px-8">
                    <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
                        <div class="flex flex-1"></div>
                        <div class="flex items-center gap-x-4 lg:gap-x-6">
                            <!-- Notifications -->
                            <button type="button" class="-m-2.5 p-2.5 text-gray-400 hover:text-gray-300">
                                <span class="sr-only">View notifications</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                </svg>
                            </button>

                            <!-- Profile dropdown -->
                            <div class="relative">
                                <button type="button" class="flex items-center gap-x-4 px-6 py-3 text-sm font-semibold leading-6 text-white" id="user-menu-button">
                                    <img class="h-8 w-8 rounded-full bg-gray-800" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="">
                                    <span class="hidden lg:flex lg:items-center">
                                        <span class="ml-4 text-sm font-semibold leading-6 text-white" aria-hidden="true">
                                            {{ $user ? $user->name : (Auth::user()->name ?? 'User') }}
                                        </span>
                                        <svg class="ml-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="px-4 py-10 sm:px-6 lg:px-8 lg:py-6">
                    {{ $slot }}
                </div>
            </div>
        </main>
    </div>

    <!-- Additional Scripts -->
    @stack('scripts')
</body>
</html> 