@extends('admin.layouts.app')

@section('content')
<div class="main-content">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1">Edit Role: {{ $role->display_name }}</h2>
            <p class="text-muted mb-0">Update role permissions and access controls</p>
        </div>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Roles
        </a>
    </div>

    <!-- Edit Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('admin.roles.update', $role) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Basic Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="display_name" class="form-label">Role Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('display_name') is-invalid @enderror" 
                                       id="display_name" name="display_name" 
                                       value="{{ old('display_name', $role->display_name) }}" 
                                       placeholder="e.g., Content Manager" required>
                                @error('display_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="is_active" class="form-label">Status</label>
                                <select class="form-select @error('is_active') is-invalid @enderror" id="is_active" name="is_active">
                                    <option value="1" {{ old('is_active', $role->is_active) == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('is_active', $role->is_active) == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('is_active')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" 
                                      placeholder="Brief description of this role's responsibilities">{{ old('description', $role->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Permissions Section -->
                        <div class="mb-4">
                            <label class="form-label">Permissions</label>
                            <p class="text-muted small mb-3">Select the permissions this role should have</p>
                            
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
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="card border">
                                            <div class="card-header bg-light py-2">
                                                <h6 class="mb-0">{{ $group }}</h6>
                                            </div>
                                            <div class="card-body py-2">
                                                @foreach($groupPermissions as $permission)
                                                    @if(isset($availablePermissions[$permission]))
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" 
                                                                   id="permission_{{ $permission }}" 
                                                                   name="permissions[]" 
                                                                   value="{{ $permission }}"
                                                                   {{ in_array($permission, old('permissions', $role->permissions ?? [])) ? 'checked' : '' }}>
                                                            <label class="form-check-label small" for="permission_{{ $permission }}">
                                                                {{ $availablePermissions[$permission] }}
                                                            </label>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('admin.roles.index') }}" class="btn btn-light me-3">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Role
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Role Information -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-info-circle text-primary me-2"></i>Role Information
                    </h6>
                    <div class="small">
                        <p><strong>Created:</strong> {{ $role->created_at->format('M d, Y \a\t H:i') }}</p>
                        <p><strong>Last Updated:</strong> {{ $role->updated_at->format('M d, Y \a\t H:i') }}</p>
                        <p><strong>Users Assigned:</strong> {{ $role->users()->count() }}</p>
                        @if($role->users()->count() > 0)
                            <div class="mt-2">
                                <strong>Assigned Users:</strong>
                                <ul class="list-unstyled mt-1">
                                    @foreach($role->users()->limit(5)->get() as $user)
                                        <li class="small text-muted">• {{ $user->name }}</li>
                                    @endforeach
                                    @if($role->users()->count() > 5)
                                        <li class="small text-muted">• and {{ $role->users()->count() - 5 }} more...</li>
                                    @endif
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Help Section -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-shield-alt text-success me-2"></i>Permission Types
                    </h6>
                    <div class="small text-muted">
                        <p><strong>View:</strong> Can see and browse items</p>
                        <p><strong>Create:</strong> Can add new items</p>
                        <p><strong>Edit:</strong> Can modify existing items</p>
                        <p><strong>Delete:</strong> Can remove items</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .form-check {
        margin-bottom: 0.5rem;
    }
    
    .card-header {
        border-bottom: 1px solid rgba(0,0,0,.125);
    }
</style>
@endsection 