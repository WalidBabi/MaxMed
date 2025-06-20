@extends('layouts.crm')

@section('title', $contactList->name)

@section('content')
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center space-x-3">
                    <h1 class="text-3xl font-bold text-gray-900">{{ $contactList->name }}</h1>
                    @if($contactList->type === 'dynamic')
                        <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
                            Dynamic
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                            Static
                        </span>
                    @endif
                    @if($contactList->is_active)
                        <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                            Active
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">
                            Inactive
                        </span>
                    @endif
                </div>
                @if($contactList->description)
                    <p class="text-gray-600 mt-2">{{ $contactList->description }}</p>
                @endif
            </div>
            <div class="flex items-center space-x-3">
                @if($contactList->isDynamic())
                    <form action="{{ route('crm.marketing.contact-lists.refresh', $contactList) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M15.312 11.424a5.5 5.5 0 01-9.201 2.466l-.312-.311h2.433a.75.75 0 000-1.5H3.989a.75.75 0 00-.75.75v4.242a.75.75 0 001.5 0v-2.43l.31.31a7 7 0 0011.712-3.138.75.75 0 00-1.449-.39zm1.23-3.723a.75.75 0 00.219-.53V2.929a.75.75 0 00-1.5 0V5.36l-.31-.31A7 7 0 003.239 8.188a.75.75 0 101.448.389A5.5 5.5 0 0113.89 6.11l.311.31h-2.432a.75.75 0 000 1.5h4.243a.75.75 0 00.53-.219z" clip-rule="evenodd" />
                            </svg>
                            Refresh List
                        </button>
                    </form>
                @endif
                <a href="{{ route('crm.marketing.contact-lists.edit', $contactList) }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M2.695 14.763l-1.262 3.154a.5.5 0 00.65.65l3.155-1.262a4 4 0 001.343-.885L17.5 5.5a2.121 2.121 0 00-3-3L3.58 13.42a4 4 0 00-.885 1.343z" />
                    </svg>
                    Edit List
                </a>
                <a href="{{ route('crm.marketing.contact-lists.index') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M7.793 2.232a.75.75 0 01-.025 1.06L3.622 7.25h10.003a5.375 5.375 0 010 10.75H10.75a.75.75 0 010-1.5h2.875a3.875 3.875 0 000-7.75H3.622l4.146 3.957a.75.75 0 01-1.036 1.085l-5.5-5.25a.75.75 0 010-1.085l5.5-5.25a.75.75 0 011.06.025z" clip-rule="evenodd" />
                    </svg>
                    Back to Lists
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 px-6 py-4">
            <dt class="text-sm font-medium text-gray-500">Total Contacts</dt>
            <dd class="text-2xl font-bold text-gray-900">{{ number_format($contacts->total()) }}</dd>
        </div>
        <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 px-6 py-4">
            <dt class="text-sm font-medium text-gray-500">Active Contacts</dt>
            <dd class="text-2xl font-bold text-green-600">{{ number_format($contactList->getActiveContactsCount()) }}</dd>
        </div>
        <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 px-6 py-4">
            <dt class="text-sm font-medium text-gray-500">List Type</dt>
            <dd class="text-2xl font-bold text-blue-600">{{ ucfirst($contactList->type) }}</dd>
        </div>
        <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 px-6 py-4">
            <dt class="text-sm font-medium text-gray-500">Created</dt>
            <dd class="text-sm font-medium text-gray-900">{{ $contactList->created_at->format('M j, Y') }}</dd>
            <dd class="text-xs text-gray-500">by {{ $contactList->creator->name ?? 'Unknown' }}</dd>
        </div>
    </div>

    @if($contactList->isDynamic() && $contactList->criteria)
        <!-- Dynamic Criteria -->
        <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Dynamic Criteria</h3>
            </div>
            <div class="p-6">
                <div class="space-y-2">
                    @foreach($contactList->criteria as $criterion)
                        <div class="flex items-center space-x-2 text-sm">
                            <span class="font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $criterion['field'] ?? '')) }}</span>
                            <span class="text-gray-500">{{ $criterion['operator'] ?? '' }}</span>
                            <span class="text-gray-900">"{{ $criterion['value'] ?? '' }}"</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Contacts Table -->
    <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Contacts in this List</h3>
                <div class="text-sm text-gray-500">
                    {{ $contacts->total() }} total contacts
                </div>
            </div>
        </div>
        
        @if($contacts->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Contact
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Company
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Industry
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Added
                            </th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($contacts as $contact)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center text-white text-sm font-bold">
                                                {{ strtoupper(substr($contact->first_name, 0, 1) . substr($contact->last_name, 0, 1)) }}
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $contact->first_name }} {{ $contact->last_name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $contact->email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $contact->company ?? '-' }}</div>
                                    @if($contact->job_title)
                                        <div class="text-sm text-gray-500">{{ $contact->job_title }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($contact->industry)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ ucfirst($contact->industry) }}
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($contact->status == 'active')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    @elseif($contact->status == 'unsubscribed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Unsubscribed
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ ucfirst($contact->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($contact->pivot && $contact->pivot->added_at)
                                        {{ \Carbon\Carbon::parse($contact->pivot->added_at)->format('M j, Y') }}
                                    @else
                                        {{ $contact->created_at->format('M j, Y') }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('crm.marketing.contacts.show', $contact) }}" class="text-indigo-600 hover:text-indigo-900" title="View Contact">
                                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M10 12.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z" />
                                                <path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 010-1.186A10.004 10.004 0 0110 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0110 17c-4.257 0-7.893-2.66-9.336-6.41zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('crm.marketing.contacts.edit', $contact) }}" class="text-gray-600 hover:text-gray-900" title="Edit Contact">
                                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M2.695 14.763l-1.262 3.154a.5.5 0 00.65.65l3.155-1.262a4 4 0 001.343-.885L17.5 5.5a2.121 2.121 0 00-3-3L3.58 13.42a4 4 0 00-.885 1.343z" />
                                            </svg>
                                        </a>
                                        <button onclick="confirmDelete('{{ $contact->id }}', '{{ $contact->full_name }}', '{{ $contact->email }}')" class="text-red-600 hover:text-red-900" title="Delete Contact">
                                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <h3 class="mt-2 text-sm font-semibold text-gray-900">No contacts</h3>
                <p class="mt-1 text-sm text-gray-500">
                    @if($contactList->isDynamic())
                        This dynamic list hasn't matched any contacts yet. Try adjusting the criteria or refresh the list.
                    @else
                        This static list is empty. Add contacts manually or import them from a CSV file.
                    @endif
                </p>
            </div>
        @endif
    </div>

    <!-- Pagination -->
    @if($contacts->hasPages())
        <div class="mt-8">
            {{ $contacts->links() }}
        </div>
    @endif

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