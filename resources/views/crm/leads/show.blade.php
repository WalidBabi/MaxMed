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
                        // Use the isPurchasingUser variable passed from controller, 
                        // but keep the old variable name for compatibility
                        $isPurchasingRole = $isPurchasingUser ?? false;
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
                    <div class="text-sm text-gray-900 formatted-content" data-cache-bust="{{ time() }}">{!! \App\Helpers\HtmlSanitizer::sanitizeRichContent($lead->notes) !!}</div>
                    <!-- Debug: Content length: {{ strlen($lead->notes) }} chars, Sanitized: {{ strlen(\App\Helpers\HtmlSanitizer::sanitizeRichContent($lead->notes)) }} chars -->
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

                <!-- Price Submissions (for Admin users) -->
                @if(!$isPurchasingRole)
                    @if($lead->hasPriceSubmissions())
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Price Submissions</h3>
                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                {{ $lead->priceSubmissions->count() }} {{ $lead->priceSubmissions->count() === 1 ? 'submission' : 'submissions' }}
                            </span>
                        </div>
                        
                        <div class="space-y-4 max-h-80 overflow-y-auto">
                            @foreach($lead->priceSubmissions->sortByDesc('created_at') as $submission)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="text-lg font-semibold text-green-600">
                                        {{ $submission->currency }} {{ number_format($submission->price, 2) }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $submission->created_at->format('M j, Y g:i A') }}
                                    </div>
                                </div>
                                <div class="text-sm text-gray-600 mb-1">
                                    Submitted by: <span class="font-medium">{{ $submission->user->name }}</span>
                                </div>
                                @if($submission->notes)
                                    <div class="text-sm text-gray-700 bg-gray-50 p-2 rounded mt-2">{{ $submission->notes }}</div>
                                @endif
                                @if($submission->attachments && count($submission->attachments) > 0)
                                    <div class="mt-2">
                                        <p class="text-xs font-medium text-gray-600 mb-1">Attachments:</p>
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($submission->attachments as $index => $attachment)
                                                <a href="{{ route('crm.price-submissions.download-attachment', [$submission->id, $index]) }}" 
                                                   class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-md hover:bg-blue-200" 
                                                   title="Download {{ $attachment['original_name'] }}">
                                                    📎 {{ $attachment['original_name'] }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
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

@push('head')
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
@endpush

@push('styles')
<style>
.formatted-content {
    line-height: 1.6;
    max-width: 100%;
    word-wrap: break-word;
}

/* Basic text formatting */
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

.formatted-content strike,
.formatted-content del {
    text-decoration: line-through;
}

.formatted-content ins {
    text-decoration: underline;
    background-color: #fef3c7;
}

.formatted-content mark {
    background-color: #fde047;
    padding: 0.125rem 0.25rem;
}

.formatted-content small {
    font-size: 0.875em;
}

.formatted-content sup {
    vertical-align: super;
    font-size: 0.75em;
}

.formatted-content sub {
    vertical-align: sub;
    font-size: 0.75em;
}

/* Headings */
.formatted-content h1 {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
    color: #1f2937;
    border-bottom: 2px solid #e5e7eb;
    padding-bottom: 0.5rem;
}

.formatted-content h2 {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 0.75rem;
    color: #374151;
}

.formatted-content h3 {
    font-size: 1.125rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: #4b5563;
}

.formatted-content h4,
.formatted-content h5,
.formatted-content h6 {
    font-size: 1rem;
    font-weight: 500;
    margin-bottom: 0.5rem;
    color: #6b7280;
}

/* Lists */
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

/* Links */
.formatted-content a {
    color: #3b82f6;
    text-decoration: underline;
}

.formatted-content a:hover {
    color: #1d4ed8;
}

/* Tables */
.formatted-content table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 1rem;
    border: 1px solid #d1d5db;
    background-color: #ffffff;
}

.formatted-content th,
.formatted-content td {
    padding: 0.5rem 0.75rem;
    border: 1px solid #d1d5db;
    text-align: left;
    vertical-align: top;
}

