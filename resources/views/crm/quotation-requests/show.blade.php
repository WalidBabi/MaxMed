@extends('layouts.crm')

@section('title', 'Quotation Request Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('crm.quotation-requests.index') }}" class="text-indigo-600 hover:text-indigo-800">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h1 class="text-3xl font-bold text-gray-900">Quotation Request Details</h1>
                </div>
                <p class="text-gray-600 mt-2">View and manage quotation request</p>
            </div>
            <div class="flex space-x-3">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $quotationRequest->status_badge_class }}">
                    {{ $quotationRequest->formatted_status }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Product Information -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Product Information</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Product</label>
                            <div class="mt-1 text-sm text-gray-900">{{ $quotationRequest->product->name }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">SKU</label>
                            <div class="mt-1 text-sm text-gray-900">{{ $quotationRequest->product->sku }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Quantity Requested</label>
                            <div class="mt-1 text-sm text-gray-900">{{ $quotationRequest->quantity }}</div>
                        </div>
                        @if($quotationRequest->size)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Size</label>
                            <div class="mt-1 text-sm text-gray-900">{{ $quotationRequest->size }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Convert to Lead Action -->
            @if(in_array($quotationRequest->status, ['pending', 'forwarded', 'supplier_responded']))
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Convert to Lead</h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('crm.quotation-requests.convert-to-lead', $quotationRequest) }}" method="POST" class="space-y-4">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                         <div>
                                 <label class="block text-sm font-medium text-gray-700 mb-1">Lead Source</label>
                                 <select name="lead_source" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                     <option value="">Select Source</option>
                                     <option value="website">Quotation Request</option>
                                     <option value="linkedin">LinkedIn</option>
                                     <option value="email">Email</option>
                                     <option value="phone">Phone</option>
                                     <option value="referral">Referral</option>
                                     <option value="google_ads">Google Ads</option>
                                     <option value="other">Other</option>
                                 </select>
                             </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Lead Status</label>
                                <select name="lead_status" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="new">New</option>
                                    <option value="contacted">Contacted</option>
                                    <option value="qualified">Qualified</option>
                                    <option value="proposal">Proposal</option>
                                    <option value="negotiation">Negotiation</option>
                                    <option value="won">Won</option>
                                    <option value="lost">Lost</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                                <select name="priority" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="medium">Medium</option>
                                    <option value="low">Low</option>
                                    <option value="high">High</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Estimated Value (AED)</label>
                                <input type="number" name="estimated_value" step="0.01" min="0" 
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                            <textarea name="notes" rows="3" 
                                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                      placeholder="Additional notes for the lead..."></textarea>
                        </div>
                        
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium">
                            <i class="fas fa-user-plus mr-2"></i>Convert to Lead
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
        
        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Status Update -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Update Status</h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('crm.quotation-requests.status.update', $quotationRequest) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="pending" {{ $quotationRequest->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="forwarded" {{ $quotationRequest->status == 'forwarded' ? 'selected' : '' }}>Forwarded</option>
                                <option value="supplier_responded" {{ $quotationRequest->status == 'supplier_responded' ? 'selected' : '' }}>Supplier Responded</option>
                                <option value="quote_created" {{ $quotationRequest->status == 'quote_created' ? 'selected' : '' }}>Quote Created</option>
                                <option value="completed" {{ $quotationRequest->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ $quotationRequest->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md font-medium">
                            Update Status
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
