@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">

    <div class="page-header mb-4">
        <h2>Welcome, {{ Auth::user()->name }}</h2>
        <p class="page-description text-muted">Overview of your store's performance and recent activities</p>
    </div>

    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card h-100 dashboard-stat-card" onclick="window.location='{{ route('admin.orders.index') }}'" style="cursor: pointer;">
                <div class="card-body">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h5 class="card-title">Orders</h5>
                    <p class="card-text display-4">{{ \App\Models\Order::count() }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card h-100 dashboard-stat-card" onclick="window.location='{{ route('admin.products.index') }}'" style="cursor: pointer;">
                <div class="card-body">
                    <div class="stat-icon">
                        <i class="fas fa-box"></i>
                    </div>
                    <h5 class="card-title">Products</h5>
                    <p class="card-text display-4">{{ \App\Models\Product::count() }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card h-100 dashboard-stat-card" onclick="window.location='{{ route('admin.news.index') }}'" style="cursor: pointer;">
                <div class="card-body">
                    <div class="stat-icon">
                        <i class="fas fa-newspaper"></i>
                    </div>
                    <h5 class="card-title">News Articles</h5>
                    <p class="card-text display-4">{{ \App\Models\News::count() }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card h-100 dashboard-stat-card" onclick="window.location='{{ route('admin.categories.index') }}'" style="cursor: pointer;">
                <div class="card-body">
                    <div class="stat-icon">
                        <i class="fas fa-th-large"></i>
                    </div>
                    <h5 class="card-title">Categories</h5>
                    <p class="card-text display-4">{{ \App\Models\Category::count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card h-100 dashboard-stat-card" onclick="window.location='{{ route('admin.brands.index') }}'" style="cursor: pointer;">
                <div class="card-body">
                    <div class="stat-icon">
                        <i class="fas fa-tag"></i>
                    </div>
                    <h5 class="card-title">Brands</h5>
                    <p class="card-text display-4">{{ \App\Models\Brand::count() }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card h-100 dashboard-stat-card" onclick="window.location='{{ route('admin.deliveries.index') }}'" style="cursor: pointer;">
                <div class="card-body">
                    <div class="stat-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <h5 class="card-title">Deliveries</h5>
                    @php
                        $deliveryCount = \App\Models\Delivery::count();
                        $inTransitCount = \App\Models\Delivery::where('status', 'in_transit')->count();
                        $deliveredCount = \App\Models\Delivery::where('status', 'delivered')->count();
                        $pendingCount = \App\Models\Delivery::where('status', 'pending')->count();
                    @endphp
                    <p class="card-text display-4">{{ $deliveryCount }}</p>
                    <div class="delivery-stats mt-2">
                        <span class="badge bg-primary me-1">In Transit: {{ $inTransitCount }}</span>
                        <span class="badge bg-success me-1">Delivered: {{ $deliveredCount }}</span>
                        <span class="badge bg-warning">Pending: {{ $pendingCount }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Orders</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(\App\Models\Order::latest()->take(5)->get() as $order)
                                <tr>
                                    <td>{{ $order->id }}</td>
                                    <td>{{ $order->user->name }}</td>
                                    <td>${{ number_format($order->total_amount, 2) }}</td>
                                    <td>
                                        <span class="badge lab-badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'processing' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-primary">View All Orders</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Low Stock Products</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(\App\Models\Product::whereHas('inventory', function($query) {
                                $query->where('quantity', '<', 10);
                                    })->take(5)->get() as $product)
                                    <tr>
                                        <td>{{ $product->name }}</td>
                                        <td>${{ number_format($product->price, 2) }}</td>
                                        <td>
                                            <span class="badge lab-badge bg-{{ $product->inventory->quantity < 5 ? 'danger' : 'warning' }}">
                                                {{ $product->inventory->quantity }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.products.index') }}" class="btn btn-primary">Manage Inventory</a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .dashboard-stat-card {
        border: none;
        border-radius: 8px;
        overflow: hidden;
        transition: all 0.35s;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        position: relative;
    }

    .dashboard-stat-card:nth-child(1),
    .dashboard-stat-card:nth-child(1) .stat-icon {
        background: linear-gradient(135deg, rgba(255, 187, 0, 0.1), rgba(255, 187, 0, 0.2));
        border-left: 4px solid #ffbb00;
    }

    .dashboard-stat-card:nth-child(2),
    .dashboard-stat-card:nth-child(2) .stat-icon {
        background: linear-gradient(135deg, rgba(23, 30, 96, 0.1), rgba(23, 30, 96, 0.2));
        border-left: 4px solid #171e60;
    }

    .dashboard-stat-card:nth-child(3),
    .dashboard-stat-card:nth-child(3) .stat-icon {
        background: linear-gradient(135deg, rgba(10, 86, 148, 0.1), rgba(10, 86, 148, 0.2));
        border-left: 4px solid #0a5694;
    }

    .dashboard-stat-card:nth-child(4),
    .dashboard-stat-card:nth-child(4) .stat-icon {
        background: linear-gradient(135deg, rgba(25, 135, 84, 0.1), rgba(25, 135, 84, 0.2));
        border-left: 4px solid #198754;
    }

    .dashboard-stat-card:nth-child(5),
    .dashboard-stat-card:nth-child(5) .stat-icon {
        background: linear-gradient(135deg, rgba(220, 53, 69, 0.1), rgba(220, 53, 69, 0.2));
        border-left: 4px solid #dc3545;
    }

    .dashboard-stat-card:nth-child(6),
    .dashboard-stat-card:nth-child(6) .stat-icon {
        background: linear-gradient(135deg, rgba(13, 110, 253, 0.1), rgba(13, 110, 253, 0.2));
        border-left: 4px solid #0d6efd;
    }
    
    .delivery-stats {
        font-size: 0.75rem;
        line-height: 1.2;
    }
    
    .delivery-stats .badge {
        margin-bottom: 0.25rem;
        display: inline-block;
        padding: 0.25rem 0.4rem;
        font-weight: 500;
    }

    .dashboard-stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 25px rgba(0, 0, 0, 0.15);
    }

    .stat-icon {
        display: inline-block;
        width: 50px;
        height: 50px;
        line-height: 50px;
        text-align: center;
        border-radius: 50%;
        font-size: 20px;
        color: #fff;
        margin-bottom: 15px;
    }

    .dashboard-stat-card:nth-child(1) .stat-icon {
        background-color: #ffbb00;
        color: #fff;
    }

    .dashboard-stat-card:nth-child(2) .stat-icon {
        background-color: #171e60;
        color: #fff;
    }

    .dashboard-stat-card:nth-child(3) .stat-icon {
        background-color: #0a5694;
        color: #fff;
    }

    .dashboard-stat-card:nth-child(4) .stat-icon {
        background-color: #198754;
        color: #fff;
    }

    .dashboard-stat-card:nth-child(5) .stat-icon {
        background-color: #dc3545;
        color: #fff;
    }

    .card-title {
        font-size: 1rem;
        color: #495057;
        margin-bottom: 10px;
    }

    .card-text.display-4 {
        font-size: 2.5rem;
        font-weight: 700;
        color: #212529;
        margin-bottom: 0;
    }

    .lab-badge {
        border-radius: 20px;
        padding: 5px 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    /* Table styling */
    .table {
        border-collapse: separate;
        border-spacing: 0;
    }

    .table thead th {
        border-bottom: 2px solid rgba(0, 0, 0, 0.05);
        font-weight: 600;
        color: #495057;
    }

    .table tbody tr {
        transition: all 0.2s;
    }

    .table tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }

    .table td,
    .table th {
        padding: 0.75rem;
        vertical-align: middle;
        border-top: 1px solid rgba(0, 0, 0, 0.05);
    }
</style>
@endsection