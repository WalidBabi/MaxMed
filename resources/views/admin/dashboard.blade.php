@extends('admin.layouts.app')

@section('content')
<div class="main-content">
    <!-- Welcome Section -->
    <div class="welcome-card bg-white rounded-3 p-4 shadow-sm mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
            <div>
                <h2 class="h4 mb-1">Welcome back, {{ Auth::user()->name }}! 👋</h2>
                <p class="text-muted mb-0">Here's what's happening with your store today.</p>
            </div>
            <div class="mt-3 mt-md-0">
                <span class="badge bg-light text-dark">
                    <i class="far fa-calendar-alt me-2"></i>
                    {{ now()->format('l, F j, Y') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Sales Section -->
    <div class="section-header mb-3">
        <h3 class="h5 text-muted mb-0">SALES</h3>
    </div>
    <div class="row g-4 mb-4">
        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <a href="{{ route('admin.customers.index') }}" class="text-decoration-none">
                <div class="card h-100 border-0 shadow-sm hover-lift">
                    <div class="card-body text-center p-4">
                        <div class="icon-shape icon-lg bg-soft-indigo text-indigo rounded-3 mb-3">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                        <h5 class="mb-1">Customers</h5>
                        <p class="text-muted small mb-0">View customer details</p>
                    </div>
                </div>
            </a>

        </div>
        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <a href="{{ route('admin.quotes.index') }}" class="text-decoration-none">
                <div class="card h-100 border-0 shadow-sm hover-lift">
                    <div class="card-body text-center p-4">
                        <div class="icon-shape icon-lg bg-soft-purple text-purple rounded-3 mb-3">
                            <i class="fas fa-file-invoice-dollar fa-2x"></i>
                        </div>
                        <h5 class="mb-1">Quotes</h5>
                        <p class="text-muted small mb-0">Manage quotes</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <a href="{{ route('admin.invoices.index') }}" class="text-decoration-none">
                <div class="card h-100 border-0 shadow-sm hover-lift">
                    <div class="card-body text-center p-4">
                        <div class="icon-shape icon-lg bg-soft-danger text-danger rounded-3 mb-3">
                            <i class="fas fa-file-invoice fa-2x"></i>
                        </div>
                        <h5 class="mb-1">Invoices</h5>
                        <p class="text-muted small mb-0">Manage invoices & payments</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <a href="{{ route('admin.orders.index') }}" class="text-decoration-none">
                <div class="card h-100 border-0 shadow-sm hover-lift">
                    <div class="card-body text-center p-4">
                        <div class="icon-shape icon-lg bg-soft-success text-success rounded-3 mb-3">
                            <i class="fas fa-shopping-cart fa-2x"></i>
                        </div>
                        <h5 class="mb-1">Orders</h5>
                        <p class="text-muted small mb-0">View and manage orders</p>
                    </div>
                </div>
            </a>
        </div>

      

        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <a href="{{ route('admin.deliveries.index') }}" class="text-decoration-none">
                <div class="card h-100 border-0 shadow-sm hover-lift">
                    <div class="card-body text-center p-4">
                        <div class="icon-shape icon-lg bg-soft-warning text-warning rounded-3 mb-3">
                            <i class="fas fa-truck fa-2x"></i>
                        </div>
                        <h5 class="mb-1">Deliveries</h5>
                        <p class="text-muted small mb-0">Track shipments</p>
                    </div>
                </div>
            </a>
        </div>


    </div>

    <!-- User Management Section -->
    <div class="section-header mb-3">
        <h3 class="h5 text-muted mb-0">USER MANAGEMENT</h3>
    </div>
    <div class="row g-4 mb-4">
        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <a href="{{ route('admin.users.index') }}" class="text-decoration-none">
                <div class="card h-100 border-0 shadow-sm hover-lift">
                    <div class="card-body text-center p-4">
                        <div class="icon-shape icon-lg bg-soft-blue text-blue rounded-3 mb-3">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                        <h5 class="mb-1">Users</h5>
                        <p class="text-muted small mb-0">Manage user accounts</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <a href="{{ route('admin.roles.index') }}" class="text-decoration-none">
                <div class="card h-100 border-0 shadow-sm hover-lift">
                    <div class="card-body text-center p-4">
                        <div class="icon-shape icon-lg bg-soft-orange text-orange rounded-3 mb-3">
                            <i class="fas fa-user-tag fa-2x"></i>
                        </div>
                        <h5 class="mb-1">Roles</h5>
                        <p class="text-muted small mb-0">Manage user roles & permissions</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Management Section -->
    <div class="section-header mb-3">
        <h3 class="h5 text-muted mb-0">MANAGEMENT</h3>
    </div>
    <div class="row g-4 mb-4">
        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <a href="{{ route('admin.products.index') }}" class="text-decoration-none">
                <div class="card h-100 border-0 shadow-sm hover-lift">
                    <div class="card-body text-center p-4">
                        <div class="icon-shape icon-lg bg-soft-primary text-primary rounded-3 mb-3">
                            <i class="fas fa-box fa-2x"></i>
                        </div>
                        <h5 class="mb-1">Products</h5>
                        <p class="text-muted small mb-0">Manage your products</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <a href="{{ route('admin.news.index') }}" class="text-decoration-none">
                <div class="card h-100 border-0 shadow-sm hover-lift">
                    <div class="card-body text-center p-4">
                        <div class="icon-shape icon-lg bg-soft-info text-info rounded-3 mb-3">
                            <i class="fas fa-newspaper fa-2x"></i>
                        </div>
                        <h5 class="mb-1">News</h5>
                        <p class="text-muted small mb-0">Manage news articles</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <a href="{{ route('admin.categories.index') }}" class="text-decoration-none">
                <div class="card h-100 border-0 shadow-sm hover-lift">
                    <div class="card-body text-center p-4">
                        <div class="icon-shape icon-lg bg-soft-danger text-danger rounded-3 mb-3">
                            <i class="fas fa-th-large fa-2x"></i>
                        </div>
                        <h5 class="mb-1">Categories</h5>
                        <p class="text-muted small mb-0">Manage product categories</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <a href="{{ route('admin.brands.index') }}" class="text-decoration-none">
                <div class="card h-100 border-0 shadow-sm hover-lift">
                    <div class="card-body text-center p-4">
                        <div class="icon-shape icon-lg bg-soft-purple text-purple rounded-3 mb-3">
                            <i class="fas fa-tag fa-2x"></i>
                        </div>
                        <h5 class="mb-1">Brands</h5>
                        <p class="text-muted small mb-0">Manage product brands</p>
                    </div>
                </div>
            </a>
        </div>
    </div>


</div>

<style>
    /* Custom styles */
    .hover-lift {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }

    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.08) !important;
    }

    .icon-shape {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 4rem;
        height: 4rem;
    }

    .bg-soft-primary {
        background-color: rgba(79, 70, 229, 0.1) !important;
    }

    .bg-soft-success {
        background-color: rgba(16, 185, 129, 0.1) !important;
    }

    .bg-soft-info {
        background-color: rgba(59, 130, 246, 0.1) !important;
    }

    .bg-soft-warning {
        background-color: rgba(245, 158, 11, 0.1) !important;
    }

    .bg-soft-danger {
        background-color: rgba(239, 68, 68, 0.1) !important;
    }

    .bg-soft-purple {
        background-color: rgba(139, 92, 246, 0.1) !important;
    }

    .bg-soft-indigo {
        background-color: rgba(99, 102, 241, 0.1) !important;
    }

    .bg-soft-blue {
        background-color: rgba(59, 130, 246, 0.1) !important;
    }

    .bg-soft-orange {
        background-color: rgba(251, 146, 60, 0.1) !important;
    }

    .text-purple {
        color: #8b5cf6 !important;
    }

    .text-indigo {
        color: #6366f1 !important;
    }

    .text-blue {
        color: #3b82f6 !important;
    }

    .text-orange {
        color: #fb923c !important;
    }

    .welcome-card {
        background: linear-gradient(135deg, #f8f9ff 0%, #f1f3ff 100%);
        border: 1px solid rgba(79, 70, 229, 0.1);
    }

    .section-header {
        padding: 0.5rem 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .section-header h3 {
        font-weight: 600;
        letter-spacing: 1px;
    }
</style>

<script>
    // Add fade-in animation to cards
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.card');
        cards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
            card.classList.add('fade-in');
        });
    });
</script>
@endsection