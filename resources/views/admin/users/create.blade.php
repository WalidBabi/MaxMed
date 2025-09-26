@extends('admin.layouts.app')

@section('title', 'Create New User')

@section('content')
<div class="-mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Create New User</h1>
                <p class="text-gray-600 mt-2">Add a new user to the system</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Back to Users
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <!-- Main Form -->
        <div class="lg:col-span-2">
            <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-8">
                @csrf
                
                <!-- Basic Information -->
                <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="h-5 w-5 text-indigo-600 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                            Basic Information
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <!-- Full Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Full Name <span class="text-red-500">*</span></label>
                                <div class="mt-2">
                                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                           class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('name') ring-red-500 focus:ring-red-500 @enderror"
                                           placeholder="Enter full name">
                                    @error('name')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Email Address -->
                            <div>
                                <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email Address <span class="text-red-500">*</span></label>
                                <div class="mt-2">
                                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                           class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('email') ring-red-500 focus:ring-red-500 @enderror"
                                           placeholder="Enter email address">
                                    @error('email')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Password -->
                            <div>
                                <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Password <span class="text-red-500">*</span></label>
                                <div class="mt-2">
                                    <input type="password" name="password" id="password" required
                                           class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 @error('password') ring-red-500 focus:ring-red-500 @enderror"
                                           placeholder="Enter password">
                                    @error('password')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium leading-6 text-gray-900">Confirm Password <span class="text-red-500">*</span></label>
                                <div class="mt-2">
                                    <input type="password" name="password_confirmation" id="password_confirmation" required
                                           class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                           placeholder="Confirm password">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Role & Permissions -->
                <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="h-5 w-5 text-purple-600 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.623 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                            </svg>
                            Role & Permissions
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <!-- Role Assignment (Multiple via checkboxes) -->
                            <div>
                                <label class="block text-sm font-medium leading-6 text-gray-900">Assign Roles</label>
                                <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    @foreach($roles as $role)
                                        <label class="inline-flex items-center space-x-2">
                                            <input type="checkbox" name="roles[]" value="{{ $role->id }}" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}>
                                            <span class="text-sm text-gray-700">{{ $role->display_name }} (ID: {{ $role->id }})</span>
                                        </label>
                                    @endforeach
                                </div>
                                <p class="mt-1 text-sm text-gray-500">Select one or more roles. The first selected will be saved to the legacy field for compatibility.</p>
                                @error('roles')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Note: Admin access is now managed through roles -->
                            <div class="flex items-center justify-center">
                                <div class="text-center">
                                    <p class="text-sm text-gray-600">Administrative access is now managed through the role system above.</p>
                                    <p class="text-xs text-gray-500 mt-1">Select "Administrator" role for full admin access.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end gap-x-6 pt-6">
                    <a href="{{ route('admin.users.index') }}" class="text-sm font-semibold leading-6 text-gray-900">Cancel</a>
                    <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z" />
                        </svg>
                        Create User
                    </button>
                </div>
            </form>
        </div>

        <!-- Help Section -->
        <div class="lg:col-span-1">
            <div class="space-y-6">
                <!-- User Guidelines -->
                <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="h-5 w-5 text-blue-600 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                            </svg>
                            User Guidelines
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4 text-sm text-gray-600">
                            <div>
                                <p class="font-medium text-gray-900">Email:</p>
                                <p>Must be unique and will be used for login.</p>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Password:</p>
                                <p>Should be at least 8 characters long.</p>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Role:</p>
                                <p>Determines what the user can access and do.</p>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Super Admin:</p>
                                <p>Has access to everything regardless of role.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Security Notes -->
                <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="h-5 w-5 text-green-600 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.623 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                            </svg>
                            Security Notes
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3 text-sm text-gray-600">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="h-1.5 w-1.5 rounded-full bg-green-600 mt-2"></div>
                                </div>
                                <p class="ml-3">Users will receive login credentials via email</p>
                            </div>
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="h-1.5 w-1.5 rounded-full bg-green-600 mt-2"></div>
                                </div>
                                <p class="ml-3">Strong passwords are recommended</p>
                            </div>
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="h-1.5 w-1.5 rounded-full bg-green-600 mt-2"></div>
                                </div>
                                <p class="ml-3">Regular role reviews ensure proper access control</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 