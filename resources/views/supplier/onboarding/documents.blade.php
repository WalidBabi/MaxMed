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

    <!-- Show uploaded documents if any exist -->
    @if($supplierInfo && $supplierInfo->documents && !empty($supplierInfo->documents))
        <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-green-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="flex-1">
                    <h3 class="text-lg font-medium text-green-800 mb-3">Uploaded Documents</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if(isset($supplierInfo->documents['trade_license_file']))
                            <div class="flex items-center p-3 bg-white rounded-lg border border-green-200">
                                <svg class="w-8 h-8 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <div>
                                    <p class="font-medium text-green-800">Trade License</p>
                                    <p class="text-sm text-green-600">✓ Uploaded</p>
                                </div>
                            </div>
                        @endif
                        
                        @if(isset($supplierInfo->documents['company_profile_file']))
                            <div class="flex items-center p-3 bg-white rounded-lg border border-green-200">
                                <svg class="w-8 h-8 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <div>
                                    <p class="font-medium text-green-800">Company Profile</p>
                                    <p class="text-sm text-green-600">✓ Uploaded</p>
                                </div>
                            </div>
                        @endif
                        
                        @if(isset($supplierInfo->documents['tax_certificate_file']))
                            <div class="flex items-center p-3 bg-white rounded-lg border border-green-200">
                                <svg class="w-8 h-8 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <div>
                                    <p class="font-medium text-green-800">Tax Certificate</p>
                                    <p class="text-sm text-green-600">✓ Uploaded</p>
                                </div>
                            </div>
                        @endif
                        
                        @if(isset($supplierInfo->documents['certification_files']) && !empty($supplierInfo->documents['certification_files']))
                            <div class="flex items-center p-3 bg-white rounded-lg border border-green-200">
                                <svg class="w-8 h-8 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <div>
                                    <p class="font-medium text-green-800">Additional Certifications</p>
                                    <p class="text-sm text-green-600">✓ {{ count($supplierInfo->documents['certification_files']) }} file(s) uploaded</p>
                                </div>
                            </div>
                        @endif
                    </div>
                    <p class="text-sm text-green-700 mt-3">
                        You can upload new files below to replace existing documents if needed.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-5">
                @if($supplierInfo && $supplierInfo->documents && !empty($supplierInfo->documents))
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
                        <label class="block text-sm font-medium text-gray-700">
                            Trade License
                            @if(isset($supplierInfo->documents['trade_license_file']))
                                <span class="text-green-600 text-xs ml-2">✓ Already uploaded</span>
                            @endif
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="trade_license_file" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                        <span>
                                            @if(isset($supplierInfo->documents['trade_license_file']))
                                                Replace file
                                            @else
                                                Upload a file
                                            @endif
                                        </span>
                                        <input id="trade_license_file" name="trade_license_file" type="file" class="sr-only" accept=".pdf,.jpg,.jpeg,.png" {{ !isset($supplierInfo->documents['trade_license_file']) ? 'required' : '' }}>
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PDF, JPG, PNG up to 5MB</p>
                            </div>
                        </div>
                        @error('trade_license_file')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tax Certificate -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Tax Registration Certificate (Optional)
                            @if(isset($supplierInfo->documents['tax_certificate_file']))
                                <span class="text-green-600 text-xs ml-2">✓ Already uploaded</span>
                            @endif
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="tax_certificate_file" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                        <span>
                                            @if(isset($supplierInfo->documents['tax_certificate_file']))
                                                Replace file
                                            @else
                                                Upload a file
                                            @endif
                                        </span>
                                        <input id="tax_certificate_file" name="tax_certificate_file" type="file" class="sr-only" accept=".pdf,.jpg,.jpeg,.png">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PDF, JPG, PNG up to 5MB</p>
                            </div>
                        </div>
                        @error('tax_certificate_file')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Company Profile -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Company Profile
                            @if(isset($supplierInfo->documents['company_profile_file']))
                                <span class="text-green-600 text-xs ml-2">✓ Already uploaded</span>
                            @endif
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="company_profile_file" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                        <span>
                                            @if(isset($supplierInfo->documents['company_profile_file']))
                                                Replace file
                                            @else
                                                Upload a file
                                            @endif
                                        </span>
                                        <input id="company_profile_file" name="company_profile_file" type="file" class="sr-only" accept=".pdf" {{ !isset($supplierInfo->documents['company_profile_file']) ? 'required' : '' }}>
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PDF up to 10MB</p>
                            </div>
                        </div>
                        @error('company_profile_file')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Additional Certifications -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Additional Certifications (Optional)
                            @if(isset($supplierInfo->documents['certification_files']) && !empty($supplierInfo->documents['certification_files']))
                                <span class="text-green-600 text-xs ml-2">✓ {{ count($supplierInfo->documents['certification_files']) }} file(s) uploaded</span>
                            @endif
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="certification_files" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                        <span>
                                            @if(isset($supplierInfo->documents['certification_files']) && !empty($supplierInfo->documents['certification_files']))
                                                Add more files
                                            @else
                                                Upload files
                                            @endif
                                        </span>
                                        <input id="certification_files" name="certification_files[]" type="file" class="sr-only" accept=".pdf,.jpg,.jpeg,.png" multiple>
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PDF, JPG, PNG up to 5MB each</p>
                            </div>
                        </div>
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
                    @if($supplierInfo && $supplierInfo->documents && !empty($supplierInfo->documents))
                        <a href="{{ route('supplier.onboarding.categories') }}" class="ml-3 bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Skip to Categories
                        </a>
                    @endif
                    <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        @if($supplierInfo && $supplierInfo->documents && !empty($supplierInfo->documents))
                            Update & Continue
                        @else
                            Continue to Categories
                        @endif
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 