@extends('admin.layouts.app')

@section('title', 'Customer Details')

@section('content')
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

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <!-- Customer Details Sidebar -->
        <div class="lg:col-span-1">
            <!-- Customer Profile Card -->
            <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Customer Details</h3>
                </div>
                <div class="p-6">
                    <!-- Avatar and Basic Info -->
                    <div class="text-center mb-6">
                        <div class="mx-auto h-20 w-20 rounded-full flex items-center justify-center text-2xl font-bold text-white mb-3" 
                             style="background-color: #{{ substr(md5($customer->name), 0, 6) }}">
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
                            <div class="text-2xl font-bold text-gray-900">0</div>
                            <div class="text-sm text-gray-500">Total Orders</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900">$0.00</div>
                            <div class="text-sm text-gray-500">Total Spent</div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Member Since</span>
                            <span class="text-gray-900">{{ $customer->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Last Updated</span>
                            <span class="text-gray-900">{{ $customer->updated_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="lg:col-span-2">
            <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                <!-- Tab Navigation -->
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8 px-6" x-data="{ activeTab: 'addresses' }">
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
                            Orders (0)
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
                                    <button class="inline-flex items-center rounded-md bg-white px-2.5 py-1.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                        </svg>
                                    </button>
                                </div>
                                @if($customer->billing_street)
                                    <address class="text-sm text-gray-900 not-italic">
                                        {{ $customer->billing_street }}<br>
                                        {{ $customer->billing_city }}, {{ $customer->billing_state }} {{ $customer->billing_zip }}<br>
                                        {{ $customer->billing_country }}
                                    </address>
                                @else
                                    <p class="text-sm text-gray-500">No billing address provided.</p>
                                @endif
                            </div>

                            <!-- Shipping Address -->
                            <div class="rounded-lg border border-gray-200 p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-lg font-medium text-gray-900">Shipping Address</h4>
                                    <button class="inline-flex items-center rounded-md bg-white px-2.5 py-1.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                        </svg>
                                    </button>
                                </div>
                                @if($customer->shipping_street)
                                    <address class="text-sm text-gray-900 not-italic">
                                        {{ $customer->shipping_street }}<br>
                                        {{ $customer->shipping_city }}, {{ $customer->shipping_state }} {{ $customer->shipping_zip }}<br>
                                        {{ $customer->shipping_country }}
                                    </address>
                                @else
                                    <p class="text-sm text-gray-500">No shipping address provided.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Orders Tab -->
                    <div x-show="activeTab === 'orders'" x-transition style="display: none;">
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-semibold text-gray-900">No orders found</h3>
                            <p class="mt-1 text-sm text-gray-500">This customer hasn't placed any orders yet.</p>
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
