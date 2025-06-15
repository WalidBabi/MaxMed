@extends('admin.layouts.app')

@section('title', 'Customer Details')

@section('content')
<div class="-mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $customer->name }}</h1>
                <p class="text-gray-600 mt-2">Customer profile and information</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.customers.edit', $customer) }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                    </svg>
                    Edit Customer
                </a>
                <a href="{{ route('admin.customers.index') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Back to Customers
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-8 xl:grid-cols-4">
        <!-- Customer Details Sidebar -->
        <div class="xl:col-span-1">
            <!-- Customer Profile Card -->
            <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Customer Details</h3>
                </div>
                <div class="p-6">
                    <!-- Avatar and Basic Info -->
                    <div class="text-center mb-6">
                        <div class="mx-auto h-20 w-20 rounded-full flex items-center justify-center text-2xl font-bold text-white mb-3 bg-indigo-600">
                            {{ strtoupper(substr($customer->name, 0, 1)) }}
                        </div>
                        <h4 class="text-xl font-semibold text-gray-900 mb-2">{{ $customer->name }}</h4>
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $customer->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $customer->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <!-- Contact Information -->
                    <div class="space-y-4">
                        <div>
                            <h6 class="text-sm font-medium text-gray-500 mb-2">Contact Information</h6>
                            <div class="space-y-2">
                                <div class="flex items-center text-sm text-gray-900">
                                    <svg class="h-4 w-4 text-gray-400 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                    </svg>
                                    {{ $customer->email ?? 'N/A' }}
                                </div>
                                <div class="flex items-center text-sm text-gray-900">
                                    <svg class="h-4 w-4 text-gray-400 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                                    </svg>
                                    {{ $customer->phone ?? 'N/A' }}
                                </div>
                                @if($customer->user)
                                    <div class="flex items-center text-sm text-gray-900">
                                        <svg class="h-4 w-4 text-gray-400 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                        </svg>
                                        Linked to: {{ $customer->user->name }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if($customer->company_name || $customer->tax_id)
                            <div>
                                <h6 class="text-sm font-medium text-gray-500 mb-2">Company Information</h6>
                                <div class="space-y-2">
                                    @if($customer->company_name)
                                        <div class="flex items-center text-sm text-gray-900">
                                            <svg class="h-4 w-4 text-gray-400 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m2.25-18v18m13.5-18v18m2.25-18v18M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.75m-.75 3h.75m-.75 3h.75m-3.75-16.5h.75m-.75 3h.75m-.75 3h.75m-3.75-7.5h.75" />
                                            </svg>
                                            {{ $customer->company_name }}
                                        </div>
                                    @endif
                                    @if($customer->tax_id)
                                        <div class="flex items-center text-sm text-gray-900">
                                            <svg class="h-4 w-4 text-gray-400 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                            </svg>
                                            Tax ID: {{ $customer->tax_id }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Additional Customer Information -->
                        <div>
                            <h6 class="text-sm font-medium text-gray-500 mb-2">System Information</h6>
                            <div class="space-y-2">
                                <div class="flex items-center text-sm text-gray-900">
                                    <svg class="h-4 w-4 text-gray-400 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                    </svg>
                                    Customer #{{ $customer->id }}
                                </div>
                                @if($customer->user_id)
                                    <div class="flex items-center text-sm text-gray-900">
                                        <svg class="h-4 w-4 text-gray-400 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                                        </svg>
                                        User #{{ $customer->user_id }}
                                    </div>
                                @else
                                    <div class="flex items-center text-sm text-red-600">
                                        <svg class="h-4 w-4 text-red-400 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728" />
                                        </svg>
                                        No User Account
                                    </div>
                                @endif
                                <div class="flex items-center text-sm {{ $customer->is_active ? 'text-green-600' : 'text-red-600' }}">
                                    <svg class="h-4 w-4 {{ $customer->is_active ? 'text-green-400' : 'text-red-400' }} mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        @if($customer->is_active)
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728" />
                                        @endif
                                    </svg>
                                    Status: {{ $customer->is_active ? 'Active' : 'Inactive' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Stats Card -->
            <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Customer Statistics</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900">{{ $stats['total_orders'] }}</div>
                            <div class="text-sm text-gray-500">Total Orders</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900">AED{{ number_format($stats['total_spent'], 2) }}</div>
                            <div class="text-sm text-gray-500">Total Spent</div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900">{{ $stats['total_quotes'] }}</div>
                            <div class="text-sm text-gray-500">Total Quotes</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900">{{ $stats['total_invoices'] }}</div>
                            <div class="text-sm text-gray-500">Total Invoices</div>
                        </div>
                    </div>
                    @if($stats['pending_invoices'] > 0 || $stats['overdue_invoices'] > 0)
                    <div class="grid grid-cols-2 gap-4 mb-4 p-3 bg-red-50 rounded-lg">
                        <div class="text-center">
                            <div class="text-xl font-bold text-red-600">{{ $stats['pending_invoices'] }}</div>
                            <div class="text-xs text-red-500">Pending Invoices</div>
                        </div>
                        <div class="text-center">
                            <div class="text-xl font-bold text-red-600">{{ $stats['overdue_invoices'] }}</div>
                            <div class="text-xs text-red-500">Overdue Invoices</div>
                        </div>
                    </div>
                    @endif
                    <div class="grid grid-cols-1 gap-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Member Since</span>
                            <span class="text-gray-900">{{ $customer->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Last Updated</span>
                            <span class="text-gray-900">{{ $customer->updated_at->format('M d, Y') }}</span>
                        </div>
                        @if($customer->deleted_at)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Soft Deleted</span>
                            <span class="text-red-600">{{ $customer->deleted_at->format('M d, Y') }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="xl:col-span-3">
            <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5" x-data="{ activeTab: 'addresses' }">
                <!-- Tab Navigation -->
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8 px-6">
                        <button @click="activeTab = 'addresses'" 
                                :class="activeTab === 'addresses' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                            <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                            </svg>
                            Addresses
                        </button>
                        <button @click="activeTab = 'orders'" 
                                :class="activeTab === 'orders' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                            <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                            </svg>
                            Orders ({{ $stats['total_orders'] }})
                        </button>
                        <button @click="activeTab = 'activity'" 
                                :class="activeTab === 'activity' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                            <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                            </svg>
                            Activity
                        </button>
                        <button @click="activeTab = 'notes'" 
                                :class="activeTab === 'notes' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                            <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 18H3.75m15.75-18H3.75m15.75 0v3.375c0 .621-.504 1.125-1.125 1.125H16.5a1.125 1.125 0 01-1.125-1.125V3.375m1.5 0H21m-1.5 0H18.375m-1.5 0v3.375c0 .621.504 1.125 1.125 1.125h1.5m0 0H21m-1.5 0H18.375" />
                            </svg>
                            Notes
                        </button>
                    </nav>
                </div>

                <!-- Tab Content -->
                <div class="p-6">
                    <!-- Addresses Tab -->
                    <div x-show="activeTab === 'addresses'" x-transition>
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <!-- Billing Address -->
                            <div class="rounded-lg border border-gray-200 p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-lg font-medium text-gray-900">Billing Address</h4>
                                  
                                </div>
                                
                                <!-- Always show address details for debugging -->
                                <div class="bg-gray-50 p-3 rounded mb-3">
                                    <h6 class="text-xs font-medium text-gray-700 mb-2">All Billing Address Fields:</h6>
                                    <div class="grid grid-cols-2 gap-2 text-xs text-gray-600">
                                        <div><span class="font-medium">Street:</span> "{{ $customer->billing_street }}"</div>
                                        <div><span class="font-medium">City:</span> "{{ $customer->billing_city }}"</div>
                                        <div><span class="font-medium">State:</span> "{{ $customer->billing_state }}"</div>
                                        <div><span class="font-medium">ZIP:</span> "{{ $customer->billing_zip }}"</div>
                                        <div class="col-span-2"><span class="font-medium">Country:</span> "{{ $customer->billing_country }}"</div>
                                    </div>
                                </div>

                                @if($customer->billing_street || $customer->billing_city || $customer->billing_state || $customer->billing_zip || $customer->billing_country)
                                    <address class="text-sm text-gray-900 not-italic space-y-1">
                                        @if($customer->billing_street)
                                            <div>{{ $customer->billing_street }}</div>
                                        @endif
                                        @if($customer->billing_city || $customer->billing_state || $customer->billing_zip)
                                            <div>
                                                {{ $customer->billing_city }}{{ $customer->billing_city && ($customer->billing_state || $customer->billing_zip) ? ', ' : '' }}
                                                {{ $customer->billing_state }}{{ $customer->billing_state && $customer->billing_zip ? ' ' : '' }}
                                                {{ $customer->billing_zip }}
                                            </div>
                                        @endif
                                        @if($customer->billing_country)
                                            <div>{{ $customer->billing_country }}</div>
                                        @endif
                                    </address>
                                @else
                                    <p class="text-sm text-red-600 bg-red-50 p-2 rounded">No billing address data found in any field.</p>
                                @endif
                            </div>

                            <!-- Shipping Address -->
                            <div class="rounded-lg border border-gray-200 p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-lg font-medium text-gray-900">Shipping Address</h4>
                                   
                                </div>
                                
                                <!-- Always show address details for debugging -->
                                <div class="bg-blue-50 p-3 rounded mb-3">
                                    <h6 class="text-xs font-medium text-blue-700 mb-2">All Shipping Address Fields:</h6>
                                    <div class="grid grid-cols-2 gap-2 text-xs text-blue-600">
                                        <div><span class="font-medium">Street:</span> "{{ $customer->shipping_street }}"</div>
                                        <div><span class="font-medium">City:</span> "{{ $customer->shipping_city }}"</div>
                                        <div><span class="font-medium">State:</span> "{{ $customer->shipping_state }}"</div>
                                        <div><span class="font-medium">ZIP:</span> "{{ $customer->shipping_zip }}"</div>
                                        <div class="col-span-2"><span class="font-medium">Country:</span> "{{ $customer->shipping_country }}"</div>
                                    </div>
                                </div>
                                @if($customer->shipping_street || $customer->shipping_city || $customer->shipping_state || $customer->shipping_zip || $customer->shipping_country)
                                    <address class="text-sm text-gray-900 not-italic space-y-1">
                                        @if($customer->shipping_street)
                                            <div>{{ $customer->shipping_street }}</div>
                                        @endif
                                        @if($customer->shipping_city || $customer->shipping_state || $customer->shipping_zip)
                                            <div>
                                                {{ $customer->shipping_city }}{{ $customer->shipping_city && ($customer->shipping_state || $customer->shipping_zip) ? ', ' : '' }}
                                                {{ $customer->shipping_state }}{{ $customer->shipping_state && $customer->shipping_zip ? ' ' : '' }}
                                                {{ $customer->shipping_zip }}
                                            </div>
                                        @endif
                                        @if($customer->shipping_country)
                                            <div>{{ $customer->shipping_country }}</div>
                                        @endif
                                    </address>
                                    <div class="mt-3 pt-3 border-t border-gray-100">
                                        <h6 class="text-xs font-medium text-gray-500 mb-2">Address Details:</h6>
                                        <div class="grid grid-cols-2 gap-2 text-xs text-gray-600">
                                            <div><span class="font-medium">Street:</span> {{ $customer->shipping_street ?: 'N/A' }}</div>
                                            <div><span class="font-medium">City:</span> {{ $customer->shipping_city ?: 'N/A' }}</div>
                                            <div><span class="font-medium">State:</span> {{ $customer->shipping_state ?: 'N/A' }}</div>
                                            <div><span class="font-medium">ZIP:</span> {{ $customer->shipping_zip ?: 'N/A' }}</div>
                                            <div class="col-span-2"><span class="font-medium">Country:</span> {{ $customer->shipping_country ?: 'N/A' }}</div>
                                        </div>
                                    </div>
                                    
                                    @if($customer->billing_street || $customer->billing_city || $customer->billing_state || $customer->billing_zip || $customer->billing_country)
                                        <div class="mt-3 pt-3 border-t border-gray-100">
                                            <div class="flex items-center text-xs text-gray-500">
                                                <svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                @if($customer->shipping_street == $customer->billing_street && 
                                                   $customer->shipping_city == $customer->billing_city && 
                                                   $customer->shipping_state == $customer->billing_state && 
                                                   $customer->shipping_zip == $customer->billing_zip && 
                                                   $customer->shipping_country == $customer->billing_country)
                                                    Same as billing address
                                                @else
                                                    Different from billing address
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    @if($customer->billing_street || $customer->billing_city || $customer->billing_state || $customer->billing_zip || $customer->billing_country)
                                        <div class="text-sm text-gray-600 bg-blue-50 p-3 rounded-lg">
                                            <div class="flex items-center mb-2">
                                                <svg class="h-4 w-4 text-blue-600 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                                                </svg>
                                                <span class="text-sm font-medium text-blue-800">Using billing address for shipping</span>
                                            </div>
                                            <address class="text-sm text-blue-700 not-italic space-y-1">
                                                @if($customer->billing_street)
                                                    <div>{{ $customer->billing_street }}</div>
                                                @endif
                                                @if($customer->billing_city || $customer->billing_state || $customer->billing_zip)
                                                    <div>
                                                        {{ $customer->billing_city }}{{ $customer->billing_city && ($customer->billing_state || $customer->billing_zip) ? ', ' : '' }}
                                                        {{ $customer->billing_state }}{{ $customer->billing_state && $customer->billing_zip ? ' ' : '' }}
                                                        {{ $customer->billing_zip }}
                                                    </div>
                                                @endif
                                                @if($customer->billing_country)
                                                    <div>{{ $customer->billing_country }}</div>
                                                @endif
                                            </address>
                                        </div>
                                    @else
                                        <p class="text-sm text-gray-500">No shipping address provided.</p>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Orders Tab -->
                    <div x-show="activeTab === 'orders'" x-transition style="display: none;">
                        @if($recentOrders->count() > 0)
                            <div class="space-y-4">
                                @foreach($recentOrders as $order)
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center space-x-2">
                                                <h4 class="text-sm font-medium text-gray-900">{{ $order->order_number }}</h4>
                                                <span class="px-2.5 py-0.5 text-xs font-medium rounded-full
                                                    @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                                                    @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                                                    @elseif($order->status === 'shipped') bg-purple-100 text-purple-800
                                                    @elseif($order->status === 'delivered') bg-green-100 text-green-800
                                                    @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                                    @else bg-gray-100 text-gray-800 @endif">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </div>
                                            <div class="text-sm text-gray-900 font-medium">AED{{ number_format($order->total_amount, 2) }}</div>
                                        </div>
                                        <div class="text-xs text-gray-500 mb-2">{{ $order->created_at->format('M d, Y') }}</div>
                                        @if($order->orderItems->count() > 0)
                                            <div class="text-xs text-gray-600">
                                                {{ $order->orderItems->count() }} item(s): {{ $order->orderItems->take(2)->pluck('product.name')->implode(', ') }}{{ $order->orderItems->count() > 2 ? '...' : '' }}
                                            </div>
                                        @endif
                                        <div class="mt-2 flex justify-end">
                                            <a href="{{ route('admin.orders.show', $order) }}" class="text-xs text-indigo-600 hover:text-indigo-800">View Details →</a>
                                        </div>
                                    </div>
                                @endforeach
                                
                                @if($stats['total_orders'] > 5)
                                    <div class="text-center pt-4">
                                        <a href="{{ route('admin.orders.index') }}?customer_id={{ $customer->id }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                                            View all {{ $stats['total_orders'] }} orders →
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-semibold text-gray-900">No orders found</h3>
                                <p class="mt-1 text-sm text-gray-500">This customer hasn't placed any orders yet.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Activity Tab -->
                    <div x-show="activeTab === 'activity'" x-transition style="display: none;">
                        <div class="space-y-6">
                            <!-- Recent Quotes -->
                            @if($recentQuotes->count() > 0)
                                <div>
                                    <h4 class="text-lg font-medium text-gray-900 mb-3 flex items-center">
                                        <svg class="h-5 w-5 text-indigo-600 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                                        </svg>
                                        Recent Quotes ({{ $stats['total_quotes'] }})
                                    </h4>
                                    <div class="space-y-3">
                                        @foreach($recentQuotes as $quote)
                                            <div class="border border-gray-200 rounded-lg p-4">
                                                <div class="flex items-center justify-between mb-2">
                                                    <div class="flex items-center space-x-2">
                                                        <h5 class="text-sm font-medium text-gray-900">{{ $quote->quote_number }}</h5>
                                                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full
                                                            @if($quote->status === 'draft') bg-gray-100 text-gray-800
                                                            @elseif($quote->status === 'sent') bg-blue-100 text-blue-800
                                                            @elseif($quote->status === 'approved') bg-green-100 text-green-800
                                                            @elseif($quote->status === 'rejected') bg-red-100 text-red-800
                                                            @elseif($quote->status === 'invoiced') bg-purple-100 text-purple-800
                                                            @else bg-gray-100 text-gray-800 @endif">
                                                            {{ ucfirst($quote->status) }}
                                                        </span>
                                                    </div>
                                                    <div class="text-sm text-gray-900 font-medium">AED{{ number_format($quote->total_amount, 2) }}</div>
                                                </div>
                                                <div class="text-xs text-gray-500 mb-1">{{ $quote->quote_date->format('M d, Y') }}</div>
                                                @if($quote->subject)
                                                    <div class="text-xs text-gray-600 mb-2">{{ Str::limit($quote->subject, 50) }}</div>
                                                @endif
                                                <div class="flex justify-end">
                                                    <a href="{{ route('admin.quotes.show', $quote) }}" class="text-xs text-indigo-600 hover:text-indigo-800">View Quote →</a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <hr class="my-6">
                            @endif

                            <!-- Recent Invoices -->
                            @if($recentInvoices->count() > 0)
                                <div>
                                    <h4 class="text-lg font-medium text-gray-900 mb-3 flex items-center">
                                        <svg class="h-5 w-5 text-indigo-600 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                        </svg>
                                        Recent Invoices ({{ $stats['total_invoices'] }})
                                    </h4>
                                    <div class="space-y-3">
                                        @foreach($recentInvoices as $invoice)
                                            <div class="border border-gray-200 rounded-lg p-4">
                                                <div class="flex items-center justify-between mb-2">
                                                    <div class="flex items-center space-x-2">
                                                        <h5 class="text-sm font-medium text-gray-900">{{ $invoice->invoice_number }}</h5>
                                                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full
                                                            @if($invoice->type === 'proforma') bg-blue-100 text-blue-800
                                                            @else bg-green-100 text-green-800 @endif">
                                                            {{ ucfirst($invoice->type) }}
                                                        </span>
                                                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full
                                                            @if($invoice->payment_status === 'pending') bg-yellow-100 text-yellow-800
                                                            @elseif($invoice->payment_status === 'paid') bg-green-100 text-green-800
                                                            @elseif($invoice->payment_status === 'overdue') bg-red-100 text-red-800
                                                            @elseif($invoice->payment_status === 'partial') bg-orange-100 text-orange-800
                                                            @else bg-gray-100 text-gray-800 @endif">
                                                            {{ ucfirst($invoice->payment_status) }}
                                                        </span>
                                                    </div>
                                                    <div class="text-sm text-gray-900 font-medium">AED{{ number_format($invoice->total_amount, 2) }}</div>
                                                </div>
                                                <div class="text-xs text-gray-500 mb-1">{{ $invoice->invoice_date->format('M d, Y') }}</div>
                                                @if($invoice->due_date)
                                                    <div class="text-xs text-gray-600 mb-2">Due: {{ $invoice->due_date->format('M d, Y') }}</div>
                                                @endif
                                                <div class="flex justify-end">
                                                    <a href="{{ route('admin.invoices.show', $invoice) }}" class="text-xs text-indigo-600 hover:text-indigo-800">View Invoice →</a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if($recentQuotes->count() === 0 && $recentInvoices->count() === 0)
                                <div class="text-center py-12">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-semibold text-gray-900">No recent activity</h3>
                                    <p class="mt-1 text-sm text-gray-500">No quotes or invoices found for this customer.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Notes Tab -->
                    <div x-show="activeTab === 'notes'" x-transition style="display: none;">
                        @if($customer->notes)
                            <div class="mb-6">
                                <h4 class="text-lg font-medium text-gray-900 mb-3">Customer Notes</h4>
                                <div class="rounded-lg bg-gray-50 p-4">
                                    <p class="text-sm text-gray-900 whitespace-pre-line">{{ $customer->notes }}</p>
                                </div>
                            </div>
                        @endif
                        
                        <form action="#" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label for="newNote" class="block text-sm font-medium leading-6 text-gray-900">Add Note</label>
                                <div class="mt-2">
                                    <textarea id="newNote" name="note" rows="4" 
                                              class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                              placeholder="Add a note about this customer..."></textarea>
                                </div>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                                    </svg>
                                    Add Note
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
