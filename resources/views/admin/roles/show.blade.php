@extends('admin.layouts.app')

@section('title', 'Role Details: ' . $role->display_name)

@section('content')
<div class="-mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Role Details: {{ $role->display_name }}</h1>
                <p class="text-gray-600 mt-2">View role permissions and assigned users</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.roles.edit', $role) }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                    </svg>
                    Edit Role
                </a>
                <a href="{{ route('admin.roles.index') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                    </svg>
                    Back to Roles
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Role Information -->
        <div class="space-y-8">
            <!-- Basic Info Card -->
            <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Role Information
                    </h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center mb-6">
                        <div class="flex-shrink-0 h-16 w-16 bg-indigo-100 rounded-full flex items-center justify-center">
                            <svg class="h-8 w-8 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-xl font-semibold text-gray-900">{{ $role->display_name }}</h4>
                            <p class="text-gray-500">{{ $role->name }}</p>
                        </div>
                    </div>
                    
                    <!-- Stats -->
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-blue-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ $role->users->count() }}</div>
                            <div class="text-sm text-blue-800">{{ Str::plural('User', $role->users->count()) }}</div>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-green-600">{{ count($role->permissions ?? []) }}</div>
                            <div class="text-sm text-green-800">{{ Str::plural('Permission', count($role->permissions ?? [])) }}</div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Status:</span>
                            @if($role->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3" />
                                    </svg>
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-red-400" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3" />
                                    </svg>
                                    Inactive
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Description -->
                    @if($role->description)
                        <div class="mb-6">
                            <h5 class="text-sm font-medium text-gray-700 mb-2">Description:</h5>
                            <p class="text-sm text-gray-600">{{ $role->description }}</p>
                        </div>
                    @endif

                    <!-- Timestamps -->
                    <div class="border-t border-gray-200 pt-4 space-y-2 text-sm text-gray-600">
                        <div class="flex justify-between">
                            <span class="font-medium">Created:</span>
                            <span>{{ $role->created_at ? $role->created_at->format('M d, Y \a\t H:i') : 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Last Updated:</span>
                            <span>{{ $role->updated_at ? $role->updated_at->format('M d, Y \a\t H:i') : 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Permissions -->
            <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.623 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                        </svg>
                        Permissions
                    </h3>
                </div>
                <div class="p-6">
                    @if(count($role->permissions ?? []) > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @php
                                $permissionGroups = [
                                    'Dashboard' => ['dashboard.view', 'dashboard.analytics'],
                                    'Users' => ['users.view', 'users.create', 'users.edit', 'users.delete'],
                                    'Roles' => ['roles.view', 'roles.create', 'roles.edit', 'roles.delete'],
                                    'Products' => ['products.view', 'products.create', 'products.edit', 'products.delete', 'products.manage_inventory'],
                                    'Supplier Products' => ['supplier.products.view', 'supplier.products.create', 'supplier.products.edit', 'supplier.products.delete'],
                                    'Orders' => ['orders.view', 'orders.create', 'orders.edit', 'orders.delete', 'orders.manage_status'],
                                    'Customers' => ['customers.view', 'customers.create', 'customers.edit', 'customers.delete'],
                                    'Deliveries' => ['deliveries.view', 'deliveries.create', 'deliveries.edit', 'deliveries.delete'],
                                    'Categories' => ['categories.view', 'categories.create', 'categories.edit', 'categories.delete'],
                                    'Brands' => ['brands.view', 'brands.create', 'brands.edit', 'brands.delete'],
                                    'News' => ['news.view', 'news.create', 'news.edit', 'news.delete'],
                                    'Purchase Orders' => ['purchase_orders.view', 'purchase_orders.create', 'purchase_orders.edit', 'purchase_orders.delete', 'purchase_orders.approve', 'purchase_orders.send_to_supplier', 'purchase_orders.manage_status', 'purchase_orders.view_financials', 'purchase_orders.manage_payments'],
                                    'Suppliers' => ['suppliers.view', 'suppliers.create', 'suppliers.edit', 'suppliers.delete', 'suppliers.manage_contracts', 'suppliers.view_performance'],
                                    'Quotations' => ['quotations.view', 'quotations.create', 'quotations.edit', 'quotations.delete', 'quotations.approve', 'quotations.compare'],
                                    'Procurement' => ['procurement.analytics', 'procurement.reports', 'procurement.budget_tracking'],
                                    'CRM Leads' => ['crm.leads.view', 'crm.leads.create', 'crm.leads.edit', 'crm.leads.delete', 'crm.leads.assign', 'crm.leads.convert', 'crm.leads.export', 'crm.leads.import', 'crm.leads.merge', 'crm.leads.bulk_actions'],
                                    'CRM Deals' => ['crm.deals.view', 'crm.deals.create', 'crm.deals.edit', 'crm.deals.delete', 'crm.deals.assign', 'crm.deals.close', 'crm.deals.export', 'crm.deals.pipeline', 'crm.deals.forecast'],
                                    'CRM Activities' => ['crm.activities.view', 'crm.activities.create', 'crm.activities.edit', 'crm.activities.delete', 'crm.activities.complete', 'crm.activities.schedule', 'crm.activities.timeline'],
                                    'CRM Contacts' => ['crm.contacts.view', 'crm.contacts.create', 'crm.contacts.edit', 'crm.contacts.delete', 'crm.contacts.merge', 'crm.contacts.export', 'crm.contacts.import'],
                                    'CRM Campaigns' => ['crm.campaigns.view', 'crm.campaigns.create', 'crm.campaigns.edit', 'crm.campaigns.delete', 'crm.campaigns.execute', 'crm.campaigns.track'],
                                    'CRM Tasks' => ['crm.tasks.view', 'crm.tasks.create', 'crm.tasks.edit', 'crm.tasks.delete', 'crm.tasks.assign', 'crm.tasks.complete', 'crm.tasks.overdue'],
                                    'CRM Reports' => ['crm.reports.view', 'crm.reports.create', 'crm.reports.export', 'crm.analytics.view', 'crm.analytics.dashboard', 'crm.analytics.performance'],
                                    'CRM Communication' => ['crm.communication.email', 'crm.communication.sms', 'crm.communication.call', 'crm.communication.meeting', 'crm.communication.templates'],
                                    'CRM Integration' => ['crm.integration.webhooks', 'crm.integration.api', 'crm.automation.workflows', 'crm.automation.rules', 'crm.automation.triggers'],
                                    'CRM Administration' => ['crm.admin.settings', 'crm.admin.fields', 'crm.admin.workflows', 'crm.admin.integrations', 'crm.admin.backup', 'crm.admin.restore'],
                                    'Feedback' => ['feedback.view', 'feedback.respond'],
                                ];
                            @endphp

                            @foreach($permissionGroups as $group => $groupPermissions)
                                @php
                                    $hasGroupPermissions = array_intersect($groupPermissions, $role->permissions ?? []);
                                @endphp
                                @if(!empty($hasGroupPermissions))
                                    <div class="border border-gray-200 rounded-lg">
                                        <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 rounded-t-lg">
                                            <h4 class="text-sm font-medium text-gray-900">{{ $group }}</h4>
                                        </div>
                                        <div class="p-4 space-y-2">
                                            @foreach($groupPermissions as $permission)
                                                @if(in_array($permission, $role->permissions ?? []))
                                                    <div class="flex items-center">
                                                        <svg class="h-4 w-4 text-green-500 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        <span class="text-sm text-gray-700">{{ $availablePermissions[$permission] ?? $permission }}</span>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.623 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No Permissions Assigned</h3>
                            <p class="mt-1 text-sm text-gray-500">This role has no permissions assigned yet.</p>
                            <div class="mt-6">
                                <a href="{{ route('admin.roles.edit', $role) }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                    </svg>
                                    Add Permissions
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Assigned Users -->
            <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                        Assigned Users ({{ $role->users->count() }})
                    </h3>
                </div>
                <div class="overflow-hidden">
                    @if($role->users->count() > 0)
                        <div class="divide-y divide-gray-200">
                            @foreach($role->users as $user)
                                <div class="px-6 py-4 hover:bg-gray-50">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                @if($user->avatar)
                                                    <img class="h-10 w-10 rounded-full" src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}">
                                                @else
                                                    <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                                        <span class="text-indigo-600 font-medium">{{ substr($user->name, 0, 1) }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            @if($user->is_active)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Active
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    Inactive
                                                </span>
                                            @endif
                                            <a href="{{ route('admin.users.show', $user) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                                View
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No Users Assigned</h3>
                            <p class="mt-1 text-sm text-gray-500">No users have been assigned to this role yet.</p>
                            <div class="mt-6">
                                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                    </svg>
                                    Manage Users
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.card-hover {
    transition: all 0.2s ease-in-out;
}
.card-hover:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}
</style>
@endpush 