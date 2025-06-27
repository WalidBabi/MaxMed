@extends('admin.layouts.app')

@section('title', 'Supplier Quotations')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold leading-6 text-gray-900">Supplier Quotations</h1>
            <p class="mt-2 text-sm text-gray-700">Manage and review quotations submitted by suppliers for product inquiries.</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Quotations -->
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">Total Quotations</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ $stats['total'] }}</dd>
        </div>

        <!-- Pending Review -->
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">Pending Review</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-yellow-600">{{ $stats['pending'] }}</dd>
        </div>

        <!-- Approved -->
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">Approved</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-green-600">{{ $stats['approved'] }}</dd>
        </div>

        <!-- Rejected -->
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">Rejected</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-red-600">{{ $stats['rejected'] }}</dd>
        </div>
    </div>

    <!-- Filters -->
    <div class="mt-8 flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center space-x-4">
            <!-- Status Filter -->
            <div class="min-w-0 flex-1">
                <form method="GET" action="{{ route('admin.quotations.index') }}" class="flex items-center space-x-4">
                    <select name="status" class="rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Pending Review</option>
                        <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>

                    <select name="currency" class="rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm" onchange="this.form.submit()">
                        <option value="">All Currencies</option>
                        <option value="AED" {{ request('currency') === 'AED' ? 'selected' : '' }}>AED</option>
                        <option value="USD" {{ request('currency') === 'USD' ? 'selected' : '' }}>USD</option>
                        <option value="EUR" {{ request('currency') === 'EUR' ? 'selected' : '' }}>EUR</option>
                    </select>

                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search quotations..." class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    
                    <button type="submit" class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Filter
                    </button>

                    @if(request()->hasAny(['status', 'currency', 'search']))
                        <a href="{{ route('admin.quotations.index') }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Clear
                        </a>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <!-- Quotations Table -->
    <div class="mt-8 flow-root">
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                @if($quotations->count() > 0)
                    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Quotation #</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Supplier</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Product</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Amount</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Submitted</th>
                                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                        <span class="sr-only">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach($quotations as $quotation)
                                    <tr>
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                            {{ $quotation->quotation_number }}
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            <div class="flex items-center">
                                                <div>
                                                    <div class="font-medium text-gray-900">{{ $quotation->supplier->name }}</div>
                                                    <div class="text-gray-500">{{ $quotation->supplier->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            @if($quotation->product)
                                                {{ $quotation->product->name }}
                                                @if($quotation->size)
                                                    <div class="text-xs text-gray-400">Size: {{ $quotation->size }}</div>
                                                @endif
                                            @else
                                                <span class="text-gray-400">Product not found</span>
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            <div class="font-medium">{{ $quotation->currency }} {{ number_format($quotation->unit_price, 2) }}</div>
                                            @if($quotation->shipping_cost > 0)
                                                <div class="text-xs text-gray-400">+ {{ $quotation->currency }} {{ number_format($quotation->shipping_cost, 2) }} shipping</div>
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                                            @if($quotation->status === 'submitted')
                                                <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800">
                                                    Pending Review
                                                </span>
                                            @elseif($quotation->status === 'accepted')
                                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                                                    Approved
                                                </span>
                                            @elseif($quotation->status === 'rejected')
                                                <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">
                                                    Rejected
                                                </span>
                                            @else
                                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800">
                                                    {{ ucfirst($quotation->status) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            {{ $quotation->created_at->format('M d, Y') }}
                                            <div class="text-xs text-gray-400">{{ $quotation->created_at->diffForHumans() }}</div>
                                        </td>
                                        <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                            <div class="flex items-center justify-end space-x-2">
                                                <a href="{{ route('admin.quotations.show', $quotation) }}" 
                                                   class="text-indigo-600 hover:text-indigo-900">
                                                    View
                                                </a>
                                                @if($quotation->status === 'submitted')
                                                    <button onclick="approveQuotation({{ $quotation->id }})" 
                                                            class="text-green-600 hover:text-green-900">
                                                        Approve
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $quotations->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No quotations found</h3>
                        <p class="mt-1 text-sm text-gray-500">No quotations match your current filters.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function approveQuotation(quotationId) {
    if (confirm('Are you sure you want to approve this quotation?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/quotations/${quotationId}/approve`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        document.body.appendChild(form);
        form.submit();
    }
}


</script>
@endsection 