@extends('admin.layouts.app')

@section('title', 'Supplier Category Management')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Supplier Category Management</h1>
            <p class="text-muted mb-0">Manage which suppliers supply which product categories</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.supplier-categories.export', request()->query()) }}" 
               class="btn btn-outline-primary">
                <i class="fas fa-download me-1"></i> Export CSV
            </a>
            <a href="{{ route('admin.supplier-categories.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Assign Category
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card border-left-primary h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Assignments
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_assignments'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-link fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-left-success h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Active Assignments
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active_assignments'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-left-warning h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Approval
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending_assignments'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-info h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Suppliers with Assignments
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['suppliers_with_assignments'] }} / {{ $stats['total_suppliers'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Supplier name, email, or category..." 
                           value="{{ request('search') }}">
                </div>
                
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="pending_approval" {{ request('status') == 'pending_approval' ? 'selected' : '' }}>Pending Approval</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Supplier</label>
                    <select name="supplier_id" class="form-select">
                        <option value="">All Suppliers</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Assignments Table -->
    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Supplier Category Assignments</h6>
            <div>
                @if($assignments->isNotEmpty())
                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#bulkActionModal">
                        <i class="fas fa-edit me-1"></i> Bulk Actions
                    </button>
                @endif
            </div>
        </div>
        
        <div class="card-body p-0">
            @if($assignments->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>
                                    <input type="checkbox" id="selectAll" class="form-check-input">
                                </th>
                                <th>Supplier</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Performance</th>
                                <th>Min Order</th>
                                <th>Lead Time</th>
                                <th>Assigned</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assignments as $assignment)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="assignment_ids[]" value="{{ $assignment->id }}" class="form-check-input assignment-checkbox">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <span class="text-white text-xs">{{ strtoupper(substr($assignment->supplier->name, 0, 2)) }}</span>
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $assignment->supplier->name }}</div>
                                                <div class="text-muted small">{{ $assignment->supplier->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-medium">{{ $assignment->category->name }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $assignment->status_badge_class }}">
                                            {{ ucfirst(str_replace('_', ' ', $assignment->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <div class="small">
                                                <span class="text-muted">Score:</span> 
                                                <span class="fw-bold">{{ number_format($assignment->performance_score, 1) }}%</span>
                                            </div>
                                            <div class="small">
                                                <span class="text-muted">Win Rate:</span> 
                                                <span class="text-success">{{ number_format($assignment->quotation_win_rate, 1) }}%</span>
                                            </div>
                                            <div class="small">
                                                <span class="text-muted">Response:</span> 
                                                <span class="text-info">{{ number_format($assignment->avg_response_time_hours, 1) }}h</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($assignment->minimum_order_value)
                                            <span class="text-muted">AED {{ number_format($assignment->minimum_order_value) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($assignment->lead_time_days)
                                            <span class="text-muted">{{ $assignment->lead_time_days }} days</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="small">
                                            <div>{{ $assignment->created_at->format('M j, Y') }}</div>
                                            @if($assignment->assignedBy)
                                                <div class="text-muted">by {{ $assignment->assignedBy->name }}</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                Actions
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.supplier-categories.show', $assignment->supplier_id) }}">
                                                        <i class="fas fa-eye me-2"></i> View Supplier
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.supplier-categories.edit', $assignment) }}">
                                                        <i class="fas fa-edit me-2"></i> Edit Assignment
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('admin.supplier-categories.destroy', $assignment) }}" 
                                                          method="POST" 
                                                          onsubmit="return confirm('Are you sure you want to remove this assignment?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="fas fa-trash me-2"></i> Remove Assignment
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center py-3">
                    {{ $assignments->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-link fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-muted">No supplier category assignments found</h5>
                    <p class="text-muted">Start by assigning categories to suppliers.</p>
                    <a href="{{ route('admin.supplier-categories.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Create First Assignment
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Bulk Action Modal -->
<div class="modal fade" id="bulkActionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.supplier-categories.bulk-status') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Bulk Actions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Update Status</label>
                        <select name="status" class="form-select" required>
                            <option value="">Select Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="pending_approval">Pending Approval</option>
                        </select>
                    </div>
                    <div id="selectedAssignments"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Selected</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select all functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    const assignmentCheckboxes = document.querySelectorAll('.assignment-checkbox');
    
    selectAllCheckbox.addEventListener('change', function() {
        assignmentCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkActionModal();
    });
    
    assignmentCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActionModal);
    });
    
    function updateBulkActionModal() {
        const selectedCheckboxes = document.querySelectorAll('.assignment-checkbox:checked');
        const selectedAssignmentsDiv = document.getElementById('selectedAssignments');
        
        // Clear existing inputs
        selectedAssignmentsDiv.innerHTML = '';
        
        // Add hidden inputs for selected assignments
        selectedCheckboxes.forEach(checkbox => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'assignment_ids[]';
            input.value = checkbox.value;
            selectedAssignmentsDiv.appendChild(input);
        });
        
        // Update select all checkbox state
        selectAllCheckbox.indeterminate = selectedCheckboxes.length > 0 && selectedCheckboxes.length < assignmentCheckboxes.length;
        selectAllCheckbox.checked = selectedCheckboxes.length === assignmentCheckboxes.length;
    }
});
</script>
@endpush
@endsection 