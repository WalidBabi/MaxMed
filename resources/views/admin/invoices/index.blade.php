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

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="card-hover overflow-hidden rounded-xl bg-white px-4 py-6 shadow-sm ring-1 ring-gray-900/5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 00-.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Invoices</p>
                    <div class="text-base font-bold text-gray-900">
                        <div class="text-lg">{{ $invoiceCounts['proforma'] }} Proforma</div>
                        <div class="text-lg text-gray-600">{{ $invoiceCounts['final'] }} Final</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-hover overflow-hidden rounded-xl bg-white px-4 py-6 shadow-sm ring-1 ring-gray-900/5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-yellow-100">
                        <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Pending</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $invoices->where('payment_status', 'pending')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="card-hover overflow-hidden rounded-xl bg-white px-4 py-6 shadow-sm ring-1 ring-gray-900/5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Paid Orders</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $paidOrdersCount }}</p>
                    <p class="text-xs text-gray-500 mt-1">Unique orders paid</p>
                </div>
            </div>
        </div>

        <div class="card-hover overflow-hidden rounded-xl bg-white px-4 py-6 shadow-sm ring-1 ring-gray-900/5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-100">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="500" zoomAndPan="magnify" viewBox="0 0 375 374.999991" height="500" preserveAspectRatio="xMidYMid meet" version="1.2"><defs><clipPath id="e4df17700a"><path d="M 112.5 122.4375 L 262.5 122.4375 L 262.5 252.9375 L 112.5 252.9375 Z M 112.5 122.4375 "/></clipPath><clipPath id="2588f1d57e"><path d="M 142.726562 161.703125 L 144.175781 161.703125 L 144.175781 162.953125 L 142.726562 162.953125 Z M 142.726562 161.703125 "/></clipPath><clipPath id="dcb47909db"><path d="M 144.148438 162.148438 C 143.476562 163.199219 142.875 162.976562 142.800781 162.75 C 142.648438 162.523438 142.800781 161.925781 142.949219 161.773438 C 143.175781 161.625 143.925781 161.851562 143.925781 161.851562 "/></clipPath></defs><g id="fc05cfb7fb"><g clip-rule="nonzero" clip-path="url(#e4df17700a)"><path style=" stroke:none;fill-rule:nonzero;fill:#000000;fill-opacity:1;" d="M 125.828125 122.585938 C 125.898438 122.679688 126.21875 123.082031 126.539062 123.472656 C 128.820312 126.203125 130.542969 130.644531 131.464844 136.242188 C 132.078125 139.917969 132.109375 141.074219 132.109375 155.089844 L 132.109375 168.144531 L 125.859375 168.144531 C 120.144531 168.144531 119.484375 168.117188 118.359375 167.890625 C 116.496094 167.472656 114.890625 166.582031 113.542969 165.21875 C 112.570312 164.183594 112.601562 164.125 112.660156 167.261719 C 112.738281 169.855469 112.765625 170.140625 113.140625 171.558594 C 113.738281 173.777344 114.5625 175.453125 115.804688 176.9375 C 117.492188 178.980469 119.214844 180.121094 121.667969 180.882812 C 122.191406 181.035156 123.300781 181.097656 127.21875 181.125 L 132.113281 181.199219 L 132.113281 194.210938 L 125.214844 194.164062 L 118.285156 194.121094 L 117.089844 193.640625 C 115.667969 193.070312 115.027344 192.648438 113.632812 191.402344 L 112.617188 190.488281 L 112.675781 193.355469 C 112.75 196.011719 112.765625 196.308594 113.140625 197.660156 C 114.441406 202.433594 117.582031 205.839844 121.726562 206.949219 C 122.761719 207.234375 123.164062 207.25 127.492188 207.3125 L 132.113281 207.371094 L 132.113281 220.824219 C 132.113281 228.941406 132.066406 234.925781 131.992188 235.949219 C 131.917969 236.878906 131.679688 238.617188 131.46875 239.832031 C 130.496094 245.433594 128.746094 249.648438 126.234375 252.378906 L 125.722656 252.933594 L 151.027344 252.933594 C 166.152344 252.933594 177.464844 252.875 179.125 252.800781 C 182.042969 252.648438 188.550781 252.003906 190.019531 251.6875 C 190.480469 251.597656 191.347656 251.476562 191.917969 251.375 C 193.132812 251.191406 195.132812 250.773438 198.023438 250.039062 C 201.941406 249.066406 205.746094 247.757812 209.433594 246.113281 C 210.570312 245.605469 213.832031 243.9375 214.699219 243.414062 C 215.164062 243.144531 215.71875 242.8125 215.929688 242.707031 C 216.957031 242.109375 217.949219 241.453125 218.90625 240.742188 C 219.609375 240.234375 220.3125 239.738281 220.460938 239.632812 C 221.089844 239.214844 223.257812 237.398438 224.246094 236.480469 C 227.917969 233.125 231.03125 229.308594 233.582031 225.03125 C 233.925781 224.429688 234.378906 223.683594 234.570312 223.367188 C 235.066406 222.527344 237.097656 218.324219 237.292969 217.695312 C 237.355469 217.46875 237.445312 217.253906 237.5625 217.050781 C 237.953125 216.542969 240.195312 209.457031 240.464844 207.914062 C 240.558594 207.417969 240.601562 207.34375 240.972656 207.269531 C 241.214844 207.222656 244.703125 207.222656 248.726562 207.253906 C 256.777344 207.3125 256.777344 207.3125 258.554688 208.136719 C 259.558594 208.601562 259.859375 208.8125 260.964844 209.816406 C 262.414062 211.125 262.28125 211.335938 262.191406 208.0625 C 262.132812 206.140625 262.058594 204.957031 261.921875 204.476562 C 261.414062 202.628906 261.292969 202.242188 260.851562 201.3125 C 259.382812 198.089844 256.933594 195.804688 253.785156 194.707031 L 252.554688 194.257812 L 247.5625 194.1875 L 242.578125 194.113281 L 242.640625 192.355469 C 242.699219 190.042969 242.699219 185.46875 242.625 183.117188 L 242.5625 181.226562 L 249.234375 181.199219 C 254.953125 181.167969 256.015625 181.199219 256.644531 181.363281 C 258.527344 181.886719 259.800781 182.609375 261.355469 184.035156 L 262.226562 184.84375 L 262.226562 182.625 C 262.226562 179.984375 262.089844 178.8125 261.550781 177.074219 C 260.480469 173.546875 258.394531 170.921875 255.402344 169.300781 C 253.457031 168.25 253.335938 168.226562 246.648438 168.175781 C 242.730469 168.144531 240.675781 168.085938 240.574219 167.996094 C 240.476562 167.894531 240.417969 167.773438 240.410156 167.632812 C 240.273438 166.929688 240.097656 166.234375 239.882812 165.546875 C 236.386719 153.132812 229.855469 143.273438 220.289062 135.972656 C 218.988281 134.964844 215.800781 132.847656 214.503906 132.128906 C 214.007812 131.84375 213.472656 131.542969 213.335938 131.453125 C 212.707031 131.109375 209.101562 129.335938 208.195312 128.964844 C 207.65625 128.722656 206.953125 128.421875 206.640625 128.300781 C 201.359375 126.007812 192.503906 123.832031 185.738281 123.144531 C 184.628906 123.042969 183.167969 122.878906 182.488281 122.816406 C 179.445312 122.484375 175.21875 122.4375 151.171875 122.4375 C 130.855469 122.4375 125.753906 122.484375 125.828125 122.585938 Z M 175.308594 129.097656 C 180.367188 129.394531 183.480469 129.785156 187.117188 130.671875 C 198.21875 133.300781 206.027344 138.882812 211.699219 148.214844 C 212.222656 149.074219 214.4375 153.617188 214.765625 154.53125 C 216.335938 158.78125 217.101562 161.300781 217.765625 164.628906 C 217.929688 165.4375 218.152344 166.519531 218.257812 167.03125 C 218.363281 167.542969 218.410156 167.992188 218.363281 168.035156 C 218.320312 168.082031 203.265625 168.125 184.949219 168.113281 L 151.65625 168.082031 L 151.609375 148.796875 C 151.597656 138.203125 151.609375 129.394531 151.65625 129.230469 L 151.714844 128.945312 L 162.355469 128.945312 C 168.191406 128.945312 174.023438 129.007812 175.296875 129.082031 Z M 219.824219 181.660156 C 219.929688 182.304688 219.929688 193.261719 219.824219 193.800781 L 219.734375 194.203125 L 185.695312 194.175781 L 151.667969 194.132812 L 151.640625 187.769531 C 151.609375 184.273438 151.640625 181.359375 151.667969 181.300781 C 151.699219 181.242188 166.214844 181.179688 185.738281 181.179688 L 219.734375 181.179688 Z M 218.296875 207.425781 C 218.371094 207.640625 218.015625 209.496094 217.28125 212.5 C 216.441406 215.875 215.304688 219.28125 214.152344 221.800781 C 213.585938 223.089844 212.164062 225.882812 211.820312 226.40625 C 211.65625 226.648438 211.175781 227.414062 210.75 228.089844 C 207.882812 232.515625 204.242188 236.160156 199.824219 239.027344 C 198.226562 240.046875 194.933594 241.789062 194.042969 242.058594 C 193.878906 242.097656 193.730469 242.164062 193.589844 242.253906 C 193.488281 242.34375 192.125 242.855469 190.539062 243.425781 C 187.621094 244.460938 182.070312 245.585938 177.613281 246.050781 C 174.726562 246.335938 174.261719 246.351562 163.144531 246.351562 L 151.652344 246.351562 L 151.652344 207.402344 L 184.707031 207.339844 C 202.886719 207.3125 217.863281 207.265625 217.984375 207.234375 C 218.128906 207.226562 218.234375 207.285156 218.296875 207.417969 Z M 218.296875 207.425781 "/></g><g clip-rule="nonzero" clip-path="url(#2588f1d57e)"><g clip-rule="nonzero" clip-path="url(#dcb47909db)"><path style=" stroke:none;fill-rule:nonzero;fill:#000000;fill-opacity:1;" d="M 139.800781 158.773438 L 147.21875 158.773438 L 147.21875 166.050781 L 139.800781 166.050781 Z M 139.800781 158.773438 "/></g></g></g></svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Value</p>
                    <div class="text-base font-bold text-gray-900">
                        @if($invoiceTotals['aed'] > 0 && $invoiceTotals['usd'] > 0)
                            <div class="text-lg">{{ number_format($invoiceTotals['aed'], 0) }} AED</div>
                            <div class="text-lg text-gray-600">{{ number_format($invoiceTotals['usd'], 0) }} USD</div>
                        @elseif($invoiceTotals['usd'] > 0)
                            <div class="text-3xl">{{ number_format($invoiceTotals['usd'], 0) }} USD</div>
                        @elseif($invoiceTotals['aed'] > 0)
                            <div class="text-3xl">{{ number_format($invoiceTotals['aed'], 0) }} AED</div>
                        @else
                            <div class="text-3xl">0 AED</div>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Sent final invoices only</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Filters</h3>
        </div>
        <div class="p-6">
            <form method="GET" action="{{ route('admin.invoices.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <div class="md:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Search invoices..." class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                </div>
                
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <select name="type" id="type" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
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
                    <select name="status" id="status" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
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
                    <select name="payment_status" id="payment_status" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">Payment Status</option>
                        @foreach($filterOptions['payment_statuses'] as $value => $label)
                            <option value="{{ $value }}" {{ request('payment_status') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex items-end space-x-2">
                    <button type="submit" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Filter
                    </button>
                    <a href="{{ route('admin.invoices.index') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Clear
                    </a>
                </div>
            </form>
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
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice #</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Terms</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
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
                            <tr class="{{ $rowClass }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ formatDubaiDate($invoice->invoice_date, 'M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($isChildInvoice)
                                            <div class="mr-2 text-green-600" title="Final invoice converted from proforma">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                <a href="{{ route('admin.invoices.show', $invoice) }}" class="text-indigo-600 hover:text-indigo-900">
                                                    {{ $invoice->invoice_number }}
                                                </a>
                                            </div>
                                            @if($invoice->quote_id)
                                                <div class="text-xs text-gray-500">from {{ $invoice->quote->quote_number }}</div>
                                            @endif
                                            @if($isChildInvoice && $invoice->parentInvoice)
                                                <div class="text-xs text-green-600">← from {{ $invoice->parentInvoice->invoice_number }}</div>
                                            @elseif($hasChildInvoice)
                                                @php $finalInvoice = $invoice->childInvoices()->where('type', 'final')->first(); @endphp
                                                @if($finalInvoice)
                                                    <div class="text-xs text-blue-600">→ converted to {{ $finalInvoice->invoice_number }}</div>
                                                @endif
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
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($invoice->type === 'proforma')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Proforma
                                            </span>
                                            @if($hasChildInvoice)
                                                <span class="ml-2 text-xs text-blue-600 font-medium">(Converted)</span>
                                            @endif
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Final
                                            </span>
                                            @if($isChildInvoice)
                                                <span class="ml-2 text-xs text-green-600 font-medium">(From Proforma)</span>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $invoice->customer_name }}</div>
                                    
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $invoice->payment_terms ?? 'Net 30' }}
                                    </div>
                                    @if($invoice->due_date)
                                        <div class="text-xs text-gray-500">
                                            Due: {{ formatDubaiDate($invoice->due_date, 'M d, Y') }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
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
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col">
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
                                        @if($invoice->paid_amount > 0)
                                            <div class="text-xs text-green-600 mt-1">
                                                Paid: {{ number_format($invoice->paid_amount, 2) }} {{ $invoice->currency }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ number_format($invoice->total_amount, 2) }} {{ $invoice->currency }}
                                    </div>
                                    @if($invoice->paid_amount > 0 && $invoice->payment_status !== 'paid')
                                        <div class="text-xs text-gray-500">
                                            Balance: {{ number_format($invoice->total_amount - $invoice->paid_amount, 2) }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('admin.invoices.show', $invoice) }}" class="text-indigo-600 hover:text-indigo-900" title="View Invoice">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.invoices.edit', $invoice) }}" class="text-green-600 hover:text-green-900" title="Edit Invoice">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                        @if($invoice->type === 'proforma' && $invoice->canConvertToFinalInvoice() && !$hasChildInvoice)
                                            <form action="{{ route('admin.invoices.convert-to-final', $invoice) }}" method="POST" class="inline" onsubmit="return confirm('Convert this proforma invoice to final invoice?');">
                                                @csrf
                                                <button type="submit" class="text-yellow-600 hover:text-yellow-900" title="Convert to Final Invoice">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        @elseif($invoice->type === 'proforma' && $invoice->status !== 'cancelled' && !$hasChildInvoice)
                                            <span class="text-gray-400 cursor-help" title="Status: {{ ucfirst($invoice->status) }} - Click invoice to view details and update status">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"></path>
                                                </svg>
                                            </span>
                                        @endif
                                        <a href="{{ route('admin.invoices.pdf', $invoice) }}" class="text-red-600 hover:text-red-900" title="Download PDF">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </a>
                                        <button class="text-blue-600 hover:text-blue-900 send-email-btn" 
                                                data-invoice-id="{{ $invoice->id }}"
                                                data-customer-name="{{ $invoice->customer_name }}"
                                                data-invoice-number="{{ $invoice->invoice_number }}"
                                                data-customer-email="{{ $invoice->customer_email ?? '' }}"
                                                title="Send Email">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                            </svg>
                                        </button>
                                        <button class="text-red-600 hover:text-red-900 delete-invoice-btn" 
                                                data-invoice-id="{{ $invoice->id }}"
                                                title="Delete Invoice">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
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
            <form id="deleteForm" method="POST" class="w-full sm:w-auto">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
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

document.addEventListener('DOMContentLoaded', function() {
    // Delete invoice functionality
    document.querySelectorAll('.delete-invoice-btn').forEach(button => {
        button.addEventListener('click', function() {
            const invoiceId = this.getAttribute('data-invoice-id');
            const deleteForm = document.getElementById('deleteForm');
            deleteForm.action = `/admin/invoices/${invoiceId}`;
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
                            statusBadge = `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Sent</span>`;
                            break;
                        case 'approved':
                            statusBadge = `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Approved</span>`;
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
</script>
@endpush
</div>
@endsection 