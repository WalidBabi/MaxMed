@extends('admin.layouts.app')

@section('content')
<div class="main-content">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1">Create New User</h2>
            <p class="text-muted mb-0">Add a new user to the system</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Users
        </a>
    </div>

    <!-- Create Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('admin.users.store') }}" method="POST">
                        @csrf
                        
                        <!-- Basic Information -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" 
                                       placeholder="Enter full name" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email') }}" 
                                       placeholder="Enter email address" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" 
                                       placeholder="Enter password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation" 
                                       placeholder="Confirm password" required>
                            </div>
                        </div>

                        <!-- Role Assignment -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="role_id" class="form-label">Assign Role</label>
                                <select class="form-select @error('role_id') is-invalid @enderror" id="role_id" name="role_id">
                                    <option value="">Select a role (optional)</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
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
                                           {{ old('is_admin') ? 'checked' : '' }}>
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
                                <i class="fas fa-save me-2"></i>Create User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Help Section -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-info-circle text-primary me-2"></i>User Guidelines
                    </h6>
                    <div class="small text-muted">
                        <p><strong>Email:</strong> Must be unique and will be used for login.</p>
                        <p><strong>Password:</strong> Should be at least 8 characters long.</p>
                        <p><strong>Role:</strong> Determines what the user can access and do.</p>
                        <p><strong>Super Admin:</strong> Has access to everything regardless of role.</p>
                    </div>
                    
                    <hr>
                    
                    <h6 class="card-title">
                        <i class="fas fa-shield-alt text-success me-2"></i>Security Notes
                    </h6>
                    <div class="small text-muted">
                        <p>• Users will receive login credentials via email</p>
                        <p>• Strong passwords are recommended</p>
                        <p>• Regular role reviews ensure proper access control</p>
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
</style>
@endsection 