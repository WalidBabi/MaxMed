@extends('layouts.crm')

@section('title', 'Create New Lead')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Create New Lead</h1>
            <p class="text-gray-600">Add a new prospect to your CRM system</p>
            
            <!-- Display Errors -->
            @if ($errors->any())
                <div class="mt-4 bg-red-50 border border-red-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">There were errors with your submission:</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="bg-white rounded-lg shadow">
            <form action="{{ route('crm.leads.store') }}" method="POST" enctype="multipart/form-data" class="p-6" id="leadForm">
                @csrf
                
                <!-- Contact Information -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                            <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" required
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('first_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                            <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" required
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('last_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="mobile" class="block text-sm font-medium text-gray-700 mb-1">Mobile</label>
                            <input type="text" id="mobile" name="mobile" value="{{ old('mobile') }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('mobile')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
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
                            <input type="text" id="company_name" name="company_name" value="{{ old('company_name') }}" required
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('company_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="job_title" class="block text-sm font-medium text-gray-700 mb-1">Job Title</label>
                            <input type="text" id="job_title" name="job_title" value="{{ old('job_title') }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('job_title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <label for="company_address" class="block text-sm font-medium text-gray-700 mb-1">Company Address</label>
                        <textarea id="company_address" name="company_address" rows="3"
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('company_address') }}</textarea>
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
                            <label for="source" class="block text-sm font-medium text-gray-700 mb-1">Lead Source *</label>
                            <select id="source" name="source" required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Source</option>
                                <option value="website" {{ old('source') == 'website' ? 'selected' : '' }}>Website</option>
                                <option value="linkedin" {{ old('source') == 'linkedin' ? 'selected' : '' }}>LinkedIn</option>
                                <option value="email" {{ old('source') == 'email' ? 'selected' : '' }}>Email Campaign</option>
                                <option value="phone" {{ old('source') == 'phone' ? 'selected' : '' }}>Cold Call</option>
                                <option value="whatsapp" {{ old('source') == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                                <option value="on_site_visit" {{ old('source') == 'on_site_visit' ? 'selected' : '' }}>On-Site Visit</option>
                                <option value="referral" {{ old('source') == 'referral' ? 'selected' : '' }}>Referral</option>
                                <option value="trade_show" {{ old('source') == 'trade_show' ? 'selected' : '' }}>Trade Show</option>
                                <option value="google_ads" {{ old('source') == 'google_ads' ? 'selected' : '' }}>Google Ads</option>
                                <option value="other" {{ old('source') == 'other' ? 'selected' : '' }}>Other</option>
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
                                <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                            </select>
                            @error('priority')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-1">Assign To *</label>
                            <select id="assigned_to" name="assigned_to" required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select User</option>
                                @foreach($users->where('role_id', 1) as $user)
                                    <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('assigned_to')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div>
                            <label for="estimated_value" class="block text-sm font-medium text-gray-700 mb-1">Estimated Value (AED)</label>
                            <input type="number" id="estimated_value" name="estimated_value" value="{{ old('estimated_value') }}" min="0" step="0.01"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('estimated_value')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="expected_close_date" class="block text-sm font-medium text-gray-700 mb-1">Expected Close Date</label>
                            <input type="date" id="expected_close_date" name="expected_close_date" value="{{ old('expected_close_date') }}"
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
                    <textarea id="notes" name="notes" rows="4" placeholder="Initial notes about this lead..."
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Requirements Attachments -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Requirements Attachments</h3>
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
                                        Upload Requirements Files
                                    </span>
                                    <span class="mt-1 block text-sm text-gray-500">
                                        PDF, Word documents, or images (JPG, PNG, GIF, WebP)
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
                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.webp"
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
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Selected Files:</h4>
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
                    <a href="{{ route('crm.leads.index') }}" class="text-gray-600 hover:text-gray-800">← Back to Leads</a>
                    
                    <div class="flex space-x-3">
                        <button type="button" onclick="window.history.back()" 
                                class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancel
                        </button>
                        <button type="submit" id="submitBtn"
                                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Create Lead
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Prevent double form submission
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('leadForm');
    const submitBtn = document.getElementById('submitBtn');
    let isSubmitting = false;
    
    form.addEventListener('submit', function(e) {
        if (isSubmitting) {
            e.preventDefault();
            return false;
        }
        
        isSubmitting = true;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Creating Lead...';
        
        // Re-enable button after 10 seconds as a failsafe
        setTimeout(function() {
            isSubmitting = false;
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Create Lead';
        }, 10000);
    });
});

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
</script>

@endsection 