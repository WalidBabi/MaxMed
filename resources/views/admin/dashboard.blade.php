@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">Admin Dashboard</h1>
        </div>
    </div>

    <div class="row">

        <div class="col-md-3 mb-4">
            <div class="card bg-warning text-white h-100" onclick="window.location='{{ route('admin.orders.index') }}'" style="cursor: pointer;">
                <div class="card-body">
                    <h5 class="card-title">Orders</h5>
                    <p class="card-text display-4">{{ \App\Models\Order::count() }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card bg-primary text-white h-100" onclick="window.location='{{ route('admin.products.index') }}'" style="cursor: pointer;">
                <div class="card-body">
                    <h5 class="card-title">Products</h5>
                    <p class="card-text display-4">{{ \App\Models\Product::count() }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card bg-info text-white h-100" onclick="window.location='{{ route('admin.news.index') }}'" style="cursor: pointer;">
                <div class="card-body">
                    <h5 class="card-title">News Articles</h5>
                    <p class="card-text display-4">{{ \App\Models\News::count() }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card bg-success text-white h-100" onclick="window.location='{{ route('admin.categories.index') }}'" style="cursor: pointer;">
                <div class="card-body">
                    <h5 class="card-title">Categories</h5>
                    <p class="card-text display-4">{{ \App\Models\Category::count() }}</p>
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
                        <table class="table table-striped">
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
                                        <span class="badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'processing' ? 'warning' : 'secondary') }}">
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
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-primary">View All Orders</a>
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
                        <table class="table table-striped">
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
                                            <span class="badge bg-{{ $product->inventory->quantity < 5 ? 'danger' : 'warning' }}">
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
                    <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-primary">Manage Inventory</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection