<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Admin</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }
        
        .admin-sidebar {
            background: #2c3e50;
            color: #ecf0f1;
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
            border-left: 3px solid #3498db;
        }
        
        .nav-pills .nav-link.active {
            background-color: rgba(52, 152, 219, 0.2);
            color: #fff;
            border-left: 3px solid #3498db;
        }

        .admin-top-navbar {
            background-color: #fff;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
            height: 60px;
        }
        
        .content-wrapper {
            transition: all 0.3s;
            background-color: #f8f9fa;
        }
        
        .alert {
            border-radius: 3px;
            border-left: 4px solid;
        }
        
        .alert-success {
            border-left-color: #2ecc71;
        }
        
        .alert-danger {
            border-left-color: #e74c3c;
        }
        
        .dropdown-menu {
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        }
        
        .user-dropdown .dropdown-toggle::after {
            display: none;
        }
        
        .btn-view-site {
            background-color: #3498db;
            color: white;
            transition: all 0.2s;
        }
        
        .btn-view-site:hover {
            background-color: #2980b9;
            color: white;
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
                    <h4 class="m-0"><i class="fas fa-medkit me-2"></i>MaxMed Admin</h4>
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
            </ul>
            <hr class="opacity-25">
            <div class="px-3 pb-3">
                <div class="user-dropdown dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=3498db&color=fff" 
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