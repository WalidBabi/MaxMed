@extends('admin.layouts.app')

@section('title', 'User Details - ' . $user->name)

@section('content')
@can('users.view')
<div class="-mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">User Details</h1>
                <p class="text-gray-600 mt-2">View user information and permissions</p>
            </div>
            <div class="flex items-center space-x-3">
                @can('users.edit')
                <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                    </svg>
                    Edit User
                </a>
                @endcan
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                    </svg>
                    Back to Users
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- User Information -->
        <div class="lg:col-span-1">
            <div class="bg-white overflow-hidden shadow-sm ring-1 ring-gray-900/5 rounded-xl">
                <div class="p-6">
                    <div class="flex items-center mb-6">
                        <div class="flex-shrink-0">
                            <div class="h-16 w-16 rounded-full bg-indigo-100 flex items-center justify-center">
                                <svg class="h-8 w-8 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $user->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $user->email }}</p>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Role</dt>
                            <dd class="mt-1">
                                @if($user->isAdmin())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-yellow-400" fill="currentColor" viewBox="0 0 8 8">
                                            <circle cx="4" cy="4" r="3" />
                                        </svg>
                                        Super Admin
                                    </span>
                                @elseif($user->role)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-blue-400" fill="currentColor" viewBox="0 0 8 8">
                                            <circle cx="4" cy="4" r="3" />
                                        </svg>
                                        {{ $user->role->display_name }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-gray-400" fill="currentColor" viewBox="0 0 8 8">
                                            <circle cx="4" cy="4" r="3" />
                                        </svg>
                                        No Role Assigned
                                    </span>
                                @endif
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Account Status</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3" />
                                    </svg>
                                    Active
                                </span>
                            </dd>
                        </div>

                        @if($user->id === Auth::user()->id)
                            <div class="rounded-md bg-blue-50 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-blue-700">This is your account.</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <dl class="space-y-2 text-sm">
                            <div>
                                <dt class="font-medium text-gray-500">User ID</dt>
                                <dd class="text-gray-900">{{ $user->id }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-500">Joined</dt>
                                <dd class="text-gray-900">{{ formatDubaiDate($user->created_at, 'M d, Y \a\t H:i') }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-500">Last Updated</dt>
                                <dd class="text-gray-900">{{ formatDubaiDate($user->updated_at, 'M d, Y \a\t H:i') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-6">
            <!-- Notifications -->
            <div class="bg-white overflow-hidden shadow-sm ring-1 ring-gray-900/5 rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.081a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75V8.967C18 5.674 15.303 3 12.01 3h-.02C8.697 3 6 5.674 6 8.967V9.75a8.967 8.967 0 01-2.31 6.02c1.76.659 3.6 1.095 5.454 1.31m5.713 0a24.255 24.255 0 01-5.713 0m5.713 0a3 3 0 11-5.713 0" />
                        </svg>
                        Notifications
                    </h3>
                    <a href="{{ route('admin.push.manage', ['user_id' => $user->id]) }}" class="text-sm text-indigo-600 hover:text-indigo-800">View subscriptions</a>
                </div>
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm font-medium text-gray-900">Mute all push notifications</div>
                            <div class="text-sm text-gray-500">When enabled, this user will not receive any push notifications.</div>
                        </div>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="muteToggle" class="sr-only" {{ ($user->push_muted ?? false) ? 'checked' : '' }}>
                            <span class="mr-3 text-sm text-gray-700">{{ ($user->push_muted ?? false) ? 'Muted' : 'Enabled' }}</span>
                            <span class="relative">
                                <span class="block w-10 h-6 bg-gray-200 rounded-full shadow-inner"></span>
                                <span class="dot absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition {{ ($user->push_muted ?? false) ? 'translate-x-4' : '' }}"></span>
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Role and Permissions -->
            @if($user->role || $user->isAdmin())
                <div class="bg-white overflow-hidden shadow-sm ring-1 ring-gray-900/5 rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.623 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                            </svg>
                            Role & Permissions
                        </h3>
                    </div>
                    <div class="p-6">
                        @if($user->isAdmin())
                            <div class="text-center py-8">
                                <div class="mx-auto h-16 w-16 rounded-full bg-yellow-100 flex items-center justify-center mb-4">
                                    <svg class="h-8 w-8 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
                                    </svg>
                                </div>
                                <h4 class="text-lg font-semibold text-gray-900">Super Administrator</h4>
                                <p class="text-gray-500 mt-2">This user has unrestricted access to all system features and functions.</p>
                            </div>
                        @elseif($user->role)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Role</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $user->role->display_name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Permissions</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $user->role->permissions()->count() }} permissions</dd>
                                </div>
                            </div>

                            @if($user->role->description)
                                <div class="mb-6">
                                    <dt class="text-sm font-medium text-gray-500">Role Description</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $user->role->description }}</dd>
                                </div>
                            @endif

                            @if($user->role->permissions()->count() > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @php
                                        $permissionGroups = [
                                            'Dashboard' => ['dashboard.view', 'dashboard.analytics', 'dashboard.admin'],
                                            'Users' => ['users.view', 'users.create', 'users.edit', 'users.delete'],
                                            'Roles' => ['roles.view', 'roles.create', 'roles.edit', 'roles.delete'],
                                            'Products' => ['products.view', 'products.create', 'products.edit', 'products.delete'],
                                            'Orders' => ['orders.view', 'orders.create', 'orders.edit', 'orders.delete'],
                                            'Customers' => ['customers.view', 'customers.create', 'customers.edit', 'customers.delete'],
                                            'Deliveries' => ['deliveries.view', 'deliveries.create', 'deliveries.edit', 'deliveries.delete'],
                                            'Categories' => ['categories.view', 'categories.create', 'categories.edit', 'categories.delete'],
                                            'Brands' => ['brands.view', 'brands.create', 'brands.edit', 'brands.delete'],
                                        ];
                                    @endphp

                                    @foreach($permissionGroups as $group => $groupPermissions)
                                        @php
                                            $userPermissions = $user->role->permissions->pluck('name')->toArray();
                                            $hasGroupPermissions = array_intersect($groupPermissions, $userPermissions);
                                        @endphp
                                        @if(!empty($hasGroupPermissions))
                                            <div class="bg-gray-50 rounded-lg p-4">
                                                <h5 class="text-sm font-medium text-gray-900 mb-3">{{ $group }}</h5>
                                                <div class="space-y-2">
                                                    @foreach($groupPermissions as $permission)
                                                        @if(in_array($permission, $userPermissions))
                                                            <div class="flex items-center">
                                                                <svg class="h-4 w-4 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75" />
                                                                </svg>
                                                                <span class="text-xs text-gray-600">{{ ucwords(str_replace(['.', '_'], [' ', ' '], $permission)) }}</span>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm ring-1 ring-gray-900/5 rounded-xl">
                    <div class="p-6">
                        <div class="text-center py-8">
                            <div class="mx-auto h-16 w-16 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                                <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                                </svg>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-900">No Role Assigned</h4>
                            <p class="text-gray-500 mt-2">This user has no role assigned and therefore has no system permissions.</p>
                            @can('users.edit')
                            <div class="mt-6">
                                <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    Assign Role
                                </a>
                            </div>
                            @endcan
                        </div>
                    </div>
                </div>
            @endif

            <!-- Activity Summary -->
            <div class="bg-white overflow-hidden shadow-sm ring-1 ring-gray-900/5 rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                        </svg>
                        Activity Summary
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
                        <div>
                            <div class="text-2xl font-bold text-gray-900">{{ $user->orders()->count() }}</div>
                            <div class="text-sm text-gray-500">Orders Placed</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900">{{ formatDubaiDateForHumans($user->created_at) }}</div>
                            <div class="text-sm text-gray-500">Member Since</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900">{{ formatDubaiDateForHumans($user->updated_at) }}</div>
                            <div class="text-sm text-gray-500">Last Updated</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
document.getElementById('muteToggle')?.addEventListener('change', async (e) => {
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const label = e.target.closest('label').querySelector('span.mr-3');
    try {
        const resp = await fetch("{{ route('admin.users.push-mute', ['userId' => $user->id]) }}", { method: 'PATCH', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' } });
        const data = await resp.json();
        if (!resp.ok) throw new Error(data.error || 'Failed');
        label.textContent = data.push_muted ? 'Muted' : 'Enabled';
        const dot = e.target.closest('label').querySelector('.dot');
        dot.classList.toggle('translate-x-4', data.push_muted);
    } catch (err) {
        alert(err.message);
        e.target.checked = !e.target.checked;
    }
});
</script>
@else
<div class="text-center py-12">
    <div class="mx-auto h-12 w-12 text-gray-400">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
        </svg>
    </div>
    <h3 class="mt-2 text-sm font-medium text-gray-900">Access Denied</h3>
    <p class="mt-1 text-sm text-gray-500">You don't have permission to view users.</p>
</div>
@endcan
@endsection 