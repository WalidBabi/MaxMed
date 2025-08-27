@extends('layouts.crm')
@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('title', 'Edit Lead - ' . $lead->full_name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Edit Lead</h1>
            <p class="text-gray-600">Update lead information for {{ $lead->full_name }}</p>
        </div>

        <div class="bg-white rounded-lg shadow">
            <form action="{{ route('crm.leads.update', $lead) }}" method="POST" enctype="multipart/form-data" class="p-6" id="editLeadForm">
                @csrf
                @method('PUT')
                
                <!-- Contact Information -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                            <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $lead->first_name) }}" required
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('first_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                            <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $lead->last_name) }}" required
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('last_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $lead->email) }}" required
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="mobile" class="block text-sm font-medium text-gray-700 mb-1">Mobile</label>
                            <input type="text" id="mobile" name="mobile" value="{{ old('mobile', $lead->mobile) }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('mobile')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', $lead->phone) }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Company Information -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Company Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="company_name" class="block text-sm font-medium text-gray-700 mb-1">Company Name *</label>
                            <input type="text" id="company_name" name="company_name" value="{{ old('company_name', $lead->company_name) }}" required
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('company_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="job_title" class="block text-sm font-medium text-gray-700 mb-1">Job Title</label>
                            <input type="text" id="job_title" name="job_title" value="{{ old('job_title', $lead->job_title) }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('job_title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <label for="company_address" class="block text-sm font-medium text-gray-700 mb-1">Company Address</label>
                        <textarea id="company_address" name="company_address" rows="3"
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('company_address', $lead->company_address) }}</textarea>
                        @error('company_address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Lead Details -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Lead Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                            <select id="status" name="status" required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Status</option>
                                <option value="new_inquiry" {{ old('status', $lead->status) == 'new_inquiry' ? 'selected' : '' }}>📩 New Inquiry</option>
                                <option value="quote_requested" {{ old('status', $lead->status) == 'quote_requested' ? 'selected' : '' }}>💰 Quote Requested</option>
                                <option value="follow_up_1" {{ old('status', $lead->status) == 'follow_up_1' ? 'selected' : '' }}>⏰ Follow-up 1</option>
                                <option value="follow_up_2" {{ old('status', $lead->status) == 'follow_up_2' ? 'selected' : '' }}>🔔 Follow-up 2</option>
                                <option value="follow_up_3" {{ old('status', $lead->status) == 'follow_up_3' ? 'selected' : '' }}>🚨 Follow-up 3</option>
                                <option value="quote_sent" {{ old('status', $lead->status) == 'quote_sent' ? 'selected' : '' }}>📤 Quote Sent</option>
                                <option value="negotiating_price" {{ old('status', $lead->status) == 'negotiating_price' ? 'selected' : '' }}>🤝 Price Negotiation</option>
                                <option value="payment_pending" {{ old('status', $lead->status) == 'payment_pending' ? 'selected' : '' }}>💳 Payment Pending</option>
                                <option value="order_confirmed" {{ old('status', $lead->status) == 'order_confirmed' ? 'selected' : '' }}>✅ Order Confirmed</option>
                                <option value="deal_lost" {{ old('status', $lead->status) == 'deal_lost' ? 'selected' : '' }}>❌ Deal Lost</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="source" class="block text-sm font-medium text-gray-700 mb-1">Lead Source *</label>
                            <select id="source" name="source" required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Source</option>
                                <option value="website" {{ old('source', $lead->source) == 'website' ? 'selected' : '' }}>Website</option>
                                <option value="linkedin" {{ old('source', $lead->source) == 'linkedin' ? 'selected' : '' }}>LinkedIn</option>
                                <option value="email" {{ old('source', $lead->source) == 'email' ? 'selected' : '' }}>Email Campaign</option>
                                <option value="phone" {{ old('source', $lead->source) == 'phone' ? 'selected' : '' }}>Cold Call</option>
                                <option value="whatsapp" {{ old('source', $lead->source) == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                                <option value="on_site_visit" {{ old('source', $lead->source) == 'on_site_visit' ? 'selected' : '' }}>On-Site Visit</option>
                                <option value="referral" {{ old('source', $lead->source) == 'referral' ? 'selected' : '' }}>Referral</option>
                                <option value="trade_show" {{ old('source', $lead->source) == 'trade_show' ? 'selected' : '' }}>Trade Show</option>
                                <option value="google_ads" {{ old('source', $lead->source) == 'google_ads' ? 'selected' : '' }}>Google Ads</option>
                                <option value="other" {{ old('source', $lead->source) == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('source')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">Priority *</label>
                            <select id="priority" name="priority" required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Priority</option>
                                <option value="low" {{ old('priority', $lead->priority) == 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ old('priority', $lead->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ old('priority', $lead->priority) == 'high' ? 'selected' : '' }}>High</option>
                            </select>
                            @error('priority')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                        <div>
                            <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-1">Assign To *</label>
                            <select id="assigned_to" name="assigned_to" required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select User</option>
                                @foreach($users->where('role_id', 1) as $user)
                                    <option value="{{ $user->id }}" {{ old('assigned_to', $lead->assigned_to) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('assigned_to')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="estimated_value" class="block text-sm font-medium text-gray-700 mb-1">Estimated Value (AED)</label>
                            <input type="number" id="estimated_value" name="estimated_value" value="{{ old('estimated_value', $lead->estimated_value) }}" min="0" step="0.01"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('estimated_value')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="expected_close_date" class="block text-sm font-medium text-gray-700 mb-1">Expected Close Date</label>
                            <input type="date" id="expected_close_date" name="expected_close_date" 
                                   value="{{ old('expected_close_date', $lead->expected_close_date ? $lead->expected_close_date->format('Y-m-d') : '') }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('expected_close_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="mb-8">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea id="notes" name="notes" rows="4" placeholder="Additional notes about this lead..."
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('notes', $lead->notes) }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Current Attachments -->
                @if($lead->hasAttachments())
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Current Attachments</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($lead->attachments as $index => $attachment)
                            <div class="bg-gray-50 border rounded-lg p-4 flex items-center justify-between">
                                <div class="flex items-center min-w-0">
                                    <span class="text-2xl mr-3">
                                        @php
                                            $extension = strtolower(pathinfo($attachment['original_name'], PATHINFO_EXTENSION));
                                        @endphp
                                        @if(in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                            🖼️
                                        @elseif($extension === 'pdf')
                                            📄
                                        @elseif(in_array($extension, ['doc', 'docx']))
                                            📝
                                        @else
                                            📎
                                        @endif
                                    </span>
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
                                <div class="flex space-x-2">
                                    <a href="{{ Storage::url($attachment['path']) }}" target="_blank" 
                                       class="text-blue-600 hover:text-blue-800 text-sm">
                                        View
                                    </a>
                                    <button type="button" onclick="removeAttachment({{ $index }})" 
                                            class="text-red-600 hover:text-red-800 text-sm">
                                        Remove
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Add New Attachments -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Add New Requirements Attachments</h3>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 bg-gray-50">
                        <div class="text-center">
                            <div class="mx-auto flex justify-center">
                                <svg class="h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                            <div class="mt-4">
                                <label for="attachments" class="cursor-pointer">
                                    <span class="mt-2 block text-sm font-medium text-gray-900">
                                        Upload Additional Files
                                    </span>
                                    <span class="mt-1 block text-sm text-gray-500">
                                        PDF, Word, Excel, CSV, or images (JPG, PNG, GIF, WebP)
                                    </span>
                                    <span class="mt-1 block text-xs text-gray-400">
                                        Maximum 10MB per file
                                    </span>
                                </label>
                                <input 
                                    id="attachments" 
                                    name="attachments[]" 
                                    type="file" 
                                    multiple 
                                    accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,.jpg,.jpeg,.png,.gif,.webp"
                                    class="sr-only"
                                    onchange="displaySelectedFiles(this)"
                                >
                            </div>
                            <p class="text-xs text-gray-500 mt-2">
                                or drag and drop
                            </p>
                        </div>
                        
                        <!-- Selected Files Display -->
                        <div id="selectedFiles" class="mt-4 hidden">
                            <h4 class="text-sm font-medium text-gray-900 mb-2">New Files Selected:</h4>
                            <ul id="filesList" class="space-y-1"></ul>
                        </div>
                    </div>
                    
                    @error('attachments')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('attachments.*')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Actions -->
                <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                    <a href="{{ route('crm.leads.show', $lead) }}" class="text-gray-600 hover:text-gray-800">← Back to Lead</a>
                    
                    <div class="flex space-x-3">
                        <button type="button" onclick="window.history.back()" 
                                class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Update Lead
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Add form submission debugging
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editLeadForm');
    form.addEventListener('submit', function(e) {
        console.log('Form being submitted...');
        
        // Log all form data
        const formData = new FormData(form);
        const formEntries = {};
        for (let [key, value] of formData.entries()) {
            if (formEntries[key]) {
                if (Array.isArray(formEntries[key])) {
                    formEntries[key].push(value);
                } else {
                    formEntries[key] = [formEntries[key], value];
                }
            } else {
                formEntries[key] = value;
            }
        }
        
        console.log('Form data being sent:', formEntries);
        console.log('Remove attachments:', formEntries['remove_attachments[]'] || 'None');
        
        // Also check hidden inputs directly
        const removeInputs = form.querySelectorAll('input[name="remove_attachments[]"]');
        console.log('Direct check - removal inputs found:', removeInputs.length);
        removeInputs.forEach((input, i) => {
            console.log(`Removal input ${i}: value=${input.value}`);
        });
    });
});

// Debug function to check current removal status
function debugRemovalInputs() {
    const form = document.getElementById('editLeadForm');
    const removeInputs = form.querySelectorAll('input[name="remove_attachments[]"]');
    console.log('=== DEBUG: Current removal status ===');
    console.log('Form found:', !!form);
    console.log('Removal inputs count:', removeInputs.length);
    removeInputs.forEach((input, i) => {
        console.log(`Input ${i}: value=${input.value}, name=${input.name}`);
    });
    console.log('===================================');
}

function displaySelectedFiles(input) {
    const selectedFilesDiv = document.getElementById('selectedFiles');
    const filesList = document.getElementById('filesList');
    
    // Clear previous list
    filesList.innerHTML = '';
    
    if (input.files.length > 0) {
        selectedFilesDiv.classList.remove('hidden');
        
        Array.from(input.files).forEach((file, index) => {
            const li = document.createElement('li');
            li.className = 'flex items-center justify-between p-2 bg-white rounded border text-sm';
            
            const fileInfo = document.createElement('span');
            fileInfo.className = 'flex items-center';
            
            // File icon based on type
            const icon = getFileIcon(file.name);
            fileInfo.innerHTML = `
                <span class="mr-2">${icon}</span>
                <span class="font-medium">${file.name}</span>
                <span class="ml-2 text-gray-500">(${formatFileSize(file.size)})</span>
            `;
            
            li.appendChild(fileInfo);
            filesList.appendChild(li);
        });
    } else {
        selectedFilesDiv.classList.add('hidden');
    }
}

function getFileIcon(filename) {
    const extension = filename.split('.').pop().toLowerCase();
    
    switch (extension) {
        case 'pdf':
            return '📄';
        case 'doc':
        case 'docx':
            return '📝';
        case 'jpg':
        case 'jpeg':
        case 'png':
        case 'gif':
        case 'webp':
            return '🖼️';
        default:
            return '📎';
    }
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function removeAttachment(index) {
    if (confirm('Are you sure you want to remove this attachment?')) {
        console.log('Removing attachment at index:', index);
        
        // Get the specific form by ID
        const form = document.getElementById('editLeadForm');
        if (!form) {
            console.error('Edit lead form not found!');
            return;
        }
        
        // Check if this attachment is already marked for removal
        const existingInput = form.querySelector(`input[name="remove_attachments[]"][value="${index}"]`);
        if (existingInput) {
            console.log('Attachment already marked for removal');
            return;
        }
        
        // Add a hidden input to mark this attachment for removal
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'remove_attachments[]';
        hiddenInput.value = index;
        hiddenInput.setAttribute('data-attachment-index', index);
        form.appendChild(hiddenInput);
        
        console.log('Added hidden input for removal:', hiddenInput);
        console.log('Form now contains removal inputs:', form.querySelectorAll('input[name="remove_attachments[]"]'));
        
        // Hide the attachment visually
        const attachmentDiv = event.target.closest('.bg-gray-50');
        if (attachmentDiv) {
            attachmentDiv.style.opacity = '0.5';
            attachmentDiv.style.pointerEvents = 'none';
            attachmentDiv.style.border = '2px solid #ef4444';
            
            // Add "Marked for removal" text
            const removedText = document.createElement('div');
            removedText.className = 'text-red-600 text-xs font-medium mt-2 p-2 bg-red-100 rounded border border-red-300';
            removedText.textContent = '❌ Marked for removal - will be deleted when you save';
            attachmentDiv.appendChild(removedText);
            
            console.log('Attachment visually marked for removal');
        } else {
            console.error('Could not find attachment div to mark');
        }
    }
}
</script>

@endsection 