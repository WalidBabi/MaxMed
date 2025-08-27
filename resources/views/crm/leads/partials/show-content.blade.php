@php
    use Illuminate\Support\Facades\Storage;
@endphp

<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-start justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">{{ $lead->full_name }}</h2>
            <p class="text-gray-600">{{ $lead->company_name }} • {{ $lead->job_title }}</p>
        </div>
        <button type="button" onclick="closeLeadModal()" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
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
 
             <!-- Notes -->
             @if($lead->notes)
             <div class="bg-white rounded-lg border border-gray-200 p-6">
                 <h3 class="text-lg font-semibold text-gray-900 mb-4">Notes</h3>
                 <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $lead->notes }}</p>
             </div>
             @endif
 
             <!-- Quick Status Update -->
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <h4 class="text-sm font-semibold text-gray-700 mb-3">Quick Status Update - Medical Equipment Trading</h4>
                <div class="flex flex-wrap gap-2">
                    <button onclick="quickStatusChange('{{ $lead->id }}', 'new_inquiry')" class="px-3 py-1 text-xs bg-blue-100 text-blue-800 rounded-full hover:bg-blue-200">📩 New Inquiry</button>
                    <button onclick="quickStatusChange('{{ $lead->id }}', 'quote_requested')" class="px-3 py-1 text-xs bg-purple-100 text-purple-800 rounded-full hover:bg-purple-200">💰 Quote Requested</button>
                    <button onclick="quickStatusChange('{{ $lead->id }}', 'follow_up_1')" class="px-3 py-1 text-xs bg-amber-100 text-amber-800 rounded-full hover:bg-amber-200">⏰ Follow-up 1</button>
                    <button onclick="quickStatusChange('{{ $lead->id }}', 'follow_up_2')" class="px-3 py-1 text-xs bg-orange-100 text-orange-800 rounded-full hover:bg-orange-200">🔔 Follow-up 2</button>
                    <button onclick="quickStatusChange('{{ $lead->id }}', 'follow_up_3')" class="px-3 py-1 text-xs bg-red-100 text-red-800 rounded-full hover:bg-red-200">🚨 Follow-up 3</button>
                    <button onclick="quickStatusChange('{{ $lead->id }}', 'quote_sent')" class="px-3 py-1 text-xs bg-indigo-100 text-indigo-800 rounded-full hover:bg-indigo-200">📤 Quote Sent</button>
                    <button onclick="quickStatusChange('{{ $lead->id }}', 'negotiating_price')" class="px-3 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full hover:bg-yellow-200">🤝 Price Negotiation</button>
                    <button onclick="quickStatusChange('{{ $lead->id }}', 'payment_pending')" class="px-3 py-1 text-xs bg-emerald-100 text-emerald-800 rounded-full hover:bg-emerald-200">💳 Payment Pending</button>
                    <button onclick="quickStatusChange('{{ $lead->id }}', 'order_confirmed')" class="px-3 py-1 text-xs bg-green-100 text-green-800 rounded-full hover:bg-green-200">✅ Order Confirmed</button>
                    <button onclick="quickStatusChange('{{ $lead->id }}', 'deal_lost')" class="px-3 py-1 text-xs bg-gray-100 text-gray-800 rounded-full hover:bg-gray-200">❌ Deal Lost</button>
                </div>
            </div>

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
                         <span class="text-sm text-gray-900">{{ $lead->assignedUser->name }}</span>
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


