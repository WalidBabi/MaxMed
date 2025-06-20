@extends('layouts.crm')

@section('title', 'Edit Campaign')

@section('content')
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit Campaign</h1>
                <p class="text-gray-600 mt-2">Modify your marketing campaign</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('crm.marketing.campaigns.show', $campaign) }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    View Campaign
                </a>
                <a href="{{ route('crm.marketing.campaigns.index') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    Back to Campaigns
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('crm.marketing.campaigns.update', $campaign) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Campaign Details</h3>
            
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Campaign Name</label>
                    <input type="text" name="name" id="name" required value="{{ old('name', $campaign->name) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="draft" {{ old('status', $campaign->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="scheduled" {{ old('status', $campaign->status) == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="sent" {{ old('status', $campaign->status) == 'sent' ? 'selected' : '' }}>Sent</option>
                        <option value="cancelled" {{ old('status', $campaign->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="mt-6">
                <label for="subject" class="block text-sm font-medium text-gray-700">Email Subject</label>
                <input type="text" name="subject" id="subject" value="{{ old('subject', $campaign->subject) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                @error('subject')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6">
                <label for="scheduled_at" class="block text-sm font-medium text-gray-700">Schedule Date & Time</label>
                <input type="datetime-local" name="scheduled_at" id="scheduled_at" value="{{ old('scheduled_at', $campaign->scheduled_at ? $campaign->scheduled_at->format('Y-m-d\TH:i') : '') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                @error('scheduled_at')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Recipients Section -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Recipients</h3>
            
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Select Recipients</label>
                    <div class="space-y-4">
                        <label class="flex items-center">
                            <input type="radio" name="recipient_type" value="all" 
                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                   {{ old('recipient_type', $campaign->recipients_criteria['type'] ?? 'all') == 'all' ? 'checked' : '' }}>
                            <span class="ml-2 text-sm text-gray-700">All Active Contacts</span>
                        </label>
                        
                        <label class="flex items-center">
                            <input type="radio" name="recipient_type" value="lists" 
                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                   {{ old('recipient_type', $campaign->recipients_criteria['type'] ?? 'all') == 'lists' ? 'checked' : '' }}>
                            <span class="ml-2 text-sm text-gray-700">Specific Contact Lists</span>
                        </label>
                    </div>
                </div>

                <div id="contact-lists-section" class="{{ old('recipient_type', $campaign->recipients_criteria['type'] ?? 'all') == 'lists' ? '' : 'hidden' }}">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Contact Lists</label>
                    <div class="space-y-2">
                        @if(isset($contactLists) && $contactLists->count() > 0)
                            @foreach($contactLists as $list)
                                @php
                                    $selectedLists = old('contact_lists', $campaign->recipients_criteria['contact_lists'] ?? []);
                                    $isSelected = in_array($list->id, $selectedLists);
                                @endphp
                                <label class="flex items-center">
                                    <input type="checkbox" name="contact_lists[]" value="{{ $list->id }}"
                                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                           {{ $isSelected ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700">{{ $list->name }} ({{ $list->getContactsCount() }} contacts)</span>
                                </label>
                            @endforeach
                        @else
                            <p class="text-sm text-gray-500">No contact lists available.</p>
                        @endif
                    </div>
                    @error('contact_lists')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="flex justify-between">
            <div></div>
            <div class="flex space-x-3">
                <a href="{{ route('crm.marketing.campaigns.show', $campaign) }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Cancel
                </a>
                <button type="submit" class="bg-indigo-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Update Campaign
                </button>
            </div>
        </div>
    </form>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const recipientTypeRadios = document.querySelectorAll('input[name="recipient_type"]');
            const contactListsSection = document.getElementById('contact-lists-section');

            function toggleContactListsSection() {
                const selectedType = document.querySelector('input[name="recipient_type"]:checked').value;
                if (selectedType === 'lists') {
                    contactListsSection.classList.remove('hidden');
                } else {
                    contactListsSection.classList.add('hidden');
                }
            }

            // Add event listeners to radio buttons
            recipientTypeRadios.forEach(function(radio) {
                radio.addEventListener('change', toggleContactListsSection);
            });

            // Initial toggle
            toggleContactListsSection();
        });
    </script>
    @endpush
@endsection 