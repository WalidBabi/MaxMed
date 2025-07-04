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
                <label for="scheduled_at" class="block text-sm font-medium text-gray-700">Schedule Date & Time</label>
                <input type="datetime-local" name="scheduled_at" id="scheduled_at" value="{{ old('scheduled_at', $campaign->scheduled_at ? $campaign->scheduled_at->format('Y-m-d\TH:i') : '') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                @error('scheduled_at')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Email Content Section -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Email Content</h3>
            
            <!-- A/B Testing Section -->
            <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg mb-6">
                <div class="flex items-center mb-4">
                    <input type="checkbox" name="is_ab_test" id="is_ab_test" value="1" 
                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                           {{ old('is_ab_test', $campaign->is_ab_test) ? 'checked' : '' }}>
                    <label for="is_ab_test" class="ml-2 text-sm font-medium text-blue-800">
                        🧪 Enable A/B Testing
                    </label>
                </div>
                
                <div id="ab-test-options" class="space-y-4" style="{{ old('is_ab_test', $campaign->is_ab_test) ? '' : 'display: none;' }}">
                    <div>
                        <label class="block text-sm font-medium text-blue-700 mb-2">Test Type</label>
                        <select name="ab_test_type" id="ab_test_type" class="block w-full rounded-md border-blue-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="subject_line" {{ old('ab_test_type', $campaign->ab_test_type) == 'subject_line' ? 'selected' : '' }}>Subject Line</option>
                            <option value="cta" {{ old('ab_test_type', $campaign->ab_test_type) == 'cta' ? 'selected' : '' }}>Call-to-Action (CTA)</option>
                            <option value="template" {{ old('ab_test_type', $campaign->ab_test_type) == 'template' ? 'selected' : '' }}>Email Template</option>
                            <option value="send_time" {{ old('ab_test_type', $campaign->ab_test_type) == 'send_time' ? 'selected' : '' }}>Send Time</option>
                        </select>
                        <p class="mt-1 text-xs text-blue-600">Choose what element you want to test between two variants</p>
                    </div>
                    
                    <div>
                        <label for="ab_test_split_percentage" class="block text-sm font-medium text-blue-700">Split Percentage (Variant A)</label>
                        <div class="mt-1 flex items-center space-x-2">
                            <input type="range" name="ab_test_split_percentage" id="ab_test_split_percentage" 
                                   min="10" max="90" value="{{ old('ab_test_split_percentage', $campaign->ab_test_split_percentage ?? 50) }}" 
                                   class="flex-1 rounded-md border-blue-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <span id="split-percentage-display" class="text-sm font-medium text-blue-700 min-w-[4rem]">{{ old('ab_test_split_percentage', $campaign->ab_test_split_percentage ?? 50) }}% / {{ 100 - old('ab_test_split_percentage', $campaign->ab_test_split_percentage ?? 50) }}%</span>
                        </div>
                        <p class="mt-1 text-xs text-blue-600">Adjust how traffic is split between the two variants</p>
                    </div>
                </div>
            </div>

            <!-- Standard Email Content (shown only when A/B testing is disabled) -->
            <div id="standard-email-section">
                <!-- Standard Subject -->
                <div id="subject-section">
                    <label for="subject" class="block text-sm font-medium text-gray-700">Email Subject</label>
                    <input type="text" name="subject" id="subject" value="{{ old('subject', $campaign->subject) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('subject')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email Template Selection -->
                <div id="template-section" class="mt-6">
                    <label for="email_template_id" class="block text-sm font-medium text-gray-700">Email Template</label>
                    <select name="email_template_id" id="email_template_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">Select Template (Optional)</option>
                        @if(isset($emailTemplates))
                            @foreach($emailTemplates as $template)
                                <option value="{{ $template->id }}" {{ old('email_template_id', $campaign->email_template_id) == $template->id ? 'selected' : '' }}>
                                    {{ $template->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <!-- Text Content -->
                <div id="text-content-section" class="mt-6">
                    <label for="text_content" class="block text-sm font-medium text-gray-700">Email Content</label>
                    <div class="mt-1 mb-2">
                        <p class="text-sm text-gray-600">✨ <strong>HTML is automatically generated</strong> from your text content for better tracking and formatting.</p>
                        <p class="text-sm text-gray-500">Use variables like @{{ '{' . '{first_name}' . '}' }}, @{{ '{' . '{company_name}' . '}' }}, @{{ '{' . '{job_title}' . '}' }} to personalize your message.</p>
                    </div>
                    <textarea name="text_content" id="text_content" rows="12"
                        placeholder="Dear {first_name},&#10;&#10;I hope this email finds you well..."
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('text_content', $campaign->text_content) }}</textarea>
                    @error('text_content')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- A/B Testing Variant Sections (shown only when A/B testing is enabled) -->

            <!-- Subject Line A/B Testing -->
            <div id="subject-ab-testing-section" style="{{ old('ab_test_type', $campaign->ab_test_type) == 'subject_line' && old('is_ab_test', $campaign->is_ab_test) ? '' : 'display: none;' }}">
                <div id="subject-variant-a-section" class="mb-4">
                    <label for="subject_variant_a" class="block text-sm font-medium text-gray-700">Email Subject (Variant A)</label>
                    <input type="text" name="subject_variant_a" id="subject_variant_a"
                           value="{{ old('subject_variant_a', $campaign->subject) }}"
                           placeholder="Enter primary subject line"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <p class="mt-1 text-sm text-gray-500">This is the primary subject line (Variant A)</p>
                </div>

                <div id="subject-variant-b-section">
                    <label for="subject_variant_b" class="block text-sm font-medium text-gray-700">Email Subject (Variant B)</label>
                    <input type="text" name="subject_variant_b" id="subject_variant_b"
                           value="{{ old('subject_variant_b', $campaign->subject_variant_b) }}"
                           placeholder="Enter alternative subject line for A/B testing"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <p class="mt-1 text-sm text-gray-500">This subject line will be tested against Variant A</p>
                </div>
            </div>

            @php
                $variantData = [];
                if ($campaign->ab_test_variant_data) {
                    $variantData = is_string($campaign->ab_test_variant_data) 
                        ? json_decode($campaign->ab_test_variant_data, true) 
                        : $campaign->ab_test_variant_data;
                }
            @endphp

            <!-- CTA Variant A Section -->
            <div id="cta-variant-a-section" class="mt-6" style="{{ old('ab_test_type', $campaign->ab_test_type) == 'cta' && old('is_ab_test', $campaign->is_ab_test) ? '' : 'display: none;' }}">
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-purple-800 mb-3">🎯 Call-to-Action (Variant A)</h4>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="cta_text_variant_a" class="block text-sm font-medium text-gray-700">CTA Text</label>
                            <input type="text" name="cta_text_variant_a" id="cta_text_variant_a"
                                   value="{{ old('cta_text_variant_a', $variantData['variant_a']['cta_text'] ?? '') }}"
                                   placeholder="e.g., Contact Us, Learn More"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="cta_url_variant_a" class="block text-sm font-medium text-gray-700">CTA URL</label>
                            <input type="url" name="cta_url_variant_a" id="cta_url_variant_a"
                                   value="{{ old('cta_url_variant_a', $variantData['variant_a']['cta_url'] ?? '') }}"
                                   placeholder="https://maxmed.ae/contact"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                    </div>
                    <div class="mt-3">
                        <label for="cta_color_variant_a" class="block text-sm font-medium text-gray-700">Button Color</label>
                        <select name="cta_color_variant_a" id="cta_color_variant_a" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="indigo" {{ old('cta_color_variant_a', $variantData['variant_a']['cta_color'] ?? 'indigo') == 'indigo' ? 'selected' : '' }}>Indigo (Default)</option>
                            <option value="green" {{ old('cta_color_variant_a', $variantData['variant_a']['cta_color'] ?? '') == 'green' ? 'selected' : '' }}>Green</option>
                            <option value="orange" {{ old('cta_color_variant_a', $variantData['variant_a']['cta_color'] ?? '') == 'orange' ? 'selected' : '' }}>Orange</option>
                            <option value="red" {{ old('cta_color_variant_a', $variantData['variant_a']['cta_color'] ?? '') == 'red' ? 'selected' : '' }}>Red</option>
                            <option value="purple" {{ old('cta_color_variant_a', $variantData['variant_a']['cta_color'] ?? '') == 'purple' ? 'selected' : '' }}>Purple</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- CTA Variant B Section -->
            <div id="cta-variant-b-section" class="mt-6" style="{{ old('ab_test_type', $campaign->ab_test_type) == 'cta' && old('is_ab_test', $campaign->is_ab_test) ? '' : 'display: none;' }}">
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-purple-800 mb-3">🎯 Call-to-Action (Variant B)</h4>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="cta_text_variant_b" class="block text-sm font-medium text-gray-700">CTA Text</label>
                            <input type="text" name="cta_text_variant_b" id="cta_text_variant_b"
                                   value="{{ old('cta_text_variant_b', $variantData['variant_b']['cta_text'] ?? '') }}"
                                   placeholder="e.g., Request Quote Now, Get Started Today"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="cta_url_variant_b" class="block text-sm font-medium text-gray-700">CTA URL</label>
                            <input type="url" name="cta_url_variant_b" id="cta_url_variant_b"
                                   value="{{ old('cta_url_variant_b', $variantData['variant_b']['cta_url'] ?? '') }}"
                                   placeholder="https://maxmed.ae/contact-alternative"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                    </div>
                    <div class="mt-3">
                        <label for="cta_color_variant_b" class="block text-sm font-medium text-gray-700">Button Color</label>
                        <select name="cta_color_variant_b" id="cta_color_variant_b" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="indigo" {{ old('cta_color_variant_b', $variantData['variant_b']['cta_color'] ?? 'indigo') == 'indigo' ? 'selected' : '' }}>Indigo (Default)</option>
                            <option value="green" {{ old('cta_color_variant_b', $variantData['variant_b']['cta_color'] ?? '') == 'green' ? 'selected' : '' }}>Green</option>
                            <option value="orange" {{ old('cta_color_variant_b', $variantData['variant_b']['cta_color'] ?? '') == 'orange' ? 'selected' : '' }}>Orange</option>
                            <option value="red" {{ old('cta_color_variant_b', $variantData['variant_b']['cta_color'] ?? '') == 'red' ? 'selected' : '' }}>Red</option>
                            <option value="purple" {{ old('cta_color_variant_b', $variantData['variant_b']['cta_color'] ?? '') == 'purple' ? 'selected' : '' }}>Purple</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Template Variant A Section -->
            <div id="template-variant-a-section" class="mt-6" style="{{ old('ab_test_type', $campaign->ab_test_type) == 'template' && old('is_ab_test', $campaign->is_ab_test) ? '' : 'display: none;' }}">
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-green-800 mb-3">📧 Template (Variant A)</h4>
                    <div>
                        <label for="email_template_variant_a_id" class="block text-sm font-medium text-gray-700">Primary Template</label>
                        <select name="email_template_variant_a_id" id="email_template_variant_a_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">Select Primary Template</option>
                            @if(isset($emailTemplates))
                                @foreach($emailTemplates as $template)
                                    <option value="{{ $template->id }}" {{ old('email_template_variant_a_id', $variantData['variant_a']['email_template_id'] ?? '') == $template->id ? 'selected' : '' }}>
                                        {{ $template->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="mt-3">
                        <label for="text_content_variant_a" class="block text-sm font-medium text-gray-700">Primary Content (Optional)</label>
                        <textarea name="text_content_variant_a" id="text_content_variant_a" rows="6"
                                  placeholder="Primary email content"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('text_content_variant_a', $variantData['variant_a']['text_content'] ?? '') }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">Leave empty to use template content only, or add custom content to override template</p>
                    </div>
                    <p class="mt-2 text-xs text-green-600">This is the primary template that will be tested against Variant B</p>
                </div>
            </div>

            <!-- Template Variant B Section -->
            <div id="template-variant-b-section" class="mt-6" style="{{ old('ab_test_type', $campaign->ab_test_type) == 'template' && old('is_ab_test', $campaign->is_ab_test) ? '' : 'display: none;' }}">
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-green-800 mb-3">📧 Template (Variant B)</h4>
                    <div>
                        <label for="email_template_variant_b_id" class="block text-sm font-medium text-gray-700">Alternative Template</label>
                        <select name="email_template_variant_b_id" id="email_template_variant_b_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">Select Alternative Template</option>
                            @if(isset($emailTemplates))
                                @foreach($emailTemplates as $template)
                                    <option value="{{ $template->id }}" {{ old('email_template_variant_b_id', $variantData['variant_b']['email_template_id'] ?? '') == $template->id ? 'selected' : '' }}>
                                        {{ $template->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="mt-3">
                        <label for="text_content_variant_b" class="block text-sm font-medium text-gray-700">Alternative Content (Optional)</label>
                        <textarea name="text_content_variant_b" id="text_content_variant_b" rows="6"
                                  placeholder="Alternative email content to test against Variant A"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('text_content_variant_b', $variantData['variant_b']['text_content'] ?? '') }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">Leave empty to use template content only, or add custom content to override template</p>
                    </div>
                    <p class="mt-2 text-xs text-green-600">Test different email designs or content to optimize engagement</p>
                </div>
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
            
            // A/B Testing elements
            const abTestCheckbox = document.getElementById('is_ab_test');
            const abTestOptions = document.getElementById('ab-test-options');
            const abTestType = document.getElementById('ab_test_type');
            const splitPercentageSlider = document.getElementById('ab_test_split_percentage');
            const splitPercentageDisplay = document.getElementById('split-percentage-display');
            
            // Standard email section
            const standardEmailSection = document.getElementById('standard-email-section');
            
            // A/B Testing sections
            const subjectAbTestingSection = document.getElementById('subject-ab-testing-section');
            const ctaVariantASection = document.getElementById('cta-variant-a-section');
            const ctaVariantBSection = document.getElementById('cta-variant-b-section');
            const templateVariantASection = document.getElementById('template-variant-a-section');
            const templateVariantBSection = document.getElementById('template-variant-b-section');

            function toggleContactListsSection() {
                const selectedType = document.querySelector('input[name="recipient_type"]:checked').value;
                if (selectedType === 'lists') {
                    contactListsSection.classList.remove('hidden');
                } else {
                    contactListsSection.classList.add('hidden');
                }
            }

            function toggleAbTestSections() {
                const isAbTestEnabled = abTestCheckbox.checked;
                const selectedTestType = abTestType.value;

                // Show/hide A/B test options
                if (isAbTestEnabled) {
                    abTestOptions.style.display = '';
                    // Hide standard email section when A/B testing is enabled
                    if (standardEmailSection) {
                        standardEmailSection.style.display = 'none';
                    }
                } else {
                    abTestOptions.style.display = 'none';
                    // Show standard email section when A/B testing is disabled
                    if (standardEmailSection) {
                        standardEmailSection.style.display = 'block';
                    }
                }

                // Show/hide Subject A/B testing section
                if (isAbTestEnabled && selectedTestType === 'subject_line') {
                    if (subjectAbTestingSection) {
                        subjectAbTestingSection.style.display = 'block';
                    }
                } else {
                    if (subjectAbTestingSection) {
                        subjectAbTestingSection.style.display = 'none';
                    }
                }

                // Show/hide CTA sections
                if (isAbTestEnabled && selectedTestType === 'cta') {
                    if (ctaVariantASection) ctaVariantASection.style.display = 'block';
                    if (ctaVariantBSection) ctaVariantBSection.style.display = 'block';
                } else {
                    if (ctaVariantASection) ctaVariantASection.style.display = 'none';
                    if (ctaVariantBSection) ctaVariantBSection.style.display = 'none';
                }

                // Show/hide Template sections
                if (isAbTestEnabled && selectedTestType === 'template') {
                    if (templateVariantASection) templateVariantASection.style.display = 'block';
                    if (templateVariantBSection) templateVariantBSection.style.display = 'block';
                } else {
                    if (templateVariantASection) templateVariantASection.style.display = 'none';
                    if (templateVariantBSection) templateVariantBSection.style.display = 'none';
                }
            }

            function updateSplitPercentage() {
                const percentage = splitPercentageSlider.value;
                const remaining = 100 - percentage;
                splitPercentageDisplay.textContent = `${percentage}% / ${remaining}%`;
            }

            // Event listeners
            recipientTypeRadios.forEach(function(radio) {
                radio.addEventListener('change', toggleContactListsSection);
            });

            abTestCheckbox.addEventListener('change', toggleAbTestSections);
            abTestType.addEventListener('change', toggleAbTestSections);
            splitPercentageSlider.addEventListener('input', updateSplitPercentage);

            // Initial setup
            toggleContactListsSection();
            toggleAbTestSections();
            updateSplitPercentage();
        });
    </script>
    @endpush
@endsection 