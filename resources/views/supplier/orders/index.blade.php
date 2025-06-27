@extends('supplier.layouts.app')

@section('title', 'Order Management')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header with View Toggle -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Order Management Pipeline</h1>
            <p class="text-gray-600">Process and manage customer orders through fulfillment stages</p>
        </div>
        <div class="flex items-center space-x-4">
            <!-- View Toggle -->
            <div class="bg-gray-100 p-1 rounded-lg">
                <a href="{{ route('supplier.orders.index', array_merge(request()->query(), ['view' => 'pipeline'])) }}" 
                   class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md {{ (!isset($viewType) || $viewType === 'pipeline') ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 17h6m0 0v-6m0 6h2a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 7h6"></path>
                    </svg>
                    Pipeline
                </a>
                <a href="{{ route('supplier.orders.index', array_merge(request()->query(), ['view' => 'table'])) }}"
                   class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md {{ (isset($viewType) && $viewType === 'table') ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0V4a2 2 0 012-2h14a2 2 0 012 2v16a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                    </svg>
                    Table
                </a>
            </div>
            <div class="flex items-center space-x-3">
                <span class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.75 2a.75.75 0 01.75.75V4h7V2.75a.75.75 0 011.5 0V4h.25A2.25 2.25 0 0117.5 6.25v8.5A2.25 2.25 0 0115.25 17H4.75A2.25 2.25 0 012.5 14.75v-8.5A2.25 2.25 0 014.75 4H5V2.75A.75.75 0 015.75 2zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75z" clip-rule="evenodd" />
                    </svg>
                    {{ nowDubai('l, F j, Y') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <input type="hidden" name="view" value="{{ $viewType ?? 'pipeline' }}">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by order number, product..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Statuses</option>
                        <option value="awaiting_quotations" {{ request('status') == 'awaiting_quotations' ? 'selected' : '' }}>Awaiting Quotations</option>
                        <option value="quotations_received" {{ request('status') == 'quotations_received' ? 'selected' : '' }}>Quotations Received</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                    <select name="date_range" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Time</option>
                        <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Today</option>
                        <option value="week" {{ request('date_range') == 'week' ? 'selected' : '' }}>This Week</option>
                        <option value="month" {{ request('date_range') == 'month' ? 'selected' : '' }}>This Month</option>
                        <option value="quarter" {{ request('date_range') == 'quarter' ? 'selected' : '' }}>This Quarter</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount Range</label>
                    <select name="amount_range" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Amounts</option>
                        <option value="0-1000" {{ request('amount_range') == '0-1000' ? 'selected' : '' }}>Below AED 1,000</option>
                        <option value="1000-5000" {{ request('amount_range') == '1000-5000' ? 'selected' : '' }}>AED 1,000 - 5,000</option>
                        <option value="5000-10000" {{ request('amount_range') == '5000-10000' ? 'selected' : '' }}>AED 5,000 - 10,000</option>
                        <option value="10000+" {{ request('amount_range') == '10000+' ? 'selected' : '' }}>Above AED 10,000</option>
                    </select>
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:outline-none focus:border-purple-900 focus:ring ring-purple-300 disabled:opacity-25 transition ease-in-out duration-150">
                        Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if(!isset($viewType) || $viewType === 'pipeline')
        @include('supplier.orders.partials.pipeline-view')
    @else
        @include('supplier.orders.partials.table-view')
    @endif
</div>
@endsection

@push('styles')
<style>
/* Pipeline specific styles */
.order-card {
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
    background: white;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    user-select: none;
    -webkit-user-select: none;
}

.order-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.12);
}

.drop-zone {
    transition: background-color 0.2s ease;
    border-radius: 0.75rem;
    background: rgba(255, 255, 255, 0.5);
    backdrop-filter: blur(8px);
    min-height: 400px;
}

.drop-zone.bg-opacity-50 {
    background-color: rgba(59, 130, 246, 0.1) !important;
    border: 2px dashed #3b82f6;
}

/* Pipeline auto-scroll container */
.pipeline-scroll-container {
    scroll-behavior: smooth;
    position: relative;
}

.pipeline-scroll-container::-webkit-scrollbar {
    height: 8px;
}

.pipeline-scroll-container::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}

.pipeline-scroll-container::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.pipeline-scroll-container::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
@endpush

@push('scripts')
<script>
// Order management specific JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Add any order-specific functionality here
    console.log('Order Management Pipeline loaded');
});
</script>
@endpush 