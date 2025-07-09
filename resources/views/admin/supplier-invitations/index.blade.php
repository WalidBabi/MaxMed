@extends('admin.layouts.app')

@section('title', 'Supplier Invitations')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    <div class="sm:flex sm:justify-between sm:items-center mb-8">
        <!-- Left: Title -->
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl font-bold text-gray-900">Supplier Invitations</h1>
            <p class="text-gray-600 mt-2">Manage and track supplier invitations</p>
        </div>

        <!-- Right: Actions -->
        <div>
            <a href="{{ route('admin.supplier-invitations.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Send New Invitation
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 mb-8">
        <div class="p-6">
            <form action="{{ route('admin.supplier-invitations.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                           placeholder="Search by name, email or company...">
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" id="status" 
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>Accepted</option>
                        <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="btn bg-indigo-500 hover:bg-indigo-600 text-white">
                        <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                            <path d="M7 14c-3.86 0-7-3.14-7-7s3.14-7 7-7 7 3.14 7 7-3.14 7-7 7zM7 2C4.243 2 2 4.243 2 7s2.243 5 5 5 5-2.243 5-5-2.243-5-5-5z" />
                            <path d="M15.707 14.293L13.314 11.9a8.019 8.019 0 01-1.414 1.414l2.393 2.393a.997.997 0 001.414 0 .999.999 0 000-1.414z" />
                        </svg>
                        <span class="ml-2">Filter</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 mb-4">
        <div class="p-4">
            <form id="bulkActionForm" action="{{ route('admin.supplier-invitations.bulk-action') }}" method="POST" class="flex items-center justify-between">
                @csrf
                <div class="flex items-center space-x-4">
                    <div class="flex items-center">
                        <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <label for="selectAll" class="ml-2 text-sm font-medium text-gray-700">Select All</label>
                    </div>
                    <div class="flex items-center space-x-2">
                        <select name="action" id="bulkAction" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">Choose Action</option>
                            <option value="resend">Resend Invitations</option>
                            <option value="cancel">Cancel Invitations</option>
                            <option value="delete">Delete Invitations</option>
                        </select>
                        <button type="submit" id="bulkActionBtn" disabled class="inline-flex items-center px-3 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                            Apply
                        </button>
                    </div>
                </div>
                <div class="text-sm text-gray-500">
                    <span id="selectedCount">0</span> invitation(s) selected
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
        <div class="overflow-x-auto">
            <table class="table-auto w-full">
                <thead class="text-xs font-semibold uppercase text-gray-500 bg-gray-50 border-t border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 whitespace-nowrap">
                            <div class="flex items-center">
                                <input type="checkbox" id="selectAllHeader" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </th>
                        <th class="px-6 py-3 whitespace-nowrap">
                            <div class="font-semibold text-left">Name</div>
                        </th>
                        <th class="px-6 py-3 whitespace-nowrap">
                            <div class="font-semibold text-left">Email</div>
                        </th>
                        <th class="px-6 py-3 whitespace-nowrap">
                            <div class="font-semibold text-left">Company</div>
                        </th>
                        <th class="px-6 py-3 whitespace-nowrap">
                            <div class="font-semibold text-left">Status</div>
                        </th>
                        <th class="px-6 py-3 whitespace-nowrap">
                            <div class="font-semibold text-left">Invited By</div>
                        </th>
                        <th class="px-6 py-3 whitespace-nowrap">
                            <div class="font-semibold text-left">Sent At</div>
                        </th>
                        <th class="px-6 py-3 whitespace-nowrap">
                            <div class="font-semibold text-left">Expires</div>
                        </th>
                        <th class="px-6 py-3 whitespace-nowrap">
                            <div class="font-semibold text-right">Actions</div>
                        </th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    @forelse($invitations as $invitation)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <input type="checkbox" name="invitation_ids[]" value="{{ $invitation->id }}" class="invitation-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-medium text-gray-800">{{ $invitation->contact_name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-gray-600">{{ $invitation->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-gray-600">{{ $invitation->company_name ?: '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @switch($invitation->status)
                                @case('pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Pending
                                    </span>
                                    @break
                                @case('accepted')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Accepted
                                    </span>
                                    @break
                                @case('expired')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Expired
                                    </span>
                                    @break
                                @default
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ ucfirst($invitation->status) }}
                                    </span>
                            @endswitch
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-gray-600">{{ $invitation->inviter->name ?? 'System' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-gray-600" title="{{ $invitation->created_at }}">
                                {{ $invitation->created_at->format('M d, Y H:i') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-gray-600" title="{{ $invitation->expires_at }}">
                                {{ $invitation->expires_at->format('M d, Y H:i') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="space-x-1">
                                <a href="{{ route('admin.supplier-invitations.show', $invitation) }}" 
                                   class="btn-sm bg-gray-100 hover:bg-gray-200 text-gray-600">View</a>
                                
                                @if($invitation->status === 'pending')
                                <form action="{{ route('admin.supplier-invitations.resend', $invitation) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-3 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        Resend
                                    </button>
                                </form>
                                
                                <form action="{{ route('admin.supplier-invitations.cancel', $invitation) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-3 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150" 
                                            onclick="return confirm('Are you sure you want to cancel this invitation?')">
                                        Cancel
                                    </button>
                                </form>
                                @endif
                                
                                <form action="{{ route('admin.supplier-invitations.destroy', $invitation) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-3 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150" 
                                            onclick="return confirm('Are you sure you want to delete this invitation? This action cannot be undone.')">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-8 text-center">
                            <div class="text-gray-500">No invitations found.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $invitations->links() }}
    </div>
</div>

@if(session('success'))
<div x-data="{ show: true }"
     x-show="show"
     x-init="setTimeout(() => show = false, 3000)"
     class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div x-data="{ show: true }"
     x-show="show"
     x-init="setTimeout(() => show = false, 3000)"
     class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
    {{ session('error') }}
</div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const selectAllHeaderCheckbox = document.getElementById('selectAllHeader');
    const invitationCheckboxes = document.querySelectorAll('.invitation-checkbox');
    const bulkActionSelect = document.getElementById('bulkAction');
    const bulkActionBtn = document.getElementById('bulkActionBtn');
    const selectedCountSpan = document.getElementById('selectedCount');
    const bulkActionForm = document.getElementById('bulkActionForm');

    // Function to update selected count
    function updateSelectedCount() {
        const checkedBoxes = document.querySelectorAll('.invitation-checkbox:checked');
        const count = checkedBoxes.length;
        selectedCountSpan.textContent = count;
        
        // Enable/disable bulk action button
        bulkActionBtn.disabled = count === 0 || bulkActionSelect.value === '';
        
        // Update select all checkboxes
        const allCheckboxes = document.querySelectorAll('.invitation-checkbox');
        const allChecked = allCheckboxes.length > 0 && allCheckboxes.length === checkedBoxes.length;
        const someChecked = count > 0;
        
        selectAllCheckbox.checked = allChecked;
        selectAllHeaderCheckbox.checked = allChecked;
        selectAllCheckbox.indeterminate = someChecked && !allChecked;
        selectAllHeaderCheckbox.indeterminate = someChecked && !allChecked;
    }

    // Select all functionality
    function setupSelectAll(checkbox, isHeader = false) {
        checkbox.addEventListener('change', function() {
            const isChecked = this.checked;
            invitationCheckboxes.forEach(cb => {
                cb.checked = isChecked;
            });
            updateSelectedCount();
        });
    }

    // Individual checkbox functionality
    invitationCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });

    // Bulk action select change
    bulkActionSelect.addEventListener('change', function() {
        const checkedBoxes = document.querySelectorAll('.invitation-checkbox:checked');
        bulkActionBtn.disabled = checkedBoxes.length === 0 || this.value === '';
    });

    // Form submission confirmation
    bulkActionForm.addEventListener('submit', function(e) {
        const action = bulkActionSelect.value;
        const checkedBoxes = document.querySelectorAll('.invitation-checkbox:checked');
        
        if (checkedBoxes.length === 0) {
            e.preventDefault();
            alert('Please select at least one invitation.');
            return false;
        }
        
        if (!action) {
            e.preventDefault();
            alert('Please select an action.');
            return false;
        }
        
        let confirmMessage = '';
        switch(action) {
            case 'resend':
                confirmMessage = `Are you sure you want to resend ${checkedBoxes.length} invitation(s)?`;
                break;
            case 'cancel':
                confirmMessage = `Are you sure you want to cancel ${checkedBoxes.length} invitation(s)?`;
                break;
            case 'delete':
                confirmMessage = `Are you sure you want to delete ${checkedBoxes.length} invitation(s)? This action cannot be undone.`;
                break;
        }
        
        if (!confirm(confirmMessage)) {
            e.preventDefault();
            return false;
        }
    });

    // Setup select all checkboxes
    setupSelectAll(selectAllCheckbox);
    setupSelectAll(selectAllHeaderCheckbox, true);
    
    // Initial count update
    updateSelectedCount();
});
</script>
@endsection 