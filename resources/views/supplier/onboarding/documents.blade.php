@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Progress Steps -->
    <div class="mb-8">
        <div class="flex items-center justify-center space-x-12">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center font-bold">✓</div>
                <span class="ml-2 text-green-500">Company Info</span>
            </div>
            <div class="flex-1 h-0.5 bg-green-500"></div>
            <div class="flex items-center">
                <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center font-bold">2</div>
                <span class="ml-2 font-medium text-green-500">Documents</span>
            </div>
            <div class="flex-1 h-0.5 bg-gray-200"></div>
            <div class="flex items-center">
                <div class="w-8 h-8 bg-gray-200 text-gray-600 rounded-full flex items-center justify-center font-bold">3</div>
                <span class="ml-2 text-gray-600">Categories</span>
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-5">
                @if(!empty($existingDocuments))
                    Update Documents
                @else
                    Required Documents
                @endif
            </h2>
            
            <form action="{{ route('supplier.onboarding.documents') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <div class="space-y-6">
                    <!-- Trade License -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Trade License</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="trade_license_file" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                        <span>Upload a file</span>
                                        <input id="trade_license_file" name="trade_license_file" type="file" class="sr-only" accept=".pdf,.jpg,.jpeg,.png" @if(!isset($existingDocuments['trade_license_file'])) required @endif data-file-type="trade_license">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PDF, JPG, PNG up to 20MB</p>
                            </div>
                        </div>
                        <!-- Selected files for Trade License -->
                        <div id="trade_license_files" class="mt-3 space-y-2"></div>
                        @error('trade_license_file')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tax Certificate -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tax Registration Certificate (Optional)</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="tax_certificate_file" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                        <span>Upload a file</span>
                                        <input id="tax_certificate_file" name="tax_certificate_file" type="file" class="sr-only" accept=".pdf,.jpg,.jpeg,.png" data-file-type="tax_certificate">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PDF, JPG, PNG up to 20MB</p>
                            </div>
                        </div>
                        <!-- Selected files for Tax Certificate -->
                        <div id="tax_certificate_files" class="mt-3 space-y-2"></div>
                        @error('tax_certificate_file')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Company Profile -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Company Profile</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="company_profile_file" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                        <span>Upload a file</span>
                                        <input id="company_profile_file" name="company_profile_file" type="file" class="sr-only" accept=".pdf" @if(!isset($existingDocuments['company_profile_file'])) required @endif data-file-type="company_profile">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PDF up to 40MB</p>
                            </div>
                        </div>
                        <!-- Selected files for Company Profile -->
                        <div id="company_profile_files" class="mt-3 space-y-2"></div>
                        @error('company_profile_file')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Additional Certifications -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Additional Certifications (Optional)</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="certification_files" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                        <span>Upload files</span>
                                        <input id="certification_files" name="certification_files[]" type="file" class="sr-only" accept=".pdf,.jpg,.jpeg,.png" multiple data-file-type="certification">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PDF, JPG, PNG up to 20MB each</p>
                            </div>
                        </div>
                        <!-- Selected files for Certifications -->
                        <div id="certification_files" class="mt-3 space-y-2"></div>
                        @error('certification_files')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @error('certification_files.*')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end mt-6">
                    <a href="{{ route('supplier.onboarding.company') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Back
                    </a>
                    <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        @if(!empty($existingDocuments))
                            Update Documents
                        @else
                            Continue to Categories
                        @endif
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectedFiles = new Map();
    
    // File input elements
    const fileInputs = document.querySelectorAll('input[type="file"]');
    
    fileInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            const fileType = this.dataset.fileType;
            
            // Clear existing files for this input
            clearFilesForType(fileType);
            
            if (files.length > 0) {
                files.forEach(file => {
                    const fileId = `${fileType}_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
                    selectedFiles.set(fileId, {
                        file: file,
                        type: fileType,
                        input: this
                    });
                });
            }
            
            updateFileDisplay(fileType);
        });
    });
    
    function clearFilesForType(fileType) {
        // Remove all files of this type from the map
        for (let [fileId, fileData] of selectedFiles.entries()) {
            if (fileData.type === fileType) {
                selectedFiles.delete(fileId);
            }
        }
    }
    
    function updateFileDisplay(fileType) {
        const containerId = `${fileType}_files`;
        const container = document.getElementById(containerId);
        
        if (!container) return;
        
        container.innerHTML = '';
        
        // Get all files for this type
        const filesForType = [];
        selectedFiles.forEach((fileData, fileId) => {
            if (fileData.type === fileType) {
                filesForType.push({ fileId, fileData });
            }
        });
        
        if (filesForType.length === 0) {
            container.style.display = 'none';
            return;
        }
        
        container.style.display = 'block';
        
        filesForType.forEach(({ fileId, fileData }) => {
            const file = fileData.file;
            const fileSize = formatFileSize(file.size);
            
            const fileElement = document.createElement('div');
            fileElement.className = 'flex items-center justify-between p-3 bg-blue-50 border border-blue-200 rounded-lg';
            
            fileElement.innerHTML = `
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <div>
                        <h4 class="font-medium text-blue-900 text-sm">${file.name}</h4>
                        <p class="text-xs text-blue-700">${fileSize}</p>
                    </div>
                </div>
                <button type="button" onclick="removeFile('${fileId}')" class="text-red-500 hover:text-red-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            `;
            
            container.appendChild(fileElement);
        });
    }
    
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    // Make removeFile function global
    window.removeFile = function(fileId) {
        const fileData = selectedFiles.get(fileId);
        if (fileData) {
            // Clear the file input
            fileData.input.value = '';
            selectedFiles.delete(fileId);
            updateFileDisplay(fileData.type);
        }
    };
    
    // Form submission handling
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        // Clear any existing files from the map to prevent memory leaks
        selectedFiles.clear();
    });
});
</script>
@endpush
@endsection 