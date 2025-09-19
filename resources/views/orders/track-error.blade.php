@extends('layouts.app')

@section('title', 'Order Not Found')

@section('content')
<div class="bg-gray-50 py-8 min-h-screen">
    <div class="max-w-md mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-6 py-8 text-center">
                <svg class="mx-auto h-16 w-16 text-red-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Order Not Found</h1>
                <p class="text-gray-600 mb-6">
                    {{ $error ?? 'The order you are looking for could not be found or you do not have permission to view it.' }}
                </p>
                
                <div class="space-y-3">
                    <a href="{{ route('products.index') }}" 
                       class="block w-full px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        Browse Products
                    </a>
                    
                    @auth
                        <a href="{{ route('orders.index') }}" 
                           class="block w-full px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            My Orders
                        </a>
                    @else
                        <a href="{{ route('login') }}" 
                           class="block w-full px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Login to View Orders
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
