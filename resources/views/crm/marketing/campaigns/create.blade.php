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
                <!-- A/B Testing Section -->
                <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-center mb-4">
                        <input type="checkbox" name="is_ab_test" id="is_ab_test" value="1" 
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                               {{ old('is_ab_test') ? 'checked' : '' }}>
                        <label for="is_ab_test" class="ml-2 text-sm font-medium text-blue-800">
                            🧪 Enable A/B Testing
                        </label>
                    </div>
                    
                    <div id="ab-test-options" class="space-y-4" style="display: none;">
                        <div>
                            <label class="block text-sm font-medium text-blue-700 mb-2">Test Type</label>
                            <select name="ab_test_type" id="ab_test_type" class="block w-full rounded-md border-blue-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="subject_line" {{ old('ab_test_type', 'subject_line') == 'subject_line' ? 'selected' : '' }}>Subject Line</option>
                                <option value="cta" {{ old('ab_test_type') == 'cta' ? 'selected' : '' }}>Call-to-Action (CTA)</option>
                                <option value="template" {{ old('ab_test_type') == 'template' ? 'selected' : '' }}>Email Template</option>
                                <option value="send_time" {{ old('ab_test_type') == 'send_time' ? 'selected' : '' }}>Send Time</option>
                            </select>
                            <p class="mt-1 text-xs text-blue-600">Choose what element you want to test between two variants</p>
                        </div>
                        
                        <div>
                            <label for="ab_test_split_percentage" class="block text-sm font-medium text-blue-700">Split Percentage (Variant A)</label>
                            <div class="mt-1 flex items-center space-x-2">
                                <input type="range" name="ab_test_split_percentage" id="ab_test_split_percentage" 
                                       min="10" max="90" value="{{ old('ab_test_split_percentage', 50) }}" 
                                       class="flex-1 rounded-md border-blue-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <span id="split-percentage-display" class="text-sm font-medium text-blue-700 min-w-[4rem]">50% / 50%</span>
                            </div>
                            <p class="mt-1 text-xs text-blue-600">Adjust how traffic is split between the two variants</p>
                        </div>
                    </div>
                </div>

                <!-- Variant A Sections -->
                <div id="subject-variant-a-section" class="ab-variant-a-section">
                    <label for="subject" class="block text-sm font-medium text-gray-700">Email Subject (Variant A)</label>
                    <input type="text" name="subject" id="subject"
                           value="{{ old('subject') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('subject') border-red-300 @enderror">
                    @error('subject')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div id="cta-variant-a-section" class="ab-variant-a-section" style="display: none;">
                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-purple-800 mb-3">🎯 Call-to-Action (Variant A)</h4>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label for="cta_text_variant_a" class="block text-sm font-medium text-gray-700">CTA Text</label>
                                <input type="text" name="cta_text_variant_a" id="cta_text_variant_a"
                                       value="{{ old('cta_text_variant_a') }}"
                                       placeholder="e.g., Contact Us, Learn More"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="cta_url_variant_a" class="block text-sm font-medium text-gray-700">CTA URL</label>
                                <input type="url" name="cta_url_variant_a" id="cta_url_variant_a"
                                       value="{{ old('cta_url_variant_a') }}"
                                       placeholder="https://maxmed.ae/contact"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                        </div>
                        <div class="mt-3">
                            <label for="cta_color_variant_a" class="block text-sm font-medium text-gray-700">Button Color</label>
                            <select name="cta_color_variant_a" id="cta_color_variant_a" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="indigo">Indigo (Default)</option>
                                <option value="green">Green</option>
                                <option value="orange">Orange</option>
                                <option value="red">Red</option>
                                <option value="purple">Purple</option>
                            </select>
                        </div>
                        <p class="mt-2 text-xs text-purple-600">This is the original CTA that will be tested against Variant B</p>
                    </div>
                </div>

                <div id="template-variant-a-section" class="ab-variant-a-section" style="display: none;">
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-green-800 mb-3">📧 Template (Variant A)</h4>
                        <div>
                            <label for="email_template_variant_a_id" class="block text-sm font-medium text-gray-700">Primary Template</label>
                            <select name="email_template_variant_a_id" id="email_template_variant_a_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">Select Primary Template</option>
                                @if(isset($emailTemplates))
                                    @foreach($emailTemplates as $template)
                                        <option value="{{ $template->id }}" {{ old('email_template_variant_a_id') == $template->id ? 'selected' : '' }}>
                                            {{ $template->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="mt-3" id="text-content-variant-a-section">
                            <label for="text_content_variant_a" class="block text-sm font-medium text-gray-700">Primary Content (Optional)</label>
                            <textarea name="text_content_variant_a" id="text_content_variant_a" rows="6"
                                      placeholder="Primary email content"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('text_content_variant_a') }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">Leave empty to use template content only, or add custom content to override template</p>
                        </div>
                        <div class="mt-3 hidden" id="template-info-variant-a-section">
                            <div class="p-3 bg-green-50 border border-green-200 rounded-md">
                                <p class="text-sm text-green-700">✓ Template content will be used for Variant A</p>
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-green-600">This is the primary template that will be tested against Variant B</p>
                    </div>
                </div>

                <div id="send-time-variant-a-section" class="ab-variant-a-section" style="display: none;">
                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-orange-800 mb-3">⏰ Send Time (Variant A)</h4>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label for="scheduled_date_variant_a" class="block text-sm font-medium text-gray-700">Primary Date</label>
                                <input type="date" name="scheduled_date_variant_a" id="scheduled_date_variant_a"
                                       value="{{ old('scheduled_date_variant_a') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="scheduled_time_variant_a" class="block text-sm font-medium text-gray-700">Primary Time</label>
                                <input type="time" name="scheduled_time_variant_a" id="scheduled_time_variant_a"
                                       value="{{ old('scheduled_time_variant_a') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-orange-600">This is the primary send time that will be tested against Variant B</p>
                    </div>
                </div>

                <!-- Variant B Sections -->
                <div id="subject-variant-b-section" class="ab-variant-section" style="display: none;">
                    <label for="subject_variant_b" class="block text-sm font-medium text-gray-700">Email Subject (Variant B)</label>
                    <input type="text" name="subject_variant_b" id="subject_variant_b"
                           value="{{ old('subject_variant_b') }}"
                           placeholder="Enter alternative subject line for A/B testing"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('subject_variant_b') border-red-300 @enderror">
                    @error('subject_variant_b')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">This subject line will be tested against Variant A to see which performs better</p>
                </div>

                <div id="cta-variant-b-section" class="ab-variant-section" style="display: none;">
                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-purple-800 mb-3">🎯 Call-to-Action (Variant B)</h4>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label for="cta_text_variant_b" class="block text-sm font-medium text-gray-700">CTA Text</label>
                                <input type="text" name="cta_text_variant_b" id="cta_text_variant_b"
                                       value="{{ old('cta_text_variant_b') }}"
                                       placeholder="e.g., Request Quote Now, Get Started Today"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="cta_url_variant_b" class="block text-sm font-medium text-gray-700">CTA URL</label>
                                <input type="url" name="cta_url_variant_b" id="cta_url_variant_b"
                                       value="{{ old('cta_url_variant_b') }}"
                                       placeholder="https://maxmed.ae/contact-alternative"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                        </div>
                        <div class="mt-3">
                            <label for="cta_color_variant_b" class="block text-sm font-medium text-gray-700">Button Color</label>
                            <select name="cta_color_variant_b" id="cta_color_variant_b" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="indigo">Indigo (Default)</option>
                                <option value="green">Green</option>
                                <option value="orange">Orange</option>
                                <option value="red">Red</option>
                                <option value="purple">Purple</option>
                            </select>
                        </div>
                        <p class="mt-2 text-xs text-purple-600">Test different CTA buttons to see which generates more clicks</p>
                    </div>
                </div>

                <div id="template-variant-b-section" class="ab-variant-section" style="display: none;">
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-green-800 mb-3">📧 Template (Variant B)</h4>
                        <div>
                            <label for="email_template_variant_b_id" class="block text-sm font-medium text-gray-700">Alternative Template</label>
                            <select name="email_template_variant_b_id" id="email_template_variant_b_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">Select Alternative Template</option>
                                @if(isset($emailTemplates))
                                    @foreach($emailTemplates as $template)
                                        <option value="{{ $template->id }}" {{ old('email_template_variant_b_id') == $template->id ? 'selected' : '' }}>
                                            {{ $template->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="mt-3" id="text-content-variant-b-section">
                            <label for="text_content_variant_b" class="block text-sm font-medium text-gray-700">Alternative Content (Optional)</label>
                            <textarea name="text_content_variant_b" id="text_content_variant_b" rows="6"
                                      placeholder="Alternative email content to test against Variant A"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('text_content_variant_b') }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">Leave empty to use template content only, or add custom content to override template</p>
                        </div>
                        <div class="mt-3 hidden" id="template-info-variant-b-section">
                            <div class="p-3 bg-green-50 border border-green-200 rounded-md">
                                <p class="text-sm text-green-700">✓ Template content will be used for Variant B</p>
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-green-600">Test different email designs or content to optimize engagement</p>
                    </div>
                </div>

                <div id="send-time-variant-b-section" class="ab-variant-section" style="display: none;">
                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-orange-800 mb-3">⏰ Send Time (Variant B)</h4>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label for="scheduled_date_variant_b" class="block text-sm font-medium text-gray-700">Alternative Date</label>
                                <input type="date" name="scheduled_date_variant_b" id="scheduled_date_variant_b"
                                       value="{{ old('scheduled_date_variant_b') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="scheduled_time_variant_b" class="block text-sm font-medium text-gray-700">Alternative Time</label>
                                <input type="time" name="scheduled_time_variant_b" id="scheduled_time_variant_b"
                                       value="{{ old('scheduled_time_variant_b') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-orange-600">Test different send times to find optimal delivery timing</p>
                    </div>
                </div>

                <!-- Standard Email Template & Content (shown only when A/B testing is disabled) -->
                <div id="standard-email-section">
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

        // A/B Testing functionality
        const abTestCheckbox = document.getElementById('is_ab_test');
        const abTestOptions = document.getElementById('ab-test-options');
        const abTestTypeSelect = document.getElementById('ab_test_type');
        const splitPercentageSlider = document.getElementById('ab_test_split_percentage');
        const splitPercentageDisplay = document.getElementById('split-percentage-display');
        const standardEmailSection = document.getElementById('standard-email-section');

        // Template variant elements
        const emailTemplateVariantASelect = document.getElementById('email_template_variant_a_id');
        const emailTemplateVariantBSelect = document.getElementById('email_template_variant_b_id');
        const textContentVariantASection = document.getElementById('text-content-variant-a-section');
        const textContentVariantBSection = document.getElementById('text-content-variant-b-section');
        const templateInfoVariantASection = document.getElementById('template-info-variant-a-section');
        const templateInfoVariantBSection = document.getElementById('template-info-variant-b-section');

        function toggleAbTest() {
            if (abTestCheckbox.checked) {
                // Show A/B test options and hide standard email section
                abTestOptions.style.display = 'block';
                standardEmailSection.style.display = 'none';
                showVariantSection(abTestTypeSelect.value || 'subject_line');
                updateLabelsForAbTest();
            } else {
                // Hide A/B test options and show standard email section
                abTestOptions.style.display = 'none';
                standardEmailSection.style.display = 'block';
                hideAllVariantSections();
                resetLabels();
            }
        }

        function showVariantSection(testType) {
            // Hide all variant sections first
            hideAllVariantSections();
            
            // Map test types to section IDs
            let variantASectionId, variantBSectionId;
            
            switch(testType) {
                case 'subject_line':
                    variantASectionId = 'subject-variant-a-section';
                    variantBSectionId = 'subject-variant-b-section';
                    break;
                case 'cta':
                    variantASectionId = 'cta-variant-a-section';
                    variantBSectionId = 'cta-variant-b-section';
                    break;
                case 'template':
                    variantASectionId = 'template-variant-a-section';
                    variantBSectionId = 'template-variant-b-section';
                    // Initialize template conditional logic for variants
                    toggleTemplateContentForVariant('a');
                    toggleTemplateContentForVariant('b');
                    break;
                case 'send_time':
                    variantASectionId = 'send-time-variant-a-section';
                    variantBSectionId = 'send-time-variant-b-section';
                    break;
                default:
                    return;
            }
            
            // Show the selected variant A section
            const variantASection = document.getElementById(variantASectionId);
            if (variantASection) {
                variantASection.style.display = 'block';
            }
            
            // Show the selected variant B section
            const variantBSection = document.getElementById(variantBSectionId);
            if (variantBSection) {
                variantBSection.style.display = 'block';
            }
        }

        function hideAllVariantSections() {
            const variantASections = document.querySelectorAll('.ab-variant-a-section');
            const variantBSections = document.querySelectorAll('.ab-variant-section');
            
            variantASections.forEach(section => {
                section.style.display = 'none';
            });
            
            variantBSections.forEach(section => {
                section.style.display = 'none';
            });
        }

        function toggleTemplateContentForVariant(variant) {
            const templateSelect = variant === 'a' ? emailTemplateVariantASelect : emailTemplateVariantBSelect;
            const contentSection = variant === 'a' ? textContentVariantASection : textContentVariantBSection;
            const infoSection = variant === 'a' ? templateInfoVariantASection : templateInfoVariantBSection;
            
            if (!templateSelect || !contentSection || !infoSection) return;
            
            if (templateSelect.value) {
                // Template selected - show info message, hide content textarea
                contentSection.style.display = 'none';
                infoSection.style.display = 'block';
            } else {
                // No template - show content textarea, hide info message
                contentSection.style.display = 'block';
                infoSection.style.display = 'none';
            }
        }

        function updateLabelsForAbTest() {
            // Labels are now handled by showing/hiding appropriate sections
        }

        function resetLabels() {
            // Reset to show default subject section when A/B testing is disabled
            document.getElementById('subject-variant-a-section').style.display = 'block';
        }

        function updateSplitPercentage() {
            const variantA = splitPercentageSlider.value;
            const variantB = 100 - variantA;
            splitPercentageDisplay.textContent = `${variantA}% / ${variantB}%`;
        }

        // Event listeners
        abTestCheckbox.addEventListener('change', toggleAbTest);
        abTestTypeSelect.addEventListener('change', function() {
            if (abTestCheckbox.checked) {
                showVariantSection(this.value);
            }
        });
        splitPercentageSlider.addEventListener('input', updateSplitPercentage);

        // Template variant change listeners
        if (emailTemplateVariantASelect) {
            emailTemplateVariantASelect.addEventListener('change', function() {
                toggleTemplateContentForVariant('a');
            });
        }
        
        if (emailTemplateVariantBSelect) {
            emailTemplateVariantBSelect.addEventListener('change', function() {
                toggleTemplateContentForVariant('b');
            });
        }

        // Initialize visibility
        toggleContactLists();
        toggleSchedule();
        toggleTextContent();
        toggleAbTest();
        updateSplitPercentage();
    });
</script>
@endpush 