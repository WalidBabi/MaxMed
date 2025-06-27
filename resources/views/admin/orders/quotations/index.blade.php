@extends('admin.layouts.app')

@section('title', 'Quotations for Order ' . $order->order_number)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.orders.show', $order) }}" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <h1 class="text-3xl font-bold text-gray-900">Quotations</h1>
                </div>
                <p class="text-gray-600 mt-2">Order #{{ $order->order_number }}</p>
            </div>
            <div class="flex items-center space-x-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $order->quotation_status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                    {{ ucfirst($order->quotation_status) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Quotations Grid -->
    <div class="grid grid-cols-1 gap-6">
        @forelse($quotations as $quotation)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $quotation->supplier->name }}</h3>
                            <p class="text-sm text-gray-500">Submitted {{ $quotation->created_at->diffForHumans() }}</p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $quotation->status_badge_class }}">
                            {{ $quotation->formatted_status }}
                        </span>
                    </div>
                    <div class="text-2xl font-bold text-gray-900">
                        {{ $quotation->currency }} {{ number_format($quotation->total_amount, 2) }}
                        @if($quotation->shipping_cost)
                            <div class="text-sm font-normal text-gray-500 mt-1">
                                + {{ $quotation->currency }} {{ number_format($quotation->shipping_cost, 2) }} shipping
                            </div>
                        @endif
                    </div>
                </div>
                <div class="p-6">
                    @if($quotation->notes)
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Notes & Terms</h4>
                            <div class="text-sm text-gray-600 bg-gray-50 rounded-lg p-4">
                                {{ $quotation->notes }}
                            </div>
                        </div>
                    @endif

                    @if($quotation->status === 'pending')
                        <div class="flex justify-end space-x-4">
                            <form action="{{ route('admin.orders.quotations.approve', [$order, $quotation]) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    Approve
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No quotations yet</h3>
                <p class="mt-1 text-sm text-gray-500">Waiting for suppliers to submit their quotations.</p>
            </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
    // No rejection functionality 
</script>
@endpush
@endsection 