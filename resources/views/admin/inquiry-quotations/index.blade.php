@extends('admin.layouts.app')

@section('title', 'Inquiries & Quotations')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold leading-6 text-gray-900">Inquiries & Quotations</h1>
            <p class="mt-2 text-sm text-gray-700">Unified view of product inquiries and supplier quotations. Track the complete lifecycle from inquiry to quotation.</p>
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none header-actions space-x-3">
            <a href="{{ route('admin.inquiries.create') }}" 
               class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                <i class="fas fa-plus mr-2"></i> Create Inquiry
            </a>
            <a href="{{ route('admin.inquiries.index') }}" 
               class="inline-flex items-center rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-500">
                <i class="fas fa-list mr-2"></i> Legacy Inquiries
            </a>
        </div>
    </div>

    <!-- Quick Statistics -->
    <div class="mt-8 mb-8">
        <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
            <!-- Total Inquiries -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:border-indigo-500 transition-colors duration-200">
                <div class="px-4 py-5 sm:p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Inquiries</dt>
                            <dd class="mt-1 text-2xl font-semibold text-indigo-600">{{ $stats['total_inquiries'] ?? 0 }}</dd>
                        </div>
                        <div class="flex-shrink-0 bg-indigo-100 rounded-md p-2">
                            <i class="fas fa-clipboard-list text-indigo-600"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Inquiries -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:border-yellow-500 transition-colors duration-200">
                <div class="px-4 py-5 sm:p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 truncate">Pending</dt>
                            <dd class="mt-1 text-2xl font-semibold text-yellow-600">{{ $stats['pending_inquiries'] ?? 0 }}</dd>
                        </div>
                        <div class="flex-shrink-0 bg-yellow-100 rounded-md p-2">
                            <i class="fas fa-clock text-yellow-600"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Quotations -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:border-green-500 transition-colors duration-200">
                <div class="px-4 py-5 sm:p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Quotations</dt>
                            <dd class="mt-1 text-2xl font-semibold text-green-600">{{ $stats['total_quotations'] ?? 0 }}</dd>
                        </div>
                        <div class="flex-shrink-0 bg-green-100 rounded-md p-2">
                            <i class="fas fa-file-invoice text-green-600"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Quotations -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:border-orange-500 transition-colors duration-200">
                <div class="px-4 py-5 sm:p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 truncate">Pending Review</dt>
                            <dd class="mt-1 text-2xl font-semibold text-orange-600">{{ $stats['pending_quotations'] ?? 0 }}</dd>
                        </div>
                        <div class="flex-shrink-0 bg-orange-100 rounded-md p-2">
                            <i class="fas fa-hourglass-half text-orange-600"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Response Rate -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:border-blue-500 transition-colors duration-200">
                <div class="px-4 py-5 sm:p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 truncate">Response Rate</dt>
                            <dd class="mt-1 text-2xl font-semibold text-blue-600">{{ $stats['response_rate'] ?? 0 }}%</dd>
                        </div>
                        <div class="flex-shrink-0 bg-blue-100 rounded-md p-2">
                            <i class="fas fa-chart-line text-blue-600"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Avg Quotations per Inquiry -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:border-purple-500 transition-colors duration-200">
                <div class="px-4 py-5 sm:p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 truncate">Avg Quotes/Inquiry</dt>
                            <dd class="mt-1 text-2xl font-semibold text-purple-600">{{ number_format($stats['avg_quotations_per_inquiry'] ?? 0, 1) }}</dd>
                        </div>
                        <div class="flex-shrink-0 bg-purple-100 rounded-md p-2">
                            <i class="fas fa-calculator text-purple-600"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Not Available Responses -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:border-red-500 transition-colors duration-200">
                <div class="px-4 py-5 sm:p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 truncate">Not Available</dt>
                            <dd class="mt-1 text-2xl font-semibold text-red-600">{{ $stats['not_available_responses'] ?? 0 }}</dd>
                        </div>
                        <div class="flex-shrink-0 bg-red-100 rounded-md p-2">
                            <i class="fas fa-times-circle text-red-600"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="mt-8 bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Filter & Search</h3>
        </div>
        <div class="p-6">
            <form method="GET" action="{{ route('admin.inquiry-quotations.index') }}" class="grid grid-cols-1 gap-6 sm:grid-cols-5">
                <div class="sm:col-span-1">
                    <label for="status" class="block text-sm font-medium text-gray-700">Inquiry Status</label>
                    <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">All Inquiries</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="broadcast" {{ request('status') == 'broadcast' ? 'selected' : '' }}>Broadcast</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="quoted" {{ request('status') == 'quoted' ? 'selected' : '' }}>Quoted</option>
                        <option value="converted" {{ request('status') == 'converted' ? 'selected' : '' }}>Converted</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="sm:col-span-1">
                    <label for="quotation_status" class="block text-sm font-medium text-gray-700">Quotation Status</label>
                    <select name="quotation_status" id="quotation_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">All Quotations</option>
                        <option value="submitted" {{ request('quotation_status') == 'submitted' ? 'selected' : '' }}>Pending Review</option>
                        <option value="accepted" {{ request('quotation_status') == 'accepted' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('quotation_status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                    <input type="text" name="search" id="search" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                           placeholder="Search by reference, product, or supplier..." 
                           value="{{ request('search') }}">
                </div>
                <div class="sm:col-span-1 flex items-end space-x-3">
                    <button type="submit" 
                            class="flex-1 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                        Filter
                    </button>
                    <a href="{{ route('admin.inquiry-quotations.index') }}" 
                       class="flex-1 text-center rounded-md bg-gray-100 px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm hover:bg-gray-200">
                        Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Inquiries with Quotations -->
    <div class="mt-8">
        @if($inquiries->count() > 0)
            <div class="space-y-6">
                @foreach($inquiries as $inquiry)
                    <div class="bg-white shadow-sm rounded-lg border border-gray-200 hover:border-indigo-300 transition-colors duration-200">
                        <!-- Inquiry Header -->
                        <div class="px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900">{{ $inquiry->reference_number }}</h3>
                                        <p class="text-sm text-gray-500">
                                            {{ $inquiry->product ? $inquiry->product->name : $inquiry->product_name }}
                                            @if($inquiry->quantity)
                                                · Qty: {{ number_format($inquiry->quantity) }}
                                            @endif
                                        </p>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'broadcast' => 'bg-indigo-100 text-indigo-800',
                                                'in_progress' => 'bg-blue-100 text-blue-800',
                                                'quoted' => 'bg-green-100 text-green-800',
                                                'converted' => 'bg-green-100 text-green-800',
                                                'cancelled' => 'bg-red-100 text-red-800',
                                            ];
                                            $color = $statusColors[$inquiry->status] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $color }}">
                                            {{ ucfirst($inquiry->status) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <span class="text-sm text-gray-500">{{ $inquiry->created_at->diffForHumans() }}</span>
                                    <a href="{{ route('admin.inquiry-quotations.show', $inquiry) }}" 
                                       class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Quotations for this Inquiry -->
                        @if($inquiry->quotations->count() > 0)
                            <div class="px-6 py-4">
                                <h4 class="text-sm font-medium text-gray-900 mb-3">
                                    Quotations Received ({{ $inquiry->quotations->count() }})
                                </h4>
                                <div class="space-y-3">
                                    @foreach($inquiry->quotations->take(3) as $quotation)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div class="flex items-center space-x-4">
                                                <div class="flex-shrink-0">
                                                    <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                                                        <i class="fas fa-building text-indigo-600 text-xs"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900">{{ $quotation->supplier->name }}</p>
                                                    <p class="text-sm text-gray-500">
                                                        {{ $quotation->currency }} {{ number_format($quotation->unit_price, 2) }}
                                                        @if($quotation->shipping_cost > 0)
                                                            + {{ $quotation->currency }} {{ number_format($quotation->shipping_cost, 2) }} shipping
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-3">
                                                @php
                                                    $quotationStatusColors = [
                                                        'draft' => 'bg-yellow-100 text-yellow-800',
                                                        'submitted' => 'bg-yellow-100 text-yellow-800',
                                                        'accepted' => 'bg-green-100 text-green-800',
                                                        'rejected' => 'bg-red-100 text-red-800',
                                                    ];
                                                    $quotationColor = $quotationStatusColors[$quotation->status] ?? 'bg-gray-100 text-gray-800';
                                                @endphp
                                                <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $quotationColor }}">
                                                    @if($quotation->status === 'draft' || $quotation->status === 'submitted')
                                                        Draft
                                                    @else
                                                        {{ ucfirst($quotation->status) }}
                                                    @endif
                                                </span>

                                                <div class="flex items-center space-x-2">
                                                    @if($quotation->status === 'draft' || $quotation->status === 'submitted')
                                                        <form action="{{ route('admin.inquiry-quotations.quotations.approve', $quotation) }}" 
                                                              method="POST" 
                                                              class="inline-block">
                                                            @csrf
                                                            <button type="submit" 
                                                                    class="inline-flex items-center rounded-md bg-green-600 px-2 py-1 text-xs font-medium text-white hover:bg-green-700">
                                                                <i class="fas fa-check mr-1"></i> Approve
                                                            </button>
                                                        </form>
                                                    @endif

                                                    <a href="{{ route('admin.inquiry-quotations.quotation-show', $quotation) }}" 
                                                       class="inline-flex items-center rounded-md bg-indigo-600 px-2 py-1 text-xs font-medium text-white hover:bg-indigo-700">
                                                        <i class="fas fa-eye mr-1"></i> View
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    @if($inquiry->quotations->count() > 3)
                                        <div class="text-center">
                                            <a href="{{ route('admin.inquiry-quotations.show', $inquiry) }}" 
                                               class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                                View all {{ $inquiry->quotations->count() }} quotations
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="px-6 py-4">
                                <div class="text-center py-4">
                                    <i class="fas fa-file-invoice text-gray-400 text-2xl mb-2"></i>
                                    <p class="text-sm text-gray-500">No quotations received yet</p>
                                    @if($inquiry->status === 'pending')
                                        <p class="text-xs text-gray-400 mt-1">Broadcast this inquiry to suppliers to receive quotations</p>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Not Available Responses -->
                        @php
                            $notAvailableResponses = $inquiry->supplierResponses->where('status', 'not_available');
                        @endphp
                        @if($notAvailableResponses->count() > 0)
                            <div class="px-6 py-4 border-t border-gray-200">
                                <h4 class="text-sm font-medium text-red-900 mb-3 flex items-center">
                                    <i class="fas fa-times-circle mr-2"></i>
                                    Not Available Responses ({{ $notAvailableResponses->count() }})
                                </h4>
                                <div class="space-y-3">
                                    @foreach($notAvailableResponses->take(3) as $response)
                                        <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg border border-red-200">
                                            <div class="flex items-center space-x-4">
                                                <div class="flex-shrink-0">
                                                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                                        <i class="fas fa-times text-red-600 text-xs"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-red-900">{{ $response->supplier->name }}</p>
                                                    <p class="text-sm text-red-600">
                                                        @if($response->notes)
                                                            {{ Str::limit($response->notes, 60) }}
                                                        @else
                                                            No reason provided
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-3">
                                                <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold bg-red-100 text-red-800">
                                                    Not Available
                                                </span>
                                                <span class="text-xs text-red-500">
                                                    {{ $response->updated_at->diffForHumans() }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                    @if($notAvailableResponses->count() > 3)
                                        <div class="text-center">
                                            <a href="{{ route('admin.inquiry-quotations.show', $inquiry) }}" 
                                               class="text-red-600 hover:text-red-900 text-sm font-medium">
                                                View all {{ $notAvailableResponses->count() }} not available responses
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $inquiries->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-12 bg-white shadow-sm rounded-lg border border-gray-200">
                <i class="fas fa-clipboard-list text-gray-400 text-6xl mb-4"></i>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No inquiries found</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating a new product inquiry.</p>
                <div class="mt-6">
                    <a href="{{ route('admin.inquiries.create') }}" 
                       class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                        <i class="fas fa-plus mr-2"></i> Create Inquiry
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Approve Quotation Modal -->
<div id="approveModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Approve Quotation</h3>
                <button type="button" onclick="closeApproveModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="approveForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                    <textarea name="notes" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Add any notes about this approval..."></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeApproveModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700">
                        Approve
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function approveQuotation(quotationId) {
    document.getElementById('approveForm').action = `/admin/inquiry-quotations/quotations/${quotationId}/approve`;
    document.getElementById('approveModal').classList.remove('hidden');
}

function closeApproveModal() {
    document.getElementById('approveModal').classList.add('hidden');
}

// Close modal when clicking outside
window.onclick = function(event) {
    const approveModal = document.getElementById('approveModal');
    
    if (event.target == approveModal) {
        closeApproveModal();
    }
}
</script>
@endsection 