@php
    use Illuminate\Support\Facades\Storage;
@endphp

<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-start justify-between">
        <div>
            @if(isset($isPurchasingUser) && $isPurchasingUser)
                <h2 class="text-2xl font-bold text-gray-900">Lead Requirements</h2>
                <p class="text-gray-600">Lead #{{ $lead->id }} • {{ ucfirst(str_replace('_', ' ', $lead->status)) }}</p>
            @else
                <h2 class="text-2xl font-bold text-gray-900">{{ $lead->full_name }}</h2>
                <p class="text-gray-600">{{ $lead->company_name }} • {{ $lead->job_title }}</p>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            @if(!isset($isPurchasingUser) || !$isPurchasingUser)
            <!-- Contact Information -->
            <div class="bg-white rounded-lg border border-gray-200 p-6">
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
                        <p class="mt-1 text-sm text-gray-900"><a href="mailto:{{ $lead->email }}" class="text-blue-600 hover:text-blue-800">{{ $lead->email }}</a></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Mobile</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $lead->mobile ?: '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Company Information -->
            <div class="bg-white rounded-lg border border-gray-200 p-6">
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
 
             <!-- Notes/Requirements -->
             @if($lead->notes)
             <div class="bg-white rounded-lg border border-gray-200 p-6">
                 <h3 class="text-lg font-semibold text-gray-900 mb-4">
                     @if(isset($isPurchasingUser) && $isPurchasingUser)
                         Requirements
                     @else
                         Notes
                     @endif
                 </h3>
                 <div class="text-sm text-gray-900 formatted-content" data-cache-bust="{{ time() }}">{!! \App\Helpers\HtmlSanitizer::sanitizeRichContent($lead->notes) !!}</div>
                <!-- Debug: Content length: {{ strlen($lead->notes) }} chars -->
                
                <style>
                .formatted-content { line-height: 1.6; max-width: 100%; word-wrap: break-word; }
                .formatted-content p { margin-bottom: 0.75rem; }
                .formatted-content p:last-child { margin-bottom: 0; }
                .formatted-content strong, .formatted-content b { font-weight: 600; color: #1f2937; }
                .formatted-content em, .formatted-content i { font-style: italic; }
                .formatted-content u { text-decoration: underline; }
                .formatted-content strike, .formatted-content del { text-decoration: line-through; }
                .formatted-content ins { text-decoration: underline; background-color: #fef3c7; }
                .formatted-content mark { background-color: #fde047; padding: 0.125rem 0.25rem; }
                .formatted-content small { font-size: 0.875em; }
                .formatted-content sup { vertical-align: super; font-size: 0.75em; }
                .formatted-content sub { vertical-align: sub; font-size: 0.75em; }
                .formatted-content h1 { font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem; color: #1f2937; border-bottom: 2px solid #e5e7eb; padding-bottom: 0.5rem; }
                .formatted-content h2 { font-size: 1.25rem; font-weight: 600; margin-bottom: 0.75rem; color: #374151; }
                .formatted-content h3 { font-size: 1.125rem; font-weight: 600; margin-bottom: 0.5rem; color: #4b5563; }
                .formatted-content h4, .formatted-content h5, .formatted-content h6 { font-size: 1rem; font-weight: 500; margin-bottom: 0.5rem; color: #6b7280; }
                .formatted-content ul, .formatted-content ol { margin-left: 1.5rem; margin-bottom: 0.75rem; }
                .formatted-content ul { list-style-type: disc; }
                .formatted-content ol { list-style-type: decimal; }
                .formatted-content li { margin-bottom: 0.25rem; }
                .formatted-content a { color: #3b82f6; text-decoration: underline; }
                .formatted-content a:hover { color: #1d4ed8; }
                .formatted-content table { width: 100%; border-collapse: collapse; margin-bottom: 1rem; border: 1px solid #d1d5db; background-color: #ffffff; }
                .formatted-content th, .formatted-content td { padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; text-align: left; vertical-align: top; }
                .formatted-content th { background-color: #f9fafb; font-weight: 600; color: #374151; }
                .formatted-content tbody tr:nth-child(even) { background-color: #f9fafb; }
                .formatted-content tbody tr:hover { background-color: #f3f4f6; }
                .formatted-content blockquote { border-left: 4px solid #3b82f6; margin: 1rem 0; padding: 0.5rem 1rem; background-color: #f8fafc; font-style: italic; color: #4b5563; }
                .formatted-content pre { background-color: #1f2937; color: #f9fafb; padding: 1rem; border-radius: 0.375rem; overflow-x: auto; margin-bottom: 1rem; font-family: 'Courier New', monospace; font-size: 0.875rem; }
                .formatted-content code { background-color: #f3f4f6; color: #1f2937; padding: 0.125rem 0.25rem; border-radius: 0.25rem; font-family: 'Courier New', monospace; font-size: 0.875rem; }
                .formatted-content pre code { background-color: transparent; color: inherit; padding: 0; }
                .formatted-content hr { border: none; height: 1px; background-color: #d1d5db; margin: 1.5rem 0; }
                .formatted-content img { max-width: 100%; height: auto; border-radius: 0.375rem; margin: 0.5rem 0; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1); }
                .formatted-content div { margin-bottom: 0.5rem; }
                @media (max-width: 768px) { .formatted-content { overflow-x: auto; } .formatted-content table { min-width: 600px; } }
                </style>
             </div>
             @endif
 
             <!-- Quick Status Update -->
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <h4 class="text-sm font-semibold text-gray-700 mb-3">Quick Status Update - Medical Equipment Trading</h4>
                <div class="flex flex-wrap gap-2">
                    <button onclick="quickStatusChange('{{ $lead->id }}', 'new_inquiry')" class="px-3 py-1 text-xs bg-blue-100 text-blue-800 rounded-full hover:bg-blue-200">📩 New Inquiry</button>
                    <button onclick="quickStatusChange('{{ $lead->id }}', 'quote_requested')" class="px-3 py-1 text-xs bg-purple-100 text-purple-800 rounded-full hover:bg-purple-200">💰 Quote Requested</button>
                    <button onclick="quickStatusChange('{{ $lead->id }}', 'getting_price')" class="px-3 py-1 text-xs bg-indigo-100 text-indigo-800 rounded-full hover:bg-indigo-200">🔍 Getting Price</button>
                    <button onclick="quickStatusChange('{{ $lead->id }}', 'price_submitted')" class="px-3 py-1 text-xs bg-teal-100 text-teal-800 rounded-full hover:bg-teal-200">📋 Price Submitted</button>
                    <button onclick="quickStatusChange('{{ $lead->id }}', 'quote_sent')" class="px-3 py-1 text-xs bg-indigo-100 text-indigo-800 rounded-full hover:bg-indigo-200">📤 Quote Sent</button>
                    <button onclick="quickStatusChange('{{ $lead->id }}', 'follow_up_1')" class="px-3 py-1 text-xs bg-amber-100 text-amber-800 rounded-full hover:bg-amber-200">⏰ Follow-up 1</button>
                    <button onclick="quickStatusChange('{{ $lead->id }}', 'follow_up_2')" class="px-3 py-1 text-xs bg-orange-100 text-orange-800 rounded-full hover:bg-orange-200">🔔 Follow-up 2</button>
                    <button onclick="quickStatusChange('{{ $lead->id }}', 'follow_up_3')" class="px-3 py-1 text-xs bg-red-100 text-red-800 rounded-full hover:bg-red-200">🚨 Follow-up 3</button>
                    <button onclick="quickStatusChange('{{ $lead->id }}', 'negotiating_price')" class="px-3 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full hover:bg-yellow-200">🤝 Price Negotiation</button>
                    <button onclick="quickStatusChange('{{ $lead->id }}', 'payment_pending')" class="px-3 py-1 text-xs bg-emerald-100 text-emerald-800 rounded-full hover:bg-emerald-200">💳 Payment Pending</button>
                    <button onclick="quickStatusChange('{{ $lead->id }}', 'order_confirmed')" class="px-3 py-1 text-xs bg-green-100 text-green-800 rounded-full hover:bg-green-200">✅ Order Confirmed</button>
                    <button onclick="quickStatusChange('{{ $lead->id }}', 'deal_lost')" class="px-3 py-1 text-xs bg-gray-100 text-gray-800 rounded-full hover:bg-gray-200">❌ Deal Lost</button>
                </div>
            </div>

            <!-- Price Submissions (for Admin users and Superadmins) -->
            @if(!isset($isPurchasingUser) || !$isPurchasingUser || $isSuperAdmin)
                @if($lead->hasPriceSubmissions())
                <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Price Submissions</h3>
                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                            {{ $lead->priceSubmissions->count() }} {{ $lead->priceSubmissions->count() === 1 ? 'submission' : 'submissions' }}
                        </span>
                    </div>
                    
                    <div class="space-y-4 max-h-64 overflow-y-auto">
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
                                            <a href="{{ route('price-submissions.download-attachment', [$submission->id, $index]) }}" 
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
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Requirements Attachments</h3>
                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                        {{ $lead->getAttachmentCount() }} {{ $lead->getAttachmentCount() === 1 ? 'file' : 'files' }}
                    </span>
                </div>

                @php
                    $attachmentsByType = $lead->getAttachmentsByType();
                @endphp

                @if(!empty($attachmentsByType['images']))
                <div class="mb-6">
                    <h4 class="text-md font-medium text-gray-700 mb-3 flex items-center">
                        <span class="mr-2">🖼️</span>
                        Images ({{ count($attachmentsByType['images']) }})
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($attachmentsByType['images'] as $attachment)
                        @if(Storage::disk('public')->exists($attachment['path']))
                        <div class="relative group">
                            <div class="aspect-square bg-gray-200 rounded-lg overflow-hidden">
                                <a href="{{ Storage::url($attachment['path']) }}" target="_blank">
                                    <img src="{{ Storage::url($attachment['path']) }}" alt="{{ $attachment['original_name'] }}" class="w-full h-full object-cover">
                                </a>
                            </div>
                            <div class="mt-2">
                                <p class="text-sm text-gray-600 truncate">{{ $attachment['original_name'] }}</p>
                            </div>
                        </div>
                        @else
                        <div class="relative group">
                            <div class="aspect-square bg-red-100 rounded-lg overflow-hidden flex items-center justify-center">
                                <div class="text-center">
                                    <span class="text-red-400 text-3xl">❌</span>
                                    <p class="text-sm text-red-600 mt-1">File Missing</p>
                                </div>
                            </div>
                            <div class="mt-2">
                                <p class="text-sm text-red-600 truncate">{{ $attachment['original_name'] }}</p>
                            </div>
                        </div>
                        @endif
                        @endforeach
                    </div>
                </div>
                @endif

                @if(!empty($attachmentsByType['pdfs']))
                <div class="mb-6">
                    <h4 class="text-md font-medium text-gray-700 mb-3 flex items-center">
                        <span class="mr-2">📄</span>
                        PDF Documents ({{ count($attachmentsByType['pdfs']) }})
                    </h4>
                    <div class="space-y-3">
                        @foreach($attachmentsByType['pdfs'] as $attachment)
                        @if(Storage::disk('public')->exists($attachment['path']))
                        <div class="flex items-center justify-between p-4 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex items-center min-w-0">
                                <span class="text-2xl mr-3">📄</span>
                                <div class="min-w-0">
                                    <p class="text-base font-medium text-gray-900 truncate">{{ $attachment['original_name'] }}</p>
                                </div>
                            </div>
                            <a href="{{ Storage::url($attachment['path']) }}" target="_blank" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium">View PDF</a>
                        </div>
                        @else
                        <div class="flex items-center justify-between p-3 bg-red-100 border border-red-300 rounded-lg">
                            <div class="flex items-center min-w-0">
                                <span class="text-2xl mr-3">❌</span>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-red-700 truncate">{{ $attachment['original_name'] }}</p>
                                </div>
                            </div>
                            <span class="bg-red-200 text-red-800 px-3 py-1 rounded-md text-sm font-medium">Missing</span>
                        </div>
                        @endif
                        @endforeach
                    </div>
                </div>
                @endif

                @if(!empty($attachmentsByType['documents']))
                <div class="mb-6">
                    <h4 class="text-md font-medium text-gray-700 mb-3 flex items-center">
                        <span class="mr-2">📝</span>
                        Word Documents ({{ count($attachmentsByType['documents']) }})
                    </h4>
                    <div class="space-y-3">
                        @foreach($attachmentsByType['documents'] as $attachment)
                        <div class="flex items-center justify-between p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex items-center min-w-0">
                                <span class="text-2xl mr-3">📝</span>
                                <div class="min-w-0">
                                    <p class="text-base font-medium text-gray-900 truncate">{{ $attachment['original_name'] }}</p>
                                </div>
                            </div>
                            <a href="{{ Storage::url($attachment['path']) }}" download class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">Download</a>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if(!empty($attachmentsByType['others']))
                <div class="mb-2">
                    <h4 class="text-md font-medium text-gray-700 mb-3 flex items-center">
                        <span class="mr-2">📎</span>
                        Other Files ({{ count($attachmentsByType['others']) }})
                    </h4>
                    <div class="space-y-3">
                        @foreach($attachmentsByType['others'] as $attachment)
                        <div class="flex items-center justify-between p-4 bg-gray-50 border border-gray-200 rounded-lg">
                            <div class="flex items-center min-w-0">
                                <span class="text-2xl mr-3">📎</span>
                                <div class="min-w-0">
                                    <p class="text-base font-medium text-gray-900 truncate">{{ $attachment['original_name'] }}</p>
                                </div>
                            </div>
                            <a href="{{ Storage::url($attachment['path']) }}" download class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium">Download</a>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            @endif
        </div>

                 <!-- Sidebar -->
         <div class="space-y-6">
             <div class="bg-white rounded-lg border border-gray-200 p-6">
                 <h3 class="text-lg font-semibold text-gray-900 mb-4">Lead Status</h3>
                 <div class="space-y-3">
                     <div class="flex justify-between items-center">
                         <span class="text-sm font-medium text-gray-500">Status</span>
                         <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-{{ $lead->status_color }}-100 text-{{ $lead->status_color }}-800">{{ ucfirst($lead->status) }}</span>
                     </div>
                     <div class="flex justify-between items-center">
                         <span class="text-sm font-medium text-gray-500">Priority</span>
                         <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-{{ $lead->priority_color }}-100 text-{{ $lead->priority_color }}-800">{{ ucfirst($lead->priority) }}</span>
                     </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-500">Assigned To</span>
                        <span class="text-sm {{ $lead->assignedUser && $lead->assignedUser->id === Auth::id() ? 'text-green-600 font-semibold' : 'text-gray-900' }}">
                            {{ $lead->assignedUser ? $lead->assignedUser->name . ($lead->assignedUser->id === Auth::id() ? ' (You)' : '') : 'Unassigned' }}
                        </span>
                    </div>
                     <div class="flex justify-between items-center">
                         <span class="text-sm font-medium text-gray-500">Created</span>
                         <span class="text-sm text-gray-900">{{ $lead->created_at->format('M d, Y') }}</span>
                     </div>
                     <div class="flex justify-between items-center">
                         <span class="text-sm font-medium text-gray-500">Age</span>
                         <span class="text-sm text-gray-900">{{ $lead->created_ago }}</span>
                     </div>
                 </div>
             </div>
         </div>
    </div>
</div>


