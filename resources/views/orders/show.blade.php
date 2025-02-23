@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold">Order Details</h2>
                        <a href="{{ route('orders.index') }}" class="text-blue-600 hover:text-blue-800">← Back to Orders</a>
                    </div>

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <!-- Order Summary -->
                    <div class="bg-gray-50 rounded-lg p-6 mb-6">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div>
                                <p class="text-gray-600">Order Number</p>
                                <p class="font-semibold">{{ $order->order_number }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Date Placed</p>
                                <p class="font-semibold">{{ $order->created_at->format('M d, Y') }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Status</p>
                                <span class="px-3 py-1 rounded-full text-sm inline-block mt-1
                                    @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                                    @elseif($order->status === 'shipped') bg-purple-100 text-purple-800
                                    @elseif($order->status === 'delivered') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-gray-600">Total Amount</p>
                                <p class="font-semibold">${{ number_format($order->total_amount, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="mb-8">
                        <h3 class="text-xl font-semibold mb-4">Order Items</h3>
                        <div class="space-y-4">
                            @foreach($order->items as $item)
                                <div class="flex items-center justify-between border-b pb-4">
                                    <div class="flex items-center">
                                        @if($item->product->image_url)
                                            <img src="{{ asset($item->product->image_url) }}" 
                                                 alt="{{ $item->product->name }}" 
                                                 class="w-16 h-16 object-cover rounded">
                                        @endif
                                        <div class="ml-4">
                                            <h4 class="font-semibold">{{ $item->product->name }}</h4>
                                            <p class="text-gray-600">Quantity: {{ $item->quantity }}</p>
                                            <p class="text-gray-600">Price per unit: ${{ number_format($item->price, 2) }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold">${{ number_format($item->price * $item->quantity, 2) }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Shipping Information -->
                    <div class="grid md:grid-cols-2 gap-8">
                        <div>
                            <h3 class="text-xl font-semibold mb-4">Shipping Address</h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p>{{ $order->shipping_address }}</p>
                                <p>{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_zipcode }}</p>
                                <p>Phone: {{ $order->shipping_phone }}</p>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-xl font-semibold mb-4">Payment Information</h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                @if($order->transaction)
                                    <p>Payment Method: {{ ucfirst($order->transaction->payment_method) }}</p>
                                    <p>Transaction ID: {{ $order->transaction->transaction_id }}</p>
                                    <p>Status: {{ ucfirst($order->transaction->status) }}</p>
                                @else
                                    <p class="text-gray-500">No payment information available</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@include('layouts.footer')
@endsection

