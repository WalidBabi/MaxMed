@extends('layouts.app')

@section('content')
    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-8">
                    <!-- Header Section -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 border-b pb-6">
                        <h2 class="text-3xl font-bold text-gray-800">Order #{{ $order->order_number }}</h2>
                        <a href="{{ route('orders.index') }}" class="mt-3 md:mt-0 flex items-center text-blue-600 hover:text-blue-800 transition-colors duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back to Orders
                        </a>
                    </div>

                    @if(session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded-md mb-6" role="alert">
                            <div class="flex">
                                <div class="py-1">
                                    <svg class="h-6 w-6 text-green-500 mr-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <span class="text-sm">{{ session('success') }}</span>
                            </div>
                        </div>
                    @endif

                    <!-- Order Summary -->
                    <div class="bg-gray-50 rounded-lg p-6 mb-8 shadow-sm">
                        <h3 class="text-xl font-semibold mb-4 text-gray-800">Order Summary</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <p class="text-gray-500 text-sm uppercase tracking-wide">Date Placed</p>
                                <p class="font-semibold text-gray-900 mt-1">{{ $order->created_at->format('M d, Y') }}</p>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <p class="text-gray-500 text-sm uppercase tracking-wide">Status</p>
                                <span class="px-3 py-1 rounded-full text-sm inline-block mt-1 font-medium
                                    @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                                    @elseif($order->status === 'shipped') bg-purple-100 text-purple-800
                                    @elseif($order->status === 'delivered') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <p class="text-gray-500 text-sm uppercase tracking-wide">Total Amount</p>
                                <p class="font-semibold text-gray-900 mt-1">${{ number_format($order->total_amount, 2) }}</p>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                <p class="text-gray-500 text-sm uppercase tracking-wide">Payment Status</p>
                                <p class="font-semibold text-gray-900 mt-1">
                                    @if($order->transaction && $order->transaction->status === 'completed')
                                        <span class="text-green-600">Paid</span>
                                    @else
                                        <span class="text-red-600">Pending</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="mb-8">
                        <h3 class="text-xl font-semibold mb-4 text-gray-800">Order Items</h3>
                        <div class="bg-white rounded-lg overflow-hidden border border-gray-200">
                            <!-- Table Header (visible on medium screens and up) -->
                            <div class="hidden md:grid md:grid-cols-12 bg-gray-50 p-4 text-gray-600 text-sm uppercase tracking-wider font-medium">
                                <div class="col-span-6">Product</div>
                                <div class="col-span-2 text-center">Quantity</div>
                                <div class="col-span-2 text-center">Price</div>
                                <div class="col-span-2 text-right">Total</div>
                            </div>
                            
                            <!-- Order Items -->
                            @foreach($order->items as $item)
                                <div class="grid grid-cols-1 md:grid-cols-12 p-4 border-b border-gray-200 items-center hover:bg-gray-50 transition-colors duration-150">
                                    <!-- Product Info -->
                                    <div class="col-span-6 flex items-center">
                                        @if($item->product->image_url)
                                            <img src="{{ asset($item->product->image_url) }}" 
                                                 alt="{{ $item->product->name }}" 
                                                 class="w-16 h-16 object-cover rounded-md">
                                        @else
                                            <div class="w-16 h-16 bg-gray-200 rounded-md flex items-center justify-center">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                        <div class="ml-4">
                                            <h4 class="font-semibold text-gray-900">{{ $item->product->name }}</h4>
                                            <p class="text-gray-500 text-sm md:hidden mt-1">
                                                {{ $item->quantity }} × ${{ number_format($item->price, 2) }}
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <!-- Quantity (hidden on mobile) -->
                                    <div class="col-span-2 text-center hidden md:block">
                                        <span class="font-medium">{{ $item->quantity }}</span>
                                    </div>
                                    
                                    <!-- Unit Price (hidden on mobile) -->
                                    <div class="col-span-2 text-center hidden md:block">
                                        <span>${{ number_format($item->price, 2) }}</span>
                                    </div>
                                    
                                    <!-- Total Price -->
                                    <div class="col-span-2 text-right mt-2 md:mt-0">
                                        <span class="font-semibold text-gray-900">${{ number_format($item->price * $item->quantity, 2) }}</span>
                                    </div>
                                </div>
                            @endforeach
                            
                            <!-- Order Total -->
                            <div class="p-4 bg-gray-50">
                                <div class="flex justify-end">
                                    <div class="text-right">
                                        <p class="text-gray-600">Subtotal: <span class="font-medium">${{ number_format($order->total_amount, 2) }}</span></p>
                                        <p class="text-gray-600">Shipping: <span class="font-medium">$0.00</span></p>
                                        <p class="text-gray-800 text-lg font-bold mt-2">Total: <span>${{ number_format($order->total_amount, 2) }}</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Information -->
                    <div class="grid md:grid-cols-2 gap-8">
                      
                        <!-- Payment Information -->
                        <div>
                            <h3 class="text-xl font-semibold mb-4 text-gray-800">Payment Information</h3>
                            <div class="bg-white rounded-lg border border-gray-200 p-5">
                                @if($order->transaction)
                                    <div class="flex items-center mb-3">
                                        <svg class="w-5 h-5 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                        </svg>
                                        <p><span class="text-gray-600">Payment Method:</span> <span class="font-medium">{{ ucfirst($order->transaction->payment_method) }}</span></p>
                                    </div>
                                    
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <p><span class="text-gray-600">Status:</span> 
                                            <span class="font-medium 
                                                @if($order->transaction->status === 'completed') text-green-600 
                                                @elseif($order->transaction->status === 'pending') text-yellow-600 
                                                @else text-red-600 
                                                @endif">
                                                {{ ucfirst($order->transaction->status) }}
                                            </span>
                                        </p>
                                    </div>
                                @else
                                    <div class="flex items-center justify-center h-24 text-gray-500">
                                        <p>No payment information available</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Actions Button -->
                    <div class="mt-8 flex justify-center md:justify-end space-x-4">
                        <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-gray-700 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                            Print Order
                        </button>
                        <a href="{{ route('orders.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200">
                            Back to Orders
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
{{-- Footer is included in app.blade.php --}}
@endsection

