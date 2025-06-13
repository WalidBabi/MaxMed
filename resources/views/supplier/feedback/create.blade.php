@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h2 class="h4 mb-1">Submit System Feedback</h2>
            <p class="text-muted mb-0">Help us improve by sharing your suggestions and feedback</p>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="{{ route('supplier.feedback.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Back to Feedback
            </a>
        </div>
    </div>

    <!-- Feedback Form -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('supplier.feedback.store') }}" method="POST">
                        @csrf
                        
                        <div class="row g-4">
                            <!-- Feedback Type -->
                            <div class="col-md-6">
                                <label for="type" class="form-label fw-medium">Feedback Type</label>
                                <select class="form-select @error('type') is-invalid @enderror" 
                                        id="type" name="type" required>
                                    <option value="">Select feedback type</option>
                                    <option value="bug_report" {{ old('type') == 'bug_report' ? 'selected' : '' }}>
                                        🐛 Bug Report
                                    </option>
                                    <option value="feature_request" {{ old('type') == 'feature_request' ? 'selected' : '' }}>
                                        ✨ Feature Request
                                    </option>
                                    <option value="improvement" {{ old('type') == 'improvement' ? 'selected' : '' }}>
                                        🚀 Improvement Suggestion
                                    </option>
                                    <option value="general" {{ old('type') == 'general' ? 'selected' : '' }}>
                                        💬 General Feedback
                                    </option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Priority -->
                            <div class="col-md-6">
                                <label for="priority" class="form-label fw-medium">Priority</label>
                                <select class="form-select @error('priority') is-invalid @enderror" 
                                        id="priority" name="priority" required>
                                    <option value="">Select priority</option>
                                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>
                                        🟢 Low
                                    </option>
                                    <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>
                                        🟡 Medium
                                    </option>
                                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>
                                        🔴 High
                                    </option>
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Title -->
                            <div class="col-12">
                                <label for="title" class="form-label fw-medium">Title</label>
                                <input type="text" 
                                       class="form-control @error('title') is-invalid @enderror" 
                                       id="title" 
                                       name="title" 
                                       value="{{ old('title') }}" 
                                       placeholder="Brief summary of your feedback"
                                       maxlength="255" 
                                       required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <label for="description" class="form-label fw-medium">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" 
                                          name="description" 
                                          rows="6" 
                                          placeholder="Please provide detailed information about your feedback..."
                                          maxlength="2000" 
                                          required>{{ old('description') }}</textarea>
                                <div class="form-text">
                                    <span id="char-count">0</span>/2000 characters
                                </div>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary me-3">
                                    <i class="fas fa-paper-plane me-2"></i> Submit Feedback
                                </button>
                                <a href="{{ route('supplier.feedback.index') }}" class="btn btn-outline-secondary">
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Feedback Guidelines -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle text-primary me-2"></i>Feedback Guidelines
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <h6 class="text-primary">Bug Reports</h6>
                            <p class="small text-muted mb-0">
                                Include steps to reproduce the issue, expected vs actual behavior, and any error messages.
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary">Feature Requests</h6>
                            <p class="small text-muted mb-0">
                                Describe the feature you'd like to see and explain how it would benefit your workflow.
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary">Improvements</h6>
                            <p class="small text-muted mb-0">
                                Suggest ways to make existing features better or more user-friendly.
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary">General Feedback</h6>
                            <p class="small text-muted mb-0">
                                Share your overall experience, suggestions, or any other feedback.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('description');
    const charCount = document.getElementById('char-count');
    
    function updateCharCount() {
        const currentLength = textarea.value.length;
        charCount.textContent = currentLength;
        
        if (currentLength > 1800) {
            charCount.style.color = '#dc3545';
        } else if (currentLength > 1500) {
            charCount.style.color = '#fd7e14';
        } else {
            charCount.style.color = '#6c757d';
        }
    }
    
    textarea.addEventListener('input', updateCharCount);
    updateCharCount(); // Initial count
});
</script>
@endsection 