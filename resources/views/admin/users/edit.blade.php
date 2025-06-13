@extends('admin.layouts.app')

@section('content')
<div class="main-content">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1">Edit User: {{ $user->name }}</h2>
            <p class="text-muted mb-0">Update user information and role assignments</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Users
        </a>
    </div>

    <!-- Edit Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('admin.users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Basic Information -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $user->name) }}" 
                                       placeholder="Enter full name" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $user->email) }}" 
                                       placeholder="Enter email address" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" 
                                       placeholder="Leave blank to keep current password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Only fill if you want to change the password</div>
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation" 
                                       placeholder="Confirm new password">
                            </div>
                        </div>

                        <!-- Role Assignment -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="role_id" class="form-label">Assign Role</label>
                                <select class="form-select @error('role_id') is-invalid @enderror" id="role_id" name="role_id">
                                    <option value="">Select a role (optional)</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                            {{ $role->display_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">User will inherit permissions from the selected role</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Administrative Access</label>
                                <div class="form-check mt-2">
                                    <input class="form-check-input @error('is_admin') is-invalid @enderror" 
                                           type="checkbox" id="is_admin" name="is_admin" value="1"
                                           {{ old('is_admin', $user->is_admin) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_admin">
                                        Super Administrator
                                    </label>
                                    @error('is_admin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Super admins have all permissions regardless of role</div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-light me-3">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- User Information -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-info-circle text-primary me-2"></i>User Information
                    </h6>
                    <div class="small">
                        <p><strong>User ID:</strong> {{ $user->id }}</p>
                        <p><strong>Joined:</strong> {{ $user->created_at->format('M d, Y \a\t H:i') }}</p>
                        <p><strong>Last Updated:</strong> {{ $user->updated_at->format('M d, Y \a\t H:i') }}</p>
                        <p><strong>Current Role:</strong> 
                            @if($user->is_admin)
                                <span class="badge bg-soft-warning text-warning">Super Admin</span>
                            @elseif($user->role)
                                <span class="badge bg-soft-info text-info">{{ $user->role->display_name }}</span>
                            @else
                                <span class="badge bg-soft-secondary text-secondary">No Role</span>
                            @endif
                        </p>
                        @if($user->id === Auth::user()->id)
                            <div class="alert alert-info small mt-3">
                                <i class="fas fa-info-circle me-2"></i>You are editing your own account.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Help Section -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-shield-alt text-success me-2"></i>Security Notes
                    </h6>
                    <div class="small text-muted">
                        <p><strong>Password:</strong> Leave blank to keep current password.</p>
                        <p><strong>Role Changes:</strong> Take effect immediately.</p>
                        <p><strong>Super Admin:</strong> Cannot be restricted by role permissions.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .form-text {
        color: #6c757d;
        font-size: 0.875rem;
    }
    
    .bg-soft-warning { background-color: rgba(245, 158, 11, 0.1) !important; }
    .bg-soft-info { background-color: rgba(59, 130, 246, 0.1) !important; }
    .bg-soft-secondary { background-color: rgba(108, 117, 125, 0.1) !important; }
</style>
@endsection 