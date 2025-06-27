@extends('admin.layouts.app')

@section('title', 'Supplier Inquiries')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold leading-6 text-gray-900">Supplier Inquiries</h1>
            <p class="mt-2 text-sm text-gray-700">Manage product inquiries sent to suppliers and track responses. Inquiries are automatically sent to suppliers who are registered for the relevant product categories.</p>
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none header-actions">
            <a href="{{ route('admin.inquiries.create') }}" 
               class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                <i class="fas fa-plus mr-2"></i> Create Inquiry
            </a>
        </div>
    </div>

    <!-- Quick Statistics -->
    <div class="mt-8 mb-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <!-- Total Inquiries -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:border-indigo-500 transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-indigo-100 rounded-md p-3">
                            <i class="fas fa-clipboard-list text-indigo-600 text-xl"></i>
                        </div>
                        <div class="ml-5">
                            <dt class="text-sm font-medium text-gray-500">Total Inquiries</dt>
                            <dd class="mt-1 text-3xl font-semibold text-indigo-600">{{ $inquiries->total() }}</dd>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:border-yellow-500 transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
                            <i class="fas fa-clock text-yellow-600 text-xl"></i>
                        </div>
                        <div class="ml-5">
                            <dt class="text-sm font-medium text-gray-500">Pending</dt>
                            <dd class="mt-1 text-3xl font-semibold text-yellow-600">{{ $inquiries->where('status', 'pending')->count() }}</dd>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Broadcast -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:border-blue-500 transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                            <i class="fas fa-broadcast-tower text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-5">
                            <dt class="text-sm font-medium text-gray-500">Broadcast</dt>
                            <dd class="mt-1 text-3xl font-semibold text-blue-600">{{ $inquiries->where('status', 'broadcast')->count() }}</dd>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quoted -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:border-green-500 transition-colors duration-200">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                            <i class="fas fa-check text-green-600 text-xl"></i>
                        </div>
                        <div class="ml-5">
                            <dt class="text-sm font-medium text-gray-500">Quoted</dt>
                            <dd class="mt-1 text-3xl font-semibold text-green-600">{{ $inquiries->where('status', 'quoted')->count() }}</dd>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="mt-8 bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Filter Inquiries</h3>
        </div>
        <div class="p-6">
            <form method="GET" action="{{ route('admin.inquiries.index') }}" class="grid grid-cols-1 gap-6 sm:grid-cols-4">
                <div class="sm:col-span-1">
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="broadcast" {{ request('status') == 'broadcast' ? 'selected' : '' }}>Broadcast</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="quoted" {{ request('status') == 'quoted' ? 'selected' : '' }}>Quoted</option>
                        <option value="converted" {{ request('status') == 'converted' ? 'selected' : '' }}>Converted</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                    <input type="text" name="search" id="search" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                           placeholder="Search by reference number or product..." 
                           value="{{ request('search') }}">
                </div>
                <div class="sm:col-span-1 flex items-end space-x-3">
                    <button type="submit" 
                            class="flex-1 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                        Filter
                    </button>
                    <a href="{{ route('admin.inquiries.index') }}" 
                       class="flex-1 text-center rounded-md bg-gray-100 px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm hover:bg-gray-200">
                        Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Inquiries Table -->
    <div class="mt-8 flow-root">
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8 table-container">
            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                @if($inquiries->count() > 0)
                    @php
                        $hasBroadcastInquiries = $inquiries->contains('status', 'broadcast');
                    @endphp
                    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg" data-has-broadcast-inquiries="{{ $hasBroadcastInquiries ? 'true' : 'false' }}">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Reference</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Product/Description</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Quantity</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide group relative">
                                        <div class="flex items-center">
                                            Broadcast To
                                            <span class="ml-1 cursor-help">
                                                <i class="fas fa-info-circle"></i>
                                                <div class="hidden group-hover:block absolute z-10 top-full left-0 mt-1 w-64 p-2 bg-gray-800 text-white text-xs rounded shadow-lg normal-case">
                                                    Inquiries are sent to all suppliers with matching product categories.
                                                </div>
                                            </span>
                                        </div>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Responses</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Created</th>
                                    <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach($inquiries as $inquiry)
                                    <tr class="hover:bg-gray-50 table-row-with-dropdown" data-inquiry-id="{{ $inquiry->id }}">
                                        <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                            <div class="font-semibold">{{ $inquiry->reference_number }}</div>
                                            @if($inquiry->customer_reference)
                                                <div class="text-gray-500 text-xs">Ref: {{ $inquiry->customer_reference }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            @if($inquiry->product_id)
                                                <div class="font-medium">{{ $inquiry->product->name ?? 'Product Not Found' }}</div>
                                                @if($inquiry->product && $inquiry->product->sku)
                                                    <div class="text-gray-500 text-xs">SKU: {{ $inquiry->product->sku }}</div>
                                                @endif
                                            @else
                                                <div class="font-medium">{{ $inquiry->product_name }}</div>
                                                @if($inquiry->product_category)
                                                    <div class="text-gray-500 text-xs">Category: {{ $inquiry->product_category }}</div>
                                                @endif
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">{{ number_format($inquiry->quantity) }}</td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                            @php
                                                $statusColors = [
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    'processing' => 'bg-blue-100 text-blue-800',
                                                    'broadcast' => 'bg-indigo-100 text-indigo-800',
                                                    'in_progress' => 'bg-blue-100 text-blue-800',
                                                    'quoted' => 'bg-green-100 text-green-800',
                                                    'converted' => 'bg-green-100 text-green-800',
                                                    'cancelled' => 'bg-red-100 text-red-800',
                                                    'expired' => 'bg-gray-100 text-gray-800'
                                                ];
                                                $color = $statusColors[$inquiry->status] ?? 'bg-gray-100 text-gray-800';
                                            @endphp
                                            <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $color }}">
                                                {{ ucfirst($inquiry->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            <span class="text-indigo-600 font-medium">Suppliers with relevant categories</span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            <div class="status-display space-y-2">
                                            @php
                                                // Only count actual responses (not pending ones)
                                                $actualResponseCount = $inquiry->supplierResponses->where('status', 'quoted')->count();
                                                $interestedCount = $inquiry->supplierResponses->where('status', 'interested')->count();
                                                $quotedCount = $inquiry->supplierResponses->where('status', 'quoted')->count();
                                                $totalSuppliersNotified = $inquiry->supplierResponses->count();
                                                
                                                // Enhanced email tracking metrics
                                                $emailsSentSuccessfully = $inquiry->supplierResponses->where('email_sent_successfully', true)->count();
                                                $emailsReceived = $emailsSentSuccessfully; // Assuming sent = received for now
                                                $suppliersClickedEmail = $inquiry->supplierResponses->whereNotNull('viewed_at')->count();
                                                $emailsFailed = $inquiry->supplierResponses->where('email_sent_successfully', false)->count();
                                                $totalClicks = $suppliersClickedEmail;
                                                
                                                // Response categories
                                                $notInterestedCount = $inquiry->supplierResponses->where('status', 'not_interested')->count();
                                            @endphp
                                            
                                            <div class="space-y-2">
                                                @if($totalSuppliersNotified > 0)
                                                    <!-- Email Metrics Section -->
                                                    <div class="bg-gray-50 rounded-lg p-3 space-y-2">
                                                        <div class="text-xs font-medium text-gray-700 uppercase tracking-wide">Email Tracking</div>
                                                        
                                                        <div class="grid grid-cols-2 gap-3 text-xs">
                                                            <!-- Emails Sent -->
                                                            <div class="flex items-center justify-between">
                                                                <span class="text-gray-600">📧 Sent:</span>
                                                                <span class="font-medium {{ $emailsSentSuccessfully > 0 ? 'text-green-600' : 'text-gray-500' }}">
                                                                    {{ $emailsSentSuccessfully }}
                                                                </span>
                                                            </div>
                                                            
                                                            <!-- Emails Received (Delivered) -->
                                                            <div class="flex items-center justify-between">
                                                                <span class="text-gray-600">✅ Received:</span>
                                                                <span class="font-medium {{ $emailsReceived > 0 ? 'text-blue-600' : 'text-gray-500' }}">
                                                                    {{ $emailsReceived }}
                                                                </span>
                                                            </div>
                                                            
                                                            <!-- Clicked on Email -->
                                                            <div class="flex items-center justify-between">
                                                                <span class="text-gray-600">👆 Clicked:</span>
                                                                <span class="font-medium {{ $suppliersClickedEmail > 0 ? 'text-purple-600' : 'text-gray-500' }}">
                                                                    {{ $suppliersClickedEmail }}
                                                                </span>
                                                            </div>
                                                            
                                                            <!-- Failed Emails -->
                                                            @if($emailsFailed > 0)
                                                                <div class="flex items-center justify-between">
                                                                    <span class="text-gray-600">❌ Failed:</span>
                                                                    <span class="font-medium text-red-600">{{ $emailsFailed }}</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <!-- Response Metrics Section -->
                                                    @if($actualResponseCount > 0)
                                                        <div class="bg-green-50 rounded-lg p-3 space-y-2">
                                                            <div class="text-xs font-medium text-green-700 uppercase tracking-wide">Supplier Responses</div>
                                                            
                                                            <div class="space-y-1">
                                                                <div class="flex items-center justify-between text-xs">
                                                                    <span class="text-green-600">💬 Total Responses:</span>
                                                                    <span class="font-medium text-green-700">{{ $quotedCount }}</span>
                                                                </div>
                                                                
                                                                @if($interestedCount > 0)
                                                                    <div class="flex items-center justify-between text-xs">
                                                                        <span class="text-green-600">⭐ Interested:</span>
                                                                        <span class="font-medium text-green-700">{{ $interestedCount }}</span>
                                                                    </div>
                                                                @endif
                                                                
                                                                @if($quotedCount > 0)
                                                                    <div class="flex items-center justify-between text-xs">
                                                                        <span class="text-blue-600">💰 Quoted:</span>
                                                                        <span class="font-medium text-blue-700">{{ $quotedCount }}</span>
                                                                    </div>
                                                                @endif
                                                                
                                                                @if($notInterestedCount > 0)
                                                                    <div class="flex items-center justify-between text-xs">
                                                                        <span class="text-orange-600">🚫 Not Interested:</span>
                                                                        <span class="font-medium text-orange-700">{{ $notInterestedCount }}</span>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="bg-yellow-50 rounded-lg p-3">
                                                            <div class="text-xs font-medium text-yellow-700 uppercase tracking-wide">Response Status</div>
                                                            <div class="text-xs text-yellow-600 mt-1">⏳ Awaiting supplier responses</div>
                                                        </div>
                                                    @endif
                                                @else
                                                    <div class="bg-gray-100 rounded-lg p-3">
                                                        <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Status</div>
                                                        <div class="text-xs text-gray-600 mt-1">📤 Not broadcast yet</div>
                                                    </div>
                                                @endif
                                            </div>
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                            <div>{{ formatDubaiDate($inquiry->created_at, 'M j, Y') }}</div>
                                            <div class="text-gray-500 text-xs">{{ formatDubaiDate($inquiry->created_at, 'g:i A') }}</div>
                                        </td>
                                        <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                            <div class="flex items-center justify-end gap-2">
                                                <a href="{{ route('admin.inquiries.show', $inquiry) }}" 
                                                   class="inline-flex items-center rounded px-2 py-1 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200">
                                                    <i class="fas fa-eye mr-1"></i>View
                                                </a>

                                                @if($inquiry->status === 'pending')
                                                    <form action="{{ route('admin.inquiries.status.update', $inquiry) }}" method="POST" class="inline-block">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="status" value="broadcast">
                                                        <button type="submit" 
                                                                class="inline-flex items-center rounded px-2.5 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                            <i class="fas fa-broadcast-tower mr-1"></i>Broadcast
                                                        </button>
                                                    </form>
                                                @endif

                                                @if(in_array($inquiry->status, ['pending', 'broadcast', 'in_progress']))
                                                    <form action="{{ route('admin.inquiries.status.update', $inquiry) }}" method="POST" 
                                                          class="inline-block"
                                                          onsubmit="return confirm('Are you sure you want to cancel this inquiry?')">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="status" value="cancelled">
                                                        <button type="submit" 
                                                                class="inline-flex items-center rounded px-2.5 py-1.5 text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                            <i class="fas fa-times mr-1"></i>Cancel
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($inquiries->hasPages())
                        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                            {{ $inquiries->appends(request()->query())->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-6m-4 0H4" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No inquiries found</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            @if(request()->hasAny(['status', 'search']))
                                No inquiries match your current filters.
                            @else
                                You haven't created any supplier inquiries yet.
                            @endif
                        </p>
                        <div class="mt-6">
                            <a href="{{ route('admin.inquiries.create') }}" 
                               class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                                <i class="fas fa-plus mr-2"></i> Create First Inquiry
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Fix dropdown overflow issues in table */
    .table-row-with-dropdown {
        position: relative;
    }
    
    .table-row-with-dropdown:last-child .dropdown-menu,
    .table-row-with-dropdown:nth-last-child(2) .dropdown-menu {
        transform: translateY(-100%);
        top: auto;
        bottom: 100%;
    }
    
    /* Ensure table container doesn't clip dropdowns */
    .table-container {
        overflow: visible !important;
    }
    
    .table-container .overflow-hidden {
        overflow: visible !important;
    }

    /* Loading animation for dynamic updates */
    .status-updating {
        position: relative;
    }
    
    .status-updating::after {
        content: '';
        position: absolute;
        top: 0;
        left: -20px;
        width: 12px;
        height: 12px;
        border: 2px solid #3B82F6;
        border-top: 2px solid transparent;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .status-updated {
        background-color: #FEF3C7;
        transition: background-color 2s ease-out;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let updateInterval;
    let isUpdating = false;
    
    // Function to update inquiry statuses
    async function updateInquiryStatuses() {
        if (isUpdating) return;
        isUpdating = true;
        
        try {
            const response = await fetch('{{ route("admin.inquiries.status-updates") }}', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                updateStatusDisplays(data.inquiries);
            }
        } catch (error) {
            console.error('Error updating statuses:', error);
        } finally {
            isUpdating = false;
        }
    }
    
    // Function to update the status displays in the UI
    function updateStatusDisplays(inquiries) {
        inquiries.forEach(inquiry => {
            const statusElement = document.querySelector(`[data-inquiry-id="${inquiry.id}"] .status-display`);
            if (statusElement) {
                const currentContent = statusElement.innerHTML;
                const newContent = generateStatusHTML(inquiry);
                
                if (currentContent !== newContent) {
                    // Add updating animation
                    statusElement.classList.add('status-updating');
                    
                    setTimeout(() => {
                        statusElement.innerHTML = newContent;
                        statusElement.classList.remove('status-updating');
                        statusElement.classList.add('status-updated');
                        
                        // Remove the highlight after 2 seconds
                        setTimeout(() => {
                            statusElement.classList.remove('status-updated');
                        }, 2000);
                    }, 500);
                }
            }
        });
    }
    
    // Function to generate status HTML
    function generateStatusHTML(inquiry) {
        let html = '';

        // Email Metrics Section
        if (inquiry.total_suppliers > 0) {
            html += `<div class="bg-blue-50 rounded-lg p-3 space-y-2">
                <div class="text-xs font-medium text-blue-700 uppercase tracking-wide">Email Metrics</div>
                <div class="space-y-1">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-blue-600">📧 Sent:</span>
                        <span class="font-medium text-blue-700">${inquiry.emails_sent}/${inquiry.total_suppliers}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-blue-600">👁️ Viewed:</span>
                        <span class="font-medium text-blue-700">${inquiry.emails_clicked}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-blue-600">❌ Failed:</span>
                        <span class="font-medium text-blue-700">${inquiry.emails_failed}</span>
                    </div>
                </div>
            </div>`;
        }

        // Response Metrics Section
        if (inquiry.quoted > 0) {
            html += `<div class="bg-green-50 rounded-lg p-3 space-y-2">
                <div class="text-xs font-medium text-green-700 uppercase tracking-wide">Supplier Responses</div>
                <div class="space-y-1">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-green-600">💬 Total Responses:</span>
                        <span class="font-medium text-green-700">${inquiry.quoted}</span>
                    </div>`;
            
            if (inquiry.interested > 0) {
                html += `<div class="flex items-center justify-between text-xs">
                    <span class="text-green-600">⭐ Interested:</span>
                    <span class="font-medium text-green-700">${inquiry.interested}</span>
                </div>`;
            }
            
            if (inquiry.quoted > 0) {
                html += `<div class="flex items-center justify-between text-xs">
                    <span class="text-blue-600">💰 Quoted:</span>
                    <span class="font-medium text-blue-700">${inquiry.quoted}</span>
                </div>`;
            }
            
            if (inquiry.not_interested > 0) {
                html += `<div class="flex items-center justify-between text-xs">
                    <span class="text-orange-600">🚫 Not Interested:</span>
                    <span class="font-medium text-orange-700">${inquiry.not_interested}</span>
                </div>`;
            }
            
            html += '</div></div>';
        } else {
            html += `<div class="bg-yellow-50 rounded-lg p-3">
                <div class="text-xs font-medium text-yellow-700 uppercase tracking-wide">Response Status</div>
                <div class="text-xs text-yellow-600 mt-1">⏳ Awaiting supplier responses</div>
            </div>`;
        }

        return html;
    }
    
    // Start auto-refresh only if there are broadcast inquiries with pending clicks
    const hasBroadcastInquiries = document.querySelector('[data-has-broadcast-inquiries]');
    if (hasBroadcastInquiries && hasBroadcastInquiries.dataset.hasBroadcastInquiries === 'true') {
        // Update every 10 seconds
        updateInterval = setInterval(updateInquiryStatuses, 10000);
        
        // Add indicator that auto-refresh is active
        const refreshIndicator = document.createElement('div');
        refreshIndicator.className = 'fixed bottom-4 right-4 bg-indigo-600 text-white px-3 py-2 rounded-lg shadow-lg text-sm z-50';
        refreshIndicator.innerHTML = '<i class="fas fa-sync-alt mr-1 animate-spin"></i> Auto-refreshing email stats...';
        document.body.appendChild(refreshIndicator);
        
        // Remove indicator when leaving page
        window.addEventListener('beforeunload', () => {
            if (updateInterval) {
                clearInterval(updateInterval);
            }
            refreshIndicator.remove();
        });
    }
    
    // Initial update
    updateInquiryStatuses();
});
</script>
@endpush 