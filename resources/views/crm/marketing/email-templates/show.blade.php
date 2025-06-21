@extends('layouts.crm')

@section('title', 'Email Template: ' . $emailTemplate->name)

@section('content')
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $emailTemplate->name }}</h1>
                <p class="text-gray-600 mt-2">Email template details and preview</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('crm.marketing.email-templates.edit', $emailTemplate) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700">
                    Edit Template
                </a>
                <a href="{{ route('crm.marketing.email-templates.index') }}" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-50">
                    Back to Templates
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-1">
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Template Information</h3>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            @if($emailTemplate->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Inactive</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Category</dt>
                        <dd class="mt-1 text-sm text-gray-900 capitalize">{{ $emailTemplate->category }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Subject Line</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $emailTemplate->subject }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ formatDubaiDate($emailTemplate->created_at, 'M j, Y g:i A') }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Template Preview</h3>
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="text-sm text-gray-600 mb-4">
                        <strong>Subject:</strong> {{ $previewData['subject'] }}
                    </div>
                    <div class="relative">
                        <iframe id="email-preview" class="w-full h-96 border border-gray-300 rounded-md" sandbox="allow-same-origin" data-content="{{ htmlspecialchars($previewData['html_content'], ENT_QUOTES, 'UTF-8') }}"></iframe>
                    </div>
                    @if($previewData['text_content'])
                        <div class="mt-4 border-t border-gray-200 pt-4">
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Plain Text Version:</h4>
                            <div class="bg-gray-50 p-3 rounded-md text-sm text-gray-700 font-mono whitespace-pre-wrap max-h-32 overflow-y-auto">{{ $previewData['text_content'] }}</div>
                        </div>
                    @endif
                    <div class="mt-4 text-center">
                        <button onclick="showRawHtml()" class="text-sm text-indigo-600 hover:text-indigo-800 mr-4">View Raw HTML</button>
                        <button onclick="previewInNewWindow()" class="text-sm text-indigo-600 hover:text-indigo-800">Open in New Window</button>
                    </div>
                    <div class="mt-2 text-center">
                        <p class="text-xs text-gray-500">Preview uses sample data for demonstration purposes</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const iframe = document.getElementById('email-preview');
    
    // Set content via data attribute
    const htmlContent = iframe.getAttribute('data-content');
    
    if (htmlContent && htmlContent.trim()) {
        // Create a safe HTML document for the iframe
        const doc = iframe.contentDocument || iframe.contentWindow.document;
        doc.open();
        doc.write(htmlContent);
        doc.close();
    } else {
        // Show a message if no content is available
        const doc = iframe.contentDocument || iframe.contentWindow.document;
        doc.open();
        doc.write('<html><body style="font-family: Arial, sans-serif; padding: 40px; text-align: center; color: #666;"><h3>No Content Available</h3><p>This template does not have any content to preview.</p></body></html>');
        doc.close();
    }
});

function showRawHtml() {
    const iframe = document.getElementById('email-preview');
    const htmlContent = iframe.getAttribute('data-content');
    
    if (!htmlContent || !htmlContent.trim()) {
        alert('No HTML content available to display.');
        return;
    }
    
    const newWindow = window.open('', '_blank', 'width=800,height=600');
    newWindow.document.open();
    newWindow.document.write('<html><head><title>Raw HTML Content</title><style>body{font-family:monospace;margin:20px;white-space:pre-wrap;word-wrap:break-word;}</style></head><body>' + htmlContent.replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</body></html>');
    newWindow.document.close();
}

function previewInNewWindow() {
    const iframe = document.getElementById('email-preview');
    const htmlContent = iframe.getAttribute('data-content');
    
    if (!htmlContent || !htmlContent.trim()) {
        alert('No content available to preview.');
        return;
    }
    
    const newWindow = window.open('', '_blank', 'width=800,height=600');
    newWindow.document.open();
    newWindow.document.write(htmlContent);
    newWindow.document.close();
}
</script>
@endpush 