@extends('admin.layouts.app')

@section('content')
<div class="main-content">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1">Role Management</h2>
            <p class="text-muted mb-0">Create and manage user roles and permissions</p>
        </div>
        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New Role
        </a>
    </div>

    <!-- Roles Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @if($roles->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Role Name</th>
                                <th>Description</th>
                                <th>Users Count</th>
                                <th>Permissions</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roles as $role)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="icon-shape icon-sm bg-soft-primary text-primary rounded-circle me-3">
                                                <i class="fas fa-user-tag"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $role->display_name }}</h6>
                                                <small class="text-muted">{{ $role->name }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ Str::limit($role->description, 50) ?: 'No description' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-soft-info text-info">{{ $role->users_count }} users</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ count($role->permissions ?? []) }} permissions</small>
                                    </td>
                                    <td>
                                        @if($role->is_active)
                                            <span class="badge bg-soft-success text-success">Active</span>
                                        @else
                                            <span class="badge bg-soft-danger text-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $role->created_at->format('M d, Y') }}</small>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="{{ route('admin.roles.show', $role) }}">
                                                    <i class="fas fa-eye me-2"></i>View
                                                </a></li>
                                                <li><a class="dropdown-item" href="{{ route('admin.roles.edit', $role) }}">
                                                    <i class="fas fa-edit me-2"></i>Edit
                                                </a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger" 
                                                                onclick="return confirm('Are you sure you want to delete this role?')">
                                                            <i class="fas fa-trash me-2"></i>Delete
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
                @if($roles->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted small">
                            Showing {{ $roles->firstItem() }} to {{ $roles->lastItem() }} of {{ $roles->total() }} roles
                        </div>
                        <div class="pagination-wrapper">
                            {{ $roles->links('admin.partials.pagination') }}
                        </div>
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <div class="icon-shape icon-lg bg-soft-primary text-primary rounded-3 mx-auto mb-3">
                        <i class="fas fa-user-tag fa-2x"></i>
                    </div>
                    <h5>No Roles Found</h5>
                    <p class="text-muted mb-4">Get started by creating your first role</p>
                    <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Create Role
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .icon-shape {
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    .icon-shape.icon-sm {
        width: 2rem;
        height: 2rem;
    }
    
    .icon-shape.icon-lg {
        width: 4rem;
        height: 4rem;
    }
    
    .bg-soft-primary { background-color: rgba(79, 70, 229, 0.1) !important; }
    .bg-soft-success { background-color: rgba(16, 185, 129, 0.1) !important; }
    .bg-soft-info { background-color: rgba(59, 130, 246, 0.1) !important; }
    .bg-soft-danger { background-color: rgba(239, 68, 68, 0.1) !important; }
    
    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }
    
    /* Custom Pagination Styling */
    .pagination-wrapper .pagination {
        gap: 0.25rem;
    }
    
    .pagination-wrapper .page-link {
        border: 1px solid #e5e7eb;
        color: #6b7280;
        padding: 0.5rem 0.75rem;
        margin: 0;
        border-radius: 0.375rem !important;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    
    .pagination-wrapper .page-item:not(.active) .page-link:hover {
        background-color: #f9fafb;
        border-color: #d1d5db;
        color: #374151;
        transform: translateY(-1px);
    }
    
    .pagination-wrapper .page-item.active .page-link {
        background-color: #4f46e5;
        border-color: #4f46e5;
        color: white;
        font-weight: 600;
    }
    
    .pagination-wrapper .page-item.disabled .page-link {
        color: #d1d5db;
        background-color: #f9fafb;
        border-color: #e5e7eb;
        cursor: not-allowed;
    }
    
    .pagination-wrapper .page-link i {
        font-size: 0.75rem;
    }
</style>
@endsection 