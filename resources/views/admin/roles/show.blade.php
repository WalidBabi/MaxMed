@extends('admin.layouts.app')

@section('content')
<div class="main-content">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1">Role Details: {{ $role->display_name }}</h2>
            <p class="text-muted mb-0">View role permissions and assigned users</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>Edit Role
            </a>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Roles
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Role Information -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-shape icon-lg bg-soft-primary text-primary rounded-3 me-3">
                            <i class="fas fa-user-tag fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $role->display_name }}</h5>
                            <p class="text-muted small mb-0">{{ $role->name }}</p>
                        </div>
                    </div>
                    
                    <div class="row text-center mb-3">
                        <div class="col-6">
                            <div class="border-end">
                                <h6 class="mb-0">{{ $role->users->count() }}</h6>
                                <small class="text-muted">Users</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h6 class="mb-0">{{ count($role->permissions ?? []) }}</h6>
                            <small class="text-muted">Permissions</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong>Status:</strong>
                        @if($role->is_active)
                            <span class="badge bg-soft-success text-success ms-2">Active</span>
                        @else
                            <span class="badge bg-soft-danger text-danger ms-2">Inactive</span>
                        @endif
                    </div>

                    @if($role->description)
                        <div class="mb-3">
                            <strong>Description:</strong>
                            <p class="text-muted small mb-0 mt-2">{{ $role->description }}</p>
                        </div>
                    @endif

                    <div class="small text-muted">
                        <p><strong>Created:</strong> {{ $role->created_at->format('M d, Y \a\t H:i') }}</p>
                        <p class="mb-0"><strong>Last Updated:</strong> {{ $role->updated_at->format('M d, Y \a\t H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <!-- Permissions -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-shield-alt me-2"></i>Permissions
                    </h6>
                </div>
                <div class="card-body">
                    @if(count($role->permissions ?? []) > 0)
                        <div class="row">
                            @php
                                $permissionGroups = [
                                    'Dashboard' => ['dashboard.view'],
                                    'Users' => ['users.view', 'users.create', 'users.edit', 'users.delete'],
                                    'Roles' => ['roles.view', 'roles.create', 'roles.edit', 'roles.delete'],
                                    'Products' => ['products.view', 'products.create', 'products.edit', 'products.delete'],
                                    'Supplier Products' => ['supplier.products.view', 'supplier.products.create', 'supplier.products.edit', 'supplier.products.delete'],
                                    'Orders' => ['orders.view', 'orders.create', 'orders.edit', 'orders.delete'],
                                    'Customers' => ['customers.view', 'customers.create', 'customers.edit', 'customers.delete'],
                                    'Deliveries' => ['deliveries.view', 'deliveries.create', 'deliveries.edit', 'deliveries.delete'],
                                    'Categories' => ['categories.view', 'categories.create', 'categories.edit', 'categories.delete'],
                                    'Brands' => ['brands.view', 'brands.create', 'brands.edit', 'brands.delete'],
                                    'News' => ['news.view', 'news.create', 'news.edit', 'news.delete'],
                                ];
                            @endphp

                            @foreach($permissionGroups as $group => $groupPermissions)
                                @php
                                    $hasGroupPermissions = array_intersect($groupPermissions, $role->permissions ?? []);
                                @endphp
                                @if(!empty($hasGroupPermissions))
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="card border">
                                            <div class="card-header bg-light py-2">
                                                <h6 class="mb-0">{{ $group }}</h6>
                                            </div>
                                            <div class="card-body py-2">
                                                @foreach($groupPermissions as $permission)
                                                    @if(in_array($permission, $role->permissions ?? []))
                                                        <div class="d-flex align-items-center mb-1">
                                                            <i class="fas fa-check text-success me-2"></i>
                                                            <span class="small">{{ $availablePermissions[$permission] ?? $permission }}</span>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-shield-alt fa-3x text-muted mb-3"></i>
                            <h6>No Permissions Assigned</h6>
                            <p class="text-muted">This role has no permissions assigned yet.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Assigned Users -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-users me-2"></i>Assigned Users ({{ $role->users->count() }})
                    </h6>
                </div>
                <div class="card-body">
                    @if($role->users->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Joined</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($role->users as $user)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="icon-shape icon-sm bg-soft-primary text-primary rounded-circle me-2">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                    {{ $user->name }}
                                                </div>
                                            </td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                @if($user->is_admin)
                                                    <span class="badge bg-soft-warning text-warning">Super Admin</span>
                                                @else
                                                    <span class="badge bg-soft-info text-info">User</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $user->created_at->format('M d, Y') }}</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h6>No Users Assigned</h6>
                            <p class="text-muted">No users have been assigned to this role yet.</p>
                        </div>
                    @endif
                </div>
            </div>
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
    .bg-soft-danger { background-color: rgba(239, 68, 68, 0.1) !important; }
    
    .card-header {
        border-bottom: 1px solid rgba(0,0,0,.125);
    }
</style>
@endsection 