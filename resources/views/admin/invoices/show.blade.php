@extends('admin.layouts.app')

@section('title', 'Invoice Details')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Invoice {{ $invoice->invoice_number }}</h1>
                <p class="text-gray-600 mt-2">
                    {{ ucfirst($invoice->type) }} Invoice - {{ $invoice->status_options[$invoice->status] ?? ucfirst($invoice->status) }}
                </p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.invoices.edit', $invoice) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit
                </a>
                <a href="{{ route('admin.invoices.pdf', $invoice) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Download PDF
                </a>
            
                <a href="{{ route('admin.invoices.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
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
            <!-- Invoice Header -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h2 class="text-2xl font-bold text-indigo-600 mb-3">{{ $invoice->invoice_number }}</h2>
                            <div class="mb-2">
                                @if($invoice->type === 'proforma')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        Proforma Invoice
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        Final Invoice
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="md:text-right">
                            <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $invoice->customer_name }}</h3>
                            <div class="text-sm text-gray-600">
                                {{ $invoice->billing_address }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoice Details -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-900">Invoice Details</h3>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Invoice Date</label>
                            <p class="text-sm font-semibold text-gray-900">{{ formatDubaiDate($invoice->invoice_date, 'M d, Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Due Date</label>
                            <p class="text-sm font-semibold text-gray-900">{{ formatDubaiDate($invoice->due_date, 'M d, Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Payment Terms</label>
                            <p class="text-sm text-gray-900">{{ $invoice::PAYMENT_TERMS[$invoice->payment_terms] ?? ucfirst($invoice->payment_terms) }}</p>
                        </div>
                        @if($invoice->reference_number)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Reference Number</label>
                            <p class="text-sm text-gray-900">{{ $invoice->reference_number }}</p>
                        </div>
                        @endif
                        @if($invoice->po_number)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">PO Number</label>
                            <p class="text-sm text-gray-900">{{ $invoice->po_number }}</p>
                        </div>
                        @endif
                        @if($invoice->quote)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Related Quote</label>
                            <p class="text-sm text-gray-900">
                                <a href="{{ route('admin.quotes.show', $invoice->quote) }}" class="text-indigo-600 hover:text-indigo-900">
                                    {{ $invoice->quote->quote_number }}
                                </a>
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Invoice Items -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V9a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-900">Invoice Items</h3>
                    </div>
                </div>
                <div class="p-6">
                    @if($invoice->items->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item Description</th>
                                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Discount</th>
                                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Specs</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Line Total (After Discount)</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($invoice->items as $index => $item)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                {{ $item->description }}
                                                @if($item->product && $item->product->brand)
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        <span class="font-medium">Brand:</span> {{ $item->product->brand->name }}
                                                    </div>
                                                @endif
                                           
                                            </td>
                                            <td class="px-6 py-4 text-center text-sm text-gray-900">
                                                {{ number_format($item->quantity, 2) }}
                                                @if($item->unit_of_measure)
                                                    {{ $item->unit_of_measure }}
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-right text-sm text-gray-900">{{ $item->formatted_unit_price }} {{ $invoice->currency }}</td>
                                            <td class="px-6 py-4 text-center text-sm text-gray-900">
                                                @if($item->discount_percentage > 0)
                                                    {{ number_format($item->discount_percentage, 2) }}%
                                                    @if($item->calculated_discount_amount > 0)
                                                        <br><span class="text-xs text-gray-500">(-{{ number_format($item->calculated_discount_amount, 2) }} {{ $invoice->currency }})</span>
                                                    @endif
                                                @elseif($item->calculated_discount_amount > 0)
                                                    -{{ number_format($item->calculated_discount_amount, 2) }} {{ $invoice->currency }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-center text-sm text-gray-900">
                                                @if($item->product && $item->product->specifications->count() > 0)
                                                    <button 
                                                        onclick="toggleSpecs({{ $index }})" 
                                                        class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200"
                                                    >
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        {{ $item->product->specifications->count() }} specs
                                                    </button>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-right text-sm font-medium text-gray-900">{{ $item->formatted_line_total }} {{ $invoice->currency }}</td>
                                        </tr>
                                        @if($item->product && $item->product->specifications->count() > 0)
                                            <tr id="specs-row-{{ $index }}" class="hidden">
                                                <td colspan="6" class="px-6 py-4 bg-gray-50">
                                                    <div class="specs-content">
                                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                                            @foreach($item->product->specifications->groupBy('category') as $category => $specs)
                                                                <div class="spec-category">
                                                                    @if($category)
                                                                        <h4 class="text-xs font-semibold text-gray-700 uppercase tracking-wide mb-2">{{ $category }}</h4>
                                                                    @endif
                                                                    <div class="space-y-1">
                                                                        @foreach($specs as $spec)
                                                                            <div class="flex justify-between text-xs">
                                                                                <span class="text-gray-600 font-medium">{{ $spec->display_name ?: $spec->specification_key }}:</span>
                                                                                <span class="text-gray-900 font-semibold">{{ $spec->formatted_value }}</span>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-6 flex justify-end">
                            <div class="w-full max-w-sm">
                                @php
                                    $subtotal = $invoice->items->sum(function($item) {
                                        return $item->quantity * $item->unit_price;
                                    });
                                    $totalDiscount = $invoice->items->sum('calculated_discount_amount') + ($invoice->discount_amount ?? 0);
                                    $taxAmount = $invoice->tax_amount ?? 0;
                                    $finalTotal = $subtotal - $totalDiscount + $taxAmount;
                                @endphp
                                
                                <div class="flex justify-between py-2 text-sm">
                                    <span class="font-medium text-gray-900">Sub Total:</span>
                                    <span class="font-bold text-gray-900">{{ number_format($subtotal, 2) }} {{ $invoice->currency }}</span>
                                </div>
                                
                                @if($totalDiscount > 0)
                                <div class="flex justify-between py-2 text-sm">
                                    <span class="font-medium text-gray-900">Total Discount:</span>
                                    <span class="font-bold text-red-600">-{{ number_format($totalDiscount, 2) }} {{ $invoice->currency }}</span>
                                </div>
                                @endif
                                
                                @if($taxAmount > 0)
                                <div class="flex justify-between py-2 text-sm">
                                    <span class="font-medium text-gray-900">Tax:</span>
                                    <span class="font-bold text-gray-900">{{ number_format($taxAmount, 2) }} {{ $invoice->currency }}</span>
                                </div>
                                @endif
                                
                                <div class="flex justify-between py-3 text-lg border-t border-gray-200">
                                    <span class="font-semibold text-gray-900">Total:</span>
                                    <span class="font-bold text-indigo-600">{{ number_format($finalTotal, 2) }} {{ $invoice->currency }}</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="w-24 h-24 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V9a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No Items Added</h3>
                            <p class="text-gray-500">This invoice doesn't have any items yet.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Payment History -->
            @if($invoice->payments->count() > 0)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-900">Payment History</h3>
                    </div>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment #</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($invoice->payments as $payment)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $payment->payment_number }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ formatDubaiDate($payment->payment_date, 'M d, Y') }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ ucfirst($payment->payment_method) }}</td>
                                        <td class="px-6 py-4 text-right text-sm font-medium text-gray-900">{{ number_format($payment->amount, 2) }} {{ $invoice->currency }}</td>
                                        <td class="px-6 py-4 text-sm">
                                            @if($payment->status === 'completed')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Completed
                                                </span>
                                            @elseif($payment->status === 'pending')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    Pending
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    {{ ucfirst($payment->status) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ $payment->transaction_reference ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Notes -->
            @if($invoice->notes)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-900">Notes</h3>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-sm text-gray-600">{{ $invoice->notes }}</p>
                </div>
            </div>
            @endif

            <!-- Terms & Conditions -->
            @if($invoice->terms_conditions)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-900">Terms & Conditions</h3>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-sm text-gray-600">{{ $invoice->terms_conditions }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Invoice Status -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Invoice Status</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Status:</span>
                        <span class="text-sm font-medium">
                            @if($invoice->status === 'draft')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    Draft
                                </span>
                            @elseif($invoice->status === 'sent')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Sent
                                </span>
                            @elseif($invoice->status === 'approved')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Approved
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Payment Status:</span>
                        <span class="text-sm font-medium">
                            @if($invoice->payment_status === 'pending')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Pending
                                </span>
                            @elseif($invoice->payment_status === 'paid')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Paid
                                </span>
                            @elseif($invoice->payment_status === 'partial')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Partial
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    {{ ucfirst($invoice->payment_status) }}
                                </span>
                            @endif
                        </span>
                    </div>
                    @if($invoice->paid_amount > 0)
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Paid Amount:</span>
                        <span class="text-sm font-medium text-green-600">{{ number_format($invoice->paid_amount, 2) }} {{ $invoice->currency }}</span>
                    </div>
                    @endif
                    @if($invoice->payment_status !== 'paid' && $invoice->paid_amount > 0)
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Balance Due:</span>
                        <span class="text-sm font-medium text-red-600">{{ number_format($invoice->total_amount - $invoice->paid_amount, 2) }} {{ $invoice->currency }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                </div>
                <div class="p-6 space-y-3">
                    <!-- Status Update for Proforma Invoices -->
                    @if($invoice->type === 'proforma' && !$invoice->canConvertToFinalInvoice() && $invoice->status !== 'cancelled')
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-yellow-400 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                </svg>
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-yellow-800 mb-2">Update Status to Enable Conversion</h4>
                                    <p class="text-sm text-yellow-700 mb-3">Current status: <strong>{{ ucfirst($invoice->status) }}</strong>. Update to enable final invoice conversion.</p>
                                    <form action="{{ route('admin.invoices.status.update', $invoice) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('PUT')
                                        <div class="flex items-center space-x-2">
                                            <select name="status" class="text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                <option value="sent" {{ $invoice->status === 'sent' ? 'selected' : '' }}>Sent</option>
                                                <option value="confirmed" {{ $invoice->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                                <option value="in_production" {{ $invoice->status === 'in_production' ? 'selected' : '' }}>In Production</option>
                                                <option value="ready_to_ship" {{ $invoice->status === 'ready_to_ship' ? 'selected' : '' }}>Ready to Ship</option>
                                                <option value="shipped" {{ $invoice->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                                                <option value="delivered" {{ $invoice->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                            </select>
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-yellow-600 border border-transparent rounded-md text-xs font-medium text-white hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                                Update
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($invoice->type === 'proforma' && $invoice->canConvertToFinalInvoice())
                        <form action="{{ route('admin.invoices.convert-to-final', $invoice) }}" method="POST" class="inline" onsubmit="return confirm('Convert this proforma invoice to final invoice?');">
                            @csrf
                            <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"></path>
                                </svg>
                                Convert to Final Invoice
                            </button>
                        </form>
                    @endif
                    
                    @if($invoice->payment_status !== 'paid')
                    <button onclick="openPaymentModal()" class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        Record Payment
                    </button>
                    @endif
                </div>
            </div>

            <!-- Payment Modal -->
            <div id="paymentModal" class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50 hidden">
                <div class="fixed inset-0 transform transition-all" onclick="closePaymentModal()">
                    <div class="absolute inset-0 bg-gray-900 bg-opacity-75 backdrop-blur-sm"></div>
                </div>
                
                <div class="relative sm:w-full sm:max-w-lg sm:mx-auto">
                    <div class="bg-white rounded-lg shadow-xl transform transition-all sm:w-full">
                        <!-- Modal Header -->
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 rounded-t-lg">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                    <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                    Record Payment
                                </h3>
                                <button onclick="closePaymentModal()" class="text-gray-400 hover:text-gray-600 focus:outline-none focus:text-gray-600 transition ease-in-out duration-150">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Modal Body -->
                        <form action="{{ route('admin.invoices.record-payment', $invoice) }}" method="POST">
                            @csrf
                            <div class="px-6 py-4 space-y-6">
                                <!-- Payment Amount -->
                                <div>
                                    @php
                                        $subtotal = $invoice->items->sum(function($item) {
                                            return $item->quantity * $item->unit_price;
                                        });
                                        $totalDiscount = $invoice->items->sum('calculated_discount_amount') + ($invoice->discount_amount ?? 0);
                                        $taxAmount = $invoice->tax_amount ?? 0;
                                        $finalTotal = $subtotal - $totalDiscount + $taxAmount;
                                        $remainingAmount = $finalTotal - $invoice->paid_amount;
                                    @endphp
                                    <label for="payment_amount" class="block text-sm font-medium text-gray-700 mb-2">Payment Amount <span class="text-red-500">*</span></label>
                                    <input type="number" 
                                           id="payment_amount" 
                                           name="amount" 
                                           step="0.01" 
                                           min="0.01" 
                                           max="{{ $remainingAmount }}"
                                           value="{{ old('amount', $remainingAmount) }}"
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('amount') border-red-300 @enderror" 
                                           required>
                                    @error('amount')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">
                                        Remaining balance: {{ number_format($remainingAmount, 2) }} {{ $invoice->currency }}
                                    </p>
                                </div>

                                <!-- Payment Method -->
                                <div>
                                    <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">Payment Method <span class="text-red-500">*</span></label>
                                    <select name="payment_method" id="payment_method" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('payment_method') border-red-300 @enderror">
                                        <option value="">Select Payment Method</option>
                                        @foreach(\App\Models\Payment::PAYMENT_METHODS as $key => $label)
                                            <option value="{{ $key }}" {{ old('payment_method') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('payment_method')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Payment Date -->
                                <div>
                                    <label for="payment_date" class="block text-sm font-medium text-gray-700 mb-2">Payment Date <span class="text-red-500">*</span></label>
                                    <input type="date" 
                                           id="payment_date" 
                                           name="payment_date" 
                                           value="{{ old('payment_date', date('Y-m-d')) }}"
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('payment_date') border-red-300 @enderror" 
                                           required>
                                    @error('payment_date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Transaction Reference -->
                                <div>
                                    <label for="transaction_reference" class="block text-sm font-medium text-gray-700 mb-2">Transaction Reference</label>
                                    <input type="text" 
                                           id="transaction_reference" 
                                           name="transaction_reference" 
                                           value="{{ old('transaction_reference') }}"
                                           placeholder="e.g., Check #1234, Transfer ID, etc."
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('transaction_reference') border-red-300 @enderror">
                                    @error('transaction_reference')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">Optional: Reference number for tracking</p>
                                </div>

                                <!-- Payment Notes -->
                                <div>
                                    <label for="payment_notes" class="block text-sm font-medium text-gray-700 mb-2">Payment Notes</label>
                                    <textarea name="payment_notes" 
                                              id="payment_notes" 
                                              rows="3" 
                                              placeholder="Additional notes about this payment..."
                                              class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('payment_notes') border-red-300 @enderror">{{ old('payment_notes') }}</textarea>
                                    @error('payment_notes')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Modal Footer -->
                            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg sm:flex sm:flex-row-reverse">
                                <button type="submit" 
                                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                                    Record Payment
                                </button>
                                <button type="button" 
                                        onclick="closePaymentModal()" 
                                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Activity Timeline -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Activity Timeline</h3>
                </div>
                <div class="p-6">
                    <div class="flow-root">
                        <ul class="-mb-8">
                            <li>
                                <div class="relative pb-8">
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div>
                                                <div class="text-sm">
                                                    <span class="font-medium text-gray-900">Invoice Created</span>
                                                </div>
                                                <p class="mt-0.5 text-xs text-gray-500">
                                                    {{ formatDubaiDate($invoice->created_at, 'M d, Y \a\t H:i') }}
                                                </p>
                                            </div>
                                            <div class="mt-2 text-sm text-gray-700">
                                                <p>Invoice {{ $invoice->invoice_number }} was created</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @if($invoice->updated_at != $invoice->created_at)
                            <li>
                                <div class="relative">
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div>
                                                <div class="text-sm">
                                                    <span class="font-medium text-gray-900">Invoice Updated</span>
                                                </div>
                                                <p class="mt-0.5 text-xs text-gray-500">
                                                    {{ formatDubaiDate($invoice->updated_at, 'M d, Y \a\t H:i') }}
                                                </p>
                                            </div>
                                            <div class="mt-2 text-sm text-gray-700">
                                                <p>Invoice details were last modified</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function openPaymentModal() {
    document.getElementById('paymentModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
    document.body.style.overflow = 'unset';
}

// Close modal when clicking outside (except for the inner modal content)
document.addEventListener('DOMContentLoaded', function() {
    const paymentModal = document.getElementById('paymentModal');
    const modalContent = paymentModal.querySelector('.bg-white');
    
    paymentModal.addEventListener('click', function(e) {
        if (e.target === paymentModal || e.target.classList.contains('bg-gray-900')) {
            closePaymentModal();
        }
    });
    
    // Prevent closing when clicking inside modal content
    modalContent.addEventListener('click', function(e) {
        e.stopPropagation();
    });
});

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePaymentModal();
    }
});

// Auto-open modal if there are validation errors for payment form
@if($errors->any() && old('amount'))
    document.addEventListener('DOMContentLoaded', function() {
        openPaymentModal();
    });
@endif

function toggleSpecs(index) {
    const specsRow = document.getElementById(`specs-row-${index}`);
    if (specsRow) {
        specsRow.classList.toggle('hidden');
    }
}
</script>

@endsection 