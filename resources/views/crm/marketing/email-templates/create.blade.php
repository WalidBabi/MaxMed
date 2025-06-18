@extends('layouts.crm')

@section('title', 'Create Email Template')

@section('content')
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Create Email Template</h1>
                <p class="text-gray-600 mt-2">Design a reusable email template for your marketing campaigns</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('crm.marketing.email-templates.index') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M7.72 12.53a.75.75 0 010-1.06L10.94 8.25H6.5a.75.75 0 010-1.5h4.44L7.72 3.53a.75.75 0 011.06-1.06l4.25 4.25a.75.75 0 010 1.06l-4.25 4.25a.75.75 0 01-1.06 0z" clip-rule="evenodd" />
                    </svg>
                    Back to Templates
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Form -->
        <div class="lg:col-span-2">
            <form action="{{ route('crm.marketing.email-templates.store') }}" method="POST" class="space-y-8">
                @csrf

                <div class="bg-white shadow rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Template Details</h3>
                    
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Template Name</label>
                            <input type="text" name="name" id="name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                            <select name="category" id="category" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Select Category</option>
                                <option value="newsletter">Newsletter</option>
                                <option value="promotional">Promotional</option>
                                <option value="welcome">Welcome</option>
                                <option value="transactional">Transactional</option>
                                <option value="announcement">Announcement</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <label for="subject" class="block text-sm font-medium text-gray-700">Email Subject</label>
                        <input type="text" name="subject" id="subject" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                </div>

                <div class="bg-white shadow rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Email Content</h3>
                    
                    <div class="mb-6">
                        <label for="html_content" class="block text-sm font-medium text-gray-700">HTML Content</label>
                        <textarea name="html_content" id="html_content" rows="15" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm font-mono"></textarea>
                    </div>
                    
                    <div>
                        <label for="text_content" class="block text-sm font-medium text-gray-700">Plain Text Content (Optional)</label>
                        <textarea name="text_content" id="text_content" rows="10" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                    </div>
                </div>

                <div class="flex justify-end">
                    <a href="{{ route('crm.marketing.email-templates.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3">
                        Cancel
                    </a>
                    <button type="submit" class="bg-indigo-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Create Template
                    </button>
                </div>
            </form>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Variables Helper -->
            <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Available Variables</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div class="text-sm">
                            <p class="font-medium text-gray-900 mb-2">Contact Variables:</p>
                            <div class="space-y-1 text-gray-600">
                                <div class="flex justify-between">
                                    <code class="text-xs bg-gray-100 px-1 rounded">{{first_name}}</code>
                                    <span class="text-xs">First name</span>
                                </div>
                                <div class="flex justify-between">
                                    <code class="text-xs bg-gray-100 px-1 rounded">{{last_name}}</code>
                                    <span class="text-xs">Last name</span>
                                </div>
                                <div class="flex justify-between">
                                    <code class="text-xs bg-gray-100 px-1 rounded">{{email}}</code>
                                    <span class="text-xs">Email address</span>
                                </div>
                                <div class="flex justify-between">
                                    <code class="text-xs bg-gray-100 px-1 rounded">{{company}}</code>
                                    <span class="text-xs">Company name</span>
                                </div>
                                <div class="flex justify-between">
                                    <code class="text-xs bg-gray-100 px-1 rounded">{{job_title}}</code>
                                    <span class="text-xs">Job title</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-sm">
                            <p class="font-medium text-gray-900 mb-2">System Variables:</p>
                            <div class="space-y-1 text-gray-600">
                                <div class="flex justify-between">
                                    <code class="text-xs bg-gray-100 px-1 rounded">{{company_name}}</code>
                                    <span class="text-xs">Your company</span>
                                </div>
                                <div class="flex justify-between">
                                    <code class="text-xs bg-gray-100 px-1 rounded">{{current_date}}</code>
                                    <span class="text-xs">Today's date</span>
                                </div>
                                <div class="flex justify-between">
                                    <code class="text-xs bg-gray-100 px-1 rounded">{{unsubscribe_url}}</code>
                                    <span class="text-xs">Unsubscribe link</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Template Tips -->
            <div class="card-hover rounded-xl bg-indigo-50 shadow-sm ring-1 ring-indigo-200">
                <div class="p-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-indigo-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-indigo-800">
                                Template Best Practices
                            </h3>
                            <div class="mt-2 text-sm text-indigo-700">
                                <ul class="space-y-1">
                                    <li>• Use clear, descriptive template names</li>
                                    <li>• Include personalization variables</li>
                                    <li>• Test on different email clients</li>
                                    <li>• Always include unsubscribe links</li>
                                    <li>• Keep HTML simple for compatibility</li>
                                    <li>• Provide plain text alternatives</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function switchTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        
        // Remove active class from all tabs
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('active', 'border-indigo-500', 'text-indigo-600');
            button.classList.add('border-transparent', 'text-gray-500');
        });
        
        // Show selected tab content
        document.getElementById(tabName + '-content').classList.remove('hidden');
        
        // Add active class to selected tab
        const activeTab = document.getElementById(tabName + '-tab');
        activeTab.classList.add('active', 'border-indigo-500', 'text-indigo-600');
        activeTab.classList.remove('border-transparent', 'text-gray-500');
    }

    function previewTemplate() {
        const htmlContent = document.getElementById('html_content').value;
        const subject = document.getElementById('subject').value;
        
        if (!htmlContent.trim()) {
            alert('Please enter some HTML content first.');
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
        
        let previewContent = htmlContent;
        let previewSubject = subject;
        
        // Replace variables with sample data
        Object.keys(sampleData).forEach(key => {
            const regex = new RegExp(`{{${key}}}`, 'g');
            previewContent = previewContent.replace(regex, sampleData[key]);
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
            '.preview-content { border: 1px solid #d1d5db; }' +
            '</style>' +
            '</head>' +
            '<body>' +
            '<div class="preview-header">' +
            '<h3>Subject: ' + previewSubject + '</h3>' +
            '<p style="margin: 5px 0 0 0; color: #6b7280; font-size: 14px;">This is a preview with sample data</p>' +
            '</div>' +
            '<div class="preview-content">' +
            previewContent +
            '</div>' +
            '</body>' +
            '</html>'
        );
        previewWindow.document.close();
    }

    // Handle form submission based on button clicked
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const buttons = form.querySelectorAll('button[type="submit"]');
        
        buttons.forEach(button => {
            button.addEventListener('click', function(e) {
                const action = this.getAttribute('name') === 'action' ? this.value : 'active';
                const isActiveCheckbox = document.getElementById('is_active');
                
                if (action === 'draft') {
                    isActiveCheckbox.checked = false;
                } else if (action === 'active') {
                    isActiveCheckbox.checked = true;
                }
            });
        });
    });
</script>
@endpush

@push('styles')
<style>
.tab-button.active {
    border-color: #4f46e5 !important;
    color: #4f46e5 !important;
}
</style>
@endpush 