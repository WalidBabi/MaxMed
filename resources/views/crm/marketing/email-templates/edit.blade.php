@extends('layouts.crm')

@section('title', 'Edit Email Template')

@section('content')
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit Email Template</h1>
                <p class="text-gray-600 mt-2">Modify your email template for marketing campaigns</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('crm.marketing.email-templates.show', $emailTemplate) }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    View Template
                </a>
                <a href="{{ route('crm.marketing.email-templates.index') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    Back to Templates
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('crm.marketing.email-templates.update', $emailTemplate) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Template Details</h3>
            
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Template Name</label>
                    <input type="text" name="name" id="name" required value="{{ old('name', $emailTemplate->name) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                    <select name="category" id="category" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">Select Category</option>
                        <option value="newsletter" {{ old('category', $emailTemplate->category) == 'newsletter' ? 'selected' : '' }}>Newsletter</option>
                        <option value="promotional" {{ old('category', $emailTemplate->category) == 'promotional' ? 'selected' : '' }}>Promotional</option>
                        <option value="welcome" {{ old('category', $emailTemplate->category) == 'welcome' ? 'selected' : '' }}>Welcome</option>
                        <option value="transactional" {{ old('category', $emailTemplate->category) == 'transactional' ? 'selected' : '' }}>Transactional</option>
                        <option value="announcement" {{ old('category', $emailTemplate->category) == 'announcement' ? 'selected' : '' }}>Announcement</option>
                    </select>
                    @error('category')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="mt-6">
                <label for="subject" class="block text-sm font-medium text-gray-700">Email Subject</label>
                <input type="text" name="subject" id="subject" value="{{ old('subject', $emailTemplate->subject) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                @error('subject')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6">
                <label for="banner_image" class="block text-sm font-medium text-gray-700">Company Banner Image</label>
                <div class="mt-1 mb-2">
                    <p class="text-sm text-gray-500">Upload a banner image that will appear at the top of your emails. Recommended size: 600x150px</p>
                    @if($emailTemplate->banner_image)
                        <div class="mt-2">
                            <p class="text-sm text-green-600">Current banner: {{ basename($emailTemplate->banner_image) }}</p>
                            <img src="{{ asset('storage/' . $emailTemplate->banner_image) }}" alt="Current banner" class="mt-1 h-20 rounded border">
                        </div>
                    @endif
                </div>
                <input type="file" name="banner_image" id="banner_image" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                @error('banner_image')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6">
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $emailTemplate->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <label for="is_active" class="ml-2 text-sm text-gray-700">
                        Template is active (available for campaigns)
                    </label>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Email Content</h3>
            
            <div class="mb-6">
                <label for="text_content" class="block text-sm font-medium text-gray-700">Email Content</label>
                <div class="mt-1 mb-2">
                    <p class="text-sm text-gray-600">✨ <strong>HTML is automatically generated</strong> from your text content for better tracking and formatting.</p>
                    <p class="text-sm text-gray-500">📧 Email addresses, websites, and phone numbers will automatically become trackable links.</p>
                    <p class="text-sm text-gray-500">Use variables like &#123;&#123;first_name&#125;&#125;, &#123;&#123;company&#125;&#125;, &#123;&#123;job_title&#125;&#125; to personalize your message.</p>
                </div>
                <textarea name="text_content" id="text_content" rows="15" placeholder="Dear &#123;&#123;first_name&#125;&#125;,

I hope this email finds you well. My name is Walid, and I am reaching out on behalf of MaxMed, a trusted provider of advanced scientific solutions tailored to life science and research professionals.

For inquiries, please contact us at:
📧 info@maxmed.ae
🌐 www.maxmed.ae  
📞 +971 55 4602500

Best regards,
Walid Babi
MaxMed Team" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('text_content', $emailTemplate->text_content) }}</textarea>
                @error('text_content')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex justify-end">
            <div class="flex space-x-3">
                <a href="{{ route('crm.marketing.email-templates.show', $emailTemplate) }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Cancel
                </a>
                <button type="submit" class="bg-indigo-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Update Template
                </button>
            </div>
        </div>
    </form>
@endsection

 