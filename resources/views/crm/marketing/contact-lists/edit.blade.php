@extends('layouts.crm')

@section('title', 'Edit Contact List')

@section('content')
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit Contact List</h1>
                <p class="text-gray-600 mt-2">Update your contact list settings and criteria</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('crm.marketing.contact-lists.show', $contactList) }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M7.793 2.232a.75.75 0 01-.025 1.06L3.622 7.25h10.003a5.375 5.375 0 010 10.75H10.75a.75.75 0 010-1.5h2.875a3.875 3.875 0 000-7.75H3.622l4.146 3.957a.75.75 0 01-1.036 1.085l-5.5-5.25a.75.75 0 010-1.085l5.5-5.25a.75.75 0 011.06.025z" clip-rule="evenodd" />
                    </svg>
                    Back to List
                </a>
                <form action="{{ route('crm.marketing.contact-lists.destroy', $contactList) }}" method="POST" class="inline" 
                      onsubmit="return confirm('Are you sure you want to delete this contact list? This action cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" />
                        </svg>
                        Delete List
                    </button>
                </form>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('crm.marketing.contact-lists.update', $contactList) }}" class="space-y-8">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            <!-- Main Form -->
            <div class="lg:col-span-2">
                <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Basic Information</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">List Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name', $contactList->name) }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                   placeholder="e.g., Healthcare Professionals">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description" id="description" rows="3"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                      placeholder="Optional description of this contact list...">{{ old('description', $contactList->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $contactList->is_active) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm font-medium text-gray-700">Active</span>
                            </label>
                            <p class="mt-1 text-xs text-gray-500">Inactive lists won't be available for campaign targeting</p>
                        </div>

                        <!-- List Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">List Type <span class="text-red-500">*</span></label>
                            <div class="space-y-3">
                                <label class="flex items-start">
                                    <input type="radio" name="type" value="static" {{ old('type', $contactList->type) == 'static' ? 'checked' : '' }}
                                           class="mt-0.5 h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <div class="ml-3">
                                        <span class="text-sm font-medium text-gray-900">Static List</span>
                                        <p class="text-sm text-gray-500">Manually manage contacts. Add or remove contacts individually.</p>
                                    </div>
                                </label>
                                <label class="flex items-start">
                                    <input type="radio" name="type" value="dynamic" {{ old('type', $contactList->type) == 'dynamic' ? 'checked' : '' }}
                                           class="mt-0.5 h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <div class="ml-3">
                                        <span class="text-sm font-medium text-gray-900">Dynamic List</span>
                                        <p class="text-sm text-gray-500">Automatically populated based on criteria like industry, location, or other fields.</p>
                                    </div>
                                </label>
                            </div>
                            @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Static Contact Selection -->
                <div id="static-contacts" class="mt-8 card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5" style="display: {{ $contactList->type == 'static' ? 'block' : 'none' }};">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Select Contacts</h3>
                        <p class="text-sm text-gray-600 mt-1">Choose which contacts to add to this static list</p>
                    </div>
                    <div class="p-6">
                        @if(isset($marketingContacts) && $marketingContacts->count() > 0)
                            <!-- Search for contacts -->
                            <div class="mb-4">
                                <input type="text" id="contactSearch" placeholder="Search contacts by name or email..." 
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            
                            <!-- Select All/None buttons -->
                            <div class="mb-4 flex items-center space-x-3">
                                <button type="button" onclick="selectAllContacts()" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                    Select All
                                </button>
                                <button type="button" onclick="selectNoContacts()" class="text-sm font-medium text-gray-600 hover:text-gray-500">
                                    Select None
                                </button>
                                <span id="selectedCount" class="text-sm text-gray-500">0 selected</span>
                            </div>
                            
                            <!-- Contacts grid -->
                            <div class="max-h-80 overflow-y-auto border border-gray-200 rounded-md">
                                <div id="contactsList" class="grid grid-cols-1 gap-2 p-4">
                                    @foreach($marketingContacts as $contact)
                                        @php
                                            $isSelected = $contactList->contacts->contains($contact->id);
                                        @endphp
                                        <label class="contact-item flex items-center p-3 hover:bg-gray-50 rounded-md cursor-pointer" data-email="{{ strtolower($contact->email) }}" data-name="{{ strtolower($contact->full_name) }}">
                                            <input type="checkbox" name="contacts[]" value="{{ $contact->id }}"
                                                   class="contact-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                                   {{ $isSelected ? 'checked' : '' }}>
                                            <div class="ml-3 flex-1">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-8 w-8">
                                                        <div class="h-8 w-8 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center text-white text-xs font-bold">
                                                            {{ strtoupper(substr($contact->first_name, 0, 1) . substr($contact->last_name, 0, 1)) }}
                                                        </div>
                                                    </div>
                                                    <div class="ml-3">
                                                        <div class="text-sm font-medium text-gray-900">{{ $contact->full_name }}</div>
                                                        <div class="text-sm text-gray-500">{{ $contact->email }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                            @if($contact->company)
                                                <div class="text-sm text-gray-500">{{ $contact->company }}</div>
                                            @endif
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-semibold text-gray-900">No contacts available</h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    Create some marketing contacts first before adding them to lists.
                                </p>
                                <div class="mt-6">
                                    <a href="{{ route('crm.marketing.contacts.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                                        </svg>
                                        Create First Contact
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Dynamic Criteria -->
                <div id="dynamic-criteria" class="mt-8 card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5" style="display: {{ $contactList->type == 'dynamic' ? 'block' : 'none' }};">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Dynamic Criteria</h3>
                        <p class="text-sm text-gray-600 mt-1">Define rules to automatically populate this list with matching contacts</p>
                    </div>
                    <div class="p-6">
                        <div id="criteria-container">
                            @if(old('criteria', $contactList->criteria))
                                @foreach(old('criteria', $contactList->criteria) as $index => $criterion)
                                    <div class="criteria-row flex items-center space-x-3 mb-3">
                                        <select name="criteria[{{ $index }}][field]" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            <option value="">Select Field</option>
                                            <option value="industry" {{ ($criterion['field'] ?? '') == 'industry' ? 'selected' : '' }}>Industry</option>
                                            <option value="country" {{ ($criterion['field'] ?? '') == 'country' ? 'selected' : '' }}>Country</option>
                                            <option value="city" {{ ($criterion['field'] ?? '') == 'city' ? 'selected' : '' }}>City</option>
                                            <option value="job_title" {{ ($criterion['field'] ?? '') == 'job_title' ? 'selected' : '' }}>Job Title</option>
                                            <option value="company" {{ ($criterion['field'] ?? '') == 'company' ? 'selected' : '' }}>Company</option>
                                            <option value="status" {{ ($criterion['field'] ?? '') == 'status' ? 'selected' : '' }}>Status</option>
                                        </select>
                                        <select name="criteria[{{ $index }}][operator]" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            <option value="equals" {{ ($criterion['operator'] ?? '') == 'equals' ? 'selected' : '' }}>Equals</option>
                                            <option value="contains" {{ ($criterion['operator'] ?? '') == 'contains' ? 'selected' : '' }}>Contains</option>
                                            <option value="not_equals" {{ ($criterion['operator'] ?? '') == 'not_equals' ? 'selected' : '' }}>Not Equals</option>
                                            <option value="starts_with" {{ ($criterion['operator'] ?? '') == 'starts_with' ? 'selected' : '' }}>Starts With</option>
                                            <option value="ends_with" {{ ($criterion['operator'] ?? '') == 'ends_with' ? 'selected' : '' }}>Ends With</option>
                                        </select>
                                        <input type="text" name="criteria[{{ $index }}][value]" placeholder="Value" value="{{ $criterion['value'] ?? '' }}"
                                               class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <button type="button" onclick="removeCriteria(this)" class="text-red-600 hover:text-red-500">
                                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                                            </svg>
                                        </button>
                                    </div>
                                @endforeach
                            @else
                                <div class="criteria-row flex items-center space-x-3 mb-3">
                                    <select name="criteria[0][field]" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <option value="">Select Field</option>
                                        <option value="industry">Industry</option>
                                        <option value="country">Country</option>
                                        <option value="city">City</option>
                                        <option value="job_title">Job Title</option>
                                        <option value="company">Company</option>
                                        <option value="status">Status</option>
                                    </select>
                                    <select name="criteria[0][operator]" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <option value="equals">Equals</option>
                                        <option value="contains">Contains</option>
                                        <option value="not_equals">Not Equals</option>
                                        <option value="starts_with">Starts With</option>
                                        <option value="ends_with">Ends With</option>
                                    </select>
                                    <input type="text" name="criteria[0][value]" placeholder="Value"
                                           class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <button type="button" onclick="removeCriteria(this)" class="text-red-600 hover:text-red-500">
                                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                                        </svg>
                                    </button>
                                </div>
                            @endif
                        </div>
                        <button type="button" onclick="addCriteria()" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                            </svg>
                            Add Criteria
                        </button>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Current Stats -->
                <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Current Stats</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Total Contacts</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ number_format($contactList->getContactsCount()) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Active Contacts</dt>
                            <dd class="text-2xl font-bold text-green-600">{{ number_format($contactList->getActiveContactsCount()) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Type</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ ucfirst($contactList->type) }} List</dd>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                    <div class="p-6">
                        <div class="flex flex-col space-y-3">
                            <button type="submit" class="w-full inline-flex justify-center items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                                <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                </svg>
                                Update Contact List
                            </button>
                            <a href="{{ route('crm.marketing.contact-lists.show', $contactList) }}" class="w-full inline-flex justify-center items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script>
        let criteriaIndex = {{ count(old('criteria', $contactList->criteria ?? [])) }};

        // Show/hide sections based on list type
        document.addEventListener('DOMContentLoaded', function() {
            const typeRadios = document.querySelectorAll('input[name="type"]');
            const dynamicCriteria = document.getElementById('dynamic-criteria');
            const staticContacts = document.getElementById('static-contacts');

            function toggleSections() {
                const selectedType = document.querySelector('input[name="type"]:checked').value;
                if (selectedType === 'dynamic') {
                    dynamicCriteria.style.display = 'block';
                    staticContacts.style.display = 'none';
                } else {
                    dynamicCriteria.style.display = 'none';
                    staticContacts.style.display = 'block';
                }
            }

            typeRadios.forEach(radio => {
                radio.addEventListener('change', toggleSections);
            });
            
            updateSelectedCount();
        });

        function addCriteria() {
            const container = document.getElementById('criteria-container');
            const newRow = document.createElement('div');
            newRow.className = 'criteria-row flex items-center space-x-3 mb-3';
            newRow.innerHTML = `
                <select name="criteria[${criteriaIndex}][field]" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">Select Field</option>
                    <option value="industry">Industry</option>
                    <option value="country">Country</option>
                    <option value="city">City</option>
                    <option value="job_title">Job Title</option>
                    <option value="company">Company</option>
                    <option value="status">Status</option>
                </select>
                <select name="criteria[${criteriaIndex}][operator]" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="equals">Equals</option>
                    <option value="contains">Contains</option>
                    <option value="not_equals">Not Equals</option>
                    <option value="starts_with">Starts With</option>
                    <option value="ends_with">Ends With</option>
                </select>
                <input type="text" name="criteria[${criteriaIndex}][value]" placeholder="Value"
                       class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <button type="button" onclick="removeCriteria(this)" class="text-red-600 hover:text-red-500">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                    </svg>
                </button>
            `;
            container.appendChild(newRow);
            criteriaIndex++;
        }

        function removeCriteria(button) {
            const row = button.closest('.criteria-row');
            const container = document.getElementById('criteria-container');
            if (container.children.length > 1) {
                row.remove();
            }
        }

        // Contact selection functions
        function selectAllContacts() {
            const visibleCheckboxes = document.querySelectorAll('.contact-item:not([style*="display: none"]) .contact-checkbox');
            visibleCheckboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
            updateSelectedCount();
        }

        function selectNoContacts() {
            const checkboxes = document.querySelectorAll('.contact-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            updateSelectedCount();
        }

        function updateSelectedCount() {
            const selectedCheckboxes = document.querySelectorAll('.contact-checkbox:checked');
            const countElement = document.getElementById('selectedCount');
            if (countElement) {
                countElement.textContent = `${selectedCheckboxes.length} selected`;
            }
        }

        // Contact search functionality
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('contactSearch');
            const contactCheckboxes = document.querySelectorAll('.contact-checkbox');
            
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    const contactItems = document.querySelectorAll('.contact-item');
                    
                    contactItems.forEach(item => {
                        const email = item.dataset.email || '';
                        const name = item.dataset.name || '';
                        
                        if (email.includes(searchTerm) || name.includes(searchTerm)) {
                            item.style.display = 'flex';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            }

            // Update count when checkboxes change
            contactCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateSelectedCount);
            });
        });
    </script>
@endsection 