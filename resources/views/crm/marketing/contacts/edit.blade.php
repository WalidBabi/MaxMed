@extends('layouts.crm')

@section('title', 'Edit Marketing Contact')

@section('content')
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit Marketing Contact</h1>
                <p class="text-gray-600 mt-2">Update contact information and preferences</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('crm.marketing.contacts.show', $contact) }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M7.72 12.53a.75.75 0 010-1.06L10.94 8.25H6.5a.75.75 0 010-1.5h4.44L7.72 3.53a.75.75 0 011.06-1.06l4.25 4.25a.75.75 0 010 1.06l-4.25 4.25a.75.75 0 01-1.06 0z" clip-rule="evenodd" />
                    </svg>
                    Back to Contact
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Form -->
        <div class="lg:col-span-2">
            <form action="{{ route('crm.marketing.contacts.update', $contact) }}" method="POST" class="space-y-8">
                @csrf
                @method('PUT')

                <!-- Personal Information -->
                <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Personal Information</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700">First Name *</label>
                                <input type="text" name="first_name" id="first_name" required
                                       value="{{ old('first_name', $contact->first_name) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('first_name') border-red-300 @enderror">
                                @error('first_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name *</label>
                                <input type="text" name="last_name" id="last_name" required
                                       value="{{ old('last_name', $contact->last_name) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('last_name') border-red-300 @enderror">
                                @error('last_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">Email Address *</label>
                                <input type="email" name="email" id="email" required
                                       value="{{ old('email', $contact->email) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('email') border-red-300 @enderror">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                                <input type="tel" name="phone" id="phone"
                                       value="{{ old('phone', $contact->phone) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('phone') border-red-300 @enderror">
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('status') border-red-300 @enderror">
                                <option value="active" {{ old('status', $contact->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $contact->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="unsubscribed" {{ old('status', $contact->status) == 'unsubscribed' ? 'selected' : '' }}>Unsubscribed</option>
                                <option value="bounced" {{ old('status', $contact->status) == 'bounced' ? 'selected' : '' }}>Bounced</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Professional Information -->
                <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Professional Information</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="company" class="block text-sm font-medium text-gray-700">Company</label>
                                <input type="text" name="company" id="company"
                                       value="{{ old('company', $contact->company) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('company') border-red-300 @enderror">
                                @error('company')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="job_title" class="block text-sm font-medium text-gray-700">Job Title</label>
                                <input type="text" name="job_title" id="job_title"
                                       value="{{ old('job_title', $contact->job_title) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('job_title') border-red-300 @enderror">
                                @error('job_title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="industry" class="block text-sm font-medium text-gray-700">Industry</label>
                            <select name="industry" id="industry" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('industry') border-red-300 @enderror">
                                <option value="">Select Industry</option>
                                @php
                                    $currentIndustry = old('industry', $contact->industry);
                                    $predefinedIndustries = [
                                        'healthcare' => 'Healthcare',
                                        'laboratory' => 'Laboratory',
                                        'research' => 'Research',
                                        'academic' => 'Academic',
                                        'pharmaceutical' => 'Pharmaceutical',
                                        'biotechnology' => 'Biotechnology',
                                        'hospital' => 'Hospital',
                                        'clinic' => 'Clinic',
                                        'medical_devices' => 'Medical Devices',
                                        'diagnostics' => 'Diagnostics',
                                        'manufacturing' => 'Manufacturing',
                                        'technology' => 'Technology',
                                        'education' => 'Education',
                                        'government' => 'Government',
                                        'other' => 'Other'
                                    ];
                                    
                                    // If current industry is not in predefined list, add it
                                    if ($currentIndustry && !array_key_exists($currentIndustry, $predefinedIndustries)) {
                                        $predefinedIndustries[$currentIndustry] = ucwords(str_replace(['_', '-'], ' ', $currentIndustry));
                                    }
                                @endphp
                                
                                @foreach($predefinedIndustries as $value => $label)
                                    <option value="{{ $value }}" {{ $currentIndustry == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('industry')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            
                            @if($currentIndustry && !array_key_exists($currentIndustry, $predefinedIndustries))
                                <p class="mt-1 text-xs text-blue-600">
                                    <svg class="inline h-3 w-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                    Current industry "{{ ucwords(str_replace(['_', '-'], ' ', $currentIndustry)) }}" has been preserved
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Location Information -->
                <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Location Information</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="country" class="block text-sm font-medium text-gray-700">Country</label>
                                <input type="text" name="country" id="country"
                                       value="{{ old('country', $contact->country) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('country') border-red-300 @enderror">
                                @error('country')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                                <input type="text" name="city" id="city"
                                       value="{{ old('city', $contact->city) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('city') border-red-300 @enderror">
                                @error('city')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="source" class="block text-sm font-medium text-gray-700">Source</label>
                            <input type="text" name="source" id="source"
                                   value="{{ old('source', $contact->source) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('source') border-red-300 @enderror">
                            @error('source')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Contact Lists -->
                <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Contact Lists</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                            @forelse($contactLists as $list)
                                <label class="flex items-center">
                                    <input type="checkbox" name="contact_lists[]" value="{{ $list->id }}"
                                           {{ $contact->contactLists->contains($list->id) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">{{ $list->name }}</span>
                                </label>
                            @empty
                                <p class="text-sm text-gray-500">No contact lists available.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Notes</h3>
                    </div>
                    <div class="p-6">
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700">Additional Notes</label>
                            <textarea name="notes" id="notes" rows="4"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('notes') border-red-300 @enderror"
                                      placeholder="Any additional information about this contact...">{{ old('notes', $contact->notes) }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-4">
                    <a href="{{ route('crm.marketing.contacts.show', $contact) }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
                        Cancel
                    </a>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
                        Update Contact
                    </button>
                </div>
            </form>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Contact Status -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Contact Information</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <span class="text-sm font-medium text-gray-500">Contact ID:</span>
                        <span class="text-sm text-gray-900 block">#{{ $contact->id }}</span>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Created:</span>
                        <span class="text-sm text-gray-900 block">{{ $contact->created_at->format('M j, Y g:i A') }}</span>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Last Updated:</span>
                        <span class="text-sm text-gray-900 block">{{ $contact->updated_at->format('M j, Y g:i A') }}</span>
                    </div>
                    @if($contact->subscribed_at)
                    <div>
                        <span class="text-sm font-medium text-gray-500">Subscribed:</span>
                        <span class="text-sm text-gray-900 block">{{ $contact->subscribed_at->format('M j, Y g:i A') }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Current Status -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Current Status</h3>
                </div>
                <div class="p-6">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                        @if($contact->status === 'active') bg-green-100 text-green-800
                        @elseif($contact->status === 'unsubscribed') bg-red-100 text-red-800
                        @elseif($contact->status === 'bounced') bg-yellow-100 text-yellow-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ ucfirst($contact->status) }}
                    </span>
                </div>
            </div>

            <!-- Current Contact Lists -->
            @if($contact->contactLists->count() > 0)
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Current Contact Lists</h3>
                </div>
                <div class="p-6">
                    <div class="flex flex-wrap gap-2">
                        @foreach($contact->contactLists as $list)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $list->name }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection
