@extends('layouts.app')

@section('title', 'Inquiry Management')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Inquiry Management</h1>
                <p class="text-gray-600">Manage customer quotation requests and supplier workflow</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.inquiries.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
                    <i class="fas fa-plus mr-2"></i>Add Manual Inquiry
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Product name, customer..." 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending Review</option>
                        <option value="forwarded" {{ request('status') == 'forwarded' ? 'selected' : '' }}>Forwarded to Supplier</option>
                        <option value="supplier_responded" {{ request('status') == 'supplier_responded' ? 'selected' : '' }}>Supplier Responded</option>
                        <option value="quote_created" {{ request('status') == 'quote_created' ? 'selected' : '' }}>Quote Created</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Supplier Response</label>
                    <select name="supplier_response" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Responses</option>
                        <option value="pending" {{ request('supplier_response') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="available" {{ request('supplier_response') == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="not_available" {{ request('supplier_response') == 'not_available' ? 'selected' : '' }}>Not Available</option>
                    </select>
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md font-medium">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Inquiries Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Inquiries ({{ $inquiries->total() }})</h3>
        </div>
        
        @if($inquiries->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($inquiries as $inquiry)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $inquiry->product->name }}</div>
                                    <div class="text-sm text-gray-500">SKU: {{ $inquiry->product->sku ?? 'N/A' }}</div>
                                    @if($inquiry->size)
                                        <div class="text-xs text-blue-600">Size: {{ $inquiry->size }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $inquiry->user->name ?? 'Guest' }}</div>
                            <div class="text-sm text-gray-500">{{ $inquiry->user->email ?? 'No email' }}</div>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ number_format($inquiry->quantity) }}
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $inquiry->status_badge_class }}">
                                {{ $inquiry->formatted_status }}
                            </span>
                            @if($inquiry->supplier_response !== 'pending')
                                <div class="mt-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $inquiry->supplier_response_badge_class }}">
                                        {{ ucfirst(str_replace('_', ' ', $inquiry->supplier_response)) }}
                                    </span>
                                </div>
                            @endif
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $inquiry->supplier->name ?? 'Not assigned' }}
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ formatDubaiDate($inquiry->created_at, 'M j, Y') }}
                            <div class="text-xs text-gray-400">{{ formatDubaiDate($inquiry->created_at, 'g:i A') }}</div>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.inquiries.show', $inquiry) }}" 
                                   class="text-blue-600 hover:text-blue-900">View</a>
                                   
                                @if($inquiry->status === 'pending')
                                    <span class="text-gray-300">|</span>
                                    <button type="button" onclick="showForwardModal({{ $inquiry->id }})" 
                                            class="text-green-600 hover:text-green-900">Forward</button>
                                @endif
                                
                                @if($inquiry->status === 'supplier_responded' && $inquiry->supplier_response === 'available')
                                    <span class="text-gray-300">|</span>
                                    <a href="{{ route('admin.inquiries.show', $inquiry) }}#generate-quote" 
                                       class="text-purple-600 hover:text-purple-900">Generate Quote</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $inquiries->appends(request()->query())->links() }}
        </div>
        @else
        <div class="px-6 py-12 text-center">
            <div class="text-gray-500">
                <i class="fas fa-inbox text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No inquiries found</h3>
                <p class="text-sm">Customer quotation requests will appear here for workflow management.</p>
                <div class="mt-6">
                    <a href="{{ route('products.index') }}" class="text-blue-600 hover:text-blue-800">
                        View products to see how customers can request quotes →
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Forward to Supplier Modal (simplified for demo) -->
<div id="forwardModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Forward to Supplier</h3>
        <form id="forwardForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Supplier</label>
                <select name="supplier_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Select a supplier...</option>
                    <!-- Suppliers will be loaded here -->
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Internal Notes</label>
                <textarea name="internal_notes" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Add any internal notes..."></textarea>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeForwardModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                    Forward to Supplier
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showForwardModal(inquiryId) {
    document.getElementById('forwardModal').classList.remove('hidden');
    document.getElementById('forwardForm').action = `/admin/inquiries/${inquiryId}/forward-to-supplier`;
}

function closeForwardModal() {
    document.getElementById('forwardModal').classList.add('hidden');
}
</script>
@endsection 