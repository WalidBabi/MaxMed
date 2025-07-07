@extends('admin.layouts.app')

@section('title', 'Supplier Category Management')

@section('content')
<!-- Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Supplier Category Management</h1>
            <p class="text-gray-600 mt-2">Assign and manage product categories for your suppliers</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.supplier-categories.response-times') }}" class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Response Times
            </a>
            <a href="{{ route('admin.supplier-categories.export') }}" class="inline-flex items-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 5.25h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5m-16.5 4.5H7.5" />
                </svg>
                Export CSV
            </a>
        </div>
    </div>
</div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <!-- Total Suppliers -->
        <div class="card-hover rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm font-medium text-blue-100">Total Suppliers</div>
                    <div class="text-3xl font-bold">{{ $suppliers->count() }}</div>
                </div>
                <div class="rounded-full bg-blue-400 bg-opacity-20 p-3">
                    <svg class="h-8 w-8 text-blue-100" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Assigned Suppliers -->
        <div class="card-hover rounded-xl bg-gradient-to-r from-green-500 to-green-600 p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm font-medium text-green-100">Assigned Suppliers</div>
                    <div class="text-3xl font-bold">{{ $suppliers->filter(fn($s) => $s->activeAssignedCategories->count() > 0)->count() }}</div>
                </div>
                <div class="rounded-full bg-green-400 bg-opacity-20 p-3">
                    <svg class="h-8 w-8 text-green-100" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Categories -->
        <div class="card-hover rounded-xl bg-gradient-to-r from-purple-500 to-purple-600 p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm font-medium text-purple-100">Total Categories</div>
                    <div class="text-3xl font-bold">{{ $categories->count() }}</div>
                </div>
                <div class="rounded-full bg-purple-400 bg-opacity-20 p-3">
                    <svg class="h-8 w-8 text-purple-100" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Unassigned Suppliers -->
        <div class="card-hover rounded-xl bg-gradient-to-r from-orange-500 to-orange-600 p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm font-medium text-orange-100">Unassigned Suppliers</div>
                    <div class="text-3xl font-bold">{{ $suppliers->filter(fn($s) => $s->activeAssignedCategories->count() === 0)->count() }}</div>
                </div>
                <div class="rounded-full bg-orange-400 bg-opacity-20 p-3">
                    <svg class="h-8 w-8 text-orange-100" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>



    <!-- Suppliers Table -->
    <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Supplier Category Assignments</h3>
                <div class="text-sm text-gray-500">
                    Showing {{ $suppliers->count() }} suppliers
                </div>
            </div>
        </div>
        
        <div class="overflow-hidden">
            @if($suppliers->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned Categories</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Suggested Categories</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Specializations</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Products</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Assignment</th>
                                <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($suppliers as $supplier)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold text-sm">
                                                    {{ strtoupper(substr($supplier->name, 0, 2)) }}
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $supplier->name }}</div>
                                                @if($supplier->activeAssignedCategories->count() === 0)
                                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        Unassigned
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $supplier->email }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($supplier->activeAssignedCategories->count() > 0)
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($supplier->activeAssignedCategories as $category)
                                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800">
                                                        {{ $category->name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-500">No categories assigned</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($supplier->supplierInformation && $supplier->supplierInformation->suggested_categories)
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($supplier->supplierInformation->suggested_categories as $suggestedCategory)
                                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        {{ $suggestedCategory }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-500">No suggestions</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($supplier->supplierInformation && $supplier->supplierInformation->specializations)
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($supplier->supplierInformation->specializations as $specialization)
                                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-purple-100 text-purple-800">
                                                        {{ $specialization }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-500">No specializations</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-green-100 text-green-800">
                                                {{ $supplier->products()->count() }} products
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @php
                                            $latestAssignment = $supplier->supplierCategories()->where('status', 'active')->latest('assigned_at')->first();
                                        @endphp
                                        @if($latestAssignment && $latestAssignment->assigned_at)
                                            {{ $latestAssignment->assigned_at->format('M d, Y H:i') }}
                                        @else
                                            <span class="text-gray-400">Never</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('admin.supplier-categories.edit', $supplier) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-semibold text-gray-900">No suppliers found</h3>
                    <p class="mt-1 text-sm text-gray-500">No suppliers are currently registered in the system.</p>
                </div>
            @endif
        </div>
    </div>
</div>


@endsection 