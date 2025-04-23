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
    
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        :root {
            --bg-color: #ffffff;
            --main-color: #171e60;
            --aux1-color: #0a5694;
            --aux2-color: #ffffff;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-color);
        }
        
        .admin-sidebar {
            background: var(--main-color);
            color: var(--aux2-color);
            width: 260px;
            min-height: 100vh;
            transition: all 0.3s;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            z-index: 999;
        }
        
        .logo-container {
            padding: 1.5rem 1rem;
        }
        
        .nav-pills .nav-link {
            border-radius: 0;
            color: rgba(255, 255, 255, 0.8);
            padding: 0.8rem 1rem;
            margin: 0.2rem 0;
            border-left: 3px solid transparent;
            transition: 0.2s ease-in-out;
        }
        
        .nav-pills .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: #fff;
            border-left: 3px solid var(--aux1-color);
        }
        
        .nav-pills .nav-link.active {
            background-color: rgba(10, 86, 148, 0.2);
            color: #fff;
            border-left: 3px solid var(--aux1-color);
        }

        .admin-top-navbar {
            background-color: var(--bg-color);
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
            height: 60px;
        }
        
        .content-wrapper {
            transition: all 0.3s;
            background-color: #f8f9fa;
        }
        
        .alert {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 
                0 8px 32px 0 rgba(31, 38, 135, 0.37),
                inset 0 0 80px rgba(255, 255, 255, 0.3);
            position: relative;
            transition: all 0.3s ease;
            border-radius: 8px;
        }
        
        .alert::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 50%;
            background: linear-gradient(
                180deg,
                rgba(255, 255, 255, 0.3) 0%,
                rgba(255, 255, 255, 0.1) 100%
            );
            border-radius: 8px 8px 0 0;
            pointer-events: none;
        }
        
        .alert-success {
            background: linear-gradient(135deg, rgba(25, 135, 84, 0.1), rgba(255, 255, 255, 0.9));
            border-left: 4px solid #2ecc71;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.1), rgba(255, 255, 255, 0.9));
            border-left: 4px solid #e74c3c;
        }
        
        .dropdown-menu {
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
            border-radius: 8px;
            overflow: hidden;
        }
        
        .user-dropdown .dropdown-toggle::after {
            display: none;
        }
        
        .btn-view-site {
            background-color: var(--aux1-color);
            color: white;
            transition: all 0.2s;
            border-radius: 20px;
            padding: 5px 15px;
        }
        
        .btn-view-site:hover {
            background-color: var(--main-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        /* Card Styling */
        .card {
            border: none;
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.35s;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.15);
        }
        
        .card .card-header {
            background-color: var(--bg-color);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .card .card-title {
            color: var(--main-color);
            position: relative;
        }
        
        /* Button Styling */
        .btn-primary {
            background-color: var(--main-color);
            border-color: var(--main-color);
        }
        
        .btn-primary:hover {
            background-color: var(--aux1-color);
            border-color: var(--aux1-color);
        }
        
        .btn-outline-primary {
            color: var(--main-color);
            border-color: var(--main-color);
        }
        
        .btn-outline-primary:hover {
            background-color: var(--main-color);
            border-color: var(--main-color);
            color: white;
        }
        
        /* Page Header Styling */
        .page-header {
            margin-bottom: 1.5rem;
            position: relative;
            border-left: 4px solid var(--aux1-color);
            padding-left: 15px;
        }
        
        .page-title {
            font-size: 1.25rem;
            color: var(--main-color);
            margin-bottom: 0.5rem;
            transition: color 0.3s;
        }
        
        /* Pagination styling */
        .pagination {
            margin-bottom: 0;
        }
        
        .page-item.active .page-link {
            background-color: var(--main-color);
            border-color: var(--main-color);
            color: white;
        }
        
        .page-link {
            color: var(--main-color);
            padding: 0.5rem 0.75rem;
            margin-left: -1px;
            line-height: 1.25;
            border: 1px solid #dddfeb;
        }
        
        .page-link:hover {
            color: var(--aux1-color);
            background-color: #eaecf4;
            border-color: #dddfeb;
        }
        
        .page-link:focus {
            box-shadow: 0 0 0 0.2rem rgba(23, 30, 96, 0.25);
        }
        
        .page-item.disabled .page-link {
            color: #858796;
            pointer-events: none;
            background-color: #fff;
            border-color: #dddfeb;
        }
    </style>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="admin-sidebar">
            <div class="logo-container d-flex align-items-center">
                <a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-white">
                    <h4 class="m-0">MaxMed Admin</h4>
                </a>
            </div>
            <hr class="mt-0 mb-3 opacity-25">
            <ul class="nav nav-pills flex-column mb-auto px-2">
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt me-2"></i>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                        <i class="fas fa-box me-2"></i>
                        Products
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.news.index') }}" class="nav-link {{ request()->routeIs('admin.news.*') ? 'active' : '' }}">
                        <i class="fas fa-newspaper me-2"></i>
                        News
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                        <i class="fas fa-shopping-cart me-2"></i>
                        Orders
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                        <i class="fas fa-th-large me-2"></i>
                        Categories
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.brands.index') }}" class="nav-link {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}">
                        <i class="fas fa-tag me-2"></i>
                        Brands
                    </a>
                </li>
            </ul>
            <hr class="opacity-25">
            <div class="px-3 pb-3">
                <div class="user-dropdown dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0a5694&color=fff" 
                            alt="User" width="36" height="36" class="rounded-circle me-2">
                        <div>
                            <strong>{{ Auth::user()->name }}</strong>
                            <div class="small text-white-50">Administrator</div>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                        <li><h6 class="dropdown-header">User Options</h6></li>
                        <li><a class="dropdown-item" href="{{ route('welcome') }}"><i class="fas fa-external-link-alt me-2"></i>View Site</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt me-2"></i>Log out</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="content-wrapper flex-grow-1 d-flex flex-column">
            <!-- Top Navigation -->
            <header class="admin-top-navbar sticky-top px-4 d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="mb-0 text-muted">{{ ucfirst(request()->segment(2) ?? 'Dashboard') }}</h5>
                </div>
                <div class="d-flex align-items-center">
                    <a href="{{ route('welcome') }}" class="btn btn-view-site btn-sm">
                        <i class="fas fa-external-link-alt me-1"></i> View Site
                    </a>
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
            <main class="container-fluid px-4 pb-4 flex-grow-1">
                @yield('content')
            </main>
            
            <!-- Footer -->
            <footer class="py-3 mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; MaxMed {{ date('Y') }}</div>
                        <div>
                            <a href="#" class="text-decoration-none text-muted">Privacy Policy</a>
                            &middot;
                            <a href="#" class="text-decoration-none text-muted">Terms &amp; Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
</body>
</html> 