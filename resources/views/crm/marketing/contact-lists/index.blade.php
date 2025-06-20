@extends('layouts.crm')

@section('title', 'Contact Lists')

@section('content')
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Contact Lists</h1>
                <p class="text-gray-600 mt-2">Organize and segment your marketing contacts for targeted campaigns</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('crm.marketing.contact-lists.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                    </svg>
                    Create Contact List
                </a>
            </div>
        </div>
    </div>

    <!-- Contact Lists Grid -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($contactLists as $contactList)
            <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 overflow-hidden">
                <div class="px-6 py-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">
                                <a href="{{ route('crm.marketing.contact-lists.show', $contactList) }}" class="hover:text-indigo-600">
                                    {{ $contactList->name }}
                                </a>
                            </h3>
                            @if($contactList->description)
                                <p class="mt-1 text-sm text-gray-600">{{ Str::limit($contactList->description, 100) }}</p>
                            @endif
                        </div>
                        <div class="flex items-center space-x-2">
                            <!-- Type Badge -->
                            @if($contactList->type === 'dynamic')
                                <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
                                    <svg class="-ml-0.5 mr-1 h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M15.312 11.424a5.5 5.5 0 01-9.201 2.466l-.312-.311h2.433a.75.75 0 000-1.5H3.989a.75.75 0 00-.75.75v4.242a.75.75 0 001.5 0v-2.43l.31.31a7 7 0 0011.712-3.138.75.75 0 00-1.449-.39zm1.23-3.723a.75.75 0 00.219-.53V2.929a.75.75 0 00-1.5 0V5.36l-.31-.31A7 7 0 003.239 8.188a.75.75 0 101.448.389A5.5 5.5 0 0113.89 6.11l.311.31h-2.432a.75.75 0 000 1.5h4.243a.75.75 0 00.53-.219z" clip-rule="evenodd" />
                                    </svg>
                                    Dynamic
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                                    <svg class="-ml-0.5 mr-1 h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                    </svg>
                                    Static
                                </span>
                            @endif
                            
                            <!-- Status Badge -->
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
                    </div>

                    <!-- Stats -->
                    <div class="mt-4 grid grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Total Contacts</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ number_format($contactList->contacts_count ?? 0) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Active Contacts</dt>
                            <dd class="text-2xl font-bold text-green-600">{{ number_format($contactList->getActiveContactsCount()) }}</dd>
                        </div>
                    </div>

                    <!-- Created Info -->
                    <div class="mt-4 text-xs text-gray-500">
                        Created by {{ $contactList->creator->name ?? 'Unknown' }} on {{ $contactList->created_at->format('M j, Y') }}
                    </div>
                </div>

                <!-- Actions -->
                <div class="border-t border-gray-200 px-6 py-3">
                    <div class="flex items-center justify-between">
                        <div class="flex space-x-2">
                            <a href="{{ route('crm.marketing.contact-lists.show', $contactList) }}" class="text-sm text-indigo-600 hover:text-indigo-500 font-medium">
                                View Details
                            </a>
                            <span class="text-gray-300">•</span>
                            <a href="{{ route('crm.marketing.contact-lists.edit', $contactList) }}" class="text-sm text-gray-600 hover:text-gray-500">
                                Edit
                            </a>
                        </div>
                        @if($contactList->isDynamic())
                            <form action="{{ route('crm.marketing.contact-lists.refresh', $contactList) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-sm text-blue-600 hover:text-blue-500 font-medium">
                                    Refresh
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <h3 class="mt-2 text-sm font-semibold text-gray-900">No contact lists</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating a new contact list to organize your marketing contacts.</p>
                    <div class="mt-6">
                        <a href="{{ route('crm.marketing.contact-lists.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                            </svg>
                            Create Contact List
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($contactLists->hasPages())
        <div class="mt-8">
            {{ $contactLists->links() }}
        </div>
    @endif
@endsection 