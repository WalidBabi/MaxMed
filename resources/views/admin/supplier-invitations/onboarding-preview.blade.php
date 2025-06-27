@extends('admin.layouts.app')

@section('title', 'Supplier Onboarding Preview')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Supplier Onboarding Flow Preview</h1>
                <p class="text-gray-600 mt-2">This is what invited suppliers will go through during the onboarding process</p>
            </div>
            <div>
                <a href="{{ route('admin.supplier-invitations.index') }}" 
                   class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Back to Invitations
                </a>
            </div>
        </div>
    </div>

    <!-- Progress Steps Overview -->
    <div class="mb-8">
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Onboarding Steps Overview</h2>
            <div class="flex items-center justify-center space-x-12">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center font-bold">1</div>
                    <span class="ml-2 font-medium text-green-500">Company Information</span>
                </div>
                <div class="flex-1 h-0.5 bg-green-500"></div>
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center font-bold">2</div>
                    <span class="ml-2 font-medium text-green-500">Documents Upload</span>
                </div>
                <div class="flex-1 h-0.5 bg-green-500"></div>
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center font-bold">3</div>
                    <span class="ml-2 font-medium text-green-500">Categories Selection</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 1: Company Information -->
    <div class="mb-8">
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <div class="w-6 h-6 bg-indigo-600 rounded-full flex items-center justify-center text-white text-xs font-bold mr-3">1</div>
                    Step 1: Company Information
                </h3>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-4">Suppliers provide comprehensive company details including:</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <h4 class="font-medium text-gray-900">Basic Information</h4>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>• Company Name</li>
                            <li>• Business Registration Number</li>
                            <li>• Trade License Number</li>
                            <li>• Tax Registration Number</li>
                        </ul>
                    </div>
                    <div class="space-y-2">
                        <h4 class="font-medium text-gray-900">Contact Details</h4>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>• Business Address & Location</li>
                            <li>• Primary Contact Information</li>
                            <li>• Website (if available)</li>
                            <li>• Years in Business</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 2: Documents Upload -->
    <div class="mb-8">
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <div class="w-6 h-6 bg-indigo-600 rounded-full flex items-center justify-center text-white text-xs font-bold mr-3">2</div>
                    Step 2: Documents Upload
                </h3>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-4">Required documents for verification and compliance:</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <h4 class="font-medium text-gray-900">Required Documents</h4>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>• Trade License Copy</li>
                            <li>• Company Profile (PDF)</li>
                            <li>• Tax Certificate (optional)</li>
                        </ul>
                    </div>
                    <div class="space-y-2">
                        <h4 class="font-medium text-gray-900">Optional Documents</h4>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>• Industry Certifications</li>
                            <li>• Quality Certificates</li>
                            <li>• ISO Certifications</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 3: Categories Selection -->
    <div class="mb-8">
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <div class="w-6 h-6 bg-indigo-600 rounded-full flex items-center justify-center text-white text-xs font-bold mr-3">3</div>
                    Step 3: Product Categories Selection
                </h3>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-4">Suppliers choose relevant product categories and provide specialization details:</p>
                
                <div class="mb-6">
                    <h4 class="font-medium text-gray-900 mb-3">Available Categories</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($categories as $category)
                        <div class="border border-gray-200 rounded-lg p-3">
                            <div class="flex items-center">
                                <input type="checkbox" disabled class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                <label class="ml-2 text-sm font-medium text-gray-900">{{ $category->name }}</label>
                            </div>
                            @if($category->children->count() > 0)
                            <div class="ml-6 mt-2 space-y-1">
                                @foreach($category->children as $subcategory)
                                <div class="flex items-center">
                                    <input type="checkbox" disabled class="h-3 w-3 text-indigo-600 border-gray-300 rounded">
                                    <label class="ml-2 text-xs text-gray-600">{{ $subcategory->name }}</label>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>

                <div>
                    <h4 class="font-medium text-gray-900 mb-2">Specializations</h4>
                    <p class="text-sm text-gray-600">Suppliers also provide detailed information about their specializations within selected categories.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Completion Flow -->
    <div class="mb-8">
        <div class="bg-green-50 border border-green-200 rounded-lg p-6">
            <div class="flex items-center mb-4">
                <svg class="h-6 w-6 text-green-600 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="text-lg font-semibold text-green-900">After Onboarding Completion</h3>
            </div>
            <div class="space-y-3">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="w-6 h-6 bg-green-600 rounded-full flex items-center justify-center text-white text-xs font-bold">1</div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-800">Supplier profile is marked as complete and submitted for admin review</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="w-6 h-6 bg-green-600 rounded-full flex items-center justify-center text-white text-xs font-bold">2</div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-800">Category assignments are set to 'pending_approval' status</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="w-6 h-6 bg-green-600 rounded-full flex items-center justify-center text-white text-xs font-bold">3</div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-800">Supplier gains access to their dashboard and can start managing their profile</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="w-6 h-6 bg-green-600 rounded-full flex items-center justify-center text-white text-xs font-bold">4</div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-800">Admin can review and approve category assignments through the admin panel</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Admin Actions -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-blue-900 mb-4">Admin Review Process</h3>
        <p class="text-blue-800 mb-4">Once suppliers complete onboarding, you can:</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <h4 class="font-medium text-blue-900 mb-2">Review & Approve</h4>
                <ul class="text-sm text-blue-800 space-y-1">
                    <li>• Review supplier information and documents</li>
                    <li>• Approve or reject category assignments</li>
                    <li>• Set supplier status to active</li>
                </ul>
            </div>
            <div>
                <h4 class="font-medium text-blue-900 mb-2">Manage Access</h4>
                <ul class="text-sm text-blue-800 space-y-1">
                    <li>• Enable/disable supplier accounts</li>
                    <li>• Modify category assignments</li>
                    <li>• Monitor supplier activity</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection 