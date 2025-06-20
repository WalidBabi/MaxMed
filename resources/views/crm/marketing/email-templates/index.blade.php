@extends('layouts.crm')

@section('title', 'Email Templates')

@section('content')
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Email Templates</h1>
                <p class="text-gray-600 mt-2">Manage your email marketing templates</p>
            </div>
            <div class="flex items-center space-x-3">
                <button type="button" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a.75.75 0 01.55.24l3.25 3.5a.75.75 0 11-1.1 1.02L10 4.852 7.3 7.76a.75.75 0 01-1.1-1.02l3.25-3.5A.75.75 0 0110 3zm-3.76 9.2a.75.75 0 011.06.04l2.7 2.908 2.7-2.908a.75.75 0 111.1 1.02l-3.25 3.5a.75.75 0 01-1.1 0l-3.25-3.5a.75.75 0 01.04-1.06z" clip-rule="evenodd" />
                    </svg>
                    Import Templates
                </button>
                <a href="{{ route('crm.marketing.email-templates.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                    </svg>
                    Create Template
                </a>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Search & Filter Templates</h3>
        </div>
        <div class="p-6">
            <form method="GET" action="{{ route('crm.marketing.email-templates.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                        <input type="text" name="search" id="search" 
                               value="{{ request('search') }}"
                               placeholder="Template name or subject..."
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Template Type</label>
                        <select name="type" id="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">All Types</option>
                            <option value="newsletter" {{ request('type') == 'newsletter' ? 'selected' : '' }}>Newsletter</option>
                            <option value="promotional" {{ request('type') == 'promotional' ? 'selected' : '' }}>Promotional</option>
                            <option value="welcome" {{ request('type') == 'welcome' ? 'selected' : '' }}>Welcome</option>
                            <option value="transactional" {{ request('type') == 'transactional' ? 'selected' : '' }}>Transactional</option>
                            <option value="announcement" {{ request('type') == 'announcement' ? 'selected' : '' }}>Announcement</option>
                        </select>
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">All Statuses</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
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
                            <a href="{{ route('crm.marketing.email-templates.index') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                Clear
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Templates Grid -->
    <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Templates</h3>
                <div class="text-sm text-gray-500">
                    {{ isset($templates) ? $templates->total() : 0 }} total templates
                </div>
            </div>
        </div>
        
        @if(isset($templates) && $templates->count() > 0)
            <div class="p-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($templates as $template)
                        <div class="relative group bg-white border border-gray-200 rounded-lg hover:shadow-md transition-shadow duration-200">
                            <!-- Template Preview -->
                            <div class="aspect-video bg-gray-50 rounded-t-lg relative overflow-hidden">
                                @if($template->preview_image)
                                    <img src="{{ $template->preview_image }}" alt="{{ $template->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="flex items-center justify-center h-full text-gray-400">
                                        <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                        </svg>
                                    </div>
                                @endif
                                
                                <!-- Overlay buttons -->
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-200 flex items-center justify-center opacity-0 group-hover:opacity-100">
                                    <div class="flex space-x-2">
                                        <button type="button" 
                                                onclick="previewTemplate({{ $template->id }})"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            <svg class="-ml-0.5 mr-1.5 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M10 12.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z" />
                                                <path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 010-1.186A10.004 10.004 0 0110 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0110 17c-4.257 0-7.893-2.66-9.336-6.41zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                            </svg>
                                            Preview
                                        </button>
                                        <a href="{{ route('crm.marketing.email-templates.edit', $template) }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                            <svg class="-ml-0.5 mr-1.5 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M2.695 14.763l-1.262 3.154a.5.5 0 00.65.65l3.155-1.262a4 4 0 001.343-.885L17.5 5.5a2.121 2.121 0 00-3-3L3.58 13.42a4 4 0 00-.885 1.343z" />
                                            </svg>
                                            Edit
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Template Info -->
                            <div class="p-4">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-medium text-gray-900 truncate">{{ $template->name }}</h4>
                                        <p class="text-sm text-gray-500 truncate mt-1">{{ $template->subject ?? 'No subject' }}</p>
                                    </div>
                                    <div class="ml-2 flex-shrink-0">
                                        <div class="relative inline-block text-left">
                                            <button type="button" class="flex items-center text-gray-400 hover:text-gray-600" onclick="toggleDropdown({{ $template->id }})">
                                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M10 3a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM10 8.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM11.5 15.5a1.5 1.5 0 10-3 0 1.5 1.5 0 003 0z" />
                                                </svg>
                                            </button>
                                            <div id="dropdown-{{ $template->id }}" class="hidden origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                                <div class="py-1">
                                                    <a href="{{ route('crm.marketing.email-templates.show', $template) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">View Details</a>
                                                    <a href="{{ route('crm.marketing.email-templates.edit', $template) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Edit</a>
                                                    <button type="button" onclick="duplicateTemplate({{ $template->id }})" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Duplicate</button>
                                                    <button type="button" onclick="deleteTemplate({{ $template->id }})" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Delete</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3 flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        @if($template->type)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                @if($template->type == 'newsletter') bg-blue-100 text-blue-800
                                                @elseif($template->type == 'promotional') bg-green-100 text-green-800
                                                @elseif($template->type == 'welcome') bg-purple-100 text-purple-800
                                                @elseif($template->type == 'transactional') bg-gray-100 text-gray-800
                                                @else bg-yellow-100 text-yellow-800 @endif">
                                                {{ ucfirst($template->type) }}
                                            </span>
                                        @endif
                                        
                                        @if($template->status == 'active')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Active
                                            </span>
                                        @elseif($template->status == 'draft')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                Draft
                                            </span>
                                        @elseif($template->status == 'archived')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Archived
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="mt-3 flex items-center justify-between text-xs text-gray-500">
                                    <span>Used {{ $template->campaigns_count ?? 0 }} times</span>
                                    <span>{{ formatDubaiDateForHumans($template->updated_at) }}</span>
                                </div>
                                

                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $templates->links() }}
                </div>
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                </svg>
                <h3 class="mt-2 text-sm font-semibold text-gray-900">No email templates found</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating your first email template.</p>
                <div class="mt-6">
                    <a href="{{ route('crm.marketing.email-templates.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                        </svg>
                        Create Template
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- Preview Modal -->
    <div id="preview-modal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="w-full">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Email Template Preview</h3>
                                <button type="button" onclick="closePreview()" class="text-gray-400 hover:text-gray-600">
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            <div id="preview-content" class="border border-gray-200 rounded-lg bg-white min-h-96">
                                <!-- Preview content will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Delete Email Template</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Are you sure you want to delete this email template? This action cannot be undone.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <form id="delete-form" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Delete
                        </button>
                    </form>
                    <button type="button" onclick="closeDeleteModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function toggleDropdown(templateId) {
        const dropdown = document.getElementById(`dropdown-${templateId}`);
        const allDropdowns = document.querySelectorAll('[id^="dropdown-"]');
        
        // Close all other dropdowns
        allDropdowns.forEach(d => {
            if (d.id !== `dropdown-${templateId}`) {
                d.classList.add('hidden');
            }
        });
        
        // Toggle current dropdown
        dropdown.classList.toggle('hidden');
    }

    function previewTemplate(templateId) {
        console.log('Preview button clicked for template ID:', templateId);
        
        // Show modal
        const modal = document.getElementById('preview-modal');
        console.log('Modal element:', modal);
        modal.classList.remove('hidden');
        
        // Load preview content
        const previewContent = document.getElementById('preview-content');
        previewContent.innerHTML = '<div class="p-8 text-center"><div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div><p class="mt-2 text-gray-500">Loading preview...</p></div>';
        
        // Make AJAX call to get preview
        const url = `/crm/marketing/email-templates/${templateId}/preview`;
        console.log('Fetching URL:', url);
        
        fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            // Display preview content
            previewContent.innerHTML = `
                <div class="p-6">
                    <div class="mb-4 p-3 bg-gray-50 border-l-4 border-indigo-500">
                        <h4 class="font-medium text-gray-900">Subject Line:</h4>
                        <p class="text-gray-700 mt-1">${data.subject}</p>
                    </div>
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div class="bg-gray-50 px-4 py-2 border-b border-gray-200">
                            <h4 class="font-medium text-gray-900">HTML Preview:</h4>
                        </div>
                        <div class="p-4">
                            <iframe id="preview-iframe-${templateId}" class="w-full h-96 border-0" 
                                    sandbox="allow-same-origin">
                            </iframe>
                        </div>
                    </div>
                    ${data.text_content ? `
                        <div class="mt-4 border border-gray-200 rounded-lg overflow-hidden">
                            <div class="bg-gray-50 px-4 py-2 border-b border-gray-200">
                                <h4 class="font-medium text-gray-900">Plain Text Version:</h4>
                            </div>
                            <div class="p-4 bg-gray-50">
                                <pre class="whitespace-pre-wrap text-sm text-gray-700 font-mono">${data.text_content}</pre>
                            </div>
                        </div>
                    ` : ''}
                    <div class="mt-4 p-3 bg-blue-50 border-l-4 border-blue-500">
                        <p class="text-sm text-blue-700">
                            <strong>Note:</strong> This preview uses sample data. Actual emails will use real contact information.
                        </p>
                    </div>
                </div>
            `;
            
            // Load HTML content into iframe
            setTimeout(() => {
                const iframe = document.getElementById(`preview-iframe-${templateId}`);
                if (iframe && data.html_content) {
                    const doc = iframe.contentDocument || iframe.contentWindow.document;
                    doc.open();
                    doc.write(data.html_content);
                    doc.close();
                }
            }, 100);
        })
        .catch(error => {
            console.error('Error loading preview:', error);
            previewContent.innerHTML = `
                <div class="p-6 text-center">
                    <div class="text-red-500 mb-2">
                        <svg class="h-12 w-12 mx-auto" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Preview Error</h3>
                    <p class="text-gray-600">Unable to load preview. Please try again.</p>
                    <button onclick="previewTemplate(${templateId})" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        Retry
                    </button>
                </div>
            `;
        });
    }

    function closePreview() {
        document.getElementById('preview-modal').classList.add('hidden');
    }

    function deleteTemplate(templateId) {
        const deleteForm = document.getElementById('delete-form');
        deleteForm.action = `/crm/marketing/email-templates/${templateId}`;
        document.getElementById('delete-modal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('delete-modal').classList.add('hidden');
    }

    function duplicateTemplate(templateId) {
        // Implement template duplication
        console.log('Duplicate template:', templateId);
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('[onclick*="toggleDropdown"]')) {
            const allDropdowns = document.querySelectorAll('[id^="dropdown-"]');
            allDropdowns.forEach(d => d.classList.add('hidden'));
        }
    });
    
    // Close modal when clicking outside
    document.addEventListener('click', function(e) {
        const previewModal = document.getElementById('preview-modal');
        const deleteModal = document.getElementById('delete-modal');
        
        if (e.target === previewModal) {
            closePreview();
        }
        
        if (e.target === deleteModal) {
            closeDeleteModal();
        }
    });
</script>
@endpush 