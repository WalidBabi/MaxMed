@extends('layouts.app')

@section('content')
<div class="bg-gray-50 py-8 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 px-4 sm:px-0">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">My Orders</h1>
                <p class="mt-1 text-sm text-gray-600">
                    @if($orders->isEmpty())
                        You haven't placed any orders yet.
                    @else
                        Showing {{ $orders->firstItem() ?? 0 }} - {{ $orders->lastItem() ?? 0 }} of {{ $orders->total() }} orders
                    @endif
                </p>
            </div>
            <div class="mt-4 md:mt-0">
                <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Continue Shopping
                </a>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6 mx-4 sm:mx-0 rounded shadow-sm" role="alert">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if($orders->isEmpty())
            <!-- Empty State -->
            <div class="bg-white shadow sm:rounded-lg">
                <div class="py-16 px-4 sm:px-6 lg:px-8 text-center">
                    <svg class="mx-auto h-24 w-24 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <h3 class="mt-2 text-lg font-medium text-gray-900">No orders yet</h3>
                    <p class="mt-1 text-sm text-gray-500">Start shopping to see your orders here.</p>
                    <div class="mt-6">
                        <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Browse Products
                        </a>
                    </div>
                </div>
            </div>
        @else
            <!-- Orders List -->
            <div class="space-y-4">
                @foreach($orders as $order)
                    <div class="bg-white shadow overflow-hidden sm:rounded-lg hover:shadow-md transition-shadow duration-300">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">
                                        Order #{{ $order->order_number }}
                                    </h3>
                                    <div class="mt-1 flex items-center">
                                        <svg class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <p class="text-sm text-gray-500">Placed on {{ $order->created_at->format('M d, Y') }}</p>
                                    </div>
                                </div>
                                <div class="mt-3 sm:mt-0">
                                    <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium
                                        @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                                        @elseif($order->status === 'shipped') bg-purple-100 text-purple-800
                                        @elseif($order->status === 'delivered') bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="border-b border-gray-200 px-4 py-5 sm:px-6">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center">
                                    <svg class="h-6 w-6 text-gray-400 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                    </svg>
                                    <span class="text-gray-600 text-sm">Total Amount</span>
                                </div>
                                <div>
                                    <p class="text-xl font-bold text-gray-900">${{ number_format($order->total_amount, 2) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 py-4 sm:px-6 flex justify-end">
                            <a href="{{ route('orders.show', $order) }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                View Details
                                <svg class="ml-2 -mr-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8 bg-white px-4 py-4 sm:px-6 shadow sm:rounded-lg">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@include('layouts.footer')
@endsection