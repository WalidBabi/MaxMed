@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h2 class="text-2xl font-bold mb-6">My Orders</h2>

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if($orders->isEmpty())
                        <p class="text-gray-500">You haven't placed any orders yet.</p>
                    @else
                        <div class="space-y-4">
                            @foreach($orders as $order)
                                <div class="border rounded-lg p-4 hover:shadow-lg transition-shadow">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="text-lg font-semibold">Order #{{ $order->order_number }}</h3>
                                            <p class="text-gray-600">Placed on {{ $order->created_at->format('M d, Y') }}</p>
                                            <div class="mt-2">
                                                <span class="px-3 py-1 rounded-full text-sm 
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
                                        <div class="text-right">
                                            <p class="text-xl font-bold">${{ number_format($order->total_amount, 2) }}</p>
                                            <a href="{{ route('orders.show', $order) }}" 
                                               class="inline-block mt-2 text-blue-600 hover:text-blue-800 font-medium">
                                                View Details →
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $orders->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@include('layouts.footer')
@endsection