@extends('admin.layouts.app')

@section('content')
<div class="-mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8">
<div class="main-content">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1">User Details: {{ $user->name }}</h2>
            <p class="text-muted mb-0">View user information and permissions</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                </svg>
                Edit User
            </a>
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                </svg>
                Back to Users
            </a>
        </div>
    </div>

    <div class="row">
        <!-- User Information -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-shape icon-lg bg-soft-primary text-primary rounded-3 me-3">
                            <i class="fas fa-user fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $user->name }}</h5>
                            <p class="text-muted small mb-0">{{ $user->email }}</p>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Status:</strong>
                        @if($user->is_admin)
                            <span class="badge bg-soft-warning text-warning ms-2">Super Admin</span>
                        @elseif($user->role)
                            <span class="badge bg-soft-info text-info ms-2">{{ $user->role->display_name }}</span>
                        @else
                            <span class="badge bg-soft-secondary text-secondary ms-2">No Role Assigned</span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <strong>Account Status:</strong>
                        <span class="badge bg-soft-success text-success ms-2">Active</span>
                    </div>

                    @if($user->id === Auth::user()->id)
                        <div class="alert alert-info small">
                            <i class="fas fa-info-circle me-2"></i>This is your account.
                        </div>
                    @endif

                    <div class="small text-muted">
                        <p><strong>User ID:</strong> {{ $user->id }}</p>
                        <p><strong>Joined:</strong> {{ $user->created_at->format('M d, Y \a\t H:i') }}</p>
                        <p class="mb-0"><strong>Last Updated:</strong> {{ $user->updated_at->format('M d, Y \a\t H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <!-- Role and Permissions -->
            @if($user->role || $user->is_admin)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-shield-alt me-2"></i>Role & Permissions
                        </h6>
                    </div>
                    <div class="card-body">
                        @if($user->is_admin)
                            <div class="text-center py-4">
                                <div class="icon-shape icon-lg bg-soft-warning text-warning rounded-3 mx-auto mb-3">
                                    <i class="fas fa-crown fa-2x"></i>
                                </div>
                                <h6>Super Administrator</h6>
                                <p class="text-muted">This user has unrestricted access to all system features and functions.</p>
                            </div>
                        @elseif($user->role)
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Role:</strong> {{ $user->role->display_name }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Permissions:</strong> {{ count($user->role->permissions ?? []) }} permissions
                                </div>
                            </div>

                            @if($user->role->description)
                                <div class="mb-3">
                                    <strong>Role Description:</strong>
                                    <p class="text-muted small mb-0 mt-2">{{ $user->role->description }}</p>
                                </div>
                            @endif

                            @if(count($user->role->permissions ?? []) > 0)
                                <div class="row">
                                    @php
                                        $availablePermissions = \App\Models\Role::getAvailablePermissions();
                                        $permissionGroups = [
                                            'Dashboard' => ['dashboard.view'],
                                            'Users' => ['users.view', 'users.create', 'users.edit', 'users.delete'],
                                            'Roles' => ['roles.view', 'roles.create', 'roles.edit', 'roles.delete'],
                                            'Products' => ['products.view', 'products.create', 'products.edit', 'products.delete'],
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
                                            $hasGroupPermissions = array_intersect($groupPermissions, $user->role->permissions ?? []);
                                        @endphp
                                        @if(!empty($hasGroupPermissions))
                                            <div class="col-md-6 col-lg-4 mb-3">
                                                <div class="card border">
                                                    <div class="card-header bg-light py-2">
                                                        <h6 class="mb-0">{{ $group }}</h6>
                                                    </div>
                                                    <div class="card-body py-2">
                                                        @foreach($groupPermissions as $permission)
                                                            @if(in_array($permission, $user->role->permissions ?? []))
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
                            @endif
                        @endif
                    </div>
                </div>
            @else
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="text-center py-4">
                            <i class="fas fa-user-times fa-3x text-muted mb-3"></i>
                            <h6>No Role Assigned</h6>
                            <p class="text-muted">This user has no role assigned and therefore has no system permissions.</p>
                            <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                                <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Assign Role
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Activity Summary -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>Activity Summary
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <h6 class="mb-0">{{ $user->orders()->count() }}</h6>
                            <small class="text-muted">Orders Placed</small>
                        </div>
                        <div class="col-md-4">
                            <h6 class="mb-0">{{ $user->created_at->diffForHumans() }}</h6>
                            <small class="text-muted">Member Since</small>
                        </div>
                        <div class="col-md-4">
                            <h6 class="mb-0">{{ $user->updated_at->diffForHumans() }}</h6>
                            <small class="text-muted">Last Updated</small>
                        </div>
                    </div>
                </div>
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
    
    .icon-shape.icon-lg {
        width: 4rem;
        height: 4rem;
    }
    
    .bg-soft-primary { background-color: rgba(79, 70, 229, 0.1) !important; }
    .bg-soft-success { background-color: rgba(16, 185, 129, 0.1) !important; }
    .bg-soft-info { background-color: rgba(59, 130, 246, 0.1) !important; }
    .bg-soft-warning { background-color: rgba(245, 158, 11, 0.1) !important; }
    .bg-soft-secondary { background-color: rgba(108, 117, 125, 0.1) !important; }
    
    .card-header {
        border-bottom: 1px solid rgba(0,0,0,.125);
    }
</style>
@endsection 