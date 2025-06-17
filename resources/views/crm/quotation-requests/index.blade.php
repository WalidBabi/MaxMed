@extends('layouts.crm')

@section('title', 'Quotation Requests')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Quotation Requests</h1>
                <p class="text-gray-600">Manage quotation requests and convert high-potential ones to leads</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('crm.contact-submissions.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium flex items-center">
                    <i class="fas fa-envelope mr-2"></i>Contact Submissions
                </a>
                <a href="{{ route('crm.leads.index') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md font-medium flex items-center">
                    <i class="fas fa-users mr-2"></i>View All Leads
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-file-alt text-blue-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Requests</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Pending</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['pending'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Urgent</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['urgent'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-green-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">High Value</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['high_value'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Product, customer, requirements..." 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="forwarded" {{ request('status') == 'forwarded' ? 'selected' : '' }}>Forwarded</option>
                        <option value="supplier_responded" {{ request('status') == 'supplier_responded' ? 'selected' : '' }}>Supplier Responded</option>
                        <option value="quote_created" {{ request('status') == 'quote_created' ? 'selected' : '' }}>Quote Created</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Timeline</label>
                    <select name="delivery_timeline" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Timelines</option>
                        <option value="urgent" {{ request('delivery_timeline') == 'urgent' ? 'selected' : '' }}>Urgent (1-2 weeks)</option>
                        <option value="standard" {{ request('delivery_timeline') == 'standard' ? 'selected' : '' }}>Standard (3-4 weeks)</option>
                        <option value="flexible" {{ request('delivery_timeline') == 'flexible' ? 'selected' : '' }}>Flexible (1-2 months)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Special Filters</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="checkbox" name="high_value" value="1" {{ request('high_value') ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600">
                            <span class="ml-2 text-sm text-gray-600">High Value (10+ qty)</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="urgent" value="1" {{ request('urgent') ? 'checked' : '' }} class="rounded border-gray-300 text-red-600">
                            <span class="ml-2 text-sm text-gray-600">Urgent Timeline</span>
                        </label>
                    </div>
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium flex items-center justify-center">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Quotation Requests Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Recent Quotation Requests</h3>
        </div>
        
        @if($quotationRequests->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product & Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request Details</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority Indicators</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($quotationRequests as $request)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-box text-blue-600"></i>
                                    </div>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="text-sm font-medium text-gray-900">{{ $request->product->name }}</div>
                                    <div class="text-xs text-gray-500">SKU: {{ $request->product->sku }}</div>
                                    
                                    @if($request->user)
                                        <div class="text-sm text-gray-600 mt-1">{{ $request->user->name }}</div>
                                        <div class="text-xs text-gray-400">{{ $request->user->email }}</div>
                                    @else
                                        <div class="text-sm text-orange-600 mt-1">Guest Customer</div>
                                        @if($request->relatedContactSubmission)
                                            <div class="text-xs text-gray-600">{{ $request->relatedContactSubmission->name }}</div>
                                            <div class="text-xs text-gray-400">{{ $request->relatedContactSubmission->email }}</div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </td>
                        
                        <td class="px-6 py-4">
                            <div class="space-y-1">
                                <div class="text-sm font-medium text-gray-900">Qty: {{ number_format($request->quantity) }}</div>
                                @if($request->size)
                                    <div class="text-xs text-gray-500">Size: {{ $request->size }}</div>
                                @endif
                                @if($request->delivery_timeline)
                                    <div class="text-xs {{ $request->delivery_timeline == 'urgent' ? 'text-red-600 font-medium' : 'text-gray-500' }}">
                                        Timeline: {{ ucfirst($request->delivery_timeline) }}
                                    </div>
                                @endif
                                @if($request->requirements)
                                    <div class="text-xs text-gray-500 max-w-xs truncate">
                                        Requirements: {{ Str::limit($request->requirements, 50) }}
                                    </div>
                                @endif
                            </div>
                        </td>
                        
                        <td class="px-6 py-4">
                            <div class="space-y-1">
                                @if($request->delivery_timeline == 'urgent')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>Urgent
                                    </span>
                                @endif
                                
                                @if($request->quantity >= 10)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-dollar-sign mr-1"></i>High Value
                                    </span>
                                @endif
                                
                                @if($request->relatedContactSubmission && $request->relatedContactSubmission->lead_potential)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                        {{ $request->relatedContactSubmission->lead_potential == 'hot' ? 'bg-red-100 text-red-800' : 
                                           ($request->relatedContactSubmission->lead_potential == 'warm' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                                        <i class="fas fa-thermometer-half mr-1"></i>{{ ucfirst($request->relatedContactSubmission->lead_potential) }} Lead
                                    </span>
                                @endif
                                
                                @if($request->relatedContactSubmission && $request->relatedContactSubmission->company)
                                    <div class="text-xs text-blue-600 font-medium">
                                        🏢 {{ $request->relatedContactSubmission->company }}
                                    </div>
                                @endif
                            </div>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $request->status_badge_class }}">
                                {{ $request->formatted_status }}
                            </span>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                                    {{ $request->created_at ? $request->created_at->format('M j, Y') : 'N/A' }}
                                        <div class="text-xs text-gray-400">{{ $request->created_at ? $request->created_at->format('g:i A') : '' }}</div>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('crm.quotation-requests.show', $request) }}" 
                                   class="text-indigo-600 hover:text-indigo-900">View</a>
                                   
                                @if($request->status == 'pending')
                                    <span class="text-gray-300">|</span>
                                    <a href="{{ route('crm.quotation-requests.show', $request) }}#convert-to-lead" 
                                       class="text-green-600 hover:text-green-900">Convert to Lead</a>
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
            {{ $quotationRequests->appends(request()->query())->links() }}
        </div>
        @else
        <div class="px-6 py-12 text-center">
            <div class="text-gray-500">
                <i class="fas fa-file-invoice text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No quotation requests found</h3>
                <p class="text-sm">Quotation requests from your forms will appear here for CRM management.</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection 