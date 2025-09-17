@extends('layouts.crm')
@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('title', 'Lead Details - ' . $lead->full_name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-start">
                <div>
                    @php
                        $isPurchasingRole = Auth::user()->hasAnyRole(['purchasing', 'purchasing_manager', 'purchasing_crm_assistant']);
                    @endphp
                    
                    @if($isPurchasingRole)
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">Lead Requirements</h1>
                        <p class="text-gray-600">View requirements and attachments for this lead</p>
                    @else
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $lead->full_name }}</h1>
                        <p class="text-gray-600">{{ $lead->company_name }} • {{ $lead->job_title }}</p>
                    @endif
                </div>
                <div class="flex space-x-3">
                    @if(!$isPurchasingRole)
                    <a href="{{ route('crm.leads.edit', $lead) }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Edit Lead
                    </a>
                    @endif
                    <a href="{{ route('crm.leads.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Back to Leads
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">

                @if(!$isPurchasingRole)
                <!-- Contact Details -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">First Name</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $lead->first_name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Last Name</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $lead->last_name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Email</label>
                            <p class="mt-1 text-sm text-gray-900">
                                <a href="mailto:{{ $lead->email }}" class="text-blue-600 hover:text-blue-800">{{ $lead->email }}</a>
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Mobile</label>
                            <p class="mt-1 text-sm text-gray-900">
                                @if($lead->mobile)
                                    <a href="tel:{{ $lead->mobile }}" class="text-blue-600 hover:text-blue-800">{{ $lead->mobile }}</a>
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Phone</label>
                            <p class="mt-1 text-sm text-gray-900">
                                @if($lead->phone)
                                    <a href="tel:{{ $lead->phone }}" class="text-blue-600 hover:text-blue-800">{{ $lead->phone }}</a>
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Company Information -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Company Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Company Name</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $lead->company_name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Job Title</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $lead->job_title ?: '-' }}</p>
                        </div>
                    </div>
                    @if($lead->company_address)
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-500">Company Address</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $lead->company_address }}</p>
                    </div>
                    @endif
                </div>
                @endif

                @if(!$isPurchasingRole)
                <!-- Lead Details -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Lead Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status</label>
                            <span class="mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-{{ $lead->status_color }}-100 text-{{ $lead->status_color }}-800">
                                {{ ucfirst($lead->status) }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Priority</label>
                            <span class="mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-{{ $lead->priority_color }}-100 text-{{ $lead->priority_color }}-800">
                                {{ ucfirst($lead->priority) }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Source</label>
                            <p class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $lead->source)) }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Assigned To</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $lead->assignedUser->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Estimated Value</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $lead->estimated_value ? 'AED ' . number_format($lead->estimated_value, 2) : '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Expected Close Date</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $lead->expected_close_date ? $lead->expected_close_date->format('M j, Y') : '-' }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Notes Section (Always visible for purchasing roles) -->
                @if($lead->notes)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Notes</h3>
                    <div class="text-sm text-gray-900 formatted-content">{!! nl2br(strip_tags($lead->notes, '<p><br><strong><b><em><i><u><ul><ol><li><a><span><div>')) !!}</div>
                </div>
                @endif

                @if(!$isPurchasingRole)
                <!-- Activity Timeline -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-900">Activity Timeline</h3>
                            <button onclick="toggleActivityForm()" 
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add Activity
                            </button>
                        </div>
                    </div>

                    <!-- Activity Form -->
                    <div id="activityForm" class="hidden border-b border-gray-200 p-6">
                        <form action="{{ route('crm.leads.activity.add', $lead) }}" method="POST">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Activity Type</label>
                                    <select name="type" id="type" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="call">Phone Call</option>
                                        <option value="email">Email</option>
                                        <option value="meeting">Meeting</option>
                                        <option value="note">Note</option>
                                        <option value="quote_sent">Quote Sent</option>
                                        <option value="demo">Demo</option>
                                        <option value="follow_up">Follow-up</option>
                                        <option value="task">Task</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                    <select name="status" id="status" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="completed">Completed</option>
                                        <option value="scheduled">Scheduled</option>
                                    </select>
                                </div>
                                <div class="col-span-2">
                                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                                    <input type="text" name="subject" id="subject" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div class="col-span-2">
                                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                    <textarea name="description" id="description" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                                </div>
                                <div>
                                    <label for="activity_date" class="block text-sm font-medium text-gray-700 mb-1">Date & Time</label>
                                    <input type="datetime-local" name="activity_date" id="activity_date" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </div>
                            <div class="mt-4 flex justify-end">
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    Add Activity
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Activities List -->
                    <div class="divide-y divide-gray-200">
                        @forelse($lead->activities as $activity)
                            <div class="p-6">
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-{{ $activity->type_color }}-100">
                                            <span class="text-{{ $activity->type_color }}-600 text-sm font-medium">
                                                {{ strtoupper(substr($activity->type, 0, 1)) }}
                                            </span>
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-medium text-gray-900">{{ $activity->subject }}</p>
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-{{ $activity->status_color }}-100 text-{{ $activity->status_color }}-800">
                                                {{ ucfirst($activity->status) }}
                                            </span>
                                        </div>
                                        @if($activity->description)
                                            <p class="mt-1 text-sm text-gray-600">{{ $activity->description }}</p>
                                        @endif
                                        <div class="mt-2 flex items-center text-xs text-gray-500">
                                            <span class="font-medium text-{{ $activity->type_color }}-600 mr-2">{{ ucfirst(str_replace('_', ' ', $activity->type)) }}</span>
                                            <span class="mr-2">•</span>
                                            <span>{{ $activity->activity_date ? $activity->activity_date->format('M j, Y g:i A') : 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-6 text-center text-gray-500">
                                No activities recorded yet.
                            </div>
                        @endforelse
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                @if(!$isPurchasingRole)
                <!-- Lead Status -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Lead Status</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-500">Status</span>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-{{ $lead->status_color }}-100 text-{{ $lead->status_color }}-800">
                                {{ ucfirst($lead->status) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-500">Priority</span>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-{{ $lead->priority_color }}-100 text-{{ $lead->priority_color }}-800">
                                {{ ucfirst($lead->priority) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-500">Source</span>
                            <span class="text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $lead->source)) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-500">Assigned To</span>
                            <span class="text-sm text-gray-900">{{ $lead->assignedUser->name }}</span>
                        </div>
                        @if($lead->estimated_value)
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-500">Est. Value</span>
                            <span class="text-sm text-gray-900">AED {{ number_format($lead->estimated_value, 2) }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Key Dates -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Key Dates</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-500">Created</span>
                            <span class="text-sm text-gray-900">{{ $lead->created_at->format('M j, Y') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-500">Last Contact</span>
                            <span class="text-sm text-gray-900">
                                @if($lead->last_contacted_at)
                                    {{ $lead->last_contacted_at->format('M j, Y') }}
                                    @if($lead->isOverdue())
                                        <span class="text-red-600 ml-1">⚠️</span>
                                    @endif
                                @else
                                    <span class="text-red-600">Never</span>
                                @endif
                            </span>
                        </div>
                        @if($lead->expected_close_date)
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-500">Expected Close</span>
                            <span class="text-sm text-gray-900">{{ $lead->expected_close_date->format('M j, Y') }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Requirements Attachments -->
                @if($lead->hasAttachments())
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Requirements Attachments</h3>
                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                            {{ $lead->getAttachmentCount() }} {{ $lead->getAttachmentCount() === 1 ? 'file' : 'files' }}
                        </span>
                    </div>

                    @php
                        $attachmentsByType = $lead->getAttachmentsByType();
                    @endphp

                    <!-- Images Section -->
                    @if(!empty($attachmentsByType['images']))
                    <div class="mb-6">
                        <h4 class="text-md font-medium text-gray-700 mb-3 flex items-center">
                            <span class="mr-2">🖼️</span>
                            Images ({{ count($attachmentsByType['images']) }})
                        </h4>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            @foreach($attachmentsByType['images'] as $attachment)
                            @if(Storage::disk('public')->exists($attachment['path']))
                            <div class="relative group">
                                <div class="aspect-square bg-gray-200 rounded-lg overflow-hidden">
                                    <img src="{{ Storage::url($attachment['path']) }}" 
                                         alt="{{ $attachment['original_name'] }}"
                                         class="w-full h-full object-cover cursor-pointer hover:scale-105 transition-transform"
                                         onclick="openImageModal('{{ Storage::url($attachment['path']) }}', '{{ $attachment['original_name'] }}')">
                                </div>
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all rounded-lg flex items-center justify-center">
                                    <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                                        <a href="{{ Storage::url($attachment['path']) }}" target="_blank" 
                                           class="bg-white text-gray-800 px-3 py-1 rounded-md text-sm font-medium shadow-lg hover:bg-gray-100">
                                            View
                                        </a>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <p class="text-xs text-gray-600 truncate">{{ $attachment['original_name'] }}</p>
                                    <p class="text-xs text-gray-400">
                                        @if(isset($attachment['size']))
                                            {{ number_format($attachment['size'] / 1024, 1) }} KB
                                        @endif
                                    </p>
                                </div>
                            </div>
                            @else
                            <div class="relative group">
                                <div class="aspect-square bg-red-100 rounded-lg overflow-hidden flex items-center justify-center">
                                    <div class="text-center">
                                        <span class="text-red-400 text-2xl">❌</span>
                                        <p class="text-xs text-red-600 mt-1">File Missing</p>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <p class="text-xs text-red-600 truncate">{{ $attachment['original_name'] }}</p>
                                    <p class="text-xs text-red-400">File not found</p>
                                </div>
                            </div>
                            @endif
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- PDFs Section -->
                    @if(!empty($attachmentsByType['pdfs']))
                    <div class="mb-6">
                        <h4 class="text-md font-medium text-gray-700 mb-3 flex items-center">
                            <span class="mr-2">📄</span>
                            PDF Documents ({{ count($attachmentsByType['pdfs']) }})
                        </h4>
                        <div class="space-y-2">
                            @foreach($attachmentsByType['pdfs'] as $attachment)
                            @if(Storage::disk('public')->exists($attachment['path']))
                            <div class="flex items-center justify-between p-3 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors">
                                <div class="flex items-center min-w-0">
                                    <span class="text-2xl mr-3">📄</span>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $attachment['original_name'] }}</p>
                                        <p class="text-xs text-gray-500">
                                            @if(isset($attachment['size']))
                                                {{ number_format($attachment['size'] / 1024, 1) }} KB
                                            @endif
                                            • {{ \Carbon\Carbon::parse($attachment['uploaded_at'])->format('M j, Y') }}
                                        </p>
                                    </div>
                                </div>
                                <a href="{{ Storage::url($attachment['path']) }}" target="_blank" 
                                   class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-md text-sm font-medium">
                                    View PDF
                                </a>
                            </div>
                            @else
                            <div class="flex items-center justify-between p-3 bg-red-100 border border-red-300 rounded-lg">
                                <div class="flex items-center min-w-0">
                                    <span class="text-2xl mr-3">❌</span>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-red-700 truncate">{{ $attachment['original_name'] }}</p>
                                        <p class="text-xs text-red-500">File not found</p>
                                    </div>
                                </div>
                                <span class="bg-red-200 text-red-800 px-3 py-1 rounded-md text-sm font-medium">
                                    Missing
                                </span>
                            </div>
                            @endif
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Documents Section -->
                    @if(!empty($attachmentsByType['documents']))
                    <div class="mb-6">
                        <h4 class="text-md font-medium text-gray-700 mb-3 flex items-center">
                            <span class="mr-2">📝</span>
                            Word Documents ({{ count($attachmentsByType['documents']) }})
                        </h4>
                        <div class="space-y-2">
                            @foreach($attachmentsByType['documents'] as $attachment)
                            <div class="flex items-center justify-between p-3 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors">
                                <div class="flex items-center min-w-0">
                                    <span class="text-2xl mr-3">📝</span>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $attachment['original_name'] }}</p>
                                        <p class="text-xs text-gray-500">
                                            @if(isset($attachment['size']))
                                                {{ number_format($attachment['size'] / 1024, 1) }} KB
                                            @endif
                                            • {{ \Carbon\Carbon::parse($attachment['uploaded_at'])->format('M j, Y') }}
                                        </p>
                                    </div>
                                </div>
                                <a href="{{ Storage::url($attachment['path']) }}" download 
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-md text-sm font-medium">
                                    Download
                                </a>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Other Files Section -->
                    @if(!empty($attachmentsByType['others']))
                    <div class="mb-6">
                        <h4 class="text-md font-medium text-gray-700 mb-3 flex items-center">
                            <span class="mr-2">📎</span>
                            Other Files ({{ count($attachmentsByType['others']) }})
                        </h4>
                        <div class="space-y-2">
                            @foreach($attachmentsByType['others'] as $attachment)
                            <div class="flex items-center justify-between p-3 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="flex items-center min-w-0">
                                    <span class="text-2xl mr-3">📎</span>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $attachment['original_name'] }}</p>
                                        <p class="text-xs text-gray-500">
                                            @if(isset($attachment['size']))
                                                {{ number_format($attachment['size'] / 1024, 1) }} KB
                                            @endif
                                            • {{ \Carbon\Carbon::parse($attachment['uploaded_at'])->format('M j, Y') }}
                                        </p>
                                    </div>
                                </div>
                                <a href="{{ Storage::url($attachment['path']) }}" download 
                                   class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-1 rounded-md text-sm font-medium">
                                    Download
                                </a>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
                @endif

                <!-- Deal Conversion -->
                @if(in_array($lead->status, ['qualified', 'proposal']) && $lead->deals->count() === 0)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Convert to Deal</h3>
                    <p class="text-sm text-gray-600 mb-4">This qualified lead is ready to be converted to a formal deal.</p>
                    <button onclick="toggleDealForm()" 
                            class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md font-medium">
                        <i class="fas fa-handshake mr-2"></i>Create Deal
                    </button>
                    
                    <!-- Deal Creation Form -->
                    <div id="dealForm" class="hidden mt-4 border-t pt-4">
                        <form action="{{ route('crm.leads.convert-to-deal', $lead) }}" method="POST">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label for="deal_name" class="block text-sm font-medium text-gray-700 mb-1">Deal Name</label>
                                    <input type="text" name="deal_name" id="deal_name" required 
                                           value="{{ $lead->company_name }} - Equipment Purchase"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="deal_value" class="block text-sm font-medium text-gray-700 mb-1">Deal Value (AED)</label>
                                        <input type="number" name="deal_value" id="deal_value" required min="0" step="0.01"
                                               value="{{ $lead->estimated_value }}"
                                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                    </div>
                                    <div>
                                        <label for="expected_close_date" class="block text-sm font-medium text-gray-700 mb-1">Expected Close Date</label>
                                        <input type="date" name="expected_close_date" id="expected_close_date" required
                                               value="{{ $lead->expected_close_date ? $lead->expected_close_date->format('Y-m-d') : now()->addDays(30)->format('Y-m-d') }}"
                                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                    </div>
                                </div>
                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deal Description</label>
                                    <textarea name="description" id="description" rows="3"
                                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                                              placeholder="Describe the deal, products interested, specific requirements...">{{ $lead->notes }}</textarea>
                                </div>
                                <div class="flex justify-end space-x-3">
                                    <button type="button" onclick="toggleDealForm()" 
                                            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                        Cancel
                                    </button>
                                    <button type="submit" 
                                            class="px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                                        Create Deal
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                @endif

                <!-- Supplier Quotation Process -->
                @if(in_array($lead->status, ['contacted', 'qualified']) && !$lead->hasActiveQuotationRequest())
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Get Supplier Pricing</h3>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    Create a quotation request to get pricing from our suppliers.
                                </p>
                            </div>
                        </div>
                    </div>
                    <!-- Removed Create Quotation Request button as the route no longer exists -->
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.formatted-content {
    line-height: 1.6;
}

.formatted-content p {
    margin-bottom: 0.75rem;
}

.formatted-content p:last-child {
    margin-bottom: 0;
}

.formatted-content strong,
.formatted-content b {
    font-weight: 600;
    color: #1f2937;
}

.formatted-content em,
.formatted-content i {
    font-style: italic;
}

.formatted-content u {
    text-decoration: underline;
}

.formatted-content ul,
.formatted-content ol {
    margin-left: 1.5rem;
    margin-bottom: 0.75rem;
}

.formatted-content ul {
    list-style-type: disc;
}

.formatted-content ol {
    list-style-type: decimal;
}

.formatted-content li {
    margin-bottom: 0.25rem;
}

.formatted-content a {
    color: #3b82f6;
    text-decoration: underline;
}

.formatted-content a:hover {
    color: #1d4ed8;
}

.formatted-content div {
    margin-bottom: 0.5rem;
}
</style>
@endpush

@push('scripts')
<script>
    function toggleActivityForm() {
        const form = document.getElementById('activityForm');
        form.classList.toggle('hidden');
    }

    function toggleDealForm() {
        const form = document.getElementById('dealForm');
        form.classList.toggle('hidden');
    }
</script>
@endpush

@endsection 