.formatted-content th {
    background-color: #f9fafb;
    font-weight: 600;
    color: #374151;
}

.formatted-content tbody tr:nth-child(even) {
    background-color: #f9fafb;
}

.formatted-content tbody tr:hover {
    background-color: #f3f4f6;
}

/* Blockquotes */
.formatted-content blockquote {
    border-left: 4px solid #3b82f6;
    margin: 1rem 0;
    padding: 0.5rem 1rem;
    background-color: #f8fafc;
    font-style: italic;
    color: #4b5563;
}

/* Code blocks */
.formatted-content pre {
    background-color: #1f2937;
    color: #f9fafb;
    padding: 1rem;
    border-radius: 0.375rem;
    overflow-x: auto;
    margin-bottom: 1rem;
    font-family: 'Courier New', monospace;
    font-size: 0.875rem;
}

.formatted-content code {
    background-color: #f3f4f6;
    color: #1f2937;
    padding: 0.125rem 0.25rem;
    border-radius: 0.25rem;
    font-family: 'Courier New', monospace;
    font-size: 0.875rem;
}

.formatted-content pre code {
    background-color: transparent;
    color: inherit;
    padding: 0;
}

/* Horizontal rules */
.formatted-content hr {
    border: none;
    height: 1px;
    background-color: #d1d5db;
    margin: 1.5rem 0;
}

/* Images */
.formatted-content img {
    max-width: 100%;
    height: auto;
    border-radius: 0.375rem;
    margin: 0.5rem 0;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
}

/* Font styling (Outlook/Word compatibility) */
.formatted-content font {
    /* Preserve font tags from Outlook */
}

.formatted-content font[color] {
    /* Font color will be preserved via color attribute */
}

.formatted-content font[size] {
    /* Font size will be preserved via size attribute */
}

.formatted-content font[face] {
    /* Font family will be preserved via face attribute */
}

/* Center alignment (Outlook compatibility) */
.formatted-content center {
    text-align: center;
    display: block;
    margin: 0.5rem 0;
}

/* Non-breaking elements */
.formatted-content nobr {
    white-space: nowrap;
}

/* Word break opportunities */
.formatted-content wbr::after {
    content: "\00200B"; /* Zero-width space */
}

/* Divs and spans */
.formatted-content div {
    margin-bottom: 0.5rem;
}

.formatted-content span {
    /* Inherit parent styling - preserve inline styles */
}

/* Preserve inline styles for colors, fonts, etc. */
.formatted-content *[style] {
    /* All inline styles will be preserved */
}

/* Common Outlook/Word color preservation */
.formatted-content *[style*="color:"] {
    /* Text colors from style attributes */
}

.formatted-content *[style*="background-color:"] {
    /* Background colors from style attributes */
}

.formatted-content *[style*="font-family:"] {
    /* Font families from style attributes */
}

.formatted-content *[style*="font-size:"] {
    /* Font sizes from style attributes */
}

.formatted-content *[style*="font-weight:"] {
    /* Font weights from style attributes */
}

.formatted-content *[style*="text-align:"] {
    /* Text alignment from style attributes */
}

/* Table cell styling preservation */
.formatted-content td[bgcolor] {
    /* Background colors in table cells */
}

.formatted-content th[bgcolor] {
    /* Background colors in table headers */
}

.formatted-content td[style*="background-color:"] {
    /* Styled table cells */
}

.formatted-content th[style*="background-color:"] {
    /* Styled table headers */
}

/* List styling preservation */
.formatted-content ul[style] {
    /* Preserve styled unordered lists */
}

.formatted-content ol[style] {
    /* Preserve styled ordered lists */
}

.formatted-content li[style] {
    /* Preserve styled list items */
}

/* Responsive table wrapper */
@media (max-width: 768px) {
    .formatted-content {
        overflow-x: auto;
    }
    
    .formatted-content table {
        min-width: 600px;
    }
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