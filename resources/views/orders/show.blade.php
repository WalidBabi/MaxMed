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
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
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
                                <p class="font-semibold text-gray-900 mt-1">AED{{ number_format($order->total_amount, 2) }}</p>
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
                                                {{ $item->quantity }} × AED{{ number_format($item->price, 2) }}
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <!-- Quantity (hidden on mobile) -->
                                    <div class="col-span-2 text-center hidden md:block">
                                        <span class="font-medium">{{ $item->quantity }}</span>
                                    </div>
                                    
                                    <!-- Unit Price (hidden on mobile) -->
                                    <div class="col-span-2 text-center hidden md:block">
                                        <span>AED{{ number_format($item->price, 2) }}</span>
                                    </div>
                                    
                                    <!-- Total Price -->
                                    <div class="col-span-2 text-right mt-2 md:mt-0">
                                        <span class="font-semibold text-gray-900">AED{{ number_format($item->price * $item->quantity, 2) }}</span>
                                    </div>
                                </div>
                            @endforeach
                            
                            <!-- Order Total -->
                            <div class="p-4 bg-gray-50">
                                <div class="flex justify-end">
                                    <div class="text-right">
                                        <p class="text-gray-600">Subtotal: <span class="font-medium">AED{{ number_format($order->total_amount, 2) }}</span></p>
                                        <p class="text-gray-600">Shipping: <span class="font-medium">AED0.00</span></p>
                                        <p class="text-gray-800 text-lg font-bold mt-2">Total: <span>AED{{ number_format($order->total_amount, 2) }}</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>   

                    <!-- Previous Feedback Section -->
                    @if($order->feedback->count() > 0)
                        <div class="mt-8">
                            <h3 class="text-xl font-semibold mb-4 text-gray-800">Previous Feedback</h3>
                            <div class="space-y-4">
                                @foreach($order->feedback as $feedback)
                                    <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <div class="flex items-center">
                                                    <span class="text-sm text-gray-500">{{ $feedback->created_at->format('M d, Y H:i') }}</span>
                                                    <span class="ml-2 px-2 py-1 text-xs rounded-full
                                                        @if($feedback->rating >= 4) bg-green-100 text-green-800
                                                        @elseif($feedback->rating >= 3) bg-blue-100 text-blue-800
                                                        @elseif($feedback->rating >= 2) bg-yellow-100 text-yellow-800
                                                        @else bg-red-100 text-red-800
                                                        @endif">
                                                        {{ $feedback->rating }}/5
                                                    </span>
                                                </div>
                                                <p class="mt-2 text-gray-700">{{ $feedback->feedback }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Feedback Section -->
                    <div class="mt-8">
                        <button onclick="openFeedbackModal('{{ $order->id }}', '{{ $order->order_number }}')" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-black bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                            </svg>
                            Provide Feedback
                        </button>
                    </div>

                    <!-- Feedback Modal -->
                    <div id="feedbackModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden" style="z-index: 50;">
                        <div class="flex items-center justify-center min-h-screen p-4">
                            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                                <div class="px-6 py-4 border-b border-gray-200">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-lg font-medium text-gray-900">Provide Feedback for Order #<span id="orderNumber"></span></h3>
                                        <button onclick="closeFeedbackModal()" class="text-gray-400 hover:text-gray-500">
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <form action="{{ route('feedback.store') }}" method="POST" class="px-6 py-4">
                                    @csrf
                                    <input type="hidden" name="order_id" id="orderId">
                                    <div class="space-y-4">
                                        <div>
                                            <label for="rating" class="block text-sm font-medium text-gray-700">Rating</label>
                                            <select name="rating" id="rating" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                <option value="5">Excellent</option>
                                                <option value="4">Very Good</option>
                                                <option value="3">Good</option>
                                                <option value="2">Fair</option>
                                                <option value="1">Poor</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label for="feedback" class="block text-sm font-medium text-gray-700">Your Feedback</label>
                                            <textarea name="feedback" id="feedback" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Please share your experience with this order..."></textarea>
                                        </div>
                                    </div>
                                    <div class="mt-6 flex justify-end space-x-3">
                                        <button type="button" onclick="closeFeedbackModal()" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            Cancel
                                        </button>
                                        <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            Submit Feedback
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <script>
                        function openFeedbackModal(orderId, orderNumber) {
                            document.getElementById('orderId').value = orderId;
                            document.getElementById('orderNumber').textContent = orderNumber;
                            document.getElementById('feedbackModal').classList.remove('hidden');
                        }

                        function closeFeedbackModal() {
                            document.getElementById('feedbackModal').classList.add('hidden');
                        }
                    </script>
                </div>
            </div>
        </div>
    </div>
{{-- Footer is included in app.blade.php --}}
@endsection

