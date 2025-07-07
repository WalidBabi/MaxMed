@extends('supplier.layouts.app')

@section('title', 'Supplier Dashboard')

@section('content')
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Welcome to MaxMed Supplier Portal</h1>
                <p class="text-gray-600 mt-2">Manage your product catalog and track your submissions</p>
            </div>
            <div class="flex items-center space-x-3">
                <span class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.75 2a.75.75 0 01.75.75V4h7V2.75a.75.75 0 011.5 0V4h.25A2.25 2.25 0 0117.5 6.25v8.5A2.25 2.25 0 0115.25 17H4.75A2.25 2.25 0 012.5 14.75v-8.5A2.25 2.25 0 014.75 4H5V2.75A.75.75 0 015.75 2zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75z" clip-rule="evenodd" />
                    </svg>
                    {{ nowDubai('l, F j, Y') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Pending Approval Message -->
    @if($isPendingApproval)
    <div class="mb-8">
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-semibold text-yellow-900 mb-2">Profile Under Review</h3>
                    <div class="text-yellow-800 space-y-2">
                        <p>Thank you for completing your onboarding! MaxMed is currently reviewing your profile and category assignments.</p>
                        <p>Once your profile is approved, you will be able to:</p>
                        <ul class="list-disc list-inside ml-4 space-y-1">
                            <li>Add products to your approved categories</li>
                            <li>Receive customer inquiries and quotation requests</li>
                            <li>Process orders and manage deliveries</li>
                            <li>Access all supplier features and analytics</li>
                        </ul>
                        <p class="mt-3 text-sm">We typically complete reviews within 2-3 business days. You'll receive a notification once your profile is approved.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Appreciation Message -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-blue-800">Your partnership drives our success. Together, we're delivering excellence to healthcare providers across the region.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Management Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Product Statistics -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        Product Management
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="bg-blue-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-100 rounded-md flex items-center justify-center">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-blue-600">Total Products</p>
                                    <p class="text-2xl font-bold text-blue-900">{{ $totalProducts }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-green-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-green-100 rounded-md flex items-center justify-center">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-green-600">Active Products</p>
                                    <p class="text-2xl font-bold text-green-900">{{ $activeProducts }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        @if($isPendingApproval)
                            <p class="text-gray-600">Product management will be available once your profile is approved</p>
                            <div class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                Manage Products (Pending Approval)
                            </div>
                        @else
                            <p class="text-gray-600">Manage your product catalog and inventory</p>
                            <a href="{{ route('supplier.products.index') }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                Manage Products
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                </div>
                <div class="p-6 space-y-4">
                    @if($isPendingApproval)
                        <div class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add New Product (Pending Approval)
                        </div>
                        
                        <div class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            View Orders (Pending Approval)
                        </div>
                    @else
                        <a href="{{ route('supplier.products.create') }}" 
                           class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add New Product
                        </a>
                        
                        <a href="{{ route('supplier.orders.index') }}" 
                           class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            View Orders
                        </a>
                    @endif
                    
                    @if($isPendingApproval)
                        <div class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            View Inquiries (Pending Approval)
                        </div>
                    @else
                        <a href="{{ route('supplier.inquiries.index') }}" 
                           class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            View Inquiries
                        </a>
                    @endif
                    
                    <a href="{{ route('supplier.feedback.create') }}" 
                       class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        Send Feedback
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Inquiry Management Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Inquiry Statistics -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Inquiry Management
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-orange-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-orange-900">{{ $inquiryStats['pending'] }}</div>
                            <div class="text-sm text-orange-600">Pending</div>
                        </div>
                        
                        <div class="bg-blue-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-blue-900">{{ $inquiryStats['viewed'] }}</div>
                            <div class="text-sm text-blue-600">Viewed</div>
                        </div>
                        
                        <div class="bg-green-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-green-900">{{ $inquiryStats['quoted'] }}</div>
                            <div class="text-sm text-green-600">Quoted</div>
                        </div>
                        
                        <div class="bg-gray-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-gray-900">{{ $inquiryStats['not_available'] }}</div>
                            <div class="text-sm text-gray-600">Not Available</div>
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        @if($isPendingApproval)
                            <p class="text-gray-600">Inquiry management will be available once your profile is approved</p>
                            <div class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Manage Inquiries (Pending Approval)
                            </div>
                        @else
                            <p class="text-gray-600">Respond to customer inquiries and quotation requests</p>
                            <a href="{{ route('supplier.inquiries.index') }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Manage Inquiries
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Inquiries Requiring Attention -->
        <div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">
                        @if($isPendingApproval)
                            Inquiries (Pending Approval)
                        @else
                            Inquiries Requiring Attention
                        @endif
                    </h3>
                </div>
                <div class="p-6">
                    @if($isPendingApproval)
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                            <p class="text-sm text-gray-500 mt-2">Inquiries will appear here once your profile is approved</p>
                            <p class="text-xs text-gray-400 mt-1">You'll be able to receive and respond to customer inquiries</p>
                        </div>
                    @elseif($recentInquiries->count() > 0)
                        <div class="space-y-3">
                            @foreach($recentInquiries as $inquiry)
                                @php
                                    $response = $inquiry->supplierResponses->where('user_id', auth()->id())->first();
                                    $statusClasses = [
                                        'pending' => 'bg-orange-100 text-orange-800',
                                        'viewed' => 'bg-blue-100 text-blue-800',
                                        'quoted' => 'bg-green-100 text-green-800',
                                        'not_available' => 'bg-gray-100 text-gray-800',
                                    ];
                                    $statusClass = $statusClasses[$response->status ?? 'pending'] ?? 'bg-gray-100 text-gray-800';
                                    $statusText = ucfirst(str_replace('_', ' ', $response->status ?? 'pending'));
                                @endphp
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $inquiry->reference_number }}</div>
                                        <div class="text-xs text-gray-500">{{ $inquiry->product_name ?: ($inquiry->product->name ?? 'Product Inquiry') }}</div>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                                            {{ $statusText }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('supplier.inquiries.index', ['status' => 'pending']) }}" 
                               class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                                View all pending inquiries →
                            </a>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-sm text-gray-500 mt-2">All caught up!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Order Management Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Order Statistics -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Order Management
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-orange-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-orange-900">{{ $orderStats['pending'] }}</div>
                            <div class="text-sm text-orange-600">Pending</div>
                        </div>
                        
                        <div class="bg-blue-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-blue-900">{{ $orderStats['processing'] }}</div>
                            <div class="text-sm text-blue-600">Processing</div>
                        </div>
                        
                        <div class="bg-purple-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-purple-900">{{ $orderStats['in_transit'] }}</div>
                            <div class="text-sm text-purple-600">In Transit</div>
                        </div>
                        
                        <div class="bg-green-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-green-900">{{ $orderStats['delivered_today'] }}</div>
                            <div class="text-sm text-green-600">Delivered Today</div>
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        @if($isPendingApproval)
                            <p class="text-gray-600">Order management will be available once your profile is approved</p>
                            <div class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Manage Orders (Pending Approval)
                            </div>
                        @else
                            <p class="text-gray-600">Process and manage customer orders</p>
                            <a href="{{ route('supplier.orders.index') }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Manage Orders
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders Requiring Attention -->
        <div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">
                        @if($isPendingApproval)
                            Orders (Pending Approval)
                        @else
                            Orders Requiring Attention
                        @endif
                    </h3>
                </div>
                <div class="p-6">
                    @if($isPendingApproval)
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                            <p class="text-sm text-gray-500 mt-2">Orders will appear here once your profile is approved</p>
                            <p class="text-xs text-gray-400 mt-1">You'll be able to receive and manage customer orders</p>
                        </div>
                    @elseif($recentOrders->count() > 0)
                        <div class="space-y-3">
                            @foreach($recentOrders as $order)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $order->order_number }}</div>
                                        <div class="text-xs text-gray-500">{{ $order->items->count() }} items</div>
                                    </div>
                                    <div class="text-right">
                                        @php
                                            $statusClasses = [
                                                'pending' => 'bg-orange-100 text-orange-800',
                                                'processing' => 'bg-blue-100 text-blue-800',
                                                'awaiting_quotations' => 'bg-yellow-100 text-yellow-800',
                                                'quotations_received' => 'bg-blue-100 text-blue-800',
                                            ];
                                            
                                            if ($order->delivery) {
                                                $currentStatus = $order->delivery->status;
                                                $statusClass = $statusClasses[$currentStatus] ?? 'bg-gray-100 text-gray-800';
                                                $statusText = ucfirst($currentStatus);
                                            } else {
                                                $currentStatus = $order->status;
                                                $statusClass = $statusClasses[$currentStatus] ?? 'bg-gray-100 text-gray-800';
                                                $statusText = $currentStatus === 'awaiting_quotations' ? 'Needs Quotation' : ucfirst(str_replace('_', ' ', $currentStatus));
                                            }
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                                            {{ $statusText }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('supplier.orders.index', ['status' => 'pending']) }}" 
                               class="text-sm text-purple-600 hover:text-purple-700 font-medium">
                                View all pending orders →
                            </a>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-sm text-gray-500 mt-2">All caught up!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection 