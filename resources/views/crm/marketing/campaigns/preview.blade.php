@extends('layouts.crm')

@section('title', 'Campaign Preview')

@section('content')
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('crm.marketing.campaigns.show', $campaign) }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 mb-4">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M7.72 12.53a.75.75 0 010-1.06L10.94 8.25H6.5a.75.75 0 010-1.5h4.44L7.72 3.53a.75.75 0 011.06-1.06l4.25 4.25a.75.75 0 010 1.06l-4.25 4.25a.75.75 0 01-1.06 0z" clip-rule="evenodd" />
                    </svg>
                    Back to Campaign
                </a>
                <h1 class="text-3xl font-bold text-gray-900">Preview Campaign</h1>
                <p class="text-gray-600 mt-2">{{ $campaign->name }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('crm.marketing.campaigns.edit', $campaign) }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M2.695 14.763l-1.262 3.154a.5.5 0 00.65.65l3.155-1.262a4 4 0 001.343-.885L17.5 5.5a2.121 2.121 0 00-3-3L3.58 13.42a4 4 0 00-.885 1.343z" />
                    </svg>
                    Edit Campaign
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-4">
        <!-- Preview Panel -->
        <div class="lg:col-span-3">
            <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Email Preview</h3>
                        <div class="flex items-center space-x-2">
                            <button id="desktop-view" class="px-3 py-1 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                                Desktop
                            </button>
                            <button id="mobile-view" class="px-3 py-1 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                Mobile
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Subject Line Preview -->
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <div class="flex items-center">
                        <span class="text-sm font-medium text-gray-600 mr-2">Subject:</span>
                        <span class="text-sm text-gray-900 font-medium">{{ $subject }}</span>
                    </div>
                </div>

                <!-- Email Content Preview -->
                <div class="p-6">
                    <div id="email-preview-container" class="transition-all duration-300">
                        <!-- Desktop Preview -->
                        <div id="desktop-preview" class="border border-gray-200 rounded-lg overflow-hidden">
                            <div class="bg-gray-100 px-4 py-2 border-b border-gray-200">
                                <div class="flex items-center space-x-2">
                                    <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                                    <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                                    <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                    <span class="text-xs text-gray-600 ml-4">{{ $sampleContact->email ?? 'sample@email.com' }}</span>
                                </div>
                            </div>
                            <div class="bg-white" style="min-height: 600px;">
                                <div id="desktop-email-content" class="p-4" style="min-height: 600px;">
                                    <!-- Content will be loaded here -->
                                    <div class="flex items-center justify-center h-full">
                                        <div class="text-center">
                                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto mb-4"></div>
                                            <p class="text-gray-500">Loading preview...</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Mobile Preview -->
                        <div id="mobile-preview" class="hidden mx-auto" style="max-width: 375px;">
                            <div class="border border-gray-200 rounded-2xl overflow-hidden shadow-lg">
                                <div class="bg-gray-100 px-4 py-2 border-b border-gray-200">
                                    <div class="flex items-center justify-center">
                                        <span class="text-xs text-gray-600">{{ $sampleContact->email ?? 'sample@email.com' }}</span>
                                    </div>
                                </div>
                                <div class="bg-white" style="height: 600px; overflow-y: auto;">
                                    <div id="mobile-email-content" class="p-4">
                                        <!-- Content will be loaded here -->
                                        <div class="flex items-center justify-center h-full">
                                            <div class="text-center">
                                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto mb-4"></div>
                                                <p class="text-gray-500">Loading preview...</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Campaign Info -->
            <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Campaign Info</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <span class="text-sm font-medium text-gray-600">Status:</span>
                        <span class="ml-2 inline-flex items-center rounded-md px-2 py-1 text-xs font-medium
                            @if($campaign->status == 'draft') bg-gray-50 text-gray-600 ring-1 ring-inset ring-gray-500/10
                            @elseif($campaign->status == 'scheduled') bg-yellow-50 text-yellow-800 ring-1 ring-inset ring-yellow-600/20
                            @elseif($campaign->status == 'sending') bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-700/10
                            @elseif($campaign->status == 'sent') bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20
                            @elseif($campaign->status == 'paused') bg-yellow-50 text-yellow-800 ring-1 ring-inset ring-yellow-600/20
                            @elseif($campaign->status == 'cancelled') bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/10
                            @endif">
                            {{ ucfirst($campaign->status) }}
                        </span>
                    </div>
                    
                    <div>
                        <span class="text-sm font-medium text-gray-600">Type:</span>
                        <span class="ml-2 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $campaign->type)) }}</span>
                    </div>
                    
                    <div>
                        <span class="text-sm font-medium text-gray-600">Recipients:</span>
                        <span class="ml-2 text-sm text-gray-900">{{ number_format($campaign->total_recipients ?? 0) }}</span>
                    </div>
                    
                    @if($campaign->scheduled_at)
                    <div>
                        <span class="text-sm font-medium text-gray-600">Scheduled:</span>
                        <span class="ml-2 text-sm text-gray-900">{{ formatDubaiDate($campaign->scheduled_at, 'M d, Y H:i') }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Sample Contact -->
            <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Sample Contact</h3>
                    <p class="text-sm text-gray-600 mt-1">Preview data being used</p>
                </div>
                <div class="p-6 space-y-3">
                    <div>
                        <span class="text-sm font-medium text-gray-600">Name:</span>
                        <span class="ml-2 text-sm text-gray-900">{{ $sampleContact->first_name ?? 'John' }} {{ $sampleContact->last_name ?? 'Doe' }}</span>
                    </div>
                    
                    <div>
                        <span class="text-sm font-medium text-gray-600">Email:</span>
                        <span class="ml-2 text-sm text-gray-900">{{ $sampleContact->email ?? 'john.doe@example.com' }}</span>
                    </div>
                    
                    @if($sampleContact->company ?? null)
                    <div>
                        <span class="text-sm font-medium text-gray-600">Company:</span>
                        <span class="ml-2 text-sm text-gray-900">{{ $sampleContact->company }}</span>
                    </div>
                    @endif
                    
                    @if($sampleContact->job_title ?? null)
                    <div>
                        <span class="text-sm font-medium text-gray-600">Job Title:</span>
                        <span class="ml-2 text-sm text-gray-900">{{ $sampleContact->job_title }}</span>
                    </div>
                    @endif
                    
                    @if($sampleContact->industry ?? null)
                    <div>
                        <span class="text-sm font-medium text-gray-600">Industry:</span>
                        <span class="ml-2 text-sm text-gray-900">{{ $sampleContact->industry }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden HTML Content for JavaScript -->
    <div id="html-content" style="display: none;">{!! $htmlContent !!}</div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const desktopBtn = document.getElementById('desktop-view');
            const mobileBtn = document.getElementById('mobile-view');
            const desktopPreview = document.getElementById('desktop-preview');
            const mobilePreview = document.getElementById('mobile-preview');
            
            // Get the HTML content
            const htmlContentDiv = document.getElementById('html-content');
            const htmlContent = htmlContentDiv ? htmlContentDiv.innerHTML : '<p>No content available for preview.</p>';
            
            // Load HTML content into preview containers
            function loadEmailContent() {
                const desktopContent = document.getElementById('desktop-email-content');
                const mobileContent = document.getElementById('mobile-email-content');
                
                if (desktopContent) {
                    try {
                        desktopContent.innerHTML = htmlContent;
                        
                        // Style adjustments for desktop preview
                        const style = document.createElement('style');
                        style.textContent = `
                            #desktop-email-content {
                                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                                line-height: 1.6;
                                color: #333;
                            }
                        `;
                        document.head.appendChild(style);
                    } catch (error) {
                        console.error('Error loading desktop content:', error);
                        desktopContent.innerHTML = '<div class="text-center text-red-600 p-8"><p>Error loading preview content</p></div>';
                    }
                }
                
                if (mobileContent) {
                    try {
                        mobileContent.innerHTML = htmlContent;
                        
                        // Style adjustments for mobile preview
                        const mobileCss = `
                            <style>
                                body { margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
                                table { width: 100% !important; max-width: 100% !important; }
                                td, th { font-size: 14px !important; }
                                img { max-width: 100% !important; height: auto !important; }
                                .container { width: 100% !important; max-width: 100% !important; padding: 10px !important; }
                            </style>
                        `;
                        mobileContent.innerHTML = mobileCss + htmlContent;
                    } catch (error) {
                        console.error('Error loading mobile content:', error);
                        mobileContent.innerHTML = '<div class="text-center text-red-600 p-8"><p>Error loading preview content</p></div>';
                    }
                }
            }
            
            // Load content
            loadEmailContent();

            // Desktop/Mobile toggle functionality
            desktopBtn.addEventListener('click', function() {
                // Update button states
                desktopBtn.classList.remove('bg-white', 'border', 'border-gray-300', 'text-gray-700');
                desktopBtn.classList.add('bg-indigo-600', 'text-white');
                mobileBtn.classList.remove('bg-indigo-600', 'text-white');
                mobileBtn.classList.add('bg-white', 'border', 'border-gray-300', 'text-gray-700');

                // Show/hide previews
                desktopPreview.classList.remove('hidden');
                mobilePreview.classList.add('hidden');
            });

            mobileBtn.addEventListener('click', function() {
                // Update button states
                mobileBtn.classList.remove('bg-white', 'border', 'border-gray-300', 'text-gray-700');
                mobileBtn.classList.add('bg-indigo-600', 'text-white');
                desktopBtn.classList.remove('bg-indigo-600', 'text-white');
                desktopBtn.classList.add('bg-white', 'border', 'border-gray-300', 'text-gray-700');

                // Show/hide previews
                mobilePreview.classList.remove('hidden');
                desktopPreview.classList.add('hidden');
            });
        });
    </script>
@endsection 