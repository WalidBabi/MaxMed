@extends('layouts.crm')

@section('title', 'Create Email Campaign')

@section('content')
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Create Email Campaign</h1>
                <p class="text-gray-600 mt-2">Create a new email marketing campaign</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('crm.marketing.campaigns.index') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M7.72 12.53a.75.75 0 010-1.06L10.94 8.25H6.5a.75.75 0 010-1.5h4.44L7.72 3.53a.75.75 0 011.06-1.06l4.25 4.25a.75.75 0 010 1.06l-4.25 4.25a.75.75 0 01-1.06 0z" clip-rule="evenodd" />
                    </svg>
                    Back to Campaigns
                </a>
            </div>
        </div>
    </div>

    <!-- Campaign Form -->
    <form action="{{ route('crm.marketing.campaigns.store') }}" method="POST" class="space-y-8">
        @csrf

        <!-- Campaign Details -->
        <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Campaign Details</h3>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Campaign Name *</label>
                        <input type="text" name="name" id="name" required
                               value="{{ old('name') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('name') border-red-300 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Campaign Type *</label>
                        <select name="type" id="type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('type') border-red-300 @enderror">
                            <option value="">Select Type</option>
                            <option value="one_time" {{ old('type') == 'one_time' ? 'selected' : '' }}>One Time</option>
                            <option value="recurring" {{ old('type') == 'recurring' ? 'selected' : '' }}>Recurring</option>
                            <option value="drip" {{ old('type') == 'drip' ? 'selected' : '' }}>Drip Campaign</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="3"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('description') border-red-300 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Email Content -->
        <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Email Content</h3>
            </div>
            <div class="p-6 space-y-6">
                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700">Email Subject</label>
                    <input type="text" name="subject" id="subject"
                           value="{{ old('subject') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('subject') border-red-300 @enderror">
                    @error('subject')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email_template_id" class="block text-sm font-medium text-gray-700">Email Template</label>
                    <select name="email_template_id" id="email_template_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('email_template_id') border-red-300 @enderror">
                        <option value="">Select Template (Optional)</option>
                        @if(isset($emailTemplates))
                            @foreach($emailTemplates as $template)
                                <option value="{{ $template->id }}" {{ old('email_template_id') == $template->id ? 'selected' : '' }}>
                                    {{ $template->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('email_template_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div id="text-content-section">
                    <label for="text_content" class="block text-sm font-medium text-gray-700">Email Content *</label>
                    <div class="mt-1 mb-2">
                        <p class="text-sm text-gray-600">✨ <strong>HTML is automatically generated</strong> from your text content for better tracking and formatting.</p>
                        <p class="text-sm text-gray-500">Use variables like {!! '{{' !!}first_name{!! '}}' !!}, {!! '{{' !!}company_name{!! '}}' !!}, {!! '{{' !!}job_title{!! '}}' !!} to personalize your message.</p>
                    </div>
                    <textarea name="text_content" id="text_content" rows="12" placeholder="Dear {!! '{{' !!}first_name{!! '}}' !!},

I hope this email finds you well. My name is Walid, and I am reaching out on behalf of MaxMed, a trusted provider of advanced scientific solutions tailored to life science and research professionals.

We specialize in:
- Laboratory Equipment
- Medical Consumables  
- Analytical Chemistry Tools

I would love to discuss how we can support your research and laboratory needs.

Best regards,
Walid Babi"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('text_content') border-red-300 @enderror">{{ old('text_content') }}</textarea>
                    @error('text_content')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div id="template-info-section" class="hidden">
                    <div class="p-4 bg-blue-50 border border-blue-200 rounded-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Email Template Selected</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <p>The content from your selected email template will be used for this campaign. HTML is automatically generated from the template for better tracking and formatting.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recipients -->
        <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Recipients</h3>
            </div>
            <div class="p-6 space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Select Recipients</label>
                    <div class="space-y-4">
                        <label class="flex items-center">
                            <input type="radio" name="recipient_type" value="all" 
                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                   {{ old('recipient_type', 'all') == 'all' ? 'checked' : '' }}>
                            <span class="ml-2 text-sm text-gray-700">All Active Contacts</span>
                        </label>
                        
                        <label class="flex items-center">
                            <input type="radio" name="recipient_type" value="lists" 
                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                   {{ old('recipient_type') == 'lists' ? 'checked' : '' }}>
                            <span class="ml-2 text-sm text-gray-700">Specific Contact Lists</span>
                        </label>
                    </div>
                </div>

                <div id="contact-lists-section" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Contact Lists</label>
                    <div class="space-y-2">
                        @if(isset($contactLists))
                            @foreach($contactLists as $list)
                                <label class="flex items-center">
                                    <input type="checkbox" name="contact_lists[]" value="{{ $list->id }}"
                                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                           {{ in_array($list->id, old('contact_lists', [])) ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700">{{ $list->name }} ({{ $list->getContactsCount() }} contacts)</span>
                                </label>
                            @endforeach
                        @else
                            <p class="text-sm text-gray-500">No contact lists available.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Schedule -->
        <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Schedule</h3>
            </div>
            <div class="p-6 space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">When to Send</label>
                    <div class="space-y-4">
                        <label class="flex items-center">
                            <input type="radio" name="send_option" value="draft" 
                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                   {{ old('send_option', 'draft') == 'draft' ? 'checked' : '' }}>
                            <span class="ml-2 text-sm text-gray-700">Save as Draft</span>
                        </label>
                        
                        <label class="flex items-center">
                            <input type="radio" name="send_option" value="now" 
                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                   {{ old('send_option') == 'now' ? 'checked' : '' }}>
                            <span class="ml-2 text-sm text-gray-700">Send Immediately</span>
                        </label>
                        
                        <label class="flex items-center">
                            <input type="radio" name="send_option" value="schedule" 
                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                   {{ old('send_option') == 'schedule' ? 'checked' : '' }}>
                            <span class="ml-2 text-sm text-gray-700">Schedule for Later</span>
                        </label>
                    </div>
                </div>

                <div id="schedule-section" class="hidden">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="scheduled_date" class="block text-sm font-medium text-gray-700">Date</label>
                            <input type="date" name="scheduled_date" id="scheduled_date"
                                   value="{{ old('scheduled_date') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="scheduled_time" class="block text-sm font-medium text-gray-700">Time</label>
                            <input type="time" name="scheduled_time" id="scheduled_time"
                                   value="{{ old('scheduled_time') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('crm.marketing.campaigns.index') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                </svg>
                Create Campaign
            </button>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const recipientTypeRadios = document.querySelectorAll('input[name="recipient_type"]');
        const contactListsSection = document.getElementById('contact-lists-section');
        
        const sendOptionRadios = document.querySelectorAll('input[name="send_option"]');
        const scheduleSection = document.getElementById('schedule-section');
        
        const emailTemplateSelect = document.getElementById('email_template_id');
        const textContentSection = document.getElementById('text-content-section');
        const templateInfoSection = document.getElementById('template-info-section');
        const textContentField = document.getElementById('text_content');

        function toggleContactLists() {
            const selectedType = document.querySelector('input[name="recipient_type"]:checked').value;
            if (selectedType === 'lists') {
                contactListsSection.classList.remove('hidden');
            } else {
                contactListsSection.classList.add('hidden');
            }
        }

        function toggleSchedule() {
            const selectedOption = document.querySelector('input[name="send_option"]:checked').value;
            if (selectedOption === 'schedule') {
                scheduleSection.classList.remove('hidden');
            } else {
                scheduleSection.classList.add('hidden');
            }
        }

        function toggleTextContent() {
            const selectedTemplate = emailTemplateSelect.value;
            if (selectedTemplate) {
                // Template selected - hide text content field and show info
                textContentSection.classList.add('hidden');
                templateInfoSection.classList.remove('hidden');
                // Remove required attribute when template is selected
                textContentField.removeAttribute('required');
            } else {
                // No template - show text content field and hide info
                textContentSection.classList.remove('hidden');
                templateInfoSection.classList.add('hidden');
                // Add required attribute when no template is selected
                textContentField.setAttribute('required', 'required');
            }
        }

        recipientTypeRadios.forEach(radio => {
            radio.addEventListener('change', toggleContactLists);
        });

        sendOptionRadios.forEach(radio => {
            radio.addEventListener('change', toggleSchedule);
        });

        emailTemplateSelect.addEventListener('change', toggleTextContent);

        // Initialize visibility
        toggleContactLists();
        toggleSchedule();
        toggleTextContent();
    });
</script>
@endpush 