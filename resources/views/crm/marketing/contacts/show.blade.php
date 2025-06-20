@extends('layouts.crm')

@section('title', 'Contact Details')

@section('content')
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('crm.marketing.contacts.index') }}" class="text-indigo-600 hover:text-indigo-800">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                    </a>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $contact->first_name }} {{ $contact->last_name }}</h1>
                </div>
                <p class="text-gray-600 mt-2">Marketing contact details and campaign statistics</p>
            </div>
            <div class="flex items-center space-x-3">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                    @if($contact->status === 'active') bg-green-100 text-green-800
                    @elseif($contact->status === 'unsubscribed') bg-red-100 text-red-800
                    @elseif($contact->status === 'bounced') bg-yellow-100 text-yellow-800
                    @else bg-gray-100 text-gray-800
                    @endif">
                    {{ ucfirst($contact->status) }}
                </span>
                <a href="{{ route('crm.marketing.contacts.edit', $contact) }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="m2.695 14.762-1.262 3.155a.5.5 0 0 0 .65.65l3.155-1.262a4 4 0 0 0 1.343-.886L17.5 5.501a2.121 2.121 0 0 0-3-3L3.58 13.419a4 4 0 0 0-.885 1.343Z" />
                    </svg>
                    Edit Contact
                </a>
                <button onclick="confirmDelete('{{ $contact->id }}', '{{ $contact->full_name }}', '{{ $contact->email }}')" class="inline-flex items-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" />
                    </svg>
                    Delete Contact
                </button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Personal Information -->
            <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Personal Information</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">First Name</label>
                            <div class="mt-1 text-sm text-gray-900">{{ $contact->first_name ?: 'Not provided' }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Last Name</label>
                            <div class="mt-1 text-sm text-gray-900">{{ $contact->last_name ?: 'Not provided' }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email Address</label>
                            <div class="mt-1 text-sm text-gray-900">
                                <a href="mailto:{{ $contact->email }}" class="text-indigo-600 hover:text-indigo-800">{{ $contact->email }}</a>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Phone Number</label>
                            <div class="mt-1 text-sm text-gray-900">
                                @if($contact->phone)
                                    <a href="tel:{{ $contact->phone }}" class="text-indigo-600 hover:text-indigo-800">{{ $contact->phone }}</a>
                                @else
                                    Not provided
                                @endif
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Source</label>
                            <div class="mt-1 text-sm text-gray-900">{{ $contact->source ?: 'Not specified' }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Subscribed At</label>
                            <div class="mt-1 text-sm text-gray-900">
                                @if($contact->subscribed_at)
                                    {{ $contact->subscribed_at->format('M j, Y g:i A') }}
                                @else
                                    Not specified
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Professional Information -->
            @if($contact->company || $contact->job_title || $contact->industry)
            <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Professional Information</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Company</label>
                            <div class="mt-1 text-sm text-gray-900">{{ $contact->company ?: 'Not provided' }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Job Title</label>
                            <div class="mt-1 text-sm text-gray-900">{{ $contact->job_title ?: 'Not provided' }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Industry</label>
                            <div class="mt-1 text-sm text-gray-900">{{ $contact->industry ? ucfirst($contact->industry) : 'Not specified' }}</div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Location Information -->
            @if($contact->country || $contact->city)
            <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Location Information</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Country</label>
                            <div class="mt-1 text-sm text-gray-900">{{ $contact->country ?: 'Not provided' }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">City</label>
                            <div class="mt-1 text-sm text-gray-900">{{ $contact->city ?: 'Not provided' }}</div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Contact Lists -->
            @if($contact->contactLists->count() > 0)
            <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Contact Lists</h3>
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

            <!-- Notes -->
            @if($contact->notes)
            <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Notes</h3>
                </div>
                <div class="p-6">
                    <div class="text-sm text-gray-900 whitespace-pre-wrap">{{ $contact->notes }}</div>
                </div>
            </div>
            @endif
        </div>
        
        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Campaign Statistics -->
            @if($campaignStats)
            <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Campaign Statistics</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Total Campaigns</span>
                            <span class="text-sm font-bold text-gray-900">{{ $campaignStats->total_campaigns ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Opened Campaigns</span>
                            <span class="text-sm font-bold text-gray-900">{{ $campaignStats->opened_campaigns ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Total Opens</span>
                            <span class="text-sm font-bold text-gray-900">{{ $campaignStats->total_opens ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Total Clicks</span>
                            <span class="text-sm font-bold text-gray-900">{{ $campaignStats->total_clicks ?? 0 }}</span>
                        </div>
                        @if($campaignStats->total_campaigns > 0)
                        <div class="pt-2 border-t border-gray-200">
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500">Open Rate</span>
                                <span class="text-sm font-bold text-green-600">
                                    {{ round(($campaignStats->opened_campaigns / $campaignStats->total_campaigns) * 100, 1) }}%
                                </span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Quick Actions -->
            <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                </div>
                <div class="p-6 space-y-3">
                                    <a href="mailto:{{ $contact->email }}" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-lg font-medium text-sm inline-flex items-center justify-center transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                    </svg>
                    Send Email
                </a>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Contact Information</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <span class="text-sm font-medium text-gray-500">Created:</span>
                        <span class="text-sm text-gray-900 block">{{ $contact->created_at->format('M j, Y g:i A') }}</span>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Last Updated:</span>
                        <span class="text-sm text-gray-900 block">{{ $contact->updated_at->format('M j, Y g:i A') }}</span>
                    </div>
                    @if($contact->last_activity_at)
                    <div>
                        <span class="text-sm font-medium text-gray-500">Last Activity:</span>
                        <span class="text-sm text-gray-900 block">{{ $contact->last_activity_at->format('M j, Y g:i A') }}</span>
                    </div>
                    @endif
                    <div>
                        <span class="text-sm font-medium text-gray-500">Contact ID:</span>
                        <span class="text-sm text-gray-900 block">#{{ $contact->id }}</span>
                    </div>
                </div>
            </div>

            <!-- Delete Contact -->
            <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-red-200/50">
                <div class="px-6 py-4 border-b border-red-100">
                    <h3 class="text-lg font-semibold text-red-900">Danger Zone</h3>
                </div>
                <div class="p-6">
                    <p class="text-sm text-gray-600 mb-4">Permanently delete this contact. This action cannot be undone.</p>
                    <button onclick="confirmDelete('{{ $contact->id }}', '{{ $contact->full_name }}', '{{ $contact->email }}')" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-lg font-medium text-sm transition-colors duration-200">
                        <svg class="w-4 h-4 inline mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                        Delete Contact
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                </div>
                <div class="mt-3 text-center">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Delete Contact</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500">
                            Are you sure you want to delete <strong id="deleteContactName"></strong> (<span id="deleteContactEmail"></span>)? This action cannot be undone.
                        </p>
                    </div>
                    <div class="items-center px-4 py-3">
                        <form id="deleteContactForm" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md w-auto mr-2 hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-auto hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function confirmDelete(contactId, contactName, contactEmail) {
    document.getElementById('deleteContactName').textContent = contactName;
    document.getElementById('deleteContactEmail').textContent = contactEmail;
    document.getElementById('deleteContactForm').action = `/crm/marketing/contacts/${contactId}`;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// Close modal when clicking outside
document.addEventListener('DOMContentLoaded', function() {
    const deleteModal = document.getElementById('deleteModal');
    
    deleteModal.addEventListener('click', function(e) {
        if (e.target === deleteModal) {
            closeDeleteModal();
        }
    });
});
</script>
@endpush
