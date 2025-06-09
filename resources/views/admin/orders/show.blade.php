@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">Order Details #{{ $order->id }}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- Customer Information -->
                    <div class="mb-4">
                        <h3 class="card-title">Customer Information</h3>
                        <p><strong>Name:</strong> {{ $order->user ? $order->user->name : 'N/A' }}</p>
                        <p><strong>Email:</strong> {{ $order->user ? $order->user->email : 'N/A' }}</p>
                        <p><strong>Order Date:</strong> {{ $order->created_at->format('Y-m-d H:i') }}</p>
                    </div>

                    <!-- Order Status -->
                    <div class="mb-4">
                        <h3 class="card-title">Order Status</h3>
                        <form action="{{ route('admin.orders.status.update', $order) }}" method="POST" class="d-flex align-items-center gap-3">
                            @csrf
                            @method('PUT')
                            <select name="status" class="form-select">
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            <button type="submit" class="btn btn-primary">
                                Update Status
                            </button>
                        </form>
                    </div>

                    <!-- Order Items -->
                    <div class="mb-4">
                        <h3 class="card-title">Order Items</h3>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->orderItems as $item)
                                        <tr>
                                            <td>{{ $item->product ? $item->product->name : 'N/A' }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>${{ number_format($item->price, 2) }}</td>
                                            <td>${{ number_format($item->price * $item->quantity, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold">Total:</td>
                                        <td>${{ number_format($order->total, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection