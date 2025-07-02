@extends('admin.layouts.app')

@section('title', 'Cash Receipt Details')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Cash Receipt {{ $cashReceipt->receipt_number }}</h1>
                <p class="text-gray-600 mt-2">
                    Receipt Details - {{ ucfirst($cashReceipt->status) }}
                </p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.cash-receipts.pdf', $cashReceipt) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Download PDF
                </a>
                @if($cashReceipt->status !== 'cancelled')
                    <form method="POST" action="{{ route('admin.cash-receipts.cancel', $cashReceipt) }}" class="inline" onsubmit="return confirm('Are you sure you want to cancel this receipt?')">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Cancel Receipt
                        </button>
                    </form>
                @endif
                <a href="{{ route('admin.cash-receipts.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to List
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Receipt Header -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h2 class="text-2xl font-bold text-indigo-600 mb-3">{{ $cashReceipt->receipt_number }}</h2>
                            <div class="mb-2">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    @if($cashReceipt->status === 'issued') bg-green-100 text-green-800
                                    @elseif($cashReceipt->status === 'draft') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst($cashReceipt->status) }}
                                </span>
                            </div>
                        </div>
                        <div class="md:text-right">
                            <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $cashReceipt->customer_name }}</h3>
                            <div class="text-sm text-gray-600">
                                {{ $cashReceipt->customer_address }}
                            </div>
                            @if($cashReceipt->customer_email)
                                <div class="text-sm text-gray-600 mt-1">
                                    {{ $cashReceipt->customer_email }}
                                </div>
                            @endif
                            @if($cashReceipt->customer_phone)
                                <div class="text-sm text-gray-600">
                                    {{ $cashReceipt->customer_phone }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Receipt Details -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-900">Receipt Details</h3>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Receipt Date</label>
                            <p class="text-sm font-semibold text-gray-900">{{ formatDubaiDate($cashReceipt->receipt_date, 'M d, Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Payment Method</label>
                            <p class="text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($cashReceipt->payment_method === 'cash') bg-green-100 text-green-800
                                    @elseif($cashReceipt->payment_method === 'check') bg-blue-100 text-blue-800
                                    @elseif($cashReceipt->payment_method === 'credit_card') bg-purple-100 text-purple-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst(str_replace('_', ' ', $cashReceipt->payment_method)) }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Amount</label>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($cashReceipt->amount, 2) }} {{ $cashReceipt->currency }}</p>
                        </div>
                        @if($cashReceipt->reference_number)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Reference Number</label>
                            <p class="text-sm text-gray-900">{{ $cashReceipt->reference_number }}</p>
                        </div>
                        @endif
                        @if($cashReceipt->description)
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500 mb-1">Description</label>
                            <p class="text-sm text-gray-900">{{ $cashReceipt->description }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Related Order (if exists) -->
            @if($cashReceipt->order)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-900">Related Order</h3>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Order Number</label>
                            <p class="text-sm text-gray-900">
                                <a href="{{ route('admin.orders.show', $cashReceipt->order) }}" class="text-indigo-600 hover:text-indigo-900">
                                    {{ $cashReceipt->order->order_number }}
                                </a>
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Order Date</label>
                            <p class="text-sm text-gray-900">{{ formatDubaiDate($cashReceipt->order->created_at, 'M d, Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Order Total</label>
                            <p class="text-sm font-semibold text-gray-900">{{ number_format($cashReceipt->order->total_amount, 2) }} {{ $cashReceipt->order->currency ?? 'AED' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Order Status</label>
                            <p class="text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ ucfirst($cashReceipt->order->status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-8">
            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('admin.cash-receipts.pdf', $cashReceipt) }}" target="_blank" class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download PDF
                    </a>
                    @if($cashReceipt->order)
                        <a href="{{ route('admin.orders.show', $cashReceipt->order) }}" class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            View Order
                        </a>
                    @endif
                    @if($cashReceipt->customer)
                        <a href="{{ route('crm.customers.show', $cashReceipt->customer) }}" class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            View Customer
                        </a>
                    @endif
                </div>
            </div>

            <!-- Receipt Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Receipt Information</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Created By</label>
                        <p class="text-sm text-gray-900">{{ $cashReceipt->user->name ?? 'System' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Created Date</label>
                        <p class="text-sm text-gray-900">{{ formatDubaiDate($cashReceipt->created_at, 'M d, Y H:i') }}</p>
                    </div>
                    @if($cashReceipt->updated_at != $cashReceipt->created_at)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Last Updated</label>
                        <p class="text-sm text-gray-900">{{ formatDubaiDate($cashReceipt->updated_at, 'M d, Y H:i') }}</p>
                    </div>
                    @endif
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($cashReceipt->status === 'issued') bg-green-100 text-green-800
                            @elseif($cashReceipt->status === 'draft') bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ ucfirst($cashReceipt->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Payment Summary -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Payment Summary</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Payment Method:</span>
                            <span class="text-sm font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $cashReceipt->payment_method)) }}</span>
                        </div>
                        @if($cashReceipt->reference_number)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Reference:</span>
                            <span class="text-sm font-medium text-gray-900">{{ $cashReceipt->reference_number }}</span>
                        </div>
                        @endif
                        <div class="border-t pt-3">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-semibold text-gray-900">Total Amount:</span>
                                <span class="text-xl font-bold text-indigo-600">{{ number_format($cashReceipt->amount, 2) }} {{ $cashReceipt->currency }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 