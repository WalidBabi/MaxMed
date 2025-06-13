@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h2 class="h4 mb-1">System Feedback</h2>
            <p class="text-muted mb-0">View and manage your feedback submissions</p>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="{{ route('supplier.feedback.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Submit New Feedback
            </a>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Feedback List -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($feedback->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($feedback as $item)
                                <tr>
                                    <td>
                                        <div class="fw-medium">{{ Str::limit($item->title, 50) }}</div>
                                        <div class="text-muted small">{{ Str::limit($item->description, 80) }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ ucfirst(str_replace('_', ' ', $item->type)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $item->priority_badge_class }}">
                                            {{ ucfirst($item->priority) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $item->status_badge_class }}">
                                            {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $item->created_at->format('M d, Y') }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('supplier.feedback.show', $item) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($feedback->hasPages())
                    <div class="d-flex justify-content-center py-3">
                        {{ $feedback->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-comment-dots fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No feedback submitted yet</h5>
                    <p class="text-muted mb-3">Help us improve by sharing your suggestions and feedback</p>
                    <a href="{{ route('supplier.feedback.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> Submit Your First Feedback
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .table th {
        font-weight: 600;
        font-size: 0.875rem;
        color: #6b7280;
        border-bottom: 1px solid #e5e7eb;
    }

    .table td {
        border-bottom: 1px solid #f3f4f6;
        vertical-align: middle;
    }

    .table-hover tbody tr:hover {
        background-color: #f9fafb;
    }
</style>
@endsection 