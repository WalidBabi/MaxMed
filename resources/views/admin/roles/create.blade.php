@extends('admin.layouts.app')

@section('title', 'Create Role')

@section('content')
<div class="-mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <nav class="flex" aria-label="Breadcrumb">
                    <ol role="list" class="flex items-center space-x-4">
                        <li>
                            <div class="flex items-center">
                                <a href="{{ route('admin.roles.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">
                                    Role Management
                                </a>
            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="flex-shrink-0 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                    </svg>
                                <span class="ml-4 text-sm font-medium text-gray-500">Create Role</span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h1 class="text-3xl font-bold text-gray-900 mt-2">Create New Role</h1>
                <p class="text-gray-600 mt-1">Define a new role with specific permissions for your team</p>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.roles.store') }}" method="POST" class="space-y-8">
        @csrf
        
                <!-- Basic Information -->
        <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-xl">
            <div class="px-4 py-6 sm:p-8">
                <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                    <div class="sm:col-span-4">
                        <label for="display_name" class="block text-sm font-medium leading-6 text-gray-900">Role Name</label>
                        <div class="mt-2">
                            <input type="text" name="display_name" id="display_name" value="{{ old('display_name') }}" 
                                   class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" 
                                   placeholder="e.g., Sales Manager" required>
                    </div>
                                @error('display_name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                    <div class="sm:col-span-2">
                        <label for="is_active" class="block text-sm font-medium leading-6 text-gray-900">Status</label>
                        <div class="mt-2">
                            <select name="is_active" id="is_active" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                            </div>

                    <div class="col-span-full">
                        <label for="description" class="block text-sm font-medium leading-6 text-gray-900">Description</label>
                        <div class="mt-2">
                            <textarea rows="3" name="description" id="description" 
                                      class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" 
                                      placeholder="Describe what this role is responsible for...">{{ old('description') }}</textarea>
                        </div>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                        </div>
                    </div>
                </div>

                <!-- Permissions -->
        <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-xl">
            <div class="px-4 py-6 sm:p-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Permissions</h3>
                        <p class="text-sm text-gray-600">Select the permissions this role should have</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button type="button" id="select-all" class="text-sm text-indigo-600 hover:text-indigo-500 font-medium">
                            Select All
                        </button>
                        <button type="button" id="deselect-all" class="text-sm text-gray-600 hover:text-gray-500 font-medium">
                            Deselect All
                        </button>
                    </div>
                </div>

                @if(isset($permissions) && $permissions->count() > 0)
                    <div class="space-y-6">
                        @foreach($permissionCategories as $categoryKey => $categoryName)
                            @if(isset($permissions[$categoryKey]))
                                <div class="border rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="text-md font-medium text-gray-900">{{ $categoryName }}</h4>
                                        <div class="flex items-center space-x-2">
                                            <button type="button" class="category-select-all text-xs text-indigo-600 hover:text-indigo-500" data-category="{{ $categoryKey }}">
                                            Select All
                                        </button>
                                            <button type="button" class="category-deselect-all text-xs text-gray-600 hover:text-gray-500" data-category="{{ $categoryKey }}">
                                                Deselect All
                                            </button>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                        @foreach($permissions[$categoryKey] as $permission)
                                            <label class="relative flex items-start py-2 px-3 rounded-md hover:bg-gray-50 cursor-pointer">
                                                <div class="flex items-center h-6">
                                                    <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" 
                                                           class="permission-checkbox category-{{ $categoryKey }} h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                                                           {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                                </div>
                                                <div class="ml-3 text-sm leading-6">
                                                    <div class="font-medium text-gray-900">{{ $permission->display_name }}</div>
                                                    @if($permission->description)
                                                        <div class="text-gray-500 text-xs">{{ $permission->description }}</div>
                                            @endif
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <p class="text-gray-500">No permissions available. Please create permissions first.</p>
                    </div>
                @endif

                @error('permissions')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
                    </div>
                </div>

        <!-- Actions -->
        <div class="flex items-center justify-end gap-x-6">
            <a href="{{ route('admin.roles.index') }}" class="text-sm font-semibold leading-6 text-gray-900">
                Cancel
            </a>
            <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                Create Role
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select/Deselect all permissions
    document.getElementById('select-all').addEventListener('click', function() {
        document.querySelectorAll('.permission-checkbox').forEach(function(checkbox) {
            checkbox.checked = true;
        });
    });

    document.getElementById('deselect-all').addEventListener('click', function() {
        document.querySelectorAll('.permission-checkbox').forEach(function(checkbox) {
            checkbox.checked = false;
        });
    });

    // Category-specific select/deselect
    document.querySelectorAll('.category-select-all').forEach(function(button) {
        button.addEventListener('click', function() {
            const category = this.dataset.category;
            document.querySelectorAll(`.category-${category}`).forEach(function(checkbox) {
                checkbox.checked = true;
            });
        });
    });

    document.querySelectorAll('.category-deselect-all').forEach(function(button) {
        button.addEventListener('click', function() {
            const category = this.dataset.category;
            document.querySelectorAll(`.category-${category}`).forEach(function(checkbox) {
                checkbox.checked = false;
            });
        });
    });
});
</script>
@endpush 
@endsection