@extends('admin.layouts.app')

@section('title', 'Edit Purchase Order')

@section('content')
<div class="-mx-4 sm:-mx-6 lg:-mx-8">
    <div class="mb-8 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit Purchase Order</h1>
                <p class="text-gray-600 mt-2">Update the purchase order details and items</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.purchase-orders.index') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                    </svg>
                    Back to Purchase Orders
                </a>
            </div>
        </div>
    </div>
    <!-- Form will go here -->
</div>
@endsection 