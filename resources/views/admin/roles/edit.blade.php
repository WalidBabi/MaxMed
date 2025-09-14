@extends('admin.layouts.app')

@section('title', 'Edit Role: ' . $role->display_name)

@section('content')
<div class="-mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit Role: {{ $role->display_name }}</h1>
                <p class="text-gray-600 mt-2">Update role permissions and access controls</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.roles.show', $role) }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    View Role
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

    <!-- Form -->
    <form action="{{ route('admin.roles.update', $role) }}" method="POST" id="roleForm">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Form -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Basic Information -->
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
                        <div class="space-y-6">
                            <div>
                                <label for="display_name" class="block text-sm font-medium text-gray-700 mb-2">Role Name <span class="text-red-500">*</span></label>
                                <input type="text" id="display_name" name="display_name" 
                                       value="{{ old('display_name', $role->display_name) }}" required
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('display_name') border-red-300 @enderror"
                                       placeholder="e.g., Product Manager">
                                @error('display_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                <textarea id="description" name="description" rows="3"
                                          class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('description') border-red-300 @enderror"
                                          placeholder="Brief description of this role's responsibilities">{{ old('description', $role->description) }}</textarea>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <div class="flex items-center">
                                    <input type="checkbox" id="is_active" name="is_active" value="1" 
                                           {{ old('is_active', $role->is_active) ? 'checked' : '' }}
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <label for="is_active" class="ml-2 block text-sm text-gray-700">
                                        Active Role
                                    </label>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Inactive roles cannot be assigned to users</p>
                            </div>
                        </div>
                    </div>
                </div>

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
                        <p class="text-sm text-gray-600 mb-6">Select the permissions this role should have</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @if($permissions && count($permissions) > 0)
                                @foreach($permissions as $categoryKey => $categoryPermissions)
                                    @if($categoryPermissions->count() > 0)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-3">
                                                <h4 class="text-sm font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $categoryKey)) }}</h4>
                                                <button type="button" onclick="toggleGroupPermissions('{{ $categoryKey }}')" 
                                                class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                                            Select All
                                        </button>
                                    </div>
                                            <div class="space-y-2" data-group="{{ $categoryKey }}">
                                                @foreach($categoryPermissions as $permission)
                                                    <div class="flex items-center">
                                                        <input type="checkbox" id="permission_{{ $permission->id }}" 
                                                               name="permissions[]" value="{{ $permission->id }}"
                                                               {{ ($rolePermissions && $rolePermissions->contains($permission->id)) || in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}
                                                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                                        <label for="permission_{{ $permission->id }}" class="ml-2 text-sm text-gray-700">
                                                            {{ $permission->display_name }}
                                                        </label>
                                                    </div>
                                        @endforeach
                                    </div>
                                </div> 
                                    @endif
                            @endforeach
                            @else
                                <div class="col-span-2 text-center py-8">
                                    <p class="text-gray-500">No permissions available. Please create permissions first.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-8">
                <!-- Actions -->
                <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Actions</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <button type="submit" class="w-full inline-flex justify-center items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                            </svg>
                            Update Role
                        </button>
                        <a href="{{ route('admin.roles.show', $role) }}" class="w-full inline-flex justify-center items-center rounded-md bg-gray-100 px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm hover:bg-gray-200">
                            Cancel
                        </a>
                    </div>
                </div>

                <!-- Role Information -->
                <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="h-5 w-5 text-purple-600 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                            </svg>
                            Role Details
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4 text-sm">
                            <div>
                                <p class="font-medium text-gray-900">Created:</p>
                                <p class="text-gray-600">{{ $role->created_at ? $role->created_at->format('M d, Y \a\t H:i') : 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Last Updated:</p>
                                <p class="text-gray-600">{{ $role->updated_at ? $role->updated_at->format('M d, Y \a\t H:i') : 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Users Assigned:</p>
                                <p class="text-gray-600">{{ $role->users()->count() }} {{ Str::plural('user', $role->users()->count()) }}</p>
                            </div>
                            @if($role->users()->count() > 0)
                                <div>
                                    <p class="font-medium text-gray-900 mb-2">Assigned Users:</p>
                                    <div class="space-y-1">
                                        @foreach($role->users()->limit(5)->get() as $user)
                                            <div class="flex items-center text-gray-600">
                                                <svg class="w-3 h-3 mr-2" fill="currentColor" viewBox="0 0 8 8">
                                                    <circle cx="4" cy="4" r="3" />
                                                </svg>
                                                {{ $user->name }}
                                            </div>
                                        @endforeach
                                        @if($role->users()->count() > 5)
                                            <div class="text-gray-600 text-xs">
                                                and {{ $role->users()->count() - 5 }} more...
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Current Permissions -->
                <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="h-5 w-5 text-blue-600 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.623 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                            </svg>
                            Current Permissions
                        </h3>
                    </div>
                    <div class="p-6">
                        @php
                            $currentPermissions = $rolePermissions ? $rolePermissions->pluck('name')->toArray() : [];
                        @endphp
                        @if(count($currentPermissions) > 0)
                            <div class="space-y-2">
                                @foreach($currentPermissions as $permission)
                                    @if(isset($availablePermissions[$permission]))
                                        <div class="flex items-center text-sm">
                                            <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3" />
                                            </svg>
                                            <span class="text-gray-700">{{ $availablePermissions[$permission] }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <p class="text-xs text-gray-500">
                                    <strong>{{ count($currentPermissions) }}</strong> permission{{ count($currentPermissions) !== 1 ? 's' : '' }} assigned
                                </p>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                </svg>
                                <p class="mt-2 text-sm text-gray-500">No permissions assigned</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Permission Types -->
                <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="h-5 w-5 text-green-600 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.623 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                            </svg>
                            Permission Types
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3 text-sm text-gray-600">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">View</p>
                                    <p class="text-xs">Can see and browse items</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">Create</p>
                                    <p class="text-xs">Can add new items</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">Edit</p>
                                    <p class="text-xs">Can modify existing items</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">Delete</p>
                                    <p class="text-xs">Can remove items</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
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

@push('scripts')
<script>
function toggleGroupPermissions(group) {
    const groupDiv = document.querySelector(`[data-group="${group}"]`);
    const checkboxes = groupDiv.querySelectorAll('input[type="checkbox"]');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = !allChecked;
    });
    
    const button = groupDiv.parentElement.querySelector('button');
    button.textContent = allChecked ? 'Select All' : 'Deselect All';
}

// Update button text based on current state
document.addEventListener('DOMContentLoaded', function() {
    const groups = document.querySelectorAll('[data-group]');
    groups.forEach(group => {
        const groupName = group.getAttribute('data-group');
        const checkboxes = group.querySelectorAll('input[type="checkbox"]');
        const button = group.parentElement.querySelector('button');
        
        function updateButtonText() {
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            button.textContent = allChecked ? 'Deselect All' : 'Select All';
        }
        
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateButtonText);
        });
        
        updateButtonText();
    });
});
</script>
@endpush 