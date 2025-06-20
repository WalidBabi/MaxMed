@extends('layouts.crm')

@section('title', 'Marketing Contacts')

@section('content')
    <!-- Import Errors -->
    @if(session('import_errors'))
        <div class="mb-6 rounded-md bg-yellow-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">Import completed with some errors:</h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach(session('import_errors') as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Marketing Contacts</h1>
                <p class="text-gray-600 mt-2">Manage your marketing contact database and segmentation</p>
            </div>
            <div class="flex items-center space-x-3">
                <button type="button" onclick="openImportModal()" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a.75.75 0 01.55.24l3.25 3.5a.75.75 0 11-1.1 1.02L10 4.852 7.3 7.76a.75.75 0 01-1.1-1.02l3.25-3.5A.75.75 0 0110 3zm-3.76 9.2a.75.75 0 011.06.04l2.7 2.908 2.7-2.908a.75.75 0 111.1 1.02l-3.25 3.5a.75.75 0 01-1.1 0l-3.25-3.5a.75.75 0 01.04-1.06z" clip-rule="evenodd" />
                    </svg>
                    Import Contacts
                </button>
                <a href="{{ route('crm.marketing.contacts.export', request()->query()) }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 17a.75.75 0 01-.75-.75V5.612L5.29 9.77a.75.75 0 01-1.08-1.04l5.25-5.5a.75.75 0 011.08 0l5.25 5.5a.75.75 0 11-1.08 1.04L10.75 5.612V16.25A.75.75 0 0110 17z" clip-rule="evenodd" />
                    </svg>
                    Export Data
                </a>
                <a href="{{ route('crm.marketing.contacts.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                    </svg>
                    Add Contact
                </a>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Search & Filter Contacts</h3>
        </div>
        <div class="p-6">
            <form method="GET" action="{{ route('crm.marketing.contacts.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                        <input type="text" name="search" id="search" 
                               value="{{ request('search') }}"
                               placeholder="Name, email, or company..."
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">All Statuses</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="unsubscribed" {{ request('status') == 'unsubscribed' ? 'selected' : '' }}>Unsubscribed</option>
                            <option value="bounced" {{ request('status') == 'bounced' ? 'selected' : '' }}>Bounced</option>
                        </select>
                    </div>
                    <div>
                        <label for="industry" class="block text-sm font-medium text-gray-700">Industry</label>
                        <select name="industry" id="industry" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">All Industries</option>
                            <option value="healthcare" {{ request('industry') == 'healthcare' ? 'selected' : '' }}>Healthcare</option>
                            <option value="laboratory" {{ request('industry') == 'laboratory' ? 'selected' : '' }}>Laboratory</option>
                            <option value="research" {{ request('industry') == 'research' ? 'selected' : '' }}>Research</option>
                            <option value="academic" {{ request('industry') == 'academic' ? 'selected' : '' }}>Academic</option>
                            <option value="pharmaceutical" {{ request('industry') == 'pharmaceutical' ? 'selected' : '' }}>Pharmaceutical</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <div class="flex space-x-2">
                            <button type="submit" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                                <svg class="-ml-0.5 mr-1.5 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                                </svg>
                                Filter
                            </button>
                            <a href="{{ route('crm.marketing.contacts.index') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                Clear
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Contacts Table -->
    <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Contacts</h3>
                <div class="text-sm text-gray-500">
                    {{ isset($contacts) ? $contacts->total() : 0 }} total contacts
                </div>
            </div>
        </div>
        
        @if(isset($contacts) && $contacts->count() > 0)
            <div class="overflow-x-auto overflow-y-hidden contacts-table-container" style="scrollbar-width: thin; scrollbar-color: #d1d5db #f9fafb;">
                <div class="inline-block min-w-full align-middle">
                    <table class="min-w-full divide-y divide-gray-200 contacts-table" style="min-width: 800px;">
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
                                Campaigns
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Last Activity
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
                                    @elseif($contact->status == 'inactive')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Inactive
                                        </span>
                                    @elseif($contact->status == 'unsubscribed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Unsubscribed
                                        </span>
                                    @elseif($contact->status == 'bounced')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Bounced
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $contact->campaigns_count ?? 0 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ formatDubaiDateForHumans($contact->updated_at) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center space-x-2">
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
            </div>

            <!-- Pagination -->
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex-1 flex justify-between sm:hidden">
                    @if($contacts->onFirstPage())
                        <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-500 bg-white cursor-default">
                            Previous
                        </span>
                    @else
                        <a href="{{ $contacts->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Previous
                        </a>
                    @endif

                    @if($contacts->hasMorePages())
                        <a href="{{ $contacts->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Next
                        </a>
                    @else
                        <span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-500 bg-white cursor-default">
                            Next
                        </span>
                    @endif
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing <span class="font-medium">{{ $contacts->firstItem() }}</span> to <span class="font-medium">{{ $contacts->lastItem() }}</span> of <span class="font-medium">{{ $contacts->total() }}</span> contacts
                        </p>
                    </div>
                    <div>
                        {{ $contacts->links() }}
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                </svg>
                <h3 class="mt-2 text-sm font-semibold text-gray-900">No contacts found</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by adding your first marketing contact.</p>
                <div class="mt-6">
                    <a href="{{ route('crm.marketing.contacts.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                        </svg>
                        Add Contact
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- Import Modal -->
    <div id="importModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Import Contacts</h3>
                    <button type="button" onclick="closeImportModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('crm.marketing.contacts.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <input type="hidden" name="step" value="preview">
                    
                    <div>
                        <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                            CSV File
                        </label>
                        <input type="file" 
                               name="file" 
                               id="file" 
                               accept=".csv,.txt,.xlsx"
                               required
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        <p class="mt-1 text-xs text-gray-500">Supported formats: CSV, TXT, XLSX (Max: 10MB)</p>
                    </div>

                    <div>
                        <label for="contact_list_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Contact List (Optional)
                        </label>
                        <select name="contact_list_id" id="contact_list_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">Select a contact list...</option>
                            @if(isset($contactLists))
                                @foreach($contactLists as $list)
                                    <option value="{{ $list->id }}">{{ $list->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Contacts will be added to the selected list</p>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-md p-3">
                        <h4 class="text-sm font-medium text-blue-800 mb-2">Smart Column Detection:</h4>
                        <ul class="text-xs text-blue-700 space-y-1">
                            <li>• Our system will automatically detect and map your CSV columns</li>
                            <li>• Works with various column names (e.g., "First Name", "fname", "first_name")</li>
                            <li>• You can review and adjust mappings before importing</li>
                            <li>• Email column is required and will be automatically detected</li>
                        </ul>
                        <div class="mt-2">
                            <button type="button" onclick="downloadTemplate()" class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                📥 Download CSV Template
                            </button>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" onclick="closeImportModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700">
                            <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Preview & Map Columns
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
/* Custom scrollbar for the contacts table */
.contacts-table-container::-webkit-scrollbar {
    height: 8px;
}

.contacts-table-container::-webkit-scrollbar-track {
    background: #f9fafb;
    border-radius: 4px;
}

.contacts-table-container::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 4px;
}

.contacts-table-container::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}

/* Ensure table columns have proper min-widths */
.contacts-table th:nth-child(1), 
.contacts-table td:nth-child(1) {
    min-width: 250px; /* Contact info */
}

.contacts-table th:nth-child(2), 
.contacts-table td:nth-child(2) {
    min-width: 200px; /* Company */
}

.contacts-table th:nth-child(3), 
.contacts-table td:nth-child(3) {
    min-width: 120px; /* Industry */
}

.contacts-table th:nth-child(4), 
.contacts-table td:nth-child(4) {
    min-width: 100px; /* Status */
}

.contacts-table th:nth-child(5), 
.contacts-table td:nth-child(5) {
    min-width: 100px; /* Campaigns */
}

.contacts-table th:nth-child(6), 
.contacts-table td:nth-child(6) {
    min-width: 140px; /* Last Activity */
}

.contacts-table th:nth-child(7), 
.contacts-table td:nth-child(7) {
    min-width: 80px; /* Actions */
}
</style>
@endpush

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

@push('scripts')
<script>
function openImportModal() {
    document.getElementById('importModal').classList.remove('hidden');
}

function closeImportModal() {
    document.getElementById('importModal').classList.add('hidden');
}

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
    const importModal = document.getElementById('importModal');
    const deleteModal = document.getElementById('deleteModal');
    
    importModal.addEventListener('click', function(e) {
        if (e.target === importModal) {
            closeImportModal();
        }
    });
    
    deleteModal.addEventListener('click', function(e) {
        if (e.target === deleteModal) {
            closeDeleteModal();
        }
    });
});

// Download CSV template
function downloadTemplate() {
    const csvContent = "first_name,last_name,email,phone,company,job_title,industry,country,city,notes\nJohn,Doe,john.doe@example.com,+1234567890,Example Corp,Manager,Healthcare,USA,New York,Sample contact\nJane,Smith,jane.smith@example.com,+0987654321,Tech Inc,Developer,Technology,Canada,Toronto,Another sample";
    
    const blob = new Blob([csvContent], { type: 'text/csv;charset-utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    link.setAttribute('href', url);
    link.setAttribute('download', 'contacts_template.csv');
    link.style.visibility = 'hidden';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>
@endpush 