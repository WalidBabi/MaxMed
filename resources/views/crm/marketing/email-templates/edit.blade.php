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

    <form action="{{ route('crm.marketing.email-templates.update', $emailTemplate) }}" method="POST">
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
                <textarea name="text_content" id="text_content" rows="15" placeholder="Dear &#123;&#123;first_name&#125;&#125;,

I hope this email finds you well. My name is Walid, and I am reaching out on behalf of MaxMed, a trusted provider of advanced scientific solutions tailored to life science and research professionals.

Use variables like &#123;&#123;first_name&#125;&#125;, &#123;&#123;company&#125;&#125;, &#123;&#123;job_title&#125;&#125; to personalize your message.

Best regards,
Walid Babi" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('text_content', $emailTemplate->text_content) }}</textarea>
                @error('text_content')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="html_content" class="block text-sm font-medium text-gray-700">HTML Content (Optional - for email clients that support HTML)</label>
                <textarea name="html_content" id="html_content" rows="8" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm font-mono" placeholder="Leave empty to send plain text only">{{ old('html_content', $emailTemplate->html_content) }}</textarea>
                @error('html_content')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex justify-between">
            <button type="button" onclick="previewTemplate()" class="bg-gray-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                Preview Template
            </button>
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

@push('scripts')
<script>
    function previewTemplate() {
        const textContent = document.getElementById('text_content').value;
        const htmlContent = document.getElementById('html_content').value;
        const subject = document.getElementById('subject').value;
        
        if (!textContent.trim()) {
            alert('Please enter some email content first.');
            return;
        }
        
        // Create preview with sample data
        const sampleData = {
            'first_name': 'John',
            'last_name': 'Doe',
            'email': 'john.doe@example.com',
            'company': 'MaxMed Solutions',
            'job_title': 'Laboratory Manager',
            'company_name': 'MaxMed',
            'current_date': new Date().toLocaleDateString(),
            'current_year': new Date().getFullYear(),
            'unsubscribe_url': '#unsubscribe',
            'company_address': '123 Business Street, City, State 12345'
        };
        
        let previewTextContent = textContent;
        let previewHtmlContent = htmlContent;
        let previewSubject = subject;
        
        // Replace variables with sample data
        Object.keys(sampleData).forEach(key => {
            const regex = new RegExp('{{' + key + '}}', 'g');
            previewTextContent = previewTextContent.replace(regex, sampleData[key]);
            if (previewHtmlContent) {
                previewHtmlContent = previewHtmlContent.replace(regex, sampleData[key]);
            }
            previewSubject = previewSubject.replace(regex, sampleData[key]);
        });
        
        // Open preview in new window
        const previewWindow = window.open('', '_blank', 'width=800,height=600');
        previewWindow.document.write(
            '<html>' +
            '<head>' +
            '<title>Email Preview: ' + previewSubject + '</title>' +
            '<style>' +
            'body { font-family: Arial, sans-serif; margin: 20px; }' +
            '.preview-header { background: #f3f4f6; padding: 10px; margin-bottom: 20px; border-radius: 4px; }' +
            '.preview-header h3 { margin: 0; color: #374151; }' +
            '.preview-content { border: 1px solid #d1d5db; padding: 20px; }' +
            '.text-content { white-space: pre-wrap; font-family: monospace; background: #f9f9f9; padding: 15px; border-radius: 5px; }' +
            '</style>' +
            '</head>' +
            '<body>' +
            '<div class="preview-header">' +
            '<h3>Subject: ' + previewSubject + '</h3>' +
            '<p style="margin: 5px 0 0 0; color: #6b7280; font-size: 14px;">This is a preview with sample data</p>' +
            '</div>' +
            '<div class="preview-content">' +
            '<h4>Plain Text Content:</h4>' +
            '<div class="text-content">' + previewTextContent + '</div>' +
            (previewHtmlContent ? '<h4 style="margin-top: 20px;">HTML Content:</h4><div>' + previewHtmlContent + '</div>' : '') +
            '</div>' +
            '</body>' +
            '</html>'
        );
        previewWindow.document.close();
    }
</script>
@endpush 