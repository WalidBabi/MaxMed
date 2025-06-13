<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Admin</title>

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('img/favicon/favicon-16x16.png') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <meta name="theme-color" content="#171e60">

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    
    <style>
        :root {
            --text-color: #7d84ab;
            --secondary-text-color: #dee2ec;
            --bg-color: #0c1e35;
            --secondary-bg-color: #0b1a2c;
            --border-color: rgba(83, 93, 125, 0.3);
            --sidebar-header-height: 100px;
            --sidebar-footer-height: 230px;
        }

        body {
            margin: 0;
            height: 100vh;
            font-family: 'Poppins', sans-serif;
            color: #3f4750;
            font-size: 0.9rem;
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 260px;
            background: var(--bg-color);
            color: var(--text-color);
            position: fixed;
            height: 100vh;
            transition: all 0.3s;
            z-index: 1000;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar.collapsed {
            width: 60px;
        }

        .sidebar-header {
            height: var(--sidebar-header-height);
            display: flex;
            align-items: center;
            padding: 0 20px;
        }

        .pro-sidebar-logo {
            display: flex;
            align-items: center;
        }

        .pro-sidebar-logo > div {
            width: 35px;
            min-width: 35px;
            height: 35px;
            min-height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            color: white;
            font-size: 24px;
            font-weight: 700;
            background-color: #ff8100;
            margin-right: 10px;
        }

        .pro-sidebar-logo > h5 {
            color: white;
            margin: 0;
            font-size: 20px;
            font-weight: 600;
            transition: opacity 0.3s;
        }

        .sidebar.collapsed .pro-sidebar-logo > h5 {
            opacity: 0;
        }

        .menu {
            padding: 20px 0;
            flex: 1;
            min-height: 0;
        }

        .menu-header {
            padding: 10px 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            color: var(--secondary-text-color);
            margin-top: 20px;
        }

        .menu-item a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: var(--text-color);
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }

        .menu-item:hover a,
        .menu-item.active a {
            background-color: rgba(255, 255, 255, 0.05);
            border-left-color: #ff8100;
            color: white;
        }

        .menu-icon {
            width: 20px;
            margin-right: 15px;
            text-align: center;
        }

        .menu-title {
            transition: opacity 0.3s;
        }

        .sidebar.collapsed .menu-title {
            opacity: 0;
        }

        .sidebar-collapser {
            position: absolute;
            top: 50%;
            left: 260px;
            transform: translateY(-50%);
            width: 30px;
            height: 30px;
            background-color: white;
            border: 1px solid var(--border-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: left 0.3s;
            z-index: 1001;
        }

        .sidebar.collapsed .sidebar-collapser {
            left: 60px;
        }

        .content-wrapper {
            margin-left: 260px;
            flex-grow: 1;
            transition: margin-left 0.3s;
        }

        .sidebar.collapsed + .content-wrapper {
            margin-left: 60px;
        }

        .admin-top-navbar {
            background-color: white;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
            height: 60px;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .btn-view-site {
            background-color: var(--aux1-color);
            color: black;
            transition: all 0.2s;
            border-radius: 20px;
            padding: 5px 15px;
            text-decoration: none;
        }

        .btn-view-site:hover {
            background-color: var(--main-color);
            color: black;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-logout {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 5px 15px;
            border-radius: 20px;
            transition: all 0.2s;
        }

        .btn-logout:hover {
            background-color: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        /* Custom Scrollbar for Sidebar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }
    </style>
</head>
<body>
    <div class="layout">
        <!-- Sidebar -->
        <aside class="sidebar">
       
            <div class="sidebar-header">
                <div class="pro-sidebar-logo">
                    <div>M</div>
                    <h5>MaxMed Admin</h5>
                </div>
            </div>
            <nav class="menu">
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('admin.dashboard') }}">
                            <span class="menu-icon">
                                <i class="fas fa-tachometer-alt"></i>
                            </span>
                            <span class="menu-title">Dashboard</span>
                        </a>
                    </li>

                    <li class="menu-header"><span>SALES</span></li>
                    <li class="menu-item {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.customers.index') }}">
                            <span class="menu-icon">
                                <i class="fas fa-users"></i>
                            </span>
                            <span class="menu-title">Customers</span>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.orders.index') }}">
                            <span class="menu-icon">
                                <i class="fas fa-shopping-cart"></i>
                            </span>
                            <span class="menu-title">Orders</span>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('admin.deliveries.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.deliveries.index') }}">
                            <span class="menu-icon">
                                <i class="fas fa-truck"></i>
                            </span>
                            <span class="menu-title">Deliveries</span>
                        </a>
                    </li>
                   

                    <li class="menu-header"><span>MANAGEMENT</span></li>
                    <li class="menu-item {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.products.index') }}">
                            <span class="menu-icon">
                                <i class="fas fa-box"></i>
                            </span>
                            <span class="menu-title">Products</span>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('admin.news.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.news.index') }}">
                            <span class="menu-icon">
                                <i class="fas fa-newspaper"></i>
                            </span>
                            <span class="menu-title">News</span>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.categories.index') }}">
                            <span class="menu-icon">
                                <i class="fas fa-th-large"></i>
                            </span>
                            <span class="menu-title">Categories</span>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.brands.index') }}">
                            <span class="menu-icon">
                                <i class="fas fa-tag"></i>
                            </span>
                            <span class="menu-title">Brands</span>
                        </a>
                    </li>

                    <li class="menu-header"><span>USER MANAGEMENT</span></li>
                    <li class="menu-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.users.index') }}">
                            <span class="menu-icon">
                                <i class="fas fa-users"></i>
                            </span>
                            <span class="menu-title">Users</span>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.roles.index') }}">
                            <span class="menu-icon">
                                <i class="fas fa-user-tag"></i>
                            </span>
                            <span class="menu-title">Roles</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="content-wrapper">
            <!-- Top Navigation -->
            <header class="admin-top-navbar sticky-top">
                <div>
                    <h5 class="mb-0">{{ ucfirst(request()->segment(2) ?? 'Dashboard') }}</h5>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('welcome') }}" class="btn-view-site">
                        <i class="fas fa-external-link-alt me-1"></i> View Site
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="btn-logout">
                            <i class="fas fa-sign-out-alt me-2"></i>Log out
                        </button>
                    </form>
                </div>
            </header>

            <!-- Flash Messages -->
            <div class="container-fluid px-4 py-3">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            </div>

            <!-- Content -->
            <main class="container-fluid px-4 pb-4">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Admin-specific scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Bootstrap tooltips if they exist
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>

    @stack('scripts')
</body>
</html> 