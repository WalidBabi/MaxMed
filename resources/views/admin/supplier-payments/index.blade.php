@extends('admin.layouts.app')

@section('title', 'Supplier Payments')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Supplier Payments</h1>
                <p class="text-gray-600 mt-2">Track all payments made to suppliers</p>
            </div>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="border-b border-gray-200">
            <nav class="flex space-x-8 px-6" aria-label="Tabs">
                <a href="{{ route('admin.supplier-payments.index', ['status' => 'all']) }}" 
                   class="@if($status === 'all') border-indigo-500 text-indigo-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                    All Payments
                    <span class="@if($status === 'all') bg-indigo-100 text-indigo-600 @else bg-gray-100 text-gray-900 @endif ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $statusCounts['all'] }}</span>
                </a>
                
                <a href="{{ route('admin.supplier-payments.index', ['status' => 'pending']) }}" 
                   class="@if($status === 'pending') border-yellow-500 text-yellow-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                    Pending
                    <span class="@if($status === 'pending') bg-yellow-100 text-yellow-600 @else bg-gray-100 text-gray-900 @endif ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $statusCounts['pending'] }}</span>
                </a>
                
                <a href="{{ route('admin.supplier-payments.index', ['status' => 'processing']) }}" 
                   class="@if($status === 'processing') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                    Processing
                    <span class="@if($status === 'processing') bg-blue-100 text-blue-600 @else bg-gray-100 text-gray-900 @endif ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $statusCounts['processing'] }}</span>
                </a>
                
                <a href="{{ route('admin.supplier-payments.index', ['status' => 'completed']) }}" 
                   class="@if($status === 'completed') border-green-500 text-green-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                    Completed
                    <span class="@if($status === 'completed') bg-green-100 text-green-600 @else bg-gray-100 text-gray-900 @endif ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $statusCounts['completed'] }}</span>
                </a>
                
                <a href="{{ route('admin.supplier-payments.index', ['status' => 'failed']) }}" 
                   class="@if($status === 'failed') border-red-500 text-red-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                    Failed
                    <span class="@if($status === 'failed') bg-red-100 text-red-600 @else bg-gray-100 text-gray-900 @endif ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ $statusCounts['failed'] }}</span>
                </a>
            </nav>
        </div>
    </div>

    <!-- Payments List -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        @if($payments->count() > 0)
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">
                    @if($status === 'all')
                        All Supplier Payments
                    @else
                        {{ ucfirst($status) }} Supplier Payments
                    @endif
                    <span class="text-gray-500 font-normal">({{ $payments->total() }} total)</span>
                </h3>
            </div>
            
            <div class="overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment #</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purchase Order</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Method</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Date</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($payments as $payment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $payment->payment_number }}</div>
                                            @if($payment->reference_number)
                                                <div class="text-sm text-gray-500">Ref: {{ $payment->reference_number }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($payment->purchaseOrder)
                                        <div class="text-sm font-medium text-gray-900">
                                            <a href="{{ route('admin.purchase-orders.show', $payment->purchaseOrder) }}" class="text-indigo-600 hover:text-indigo-700">
                                                {{ $payment->purchaseOrder->po_number }}
                                            </a>
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $payment->purchaseOrder->supplier_name }}</div>
                                    @else
                                        <div class="text-sm font-medium text-gray-900">N/A</div>
                                    @endif
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $payment->currency }} {{ $payment->formatted_amount }}</div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</div>
                                    @if($payment->bank_name)
                                        <div class="text-sm text-gray-500">{{ $payment->bank_name }}</div>
                                    @endif
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $payment->status_badge_class }}">
                                        {{ \App\Models\SupplierPayment::$statuses[$payment->status] ?? ucfirst($payment->status) }}
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $payment->payment_date->format('M d, Y') }}
                                    <br>
                                    <span class="text-xs">{{ $payment->created_at->format('H:i') }}</span>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('admin.supplier-payments.show', $payment) }}" 
                                           class="text-indigo-600 hover:text-indigo-900 inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            View
                                        </a>
                                        
                                        @if($payment->status === 'pending' || $payment->status === 'processing')
                                            <div class="flex items-center space-x-1">
                                                <form action="{{ route('admin.supplier-payments.mark-completed', $payment) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="text-green-600 hover:text-green-900 inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-green-700 bg-green-100 hover:bg-green-200"
                                                            onclick="return confirm('Mark this payment as completed?')">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                        Complete
                                                    </button>
                                                </form>
                                                
                                                <form action="{{ route('admin.supplier-payments.mark-failed', $payment) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="text-red-600 hover:text-red-900 inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200"
                                                            onclick="return confirm('Mark this payment as failed?')">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                        Failed
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($payments->hasPages())
                <div class="px-6 py-3 border-t border-gray-200">
                    {{ $payments->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No supplier payments found</h3>
                <p class="mt-1 text-sm text-gray-500">
                    @if($status === 'all')
                        No payments have been recorded yet.
                    @else
                        No {{ $status }} payments found.
                    @endif
                </p>
            </div>
        @endif
    </div>
</div>
@endsection 