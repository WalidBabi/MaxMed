@extends('admin.layouts.app')

@section('title', 'Supplier Profile - ' . $supplier->name)

@section('content')
<div class="-mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8">
    
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <nav class="flex" aria-label="Breadcrumb">
                    <ol role="list" class="flex items-center space-x-4">
                        <li>
                            <div class="flex">
                                <a href="{{ route('admin.supplier-profiles.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">Supplier Profiles</a>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="h-5 w-5 flex-shrink-0 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span class="ml-4 text-sm font-medium text-gray-500">{{ $supplier->name }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h1 class="text-3xl font-bold text-gray-900 mt-2">{{ $supplier->name }}</h1>
                @if($supplier->supplierInformation)
                    <p class="text-gray-600 mt-1">{{ $supplier->supplierInformation->company_name ?: 'Company name not provided' }}</p>
                @endif
            </div>
            
            <!-- Action Buttons -->
            <div class="flex space-x-3">
                @if($supplier->supplierInformation && $supplier->supplierInformation->status !== 'active')
                    <form method="POST" action="{{ route('admin.supplier-profiles.update-status', $supplier) }}" class="inline">
                        @csrf
                        <input type="hidden" name="status" value="active">
                        <button type="submit" class="inline-flex items-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Approve Supplier
                        </button>
                    </form>
                @endif
                
                <a href="mailto:{{ $supplier->email }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                    </svg>
                    Contact Supplier
                </a>
            </div>
        </div>
    </div>

    @if($supplier->supplierInformation)
        <!-- Status Update Form -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-base font-semibold leading-6 text-gray-900">Supplier Status</h3>
                <form method="POST" action="{{ route('admin.supplier-profiles.update-status', $supplier) }}" class="mt-4">
                    @csrf
                    <div class="flex items-center space-x-4">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" id="status" class="mt-1 block w-48 rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                <option value="active" {{ $supplier->supplierInformation->status === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="pending_approval" {{ $supplier->supplierInformation->status === 'pending_approval' ? 'selected' : '' }}>Pending Approval</option>
                                <option value="suspended" {{ $supplier->supplierInformation->status === 'suspended' ? 'selected' : '' }}>Suspended</option>
                                <option value="inactive" {{ $supplier->supplierInformation->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="flex-1">
                            <label for="notes" class="block text-sm font-medium text-gray-700">Admin Notes (Optional)</label>
                            <input type="text" name="notes" id="notes" placeholder="Reason for status change..." class="mt-1 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        </div>
                        <div class="pt-6">
                            <button type="submit" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                                Update Status
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Performance Metrics -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
            <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                <dt class="truncate text-sm font-medium text-gray-500">Total Products</dt>
                <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ $performanceMetrics['total_products'] }}</dd>
            </div>
            <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                <dt class="truncate text-sm font-medium text-gray-500">Active Products</dt>
                <dd class="mt-1 text-3xl font-semibold tracking-tight text-green-600">{{ $performanceMetrics['active_products'] }}</dd>
            </div>
            <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                <dt class="truncate text-sm font-medium text-gray-500">Categories</dt>
                <dd class="mt-1 text-3xl font-semibold tracking-tight text-blue-600">{{ $performanceMetrics['total_categories'] }}</dd>
            </div>
            <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                <dt class="truncate text-sm font-medium text-gray-500">Win Rate</dt>
                <dd class="mt-1 text-3xl font-semibold tracking-tight text-purple-600">{{ $performanceMetrics['win_rate'] }}%</dd>
            </div>
        </div>

        <!-- Tabs -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div x-data="{ activeTab: 'company' }" class="">
                <!-- Tab Navigation -->
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                        <button @click="activeTab = 'company'" :class="activeTab === 'company' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Company Information
                        </button>
                        <button @click="activeTab = 'documents'" :class="activeTab === 'documents' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Documents
                        </button>
                        <button @click="activeTab = 'categories'" :class="activeTab === 'categories' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Categories & Specializations
                        </button>
                        <button @click="activeTab = 'activity'" :class="activeTab === 'activity' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Recent Activity
                        </button>
                    </nav>
                </div>

                <!-- Tab Content -->
                <div class="p-6">
                    <!-- Company Information Tab -->
                    <div x-show="activeTab === 'company'">
                        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                            <!-- Basic Company Info -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                                <dl class="space-y-3">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Company Name</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $supplier->supplierInformation->company_name ?: 'Not provided' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Business Registration Number</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $supplier->supplierInformation->business_registration_number ?: 'Not provided' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Tax Registration Number</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $supplier->supplierInformation->tax_registration_number ?: 'Not provided' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Trade License Number</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $supplier->supplierInformation->trade_license_number ?: 'Not provided' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Years in Business</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $supplier->supplierInformation->years_in_business ?: 'Not provided' }}</dd>
                                    </div>
                                    @if($supplier->supplierInformation->website)
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Website</dt>
                                            <dd class="mt-1 text-sm text-gray-900">
                                                <a href="{{ $supplier->supplierInformation->website }}" target="_blank" class="text-indigo-600 hover:text-indigo-500">{{ $supplier->supplierInformation->website }}</a>
                                            </dd>
                                        </div>
                                    @endif
                                </dl>
                            </div>

                            <!-- Contact Information -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Contact Information</h3>
                                <dl class="space-y-3">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Primary Contact Name</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $supplier->supplierInformation->primary_contact_name ?: 'Not provided' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Primary Contact Position</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $supplier->supplierInformation->primary_contact_position ?: 'Not provided' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Primary Contact Email</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            @if($supplier->supplierInformation->primary_contact_email)
                                                <a href="mailto:{{ $supplier->supplierInformation->primary_contact_email }}" class="text-indigo-600 hover:text-indigo-500">{{ $supplier->supplierInformation->primary_contact_email }}</a>
                                            @else
                                                Not provided
                                            @endif
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Primary Contact Phone</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $supplier->supplierInformation->primary_contact_phone ?: 'Not provided' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Primary Phone</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $supplier->supplierInformation->phone_primary ?: 'Not provided' }}</dd>
                                    </div>
                                </dl>
                            </div>

                            <!-- Address Information -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Address</h3>
                                <dl class="space-y-3">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Business Address</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $supplier->supplierInformation->formatted_address ?: 'Not provided' }}</dd>
                                    </div>
                                </dl>
                            </div>

                            <!-- Company Description -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Company Description</h3>
                                <p class="text-sm text-gray-900">{{ $supplier->supplierInformation->company_description ?: 'No description provided' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Documents Tab -->
                    <div x-show="activeTab === 'documents'">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Uploaded Documents</h3>
                        @php
                            $documents = $supplier->supplierInformation->documents ?? [];
                        @endphp
                        
                        @if(!empty($documents))
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                @foreach($documents as $type => $path)
                                    @if($type !== 'certification_files')
                                        <div class="border border-gray-200 rounded-lg p-4">
                                            <div class="flex items-center">
                                                <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <div class="ml-3 flex-1">
                                                    <h4 class="text-sm font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $type)) }}</h4>
                                                    <p class="text-sm text-gray-500">{{ basename($path) }}</p>
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <a href="{{ route('admin.supplier-profiles.download-document', [$supplier, $type]) }}" class="text-sm text-indigo-600 hover:text-indigo-500">
                                                    Download
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach

                                @if(isset($documents['certification_files']) && is_array($documents['certification_files']))
                                    @foreach($documents['certification_files'] as $index => $certPath)
                                        <div class="border border-gray-200 rounded-lg p-4">
                                            <div class="flex items-center">
                                                <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <div class="ml-3 flex-1">
                                                    <h4 class="text-sm font-medium text-gray-900">Certification {{ $index + 1 }}</h4>
                                                    <p class="text-sm text-gray-500">{{ basename($certPath) }}</p>
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <a href="{{ route('admin.supplier-profiles.download-certification', [$supplier, $index]) }}" class="text-sm text-indigo-600 hover:text-indigo-500">
                                                    Download
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        @else
                            <p class="text-sm text-gray-500">No documents uploaded.</p>
                        @endif
                    </div>

                    <!-- Categories & Specializations Tab -->
                    <div x-show="activeTab === 'categories'">
                        <div class="space-y-6">
                            <!-- Assigned Categories -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Assigned Categories</h3>
                                @if($supplier->activeSupplierCategories->count() > 0)
                                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                        @foreach($supplier->activeSupplierCategories as $assignment)
                                            <div class="border border-gray-200 rounded-lg p-4">
                                                <h4 class="text-sm font-medium text-gray-900">{{ $assignment->category->name }}</h4>
                                                <p class="text-sm text-gray-500 mt-1">Status: {{ ucfirst($assignment->status) }}</p>
                                                @if($assignment->assigned_at)
                                                    <p class="text-xs text-gray-400 mt-1">Assigned: {{ $assignment->assigned_at->format('M j, Y') }}</p>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-sm text-gray-500">No categories assigned.</p>
                                @endif
                            </div>

                            <!-- Specializations -->
                            @if($supplier->supplierInformation->specializations)
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Specializations</h3>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($supplier->supplierInformation->specializations as $specialization)
                                            <span class="inline-flex items-center rounded-full bg-blue-100 px-3 py-0.5 text-sm font-medium text-blue-800">
                                                {{ $specialization }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Suggested Categories -->
                            @if($supplier->supplierInformation->suggested_categories)
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Suggested Categories</h3>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($supplier->supplierInformation->suggested_categories as $suggestion)
                                            <span class="inline-flex items-center rounded-full bg-yellow-100 px-3 py-0.5 text-sm font-medium text-yellow-800">
                                                {{ $suggestion }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Recent Activity Tab -->
                    <div x-show="activeTab === 'activity'">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Activity</h3>
                        @if($recentActivity->count() > 0)
                            <div class="flow-root">
                                <ul role="list" class="-mb-8">
                                    @foreach($recentActivity as $activity)
                                        <li>
                                            <div class="relative pb-8">
                                                @if(!$loop->last)
                                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                                @endif
                                                <div class="relative flex space-x-3">
                                                    <div>
                                                        <span class="h-8 w-8 rounded-full bg-gray-400 flex items-center justify-center ring-8 ring-white">
                                                            @if($activity['type'] === 'product')
                                                                <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M10 2L3 7v11a2 2 0 002 2h10a2 2 0 002-2V7l-7-5zM10 12a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                                                </svg>
                                                            @else
                                                                <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z" clip-rule="evenodd" />
                                                                </svg>
                                                            @endif
                                                        </span>
                                                    </div>
                                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                        <div>
                                                            <p class="text-sm text-gray-500">{{ $activity['description'] }}</p>
                                                        </div>
                                                        <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                            {{ $activity['timestamp']->diffForHumans() }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            <p class="text-sm text-gray-500">No recent activity.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- No Supplier Information -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-12 sm:px-6 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No Supplier Information</h3>
                <p class="mt-1 text-sm text-gray-500">This supplier has not completed the onboarding process yet.</p>
            </div>
        </div>
    @endif
</div>
@endsection 