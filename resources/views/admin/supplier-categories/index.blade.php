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

    <!-- Bulk Actions Section -->
    <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Bulk Actions</h3>
            <p class="text-sm text-gray-600 mt-1">Select suppliers and apply category assignments in bulk</p>
        </div>
        <div class="p-6">
            <form id="bulkActionForm" action="{{ route('admin.supplier-categories.bulk-assign') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <label for="bulk_categories" class="block text-sm font-medium text-gray-700 mb-2">Select Categories</label>
                        <select name="category_ids[]" id="bulk_categories" multiple class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="bulk_action" class="block text-sm font-medium text-gray-700 mb-2">Action</label>
                        <select name="action" id="bulk_action" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="assign">Assign Categories</option>
                            <option value="remove">Remove Categories</option>
                        </select>
                    </div>
                    <div class="sm:col-span-2 flex items-end space-x-3">
                        <button type="submit" disabled id="bulkActionBtn" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 disabled:bg-gray-300 disabled:cursor-not-allowed">
                            <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 011.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.56.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.893.149c-.425.07-.765.383-.93.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 01-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.397.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 01-.12-1.45l.527-.737c.25-.35.273-.806.108-1.204-.165-.397-.505-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.107-1.204l-.527-.738a1.125 1.125 0 01.12-1.45l.773-.773a1.125 1.125 0 011.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Apply to Selected
                        </button>
                        <span class="text-sm text-gray-500" id="selectedCount">No suppliers selected</span>
                    </div>
                </div>
            </form>
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
                                <th scope="col" class="px-6 py-3 text-left">
                                    <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </th>
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
                                        <input type="checkbox" name="supplier_ids[]" value="{{ $supplier->id }}" class="supplier-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    </td>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const supplierCheckboxes = document.querySelectorAll('.supplier-checkbox');
    const bulkActionBtn = document.getElementById('bulkActionBtn');
    const selectedCountSpan = document.getElementById('selectedCount');
    const bulkActionForm = document.getElementById('bulkActionForm');

    // Handle select all functionality
    selectAllCheckbox.addEventListener('change', function() {
        supplierCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkActionState();
    });

    // Handle individual checkbox changes
    supplierCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateBulkActionState();
            
            // Update select all checkbox state
            const checkedCount = document.querySelectorAll('.supplier-checkbox:checked').length;
            selectAllCheckbox.checked = checkedCount === supplierCheckboxes.length;
            selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < supplierCheckboxes.length;
        });
    });

    function updateBulkActionState() {
        const checkedBoxes = document.querySelectorAll('.supplier-checkbox:checked');
        const count = checkedBoxes.length;
        
        bulkActionBtn.disabled = count === 0;
        
        if (count === 0) {
            selectedCountSpan.textContent = 'No suppliers selected';
        } else if (count === 1) {
            selectedCountSpan.textContent = '1 supplier selected';
        } else {
            selectedCountSpan.textContent = `${count} suppliers selected`;
        }
    }

    // Handle bulk action form submission
    bulkActionForm.addEventListener('submit', function(e) {
        const checkedBoxes = document.querySelectorAll('.supplier-checkbox:checked');
        const categorySelect = document.getElementById('bulk_categories');
        const actionSelect = document.getElementById('bulk_action');
        
        if (checkedBoxes.length === 0) {
            e.preventDefault();
            alert('Please select at least one supplier.');
            return;
        }
        
        if (categorySelect.selectedOptions.length === 0) {
            e.preventDefault();
            alert('Please select at least one category.');
            return;
        }
        
        const action = actionSelect.value === 'assign' ? 'assign' : 'remove';
        const supplierCount = checkedBoxes.length;
        const categoryCount = categorySelect.selectedOptions.length;
        
        const message = `Are you sure you want to ${action} ${categoryCount} ${categoryCount === 1 ? 'category' : 'categories'} ${action === 'assign' ? 'to' : 'from'} ${supplierCount} ${supplierCount === 1 ? 'supplier' : 'suppliers'}?`;
        
        if (!confirm(message)) {
            e.preventDefault();
        }
    });

    // Initialize state
    updateBulkActionState();
});
</script>
@endpush
@endsection 