@extends('admin.layouts.app')

@section('content')
<div class="main-content">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1">User Management</h2>
            <p class="text-muted mb-0">Manage system users and their roles</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New User
        </a>
    </div>

    <!-- Search Section -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" 
                               class="form-control border-start-0" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Search by name or email...">
                    </div>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="role">
                        <option value="">All Roles</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Super Admin</option>
                        @if(isset($roles))
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ request('role') == $role->id ? 'selected' : '' }}>
                                    {{ $role->display_name }}
                                </option>
                            @endforeach
                        @endif
                        <option value="no_role" {{ request('role') == 'no_role' ? 'selected' : '' }}>No Role</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="status">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="sort">
                        <option value="created_desc" {{ request('sort') == 'created_desc' ? 'selected' : '' }}>Newest First</option>
                        <option value="created_asc" {{ request('sort') == 'created_asc' ? 'selected' : '' }}>Oldest First</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name A-Z</option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name Z-A</option>
                        <option value="email_asc" {{ request('sort') == 'email_asc' ? 'selected' : '' }}>Email A-Z</option>
                        <option value="email_desc" {{ request('sort') == 'email_desc' ? 'selected' : '' }}>Email Z-A</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="fas fa-search me-1"></i>Search
                        </button>
                        @if(request()->hasAny(['search', 'role', 'status', 'sort']))
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @if($users->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>User</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Joined</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="icon-shape icon-sm bg-soft-primary text-primary rounded-circle me-3">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $user->name }}</h6>
                                                @if($user->id === Auth::user()->id)
                                                    <small class="text-muted">(You)</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if($user->is_admin)
                                            <span class="badge bg-soft-warning text-warning">Super Admin</span>
                                        @elseif($user->role)
                                            <span class="badge bg-soft-info text-info">{{ $user->role->display_name }}</span>
                                        @else
                                            <span class="badge bg-soft-secondary text-secondary">No Role</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-soft-success text-success">Active</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $user->created_at->format('M d, Y') }}</small>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="{{ route('admin.users.show', $user) }}">
                                                    <i class="fas fa-eye me-2"></i>View
                                                </a></li>
                                                <li><a class="dropdown-item" href="{{ route('admin.users.edit', $user) }}">
                                                    <i class="fas fa-edit me-2"></i>Edit
                                                </a></li>
                                                @if($user->id !== Auth::user()->id)
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger" 
                                                                    onclick="return confirm('Are you sure you want to delete this user?')">
                                                                <i class="fas fa-trash me-2"></i>Delete
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($users->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted small">
                            Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} users
                        </div>
                        <div class="pagination-wrapper">
                            {{ $users->links('admin.partials.pagination') }}
                        </div>
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <div class="icon-shape icon-lg bg-soft-primary text-primary rounded-3 mx-auto mb-3">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                    <h5>No Users Found</h5>
                    <p class="text-muted mb-4">Get started by creating your first user</p>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Create User
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
    .bg-soft-warning { background-color: rgba(245, 158, 11, 0.1) !important; }
    .bg-soft-secondary { background-color: rgba(108, 117, 125, 0.1) !important; }
    
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
    
    /* Pagination info styling */
    .pagination-info {
        color: #6b7280;
        font-size: 0.875rem;
        font-weight: 500;
    }
    
    /* Search Form Styling */
    .input-group-text {
        background-color: #f8f9fa !important;
        border-color: #e9ecef;
    }
    
    .form-control:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.25);
    }
    
    .form-select:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.25);
    }
    
    .btn-outline-secondary {
        border-color: #e5e7eb;
        color: #6b7280;
    }
    
    .btn-outline-secondary:hover {
        background-color: #f3f4f6;
        border-color: #d1d5db;
        color: #374151;
    }
</style>
@endsection 