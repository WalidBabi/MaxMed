@extends('layouts.crm')

@section('title', 'Contact Submissions')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Contact Submissions</h1>
                <p class="text-gray-600">Convert contact form submissions to leads and manage customer inquiries</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('crm.leads.index') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md font-medium flex items-center">
                    <i class="fas fa-users mr-2"></i>View All Leads
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
                           placeholder="Name, email, company..." 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Statuses</option>
                        <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>New</option>
                        <option value="in_review" {{ request('status') == 'in_review' ? 'selected' : '' }}>In Review</option>
                        <option value="converted_to_lead" {{ request('status') == 'converted_to_lead' ? 'selected' : '' }}>Converted to Lead</option>
                        <option value="converted_to_inquiry" {{ request('status') == 'converted_to_inquiry' ? 'selected' : '' }}>Converted to Inquiry</option>
                        <option value="responded" {{ request('status') == 'responded' ? 'selected' : '' }}>Responded</option>
                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <select name="sales_only" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Submissions</option>
                        <option value="1" {{ request('sales_only') == '1' ? 'selected' : '' }}>Sales Inquiries Only</option>
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

    <!-- CRM Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-envelope text-blue-600"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">New Submissions</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $submissions->where('status', 'new')->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-shopping-cart text-green-600"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Sales Inquiries</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $submissions->filter(fn($s) => $s->isSalesInquiry())->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-plus text-indigo-600"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Converted to Leads</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $submissions->where('status', 'converted_to_lead')->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-chart-line text-purple-600"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Conversion Rate</p>
                    <p class="text-lg font-semibold text-gray-900">
                        @if($submissions->count() > 0)
                            {{ round(($submissions->where('status', 'converted_to_lead')->count() / $submissions->count()) * 100, 1) }}%
                        @else
                            0%
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Submissions Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Recent Contact Submissions</h3>
        </div>
        
        @if($submissions->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact Info</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject & Message</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lead Potential</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($submissions as $submission)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $submission->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $submission->email }}</div>
                                    @if($submission->phone)
                                        <div class="text-xs text-gray-400">📞 {{ $submission->phone }}</div>
                                    @endif
                                    @if($submission->company)
                                        <div class="text-xs text-blue-600 font-medium">🏢 {{ $submission->company }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $submission->subject }}</div>
                            <div class="text-sm text-gray-500 max-w-xs truncate">{{ Str::limit($submission->message, 80) }}</div>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($submission->isSalesInquiry())
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-star mr-1"></i>High Potential
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <i class="fas fa-info mr-1"></i>General Inquiry
                                </span>
                            @endif
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $submission->status_badge_class }}">
                                {{ $submission->formatted_status }}
                            </span>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                                    {{ $submission->created_at ? $submission->created_at->format('M j, Y') : 'N/A' }}
                                        <div class="text-xs text-gray-400">{{ $submission->created_at ? $submission->created_at->format('g:i A') : '' }}</div>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('crm.contact-submissions.show', $submission) }}" 
                                   class="text-indigo-600 hover:text-indigo-900">View</a>
                                   
                                @if($submission->canConvertToLead())
                                    <span class="text-gray-300">|</span>
                                    <a href="{{ route('crm.contact-submissions.show', $submission) }}#convert-to-lead" 
                                       class="text-green-600 hover:text-green-900">Convert to Lead</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="px-6 py-12 text-center">
            <div class="text-gray-500">
                <i class="fas fa-inbox text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No contact submissions found</h3>
                <p class="text-sm">Contact form submissions will appear here for lead conversion and management.</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection 