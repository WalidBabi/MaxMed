@extends('admin.layouts.app')

@section('title', 'Permission Documentation')

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
                                <span class="ml-4 text-sm font-medium text-gray-500">Permission Documentation</span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h1 class="text-3xl font-bold text-gray-900 mt-2">Permission Documentation</h1>
                <p class="text-gray-600 mt-1">Comprehensive guide to all system permissions and their functions</p>
            </div>
        </div>
    </div>

    <!-- Permission Categories -->
    <div class="space-y-8">
        @foreach($actualCategories as $categoryKey => $categoryName)
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                        @php
                            $categoryIcon = match($categoryKey) {
                                'dashboard' => 'M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.707A1 1 0 013 7V4z',
                                'users' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z',
                                'roles' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                                'products' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                                'crm' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
                                'suppliers' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                                default => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
                            };
                        @endphp
                        <svg class="h-6 w-6 text-indigo-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $categoryIcon }}"></path>
                        </svg>
                        {{ $categoryName }}
                        <span class="ml-2 text-sm font-normal text-gray-500">
                            ({{ isset($permissions[$categoryKey]) ? $permissions[$categoryKey]->count() : 0 }} permissions)
                        </span>
                    </h2>
                </div>
                <div class="p-6">
                    @if(isset($permissions[$categoryKey]) && $permissions[$categoryKey]->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($permissions[$categoryKey] as $permission)
                                @php
                                    $doc = $permissionDocumentation[$permission->name] ?? null;
                                    $securityLevel = $doc['security_level'] ?? 'Basic';
                                    $securityColor = \App\Services\PermissionDocumentationService::getSecurityLevelColor($securityLevel);
                                    $securityBgColor = \App\Services\PermissionDocumentationService::getSecurityLevelBgColor($securityLevel);
                                @endphp
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex items-start justify-between mb-2">
                                        <h3 class="font-medium text-gray-900">{{ $permission->display_name }}</h3>
                                        @if($doc)
                                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $securityBgColor }} {{ $securityColor }}">
                                                {{ $securityLevel }}
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <p class="text-sm text-gray-600 mb-3">{{ $permission->description }}</p>
                                    
                                    @if($doc)
                                        <div class="space-y-2 text-xs">
                                            <div>
                                                <span class="font-medium text-gray-700">Impact:</span>
                                                <span class="text-gray-600">{{ $doc['impact'] }}</span>
                                            </div>
                                            
                                            @if(!empty($doc['related_modules']))
                                                <div>
                                                    <span class="font-medium text-gray-700">Related Modules:</span>
                                                    <span class="text-gray-600">{{ implode(', ', $doc['related_modules']) }}</span>
                                                </div>
                                            @endif
                                            
                                            @if(!empty($doc['examples']))
                                                <div>
                                                    <span class="font-medium text-gray-700">Examples:</span>
                                                    <ul class="text-gray-600 mt-1">
                                                        @foreach(array_slice($doc['examples'], 0, 3) as $example)
                                                            <li class="flex items-center">
                                                                <svg class="w-3 h-3 mr-1 text-green-500" fill="currentColor" viewBox="0 0 8 8">
                                                                    <circle cx="4" cy="4" r="3"/>
                                                                </svg>
                                                                {{ $example }}
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                            
                                            @if(!empty($doc['dependencies']))
                                                <div>
                                                    <span class="font-medium text-gray-700">Dependencies:</span>
                                                    <span class="text-gray-600">{{ implode(', ', $doc['dependencies']) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-500">No permissions found in this category.</p>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

@push('styles')
<style>
.permission-card {
    transition: all 0.2s ease-in-out;
}
.permission-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
</style>
@endpush