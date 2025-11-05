@extends('admin.layouts.app')

@section('title', 'Invoices')

@section('content')
<div class="-mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8">
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Invoice Management</h1>
                <p class="text-gray-600 mt-2">Manage proforma invoices, final invoices, and payments</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.invoices.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Create Invoice
                </a>
            </div>
        </div>
    </div>

    <!-- All Cards in One Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 mb-6">
        <!-- Invoices Card -->
        <div class="group relative overflow-hidden rounded-md bg-gradient-to-br from-blue-50 to-indigo-100 p-1.5 shadow-sm ring-1 ring-blue-200/50 transition-all duration-300 hover:shadow-md hover:ring-blue-300/50">
            <div class="absolute -right-1 -top-1 h-12 w-12 rounded-full bg-blue-200/30 transition-all duration-300 group-hover:scale-110"></div>
            <div class="relative">
                <div class="flex items-center justify-between">
                    <div class="flex h-6 w-6 items-center justify-center rounded-md bg-blue-500 shadow-sm">
                        <svg class="h-3 w-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 00-.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-medium text-blue-600 uppercase tracking-wide">Invoices</p>
                        <p class="text-xs text-blue-500">All types</p>
                </div>
                    </div>
                <div class="mt-1">
                    <div class="space-y-0">
                        <p class="text-sm font-bold text-gray-900">{{ $invoiceCounts['final'] }} Final</p>
                        <p class="text-xs font-semibold text-gray-700">{{ $invoiceCounts['proforma'] }} Proforma</p>
                    </div>
                    <p class="text-xs text-blue-600 mt-0.5">Total: {{ $invoiceCounts['total'] }}</p>
                </div>
            </div>
        </div>

        <!-- Pending Card -->
        <div class="group relative overflow-hidden rounded-md bg-gradient-to-br from-yellow-50 to-amber-100 p-1.5 shadow-sm ring-1 ring-yellow-200/50 transition-all duration-300 hover:shadow-md hover:ring-yellow-300/50">
            <div class="absolute -right-1 -top-1 h-12 w-12 rounded-full bg-yellow-200/30 transition-all duration-300 group-hover:scale-110"></div>
            <div class="relative">
                <div class="flex items-center justify-between">
                    <div class="flex h-6 w-6 items-center justify-center rounded-md bg-yellow-500 shadow-sm">
                        <svg class="h-3 w-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-medium text-yellow-600 uppercase tracking-wide">Pending</p>
                        <p class="text-xs text-yellow-500">Payment status</p>
                </div>
                </div>
                <div class="mt-1">
                    <p class="text-sm font-bold text-gray-900">{{ $pendingAll ?? $invoices->where('payment_status', 'pending')->count() }}</p>
                    <p class="text-xs text-yellow-600 mt-0.5">Awaiting payment</p>
                </div>
            </div>
        </div>

        <!-- Revenue Card -->
        <div class="group relative overflow-hidden rounded-md bg-gradient-to-br from-emerald-50 to-green-100 p-1.5 shadow-sm ring-1 ring-emerald-200/50 transition-all duration-300 hover:shadow-md hover:ring-emerald-300/50">
            <div class="absolute -right-1 -top-1 h-12 w-12 rounded-full bg-emerald-200/20 transition-all duration-300 group-hover:scale-110"></div>
            <div class="relative">
                <div class="flex items-center justify-between">
                    <div class="flex h-6 w-6 items-center justify-center rounded-md bg-emerald-500 shadow-sm">
                        <svg class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                        </svg>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wide">Revenue</p>
                        <p class="text-xs text-emerald-500">All sent invoices</p>
                </div>
                </div>
                <div class="mt-1">
                    <div class="space-y-0">
                        <p class="text-sm font-bold text-gray-900">{{ number_format($invoiceTotals['revenue']['aed'], 2) }} AED</p>
                        <p class="text-xs font-semibold text-gray-700">${{ number_format($invoiceTotals['revenue']['usd'], 2) }} USD</p>
                        <p class="text-xs font-bold text-emerald-600">Combined: {{ number_format($invoiceTotals['revenue']['combined'], 2) }} AED</p>
                    </div>
                    <p class="text-xs text-emerald-600 mt-0.5">Excludes pending payments</p>
                </div>
            </div>
        </div>

        <!-- Cash Flow Card -->
        <div class="group relative overflow-hidden rounded-md bg-gradient-to-br from-violet-50 to-purple-100 p-1.5 shadow-sm ring-1 ring-violet-200/50 transition-all duration-300 hover:shadow-md hover:ring-violet-300/50">
            <div class="absolute -right-1 -top-1 h-12 w-12 rounded-full bg-violet-200/20 transition-all duration-300 group-hover:scale-110"></div>
            <div class="relative">
                <div class="flex items-center justify-between">
                    <div class="flex h-6 w-6 items-center justify-center rounded-md bg-violet-500 shadow-sm">
                        <svg class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-semibold text-violet-600 uppercase tracking-wide">Cash Flow</p>
                        <p class="text-xs text-violet-500">Paid invoices only</p>
                </div>
                    </div>
                <div class="mt-1">
                    <div class="space-y-0">
                        <p class="text-sm font-bold text-gray-900">{{ number_format($invoiceTotals['cash_flow']['aed'], 2) }} AED</p>
                        <p class="text-xs font-semibold text-gray-700">${{ number_format($invoiceTotals['cash_flow']['usd'], 2) }} USD</p>
                        <p class="text-xs font-bold text-violet-600">Combined: {{ number_format($invoiceTotals['cash_flow']['combined'], 2) }} AED</p>
                    </div>
                    <p class="text-xs text-violet-600 mt-0.5">Actual money received</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 mb-8" x-data="{ filtersOpen: false }">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Filters</h3>
                <button @click="filtersOpen = !filtersOpen" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none">
                    <span x-text="filtersOpen ? 'Hide Filters' : 'Show Filters'"></span>
                    <svg class="ml-2 h-4 w-4 transition-transform duration-200" :class="{ 'rotate-180': filtersOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
        </div>
        </div>
        <div class="p-6" x-show="filtersOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-2">
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <div class="md:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Search invoices, customers, products..." class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" oninput="filterInvoices()">
                    </div>
                </div>
                
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <select name="type" id="type" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" onchange="filterInvoices()">
                        <option value="">All Types</option>
                        @foreach($filterOptions['types'] as $value => $label)
                            <option value="{{ $value }}" {{ request('type') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="status" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" onchange="filterInvoices()">
                        <option value="">All Status</option>
                        @foreach($filterOptions['statuses'] as $value => $label)
                            <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="payment_status" class="block text-sm font-medium text-gray-700 mb-1">Payment Status</label>
                    <select name="payment_status" id="payment_status" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" onchange="filterInvoices()">
                        <option value="">Payment Status</option>
                        @foreach($filterOptions['payment_statuses'] as $value => $label)
                            <option value="{{ $value }}" {{ request('payment_status') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex items-end space-x-2">
                    <button type="button" onclick="clearInvoiceFilters()" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Clear
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoices Table -->
    <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">All Invoices</h3>
            <p class="text-sm text-gray-600 mt-1">Manage and track all customer invoices</p>
        </div>

        @if($invoices->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200" style="min-width: 1000px;">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">Date</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-28">Invoice #</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-14">Type</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-36">Customer</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">Terms</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-14">Status</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-14">Payment</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">Amount</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">
                                <div class="flex items-center space-x-1">
                                    <span>Products</span>
                                    <button type="button" onclick="toggleInvoiceProductsColumn()" class="text-gray-400 hover:text-gray-600" title="Toggle Products Column">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                </div>
                            </th>
                            <th scope="col" class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-28">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($invoices as $invoice)
                            @php
                                $isChildInvoice = $invoice->parent_invoice_id !== null;
                                $hasChildInvoice = $invoice->childInvoices()->where('type', 'final')->exists();
                                $rowClass = 'hover:bg-gray-50';
                                
                                if ($isChildInvoice) {
                                    $rowClass .= ' bg-green-50 border-l-4 border-green-500';
                                } elseif ($hasChildInvoice && $invoice->type === 'proforma') {
                                    $rowClass .= ' bg-blue-50 border-l-4 border-blue-500';
                                }
                            @endphp
                            @if($invoice->type === 'proforma' && $hasChildInvoice)
                                <!-- Hide proforma if it has a final; it will be shown collapsed under final -->
                                @continue
                            @endif
                            <tr class="{{ $rowClass }}" id="invoice-row-{{ $invoice->id }}">
                                <td class="px-3 py-3 whitespace-nowrap text-xs text-gray-900">
                                    {{ formatDubaiDate($invoice->invoice_date, 'M d') }}
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($invoice->type === 'final' && $invoice->parentInvoice)
                                            <button type="button" class="mr-1 text-gray-500 hover:text-gray-700 focus:outline-none toggle-proforma-btn" data-invoice-id="{{ $invoice->id }}" title="Toggle proforma details">
                                                <svg class="w-3 h-3 transform transition-transform duration-200" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                            </button>
                                        @endif
                                        <div>
                                            <div class="text-xs font-medium text-gray-900">
                                                <a href="{{ route('admin.invoices.show', $invoice) }}" class="text-indigo-600 hover:text-indigo-900 truncate block">
                                                    {{ $invoice->invoice_number }}
                                                </a>
                                            </div>
                                            @if($invoice->quote_id)
                                                <div class="text-xs text-gray-500 truncate">from {{ $invoice->quote->quote_number }}</div>
                                            @endif
                                            
                                            <!-- Related Order Information -->
                                            @if($invoice->order)
                                                <div class="text-xs text-purple-600 mt-1">
                                                    📦 Order: <a href="{{ route('admin.orders.show', $invoice->order) }}" class="hover:underline">{{ $invoice->order->order_number }}</a>
                                                    @if($invoice->order->status)
                                                        <span class="text-gray-500">({{ ucfirst($invoice->order->status) }})</span>
                                                    @endif
                                                </div>
                                            @endif
                                            
                                            <!-- Related Delivery Information -->
                                            @if($invoice->delivery)
                                                <div class="text-xs text-orange-600">
                                                    🚚 Delivery: {{ $invoice->delivery->tracking_number ?? 'ID: ' . $invoice->delivery->id }}
                                                    @if($invoice->delivery->status)
                                                        <span class="text-gray-500">({{ ucfirst($invoice->delivery->status) }})</span>
                                                    @endif
                                                </div>
                                            @elseif($invoice->order && $invoice->order->delivery)
                                                <div class="text-xs text-orange-600">
                                                    🚚 Delivery: {{ $invoice->order->delivery->tracking_number ?? 'ID: ' . $invoice->order->delivery->id }}
                                                    @if($invoice->order->delivery->status)
                                                        <span class="text-gray-500">({{ ucfirst($invoice->order->delivery->status) }})</span>
                                                    @endif
                                                </div>
                                            @endif
                                            
                                            <!-- Related Cash Receipt Information -->
                                            @if($invoice->order)
                                                @php
                                                    $cashReceipts = \App\Models\CashReceipt::where('order_id', $invoice->order->id)->get();
                                                @endphp
                                                @if($cashReceipts->count() > 0)
                                                    <div class="text-xs text-green-600">
                                                        🧾 Receipts: 
                                                        @foreach($cashReceipts->take(2) as $receipt)
                                                            <a href="{{ route('admin.cash-receipts.show', $receipt) }}" class="hover:underline">{{ $receipt->receipt_number }}</a>{{ !$loop->last ? ', ' : '' }}
                                                        @endforeach
                                                        @if($cashReceipts->count() > 2)
                                                            <span class="text-gray-500">(+{{ $cashReceipts->count() - 2 }} more)</span>
                                                        @endif
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($invoice->type === 'proforma')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Proforma
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Final
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap">
                                    <div class="text-xs font-medium text-gray-900 truncate" title="{{ $invoice->customer_name }}">{{ Str::limit($invoice->customer_name, 20) }}</div>
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap">
                                    <div class="text-xs text-gray-900">
                                        {{ $invoice->payment_terms ?? 'Net 30' }}
                                    </div>
                                    @if($invoice->due_date)
                                        <div class="text-xs text-gray-500">
                                            Due: {{ formatDubaiDate($invoice->due_date, 'M d') }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap">
                                    @if($invoice->status === 'draft')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Draft
                                        </span>
                                    @elseif($invoice->status === 'sent')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Sent
                                        </span>
                                    @elseif($invoice->status === 'approved')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Approved
                                        </span>
                                    @elseif($invoice->status === 'completed')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Completed
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            {{ ucfirst($invoice->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        @if($invoice->payment_status === 'pending')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Pending
                                            </span>
                                        @elseif($invoice->payment_status === 'paid')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Paid
                                            </span>
                                        @elseif($invoice->payment_status === 'partial')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Partial
                                            </span>
                                            @if($invoice->paid_amount > 0)
                                                <div class="text-xs text-green-600 font-medium mt-1">
                                                    {{ number_format($invoice->paid_amount, 2) }} {{ $invoice->currency }} paid
                                                </div>
                                            @endif
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                {{ ucfirst($invoice->payment_status) }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap">
                                    <div class="text-xs font-medium text-gray-900">
                                        {{ number_format($invoice->total_amount, 0) }} {{ $invoice->currency }}
                                    </div>
                                </td>
                                <td class="px-3 py-3 text-xs text-gray-900 invoice-products-column">
                                    @if($invoice->items->count() > 0)
                                        <div class="space-y-1">
                                            @foreach($invoice->items->take(2) as $item)
                                                <div class="flex items-center space-x-1">
                                                    <div class="flex-1 min-w-0">
                                                        <div class="text-xs font-medium text-gray-900 truncate">
                                                            {{ Str::limit($item->product ? $item->product->name : $item->description, 25) }}
                                                        </div>
                                                        <div class="text-xs text-gray-500">
                                                            {{ number_format($item->quantity, 0) }} × {{ number_format($item->unit_price, 0) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                            @if($invoice->items->count() > 2)
                                                <div class="text-xs text-gray-500 font-medium">
                                                    +{{ $invoice->items->count() - 2 }} more
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-xs">No products</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap text-right text-xs font-medium">
                                    <div class="flex items-center justify-end space-x-1">
                                        <a href="{{ route('admin.invoices.show', $invoice) }}" class="text-indigo-600 hover:text-indigo-900" title="View">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.invoices.edit', $invoice) }}" class="text-green-600 hover:text-green-900" title="Edit">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                        @if($invoice->payment_status !== 'paid')
                                            <button onclick="openInvoicePaymentModal('{{ $invoice->id }}', '{{ $invoice->invoice_number }}', '{{ $invoice->customer_name }}', '{{ number_format($invoice->total_amount - $invoice->paid_amount, 2) }}', '{{ $invoice->currency }}')" 
                                                    class="text-purple-600 hover:text-purple-900" title="Record Payment">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                </svg>
                                            </button>
                                        @endif
                                        @if($invoice->type === 'proforma' && $invoice->canConvertToFinalInvoice() && !$hasChildInvoice)
                                            <form
                                                action="{{ route('admin.invoices.convert-to-final', $invoice) }}"
                                                method="POST"
                                                class="inline"
                                                data-ajax="form"
                                                data-confirm="Convert this proforma invoice to final invoice?"
                                                data-loading-text="Converting..."
                                                data-success-message="Final invoice created successfully."
                                                data-error-message="Unable to convert invoice."
                                            >
                                                @csrf
                                                <button type="submit" class="text-yellow-600 hover:text-yellow-900" title="Convert" data-loading-text="Converting...">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        @elseif($invoice->type === 'proforma' && $invoice->status !== 'cancelled' && !$hasChildInvoice)
                                            <span class="text-gray-400 cursor-help" title="Status: {{ ucfirst($invoice->status) }} - Click invoice to view details and update status">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"></path>
                                                </svg>
                                            </span>
                                        @endif
                                        <a href="{{ route('admin.invoices.pdf', $invoice) }}" class="text-red-600 hover:text-red-900" title="PDF">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </a>
                                        <button class="text-blue-600 hover:text-blue-900 send-email-btn" 
                                                data-invoice-id="{{ $invoice->id }}"
                                                data-customer-name="{{ $invoice->customer_name }}"
                                                data-invoice-number="{{ $invoice->invoice_number }}"
                                                data-customer-email="{{ $invoice->customer_email ?? '' }}"
                                                title="Email">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                            </svg>
                                        </button>
                                        <button class="text-red-600 hover:text-red-900 delete-invoice-btn" 
                                                data-invoice-id="{{ $invoice->id }}"
                                                title="Delete">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @if($invoice->type === 'final' && $invoice->parentInvoice)
                                @php $parent = $invoice->parentInvoice; @endphp
                                <tr id="proforma-row-{{ $invoice->id }}" class="{{ ($parent->status === 'completed' && $parent->payment_status === 'paid') ? 'bg-green-50' : 'bg-blue-50' }} hidden">
                                    <td class="px-3 py-3 whitespace-nowrap text-xs text-gray-900">
                                        {{ formatDubaiDate($parent->invoice_date, 'M d') }}
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap">
                                            <div>
                                            <div class="text-xs font-medium text-gray-900">
                                                <a href="{{ route('admin.invoices.show', $parent) }}" class="text-indigo-600 hover:text-indigo-900 truncate block">{{ $parent->invoice_number }}</a>
                                                </div>
                                                @if($parent->quote_id)
                                                <div class="text-xs text-gray-500 truncate">from {{ $parent->quote->quote_number }}</div>
                                                @endif
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Proforma</span>
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap">
                                        <div class="text-xs font-medium text-gray-900 truncate" title="{{ $parent->customer_name }}">{{ Str::limit($parent->customer_name, 20) }}</div>
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap">
                                        <div class="text-xs text-gray-900">{{ $parent->payment_terms ?? 'Net 30' }}</div>
                                        @if($parent->due_date)
                                            <div class="text-xs text-gray-500">Due: {{ formatDubaiDate($parent->due_date, 'M d') }}</div>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap">
                                        @if($parent->status === 'draft')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Draft</span>
                                        @elseif($parent->status === 'sent')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Sent</span>
                                        @elseif($parent->status === 'approved')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Approved</span>
                                        @elseif($parent->status === 'completed')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Completed</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">{{ ucfirst($parent->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            @if($parent->payment_status === 'pending')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                                            @elseif($parent->payment_status === 'paid')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Paid</span>
                                            @elseif($parent->payment_status === 'partial')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Partial</span>
                                                @if($parent->paid_amount > 0)
                                                    <div class="text-xs text-green-600 font-medium mt-1">
                                                        {{ number_format($parent->paid_amount, 2) }} {{ $parent->currency }} paid
                                                    </div>
                                                @endif
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">{{ ucfirst($parent->payment_status) }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap">
                                        <div class="text-xs font-medium text-gray-900">{{ number_format($parent->total_amount, 0) }} {{ $parent->currency }}</div>
                                    </td>
                                    <td class="px-3 py-3 text-xs text-gray-900 invoice-products-column">
                                        @if($parent->items && $parent->items->count() > 0)
                                            <div class="space-y-1">
                                                @foreach($parent->items->take(2) as $pitem)
                                                    <div class="flex items-center space-x-1">
                                                        <div class="flex-1 min-w-0">
                                                            <div class="text-xs font-medium text-gray-900 truncate">{{ Str::limit($pitem->product ? $pitem->product->name : $pitem->description, 25) }}</div>
                                                            <div class="text-xs text-gray-500">{{ number_format($pitem->quantity, 0) }} × {{ number_format($pitem->unit_price, 0) }}</div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                                @if($parent->items->count() > 2)
                                                    <div class="text-xs text-gray-500 font-medium">+{{ $parent->items->count() - 2 }} more</div>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-gray-400 text-xs">No products</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap text-right text-xs font-medium">
                                        <div class="flex items-center justify-end space-x-1">
                                            <a href="{{ route('admin.invoices.show', $parent) }}" class="text-indigo-600 hover:text-indigo-900" title="View">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                            <a href="{{ route('admin.invoices.edit', $parent) }}" class="text-green-600 hover:text-green-900" title="Edit">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                            <a href="{{ route('admin.invoices.pdf', $parent) }}" class="text-red-600 hover:text-red-900" title="PDF">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            </a>
                                            <button class="text-blue-600 hover:text-blue-900 send-email-btn" 
                                                    data-invoice-id="{{ $parent->id }}"
                                                    data-customer-name="{{ $parent->customer_name }}"
                                                    data-invoice-number="{{ $parent->invoice_number }}"
                                                    data-customer-email="{{ $parent->customer_email ?? '' }}"
                                                    title="Email">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                </svg>
                                            </button>
                                            <button class="text-red-600 hover:text-red-900 delete-invoice-btn" 
                                                    data-invoice-id="{{ $parent->id }}"
                                                    title="Delete">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($invoices->hasPages())
                <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                    <div class="flex-1 flex justify-between sm:hidden">
                        @if($invoices->onFirstPage())
                            <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-500 bg-white cursor-default">
                                Previous
                            </span>
                        @else
                            <a href="{{ $invoices->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Previous
                            </a>
                        @endif

                        @if($invoices->hasMorePages())
                            <a href="{{ $invoices->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Next
                            </a>
                        @else
                            <span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-500 bg-white cursor-default">
                                Next
                            </span>
                        @endif
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Showing <span class="font-medium">{{ $invoices->firstItem() ?? 0 }}</span> to <span class="font-medium">{{ $invoices->lastItem() ?? 0 }}</span> of <span class="font-medium">{{ $invoices->total() }}</span> results
                            </p>
                        </div>
                        <div>
                            {{ $invoices->links() }}
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-12">
                <div class="w-24 h-24 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 00-.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Invoices Found</h3>
                <p class="text-gray-500 mb-6">Start by creating your first invoice or converting a quote to proforma invoice</p>
                <a href="{{ route('admin.invoices.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Create Invoice
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div x-data="{ show: false }" x-on:open-modal.window="$event.detail == 'confirm-invoice-deletion' ? show = true : null" x-on:close-modal.window="$event.detail == 'confirm-invoice-deletion' ? show = false : null" x-show="show" class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50" style="display: none;">
    <div x-show="show" class="fixed inset-0 transform transition-all" x-on:click="show = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
    </div>

    <div x-show="show" class="mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-lg sm:mx-auto" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Delete Invoice</h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">Are you sure you want to delete this invoice? This action cannot be undone.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <form
                id="deleteForm"
                method="POST"
                class="w-full sm:w-auto"
                data-ajax="form"
                data-loading-text="Deleting..."
                data-success-message="Invoice deleted."
                data-error-message="Unable to delete invoice."
                data-reset-form="false"
                data-success-dispatch="close-modal"
                data-success-dispatch-detail="confirm-invoice-deletion"
            >
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm" data-loading-text="Deleting...">
                    Delete
                </button>
            </form>
            <button type="button" x-on:click="show = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                Cancel
            </button>
        </div>
    </div>
</div>

<!-- Send Email Modal -->
<div x-data="{ show: false, isLoading: false }" 
     x-on:open-modal.window="console.log('Modal event received:', $event.detail); $event.detail == 'send-invoice-email' ? show = true : null" 
     x-on:close-modal.window="$event.detail == 'send-invoice-email' ? show = false : null" 
     x-show="show" 
     class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50" 
     style="display: none;">
    <div x-show="show" class="fixed inset-0 transform transition-all" x-on:click="show = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="absolute inset-0 bg-gray-900 bg-opacity-75 backdrop-blur-sm"></div>
    </div>

    <div x-show="show" class="mb-6 bg-white rounded-2xl overflow-hidden shadow-2xl transform transition-all sm:w-full sm:max-w-2xl sm:mx-auto border border-gray-200" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
        <form id="sendEmailForm" method="POST" action="" x-data="{ submitting: false }" @submit.prevent="submitEmailForm($event)">
            @csrf
            
            <!-- Header with gradient background -->
            <div class="bg-gradient-to-r from-green-600 to-emerald-700 px-6 py-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white bg-opacity-20 backdrop-blur-sm">
                        <svg class="h-7 w-7 text-emerald-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-xl font-semibold">Send Invoice Email</h3>
                        <p class="text-green-100 text-sm mt-1">Send the invoice directly to your customer's email</p>
                    </div>
                    <button type="button" x-on:click="show = false" class="rounded-full p-2 hover:bg-white hover:bg-opacity-20 transition-colors duration-200">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Content -->
            <div class="px-6 py-6">
                <div class="space-y-6">
                    <!-- Invoice Information Card -->
                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-gray-900" id="emailModalInvoiceNumber">Invoice #</h4>
                                <p class="text-sm text-gray-600" id="emailModalCustomerName">Customer Name</p>
                            </div>
                        </div>
                    </div>

                    <!-- Email Fields -->
                    <div class="space-y-5">
                        <div>
                            <label for="customer_email" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                </svg>
                                Customer Email Address
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative">
                                <input type="email" 
                                       id="customer_email" 
                                       name="customer_email" 
                                       required
                                       class="block w-full px-4 py-3 pr-10 text-gray-900 placeholder-gray-500 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200" 
                                       placeholder="Enter customer email address">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <div id="emailLoadingSpinner" class="hidden">
                                        <svg class="animate-spin h-5 w-5 text-emerald-500" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                    <svg id="emailFoundIcon" class="hidden h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            </div>
                            <p id="emailStatus" class="mt-2 text-sm text-gray-600 hidden"></p>
                        </div>
                        
                        <div>
                            <label for="cc_emails" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                CC Email Addresses
                            </label>
                            <input type="text" 
                                   id="cc_emails" 
                                   name="cc_emails" 
                                   value="sales@maxmedme.com" 
                                   class="block w-full px-4 py-3 text-gray-900 placeholder-gray-500 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200"
                                   placeholder="Additional email addresses (comma separated)">
                            <p class="mt-2 text-xs text-gray-500 flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                                Separate multiple email addresses with commas
                            </p>
                        </div>
                        
                        <!-- Enhanced Info Box -->
                        <div class="bg-gradient-to-r from-emerald-50 to-green-50 border border-emerald-200 rounded-xl p-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-emerald-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3 flex-1">
                                    <h4 class="text-sm font-medium text-emerald-800">Email will include:</h4>
                                    <ul class="mt-2 text-sm text-emerald-700 space-y-1">
                                        <li class="flex items-center">
                                            <svg class="w-3 h-3 mr-2 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            Professional invoice PDF attachment
                                        </li>
                                        <li class="flex items-center">
                                            <svg class="w-3 h-3 mr-2 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            Personalized email message
                                        </li>
                                        <li class="flex items-center">
                                            <svg class="w-3 h-3 mr-2 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            Company branding and contact information
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Footer -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 rounded-b-2xl">
                <div class="flex items-center justify-between">
                    <button type="button" 
                            x-on:click="show = false" 
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Cancel
                    </button>
                    <button type="submit" 
                            x-bind:disabled="submitting"
                            class="inline-flex items-center px-6 py-2 text-sm font-medium bg-gradient-to-r from-emerald-600 to-green-600 border border-transparent rounded-lg shadow-lg hover:from-emerald-700 hover:to-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 transform hover:scale-105">
                        <span x-show="!submitting" class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            Send Email
                        </span>
                        <span x-show="submitting" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Sending...
                        </span>
                     </button>
                 </div>
             </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let currentInvoiceId = null; // Store current invoice ID for status updates
let invoiceProductsColumnVisible = true; // Track invoice products column visibility

// Toggle proforma inline row for a final invoice
function toggleProformaRow(invoiceId, btn) {
    const row = document.getElementById(`proforma-row-${invoiceId}`);
    if (!row) return;
    const icon = btn.querySelector('svg');
    const isHidden = row.classList.contains('hidden');
    if (isHidden) {
        row.classList.remove('hidden');
        icon.classList.add('rotate-90');
    } else {
        row.classList.add('hidden');
        icon.classList.remove('rotate-90');
    }
}

// Function to toggle invoice products column visibility
function toggleInvoiceProductsColumn() {
    const productsColumns = document.querySelectorAll('.invoice-products-column');
    const toggleButton = document.querySelector('button[onclick="toggleInvoiceProductsColumn()"] svg');
    
    invoiceProductsColumnVisible = !invoiceProductsColumnVisible;
    
    productsColumns.forEach(column => {
        if (invoiceProductsColumnVisible) {
            column.style.display = 'table-cell';
            toggleButton.style.transform = 'rotate(0deg)';
        } else {
            column.style.display = 'none';
            toggleButton.style.transform = 'rotate(180deg)';
        }
    });
    
    // Store preference in localStorage
    localStorage.setItem('invoiceProductsColumnVisible', invoiceProductsColumnVisible);
}

// Initialize invoice products column visibility on page load (robust to timing)
(function onReady(fn){
    if (document.readyState !== 'loading') {
        fn();
    } else {
        document.addEventListener('DOMContentLoaded', fn, { once: true });
    }
})(function() {
    const savedVisibility = localStorage.getItem('invoiceProductsColumnVisible');
    if (savedVisibility === 'false') {
        toggleInvoiceProductsColumn();
    }
});

// Real-time filtering functions for invoices
let invoiceFilterTimeout;
let isInvoiceFiltering = false;

function filterInvoices() {
    clearTimeout(invoiceFilterTimeout);
    
    // Show filtering indicator
    if (!isInvoiceFiltering) {
        showInvoiceFilteringIndicator();
    }
    
    invoiceFilterTimeout = setTimeout(() => {
        const searchTerm = document.getElementById('search').value.toLowerCase();
        const typeFilter = document.getElementById('type').value;
        const statusFilter = document.getElementById('status').value;
        const paymentStatusFilter = document.getElementById('payment_status').value;
        
        const rows = document.querySelectorAll('tbody tr');
        let visibleCount = 0;
        
        rows.forEach(row => {
            const invoiceNumber = row.querySelector('td:nth-child(2) .text-sm')?.textContent?.toLowerCase() || '';
            const customerName = row.querySelector('td:nth-child(4) .text-sm')?.textContent?.toLowerCase() || '';
            const type = row.querySelector('td:nth-child(3) span')?.textContent?.toLowerCase() || '';
            const status = row.querySelector('td:nth-child(6) span')?.textContent?.toLowerCase() || '';
            const paymentStatus = row.querySelector('td:nth-child(7) span')?.textContent?.toLowerCase() || '';
            const products = row.querySelector('td:nth-child(9)')?.textContent?.toLowerCase() || '';
            
            // Check if row matches all filters
            const matchesSearch = !searchTerm || 
                invoiceNumber.includes(searchTerm) || 
                customerName.includes(searchTerm) || 
                products.includes(searchTerm);
            
            const matchesType = !typeFilter || type.includes(typeFilter);
            const matchesStatus = !statusFilter || status.includes(statusFilter);
            const matchesPaymentStatus = !paymentStatusFilter || paymentStatus.includes(paymentStatusFilter);
            
            if (matchesSearch && matchesType && matchesStatus && matchesPaymentStatus) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Update results count
        updateInvoiceResultsCount(visibleCount, rows.length);
        
        // Hide filtering indicator
        hideInvoiceFilteringIndicator();
    }, 300); // Debounce for 300ms
}

function showInvoiceFilteringIndicator() {
    isInvoiceFiltering = true;
    const searchInput = document.getElementById('search');
    const parent = searchInput.parentElement;
    
    // Add loading spinner
    let spinner = parent.querySelector('.invoice-filtering-spinner');
    if (!spinner) {
        spinner = document.createElement('div');
        spinner.className = 'invoice-filtering-spinner absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none';
        spinner.innerHTML = `
            <svg class="animate-spin h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        `;
        parent.appendChild(spinner);
    }
    spinner.style.display = 'flex';
}

function hideInvoiceFilteringIndicator() {
    isInvoiceFiltering = false;
    const spinner = document.querySelector('.invoice-filtering-spinner');
    if (spinner) {
        spinner.style.display = 'none';
    }
}
 

function clearInvoiceFilters() {
    document.getElementById('search').value = '';
    document.getElementById('type').value = '';
    document.getElementById('status').value = '';
    document.getElementById('payment_status').value = '';
    filterInvoices();
}

function updateInvoiceResultsCount(visible, total) {
    // Find or create results count element
    let resultsCount = document.getElementById('invoice-results-count');
    if (!resultsCount) {
        resultsCount = document.createElement('div');
        resultsCount.id = 'invoice-results-count';
        resultsCount.className = 'text-sm text-gray-600 mt-2';
        const filterSection = document.querySelector('.card-hover .p-6');
        filterSection.appendChild(resultsCount);
    }
    
    if (visible === total) {
        resultsCount.style.display = 'none';
    } else {
        resultsCount.style.display = 'block';
        resultsCount.textContent = `Showing ${visible} of ${total} invoices`;
    }
}

(function onReady(fn){
    if (document.readyState !== 'loading') {
        fn();
    } else {
        document.addEventListener('DOMContentLoaded', fn, { once: true });
    }
})(function() {
    // Toggle proforma functionality
    document.querySelectorAll('.toggle-proforma-btn').forEach(button => {
        button.addEventListener('click', function() {
            const invoiceId = this.getAttribute('data-invoice-id');
            const proformaRow = document.getElementById(`proforma-row-${invoiceId}`);
            const caretIcon = this.querySelector('svg');

            if (proformaRow.classList.contains('hidden')) {
                proformaRow.classList.remove('hidden');
                caretIcon.classList.add('rotate-90');
            } else {
                proformaRow.classList.add('hidden');
                caretIcon.classList.remove('rotate-90');
            }
        });
    });

    // Delete invoice functionality
    document.querySelectorAll('.delete-invoice-btn').forEach(button => {
        button.addEventListener('click', function() {
            const invoiceId = this.getAttribute('data-invoice-id');
            const deleteForm = document.getElementById('deleteForm');
            deleteForm.action = `/admin/invoices/${invoiceId}`;
            deleteForm.dataset.successRemove = `#invoice-row-${invoiceId}, #proforma-row-${invoiceId}`;
            deleteForm.dataset.successDispatchDetail = 'confirm-invoice-deletion';
            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'confirm-invoice-deletion' }));
        });
    });

    // Enhanced send email functionality
    document.querySelectorAll('.send-email-btn').forEach(button => {
        button.addEventListener('click', function() {
            console.log('Send email button clicked for invoices');
            const invoiceId = this.getAttribute('data-invoice-id');
            const customerName = this.getAttribute('data-customer-name');
            const invoiceNumber = this.getAttribute('data-invoice-number');
            const customerEmail = this.getAttribute('data-customer-email');
            
            // Store invoice ID globally for status updates
            currentInvoiceId = invoiceId;
            
            console.log('Invoice data:', { invoiceId, customerName, invoiceNumber, customerEmail });
            
            const sendEmailForm = document.getElementById('sendEmailForm');
            if (!sendEmailForm) {
                console.error('Send email form not found');
                return;
            }
            
            sendEmailForm.action = `/admin/invoices/${invoiceId}/send-email`;
            console.log('Form action set to:', sendEmailForm.action);
            
            // Update modal content
            const emailModalInvoiceNumber = document.getElementById('emailModalInvoiceNumber');
            const emailModalCustomerName = document.getElementById('emailModalCustomerName');
            
            if (emailModalInvoiceNumber) emailModalInvoiceNumber.textContent = `Invoice ${invoiceNumber}`;
            if (emailModalCustomerName) emailModalCustomerName.textContent = customerName;
            
            // Use existing email or fetch by customer name
            if (customerEmail && customerEmail.trim() !== '') {
                populateEmailField(customerEmail, 'Customer email loaded from invoice');
            } else {
                fetchCustomerEmail(customerName);
            }
            
            console.log('Dispatching open-modal event for send-invoice-email');
            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'send-invoice-email' }));
        });
    });

    // Helper function to populate email field
    function populateEmailField(email, message) {
        const emailInput = document.getElementById('customer_email');
        const loadingSpinner = document.getElementById('emailLoadingSpinner');
        const emailFoundIcon = document.getElementById('emailFoundIcon');
        const emailStatus = document.getElementById('emailStatus');
        
        loadingSpinner.classList.add('hidden');
        emailInput.disabled = false;
        emailInput.value = email;
        emailFoundIcon.classList.remove('hidden');
        emailStatus.className = 'mt-2 text-sm text-green-600 flex items-center';
        emailStatus.innerHTML = `
            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
            </svg>
            ${message}
        `;
        emailStatus.classList.remove('hidden');
    }

    // Function to fetch customer email
    function fetchCustomerEmail(customerName) {
        const emailInput = document.getElementById('customer_email');
        const loadingSpinner = document.getElementById('emailLoadingSpinner');
        const emailFoundIcon = document.getElementById('emailFoundIcon');
        const emailStatus = document.getElementById('emailStatus');
        
        // Show loading state
        loadingSpinner.classList.remove('hidden');
        emailFoundIcon.classList.add('hidden');
        emailStatus.classList.add('hidden');
        emailInput.value = '';
        emailInput.disabled = true;
        
        // Fetch customer email
        fetch(`/admin/customers/by-name/${encodeURIComponent(customerName)}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                loadingSpinner.classList.add('hidden');
                emailInput.disabled = false;
                
                if (data.email) {
                    populateEmailField(data.email, 'Customer email found and populated automatically');
                } else {
                    emailStatus.textContent = 'Customer email not found. Please enter manually.';
                    emailStatus.className = 'mt-2 text-sm text-amber-600 flex items-center';
                    emailStatus.innerHTML = `
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        Customer email not found. Please enter manually.
                    `;
                    emailStatus.classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Error fetching customer email:', error);
                loadingSpinner.classList.add('hidden');
                emailInput.disabled = false;
                emailStatus.textContent = 'Failed to fetch customer email. Please enter manually.';
                emailStatus.className = 'mt-2 text-sm text-red-600 flex items-center';
                emailStatus.innerHTML = `
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    Failed to fetch customer email. Please enter manually.
                `;
                emailStatus.classList.remove('hidden');
            });
    }

    // Function to handle email form submission
    window.submitEmailForm = function(event) {
        const form = event.target;
        const formData = new FormData(form);
        const submitButton = form.querySelector('button[type="submit"]');
        
        // Set submitting state
        Alpine.store('emailModal', { submitting: true });
        
        console.log('Submitting email form...');
        
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                // Get the text response for debugging
                return response.text().then(text => {
                    console.error('Non-JSON response received:', text);
                    throw new Error('Server did not return JSON response');
                });
            }
            
            return response.json();
        })
        .then(data => {
            console.log('Email response:', data);
            
            // Reset submitting state
            Alpine.store('emailModal', { submitting: false });
            
            if (data.success) {
                // Close modal
                window.dispatchEvent(new CustomEvent('close-modal', { detail: 'send-invoice-email' }));
                
                // Show success message with status update info
                let message = 'Email sent successfully!';
                if (data.previous_status && data.new_status && data.previous_status !== data.new_status) {
                    message += ` Status updated from ${data.previous_status} to ${data.new_status}.`;
                }
                showNotification(message, 'success');
                
                // Update invoice status in the UI if it changed
                if (data.new_status && data.previous_status !== data.new_status) {
                    updateInvoiceStatusInUI(currentInvoiceId, data.new_status);
                }
                
                // Reset form
                form.reset();
            } else {
                // Show error message
                showNotification(data.message || 'Failed to send email. Please try again.', 'error');
            }
        })
        .catch(error => {
            console.error('Email submission error:', error);
            
            // Reset submitting state
            Alpine.store('emailModal', { submitting: false });
            
            // Show more detailed error message
            let errorMessage = 'An error occurred while sending the email.';
            if (error.message.includes('HTTP error')) {
                errorMessage += ' Server returned an error status.';
            } else if (error.message.includes('JSON')) {
                errorMessage += ' Server response was not in the expected format.';
            } else if (error.message.includes('NetworkError') || error.message.includes('fetch')) {
                errorMessage += ' Network connection failed.';
            }
            errorMessage += ' Check console for details.';
            
            showNotification(errorMessage, 'error');
        });
    };

    // Function to update invoice status in the UI
    function updateInvoiceStatusInUI(invoiceId, newStatus) {
        console.log('Updating invoice status in UI:', { invoiceId, newStatus });
        
        // Find the invoice row in the table
        const invoiceRows = document.querySelectorAll('tbody tr');
        invoiceRows.forEach(row => {
            const sendEmailBtn = row.querySelector('.send-email-btn');
            if (sendEmailBtn && sendEmailBtn.getAttribute('data-invoice-id') === invoiceId) {
                // Find the status cell (6th column)
                const statusCell = row.querySelector('td:nth-child(6)');
                if (statusCell) {
                    let statusBadge = '';
                    switch (newStatus) {
                        case 'draft':
                            statusBadge = `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Draft</span>`;
                            break;
                        case 'sent':
                            statusBadge = `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Sent</span>`;
                            break;
                        case 'approved':
                            statusBadge = `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Approved</span>`;
                            break;
                        case 'completed':
                            statusBadge = `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Completed</span>`;
                            break;
                        default:
                            statusBadge = `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">${newStatus.charAt(0).toUpperCase() + newStatus.slice(1)}</span>`;
                    }
                    statusCell.innerHTML = statusBadge;
                    
                    // Add a subtle animation to highlight the change
                    statusCell.classList.add('bg-green-50');
                    setTimeout(() => {
                        statusCell.classList.remove('bg-green-50');
                    }, 2000);
                }
                
                // Update statistics if needed (for draft->sent conversion)
                if (newStatus === 'sent') {
                    updateInvoiceStatistics();
                }
                
                // Update proforma row background color if status changed to completed AND payment is paid
                if (newStatus === 'completed') {
                    const proformaRow = document.getElementById(`proforma-row-${invoiceId}`);
                    if (proformaRow) {
                        // Check if payment status is also paid
                        const paymentStatusCell = row.querySelector('td:nth-child(7)'); // Payment status column
                        const isPaid = paymentStatusCell && paymentStatusCell.textContent.toLowerCase().includes('paid');
                        
                        if (isPaid) {
                            // Remove any existing background classes and add green background
                            proformaRow.classList.remove('bg-blue-50', 'bg-yellow-50', 'bg-gray-50');
                            proformaRow.classList.add('bg-green-50');
                        } else {
                            // Keep blue background if not paid
                            proformaRow.classList.remove('bg-green-50', 'bg-yellow-50', 'bg-gray-50');
                            proformaRow.classList.add('bg-blue-50');
                        }
                    }
                }
            }
        });
    }

    // Function to update statistics cards for invoices
    function updateInvoiceStatistics() {
        // The statistics are calculated server-side, but we could provide visual feedback
        // For now, we'll just log that the status changed
        console.log('Invoice status updated - statistics may need refresh');
    }

    // Function to show notifications
    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 max-w-sm w-full bg-white rounded-lg shadow-lg border-l-4 ${
            type === 'success' ? 'border-green-400' : 
            type === 'error' ? 'border-red-400' : 'border-blue-400'
        } p-4 transition-all duration-300 transform translate-x-full`;
        
        notification.innerHTML = `
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    ${type === 'success' ? 
                        '<svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>' :
                        type === 'error' ? 
                        '<svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>' :
                        '<svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>'
                    }
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium text-gray-900">${message}</p>
                </div>
                <div class="ml-4 flex-shrink-0">
                    <button class="inline-flex text-gray-400 hover:text-gray-500" onclick="this.parentElement.parentElement.parentElement.remove()">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 5000);
    }
});

// Invoice Payment Modal functionality
function openInvoicePaymentModal(invoiceId, invoiceNumber, customerName, remainingAmount, currency) {
    console.log('Opening payment modal for invoice:', invoiceId, invoiceNumber);
    
    const modalHTML = `
        <div id="invoicePaymentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-10 mx-auto p-6 border w-full max-w-lg shadow-lg rounded-lg bg-white">
                <div class="mt-3">
                    <!-- Header -->
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900">Record Payment</h3>
                            <p class="text-sm text-gray-600 mt-1">Invoice #${invoiceNumber} - ${customerName}</p>
                        </div>
                        <button onclick="closeInvoicePaymentModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Payment Summary -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <div class="text-center">
                            <div class="text-sm text-gray-500">Remaining Amount</div>
                            <div class="text-2xl font-bold text-red-600">${currency} ${remainingAmount}</div>
                        </div>
                    </div>

                    <!-- Payment Form -->
                    <form action="/admin/invoices/${invoiceId}/record-payment" method="POST" enctype="multipart/form-data" class="space-y-4">
                        <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
                        
                        <!-- Payment Amount -->
                        <div>
                            <label for="payment_amount" class="block text-sm font-medium text-gray-700 mb-2">Payment Amount *</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">${currency}</span>
                                </div>
                                <input type="number" id="payment_amount" name="amount" step="0.01" min="0.01" 
                                       max="${remainingAmount}" value="${remainingAmount}" required
                                       class="pl-12 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div>
                            <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">Payment Method *</label>
                            <select id="payment_method" name="payment_method" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Select Payment Method</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="credit_card">Credit Card</option>
                                <option value="check">Check</option>
                                <option value="cash">Cash</option>
                                <option value="online">Online Payment</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <!-- Payment Date -->
                        <div>
                            <label for="payment_date" class="block text-sm font-medium text-gray-700 mb-2">Payment Date *</label>
                            <input type="date" id="payment_date" name="payment_date" value="${new Date().toISOString().split('T')[0]}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <!-- Transaction Reference -->
                        <div>
                            <label for="transaction_reference" class="block text-sm font-medium text-gray-700 mb-2">Transaction Reference</label>
                            <input type="text" id="transaction_reference" name="transaction_reference" 
                                   placeholder="e.g., Check #1234, Transfer ID, etc."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <!-- Payment Notes -->
                        <div>
                            <label for="payment_notes" class="block text-sm font-medium text-gray-700 mb-2">Payment Notes</label>
                            <textarea id="payment_notes" name="payment_notes" rows="2" 
                                      placeholder="Additional notes about this payment..."
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        </div>

                        <!-- Attachments -->
                        <div>
                            <label for="payment_attachments" class="block text-sm font-medium text-gray-700 mb-2">Attachments</label>
                            <input type="file" id="payment_attachments" name="attachments[]" multiple 
                                   accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.webp,.xls,.xlsx"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <p class="text-xs text-gray-500 mt-1">Max 10MB per file. Supported: PDF, DOC, DOCX, JPG, PNG, XLS, XLSX</p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end space-x-3 mt-6 pt-4 border-t border-gray-200">
                            <button type="button" onclick="closeInvoicePaymentModal()" 
                                    class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                Record Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHTML);
}

function closeInvoicePaymentModal() {
    const modal = document.getElementById('invoicePaymentModal');
    if (modal) {
        modal.remove();
    }
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    if (e.target.id === 'invoicePaymentModal') {
        closeInvoicePaymentModal();
    }
});
</script>
@endpush
</div>
@endsection 