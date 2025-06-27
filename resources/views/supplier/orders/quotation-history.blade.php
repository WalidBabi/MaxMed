@extends('supplier.layouts.app')

@section('title', 'Quotation History - Order ' . $order->order_number)

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('supplier.orders.show', $order) }}" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <h1 class="text-3xl font-bold text-gray-900">Quotation History</h1>
                </div>
                <p class="text-gray-600 mt-2">Order #{{ $order->order_number }}</p>
            </div>
        </div>
    </div>

    <!-- Quotations List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                Your Quotations
            </h3>
        </div>
        <div class="divide-y divide-gray-200">
            @forelse($quotations as $quotation)
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $quotation->status_badge_class }}">
                                {{ $quotation->formatted_status }}
                            </span>
                            <span class="text-sm text-gray-500">
                                Submitted {{ $quotation->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <div class="text-xl font-bold text-gray-900">
                            {{ $quotation->currency }} {{ number_format($quotation->total_amount, 2) }}
                            @if($quotation->shipping_cost)
                                <div class="text-sm font-normal text-gray-500 mt-1">
                                    + {{ $quotation->currency }} {{ number_format($quotation->shipping_cost, 2) }} shipping
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($quotation->notes)
                        <div class="mt-4">
                            <h4 class="text-sm font-medium text-gray-700">Notes & Terms</h4>
                            <p class="mt-1 text-sm text-gray-600 bg-gray-50 rounded-lg p-3">
                                {{ $quotation->notes }}
                            </p>
                        </div>
                    @endif

                    @if($quotation->status === 'rejected' && $quotation->rejection_reason)
                        <div class="mt-4 bg-red-50 border border-red-200 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-red-800">Rejection Reason:</h4>
                            <p class="mt-1 text-sm text-red-700">{{ $quotation->rejection_reason }}</p>
                        </div>
                    @endif

                    @if($quotation->status === 'approved')
                        <div class="mt-4 bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex">
                                <svg class="h-5 w-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <h4 class="text-sm font-medium text-green-800">Quotation Approved</h4>
                                    <p class="mt-1 text-sm text-green-700">
                                        Your quotation was approved on {{ $quotation->approved_at->format('M d, Y H:i') }}.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="mt-4 text-sm text-gray-500">
                        <div class="flex items-center space-x-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Status last updated: {{ $quotation->updated_at->format('M d, Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center">
                    <p class="text-gray-500">No quotations found for this order.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection 