@extends('admin.layouts.app')

@section('title', 'Profile')

@section('content')
@php
    $userPermissions = $user->getAllPermissions();
    $permissionCount = $userPermissions ? $userPermissions->count() : 0;
@endphp

<!-- Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Your Profile</h1>
            <p class="text-gray-600 mt-2">Manage your account information and settings</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit Profile
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-4 gap-8">
    <!-- Profile Information -->
    <div class="xl:col-span-3">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">Profile Information</h3>
            </div>
            <div class="p-6">
                <div class="flex items-center space-x-6 mb-6">
                    <div class="flex-shrink-0">
                        @if($user->profile_photo)
                            <img class="h-20 w-20 rounded-full object-cover border-4 border-white shadow-lg" src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}">
                        @else
                            <div class="h-20 w-20 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center text-white text-2xl font-bold border-4 border-white shadow-lg">
                                {{ $user->profile_display }}
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <h4 class="text-xl font-bold text-gray-900">{{ $user->name }}</h4>
                        <p class="text-gray-600">{{ $user->email }}</p>
                        <div class="mt-2 flex items-center space-x-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                    <circle cx="4" cy="4" r="3"/>
                                </svg>
                                Active
                            </span>
                            @if($user->role)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if($user->role->name === 'super_admin') bg-red-100 text-red-800
                                @elseif(in_array($user->role->name, ['admin', 'system_admin', 'business_admin'])) bg-purple-100 text-purple-800
                                @elseif(in_array($user->role->name, ['operations_manager', 'sales_manager'])) bg-indigo-100 text-indigo-800
                                @elseif(in_array($user->role->name, ['purchasing_crm_assistant', 'customer_service_manager'])) bg-blue-100 text-blue-800
                                @elseif($user->role->name === 'supplier') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                {{ $user->role->display_name }}
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                No Role Assigned
                            </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <p class="text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ $user->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <p class="text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ $user->email }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Account Status</label>
                        <p class="text-gray-900 bg-gray-50 px-3 py-2 rounded-md">
                            <span class="inline-flex items-center text-green-700">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Active
                            </span>
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Role & Permissions</label>
                        <div class="bg-gray-50 px-3 py-2 rounded-md">
                            @if($user->role)
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-gray-900 font-medium">{{ $user->role->display_name }}</p>
                                        @if($user->role->description)
                                        <p class="text-sm text-gray-600 mt-1">{{ $user->role->description }}</p>
                                        @endif
                                    </div>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                        @if($user->role->name === 'super_admin') bg-red-100 text-red-800
                                        @elseif(in_array($user->role->name, ['admin', 'system_admin', 'business_admin'])) bg-purple-100 text-purple-800
                                        @elseif(in_array($user->role->name, ['operations_manager', 'sales_manager'])) bg-indigo-100 text-indigo-800
                                        @elseif(in_array($user->role->name, ['purchasing_crm_assistant', 'customer_service_manager'])) bg-blue-100 text-blue-800
                                        @elseif($user->role->name === 'supplier') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ $permissionCount }} permissions
                                    </span>
                                </div>
                            @else
                                <p class="text-gray-500 italic">No role assigned</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Role Permissions Details -->
                @if($user->role && $userPermissions && $permissionCount > 0)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Your Permissions & Access</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @php
                            $permissions = $userPermissions;
                            $categories = $permissions->groupBy(function($permission) {
                                $parts = explode('.', $permission->name);
                                return ucfirst(str_replace('_', ' ', $parts[0]));
                            });
                        @endphp

                        @foreach($categories as $category => $categoryPermissions)
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h5 class="font-medium text-gray-900 mb-2 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $category }}
                            </h5>
                            <div class="space-y-1">
                                @foreach($categoryPermissions->take(5) as $permission)
                                <div class="text-sm text-gray-600 flex items-center">
                                    <svg class="w-3 h-3 mr-2 text-green-500" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3"/>
                                    </svg>
                                    {{ ucfirst(str_replace(['.', '_'], [' ', ' '], explode('.', $permission->name)[1] ?? $permission->name)) }}
                                </div>
                                @endforeach
                                @if($categoryPermissions->count() > 5)
                                <div class="text-xs text-gray-500">
                                    +{{ $categoryPermissions->count() - 5 }} more permissions
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-400 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <h6 class="text-sm font-medium text-blue-900">About Your Role</h6>
                                <p class="text-sm text-blue-700 mt-1">
                                    You have been assigned the <strong>{{ $user->role->display_name }}</strong> role with 
                                    <strong>{{ $permissionCount }} permissions</strong>. 
                                    This controls what features and actions you can access in the system.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="xl:col-span-1">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    <a href="{{ route('profile.edit') }}" class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Profile
                    </a>
                    <a href="{{ route('admin.dashboard') }}" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h2a2 2 0 012 2v2H8V5z"></path>
                        </svg>
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 