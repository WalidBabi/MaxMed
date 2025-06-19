@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h2 class="h4 mb-1">Feedback Details</h2>
            <p class="text-muted mb-0">View feedback submission and admin response</p>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="{{ route('supplier.feedback.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Back to Feedback
            </a>
        </div>
    </div>

    <!-- Feedback Details -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $feedback->title }}</h5>
                    <div class="d-flex gap-2">
                        <span class="badge bg-secondary">
                            {{ ucfirst(str_replace('_', ' ', $feedback->type)) }}
                        </span>
                        <span class="badge {{ $feedback->priority_badge_class }}">
                            {{ ucfirst($feedback->priority) }}
                        </span>
                        <span class="badge {{ $feedback->status_badge_class }}">
                            {{ ucfirst(str_replace('_', ' ', $feedback->status)) }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Feedback Meta Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center text-muted">
                                <i class="fas fa-calendar-alt me-2"></i>
                                <span>Submitted on {{ formatDubaiDate($feedback->created_at, 'F j, Y \a\t g:i A') }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center text-muted">
                                <i class="fas fa-user me-2"></i>
                                <span>By {{ $feedback->user->name }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Feedback Description -->
                    <div class="mb-4">
                        <h6 class="text-primary mb-2">Description</h6>
                        <div class="bg-light p-3 rounded">
                            <p class="mb-0" style="white-space: pre-wrap;">{{ $feedback->description }}</p>
                        </div>
                    </div>

                    <!-- Admin Response -->
                    @if($feedback->admin_response)
                        <div class="mb-4">
                            <h6 class="text-primary mb-2">
                                <i class="fas fa-reply me-2"></i>Admin Response
                            </h6>
                            <div class="bg-primary bg-opacity-10 p-3 rounded border-start border-primary border-4">
                                <p class="mb-0" style="white-space: pre-wrap;">{{ $feedback->admin_response }}</p>
                            </div>
                        </div>
                    @else
                        <div class="mb-4">
                            <h6 class="text-muted mb-2">
                                <i class="fas fa-clock me-2"></i>Admin Response
                            </h6>
                            <div class="bg-light p-3 rounded text-center">
                                <i class="fas fa-hourglass-half text-muted mb-2"></i>
                                <p class="text-muted mb-0">
                                    @if($feedback->status === 'pending')
                                        Waiting for admin review. We'll respond as soon as possible.
                                    @elseif($feedback->status === 'in_progress')
                                        Your feedback is being reviewed. We'll provide a response soon.
                                    @else
                                        No response available yet.
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endif

                    <!-- Status Timeline -->
                    <div class="mb-4">
                        <h6 class="text-primary mb-3">Status Timeline</h6>
                        <div class="timeline">
                            <div class="timeline-item {{ $feedback->status === 'pending' ? 'active' : 'completed' }}">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Submitted</h6>
                                    <p class="text-muted small mb-0">{{ formatDubaiDate($feedback->created_at, 'M j, Y g:i A') }}</p>
                                </div>
                            </div>
                            
                            <div class="timeline-item {{ $feedback->status === 'in_progress' ? 'active' : ($feedback->status === 'completed' || $feedback->status === 'rejected' ? 'completed' : '') }}">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Under Review</h6>
                                    <p class="text-muted small mb-0">
                                        @if($feedback->status !== 'pending')
                                            {{ formatDubaiDate($feedback->updated_at, 'M j, Y g:i A') }}
                                        @else
                                            Pending review
                                        @endif
                                    </p>
                                </div>
                            </div>
                            
                            <div class="timeline-item {{ $feedback->status === 'completed' || $feedback->status === 'rejected' ? 'completed' : '' }}">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">
                                        @if($feedback->status === 'completed')
                                            Completed
                                        @elseif($feedback->status === 'rejected')
                                            Closed
                                        @else
                                            Response
                                        @endif
                                    </h6>
                                    <p class="text-muted small mb-0">
                                        @if($feedback->status === 'completed' || $feedback->status === 'rejected')
                                            {{ formatDubaiDate($feedback->updated_at, 'M j, Y g:i A') }}
                                        @else
                                            Pending completion
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Actions -->
            @if($feedback->status === 'pending' || $feedback->status === 'in_progress')
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-2">Need to add more information?</h6>
                        <p class="text-muted small mb-3">
                            If you have additional details or updates to share about this feedback, 
                            you can submit a new feedback referencing this one.
                        </p>
                        <a href="{{ route('supplier.feedback.create') }}" class="btn btn-outline-primary">
                            <i class="fas fa-plus me-2"></i> Submit Additional Feedback
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .timeline {
        position: relative;
        padding-left: 30px;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e9ecef;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 30px;
    }

    .timeline-item:last-child {
        margin-bottom: 0;
    }

    .timeline-marker {
        position: absolute;
        left: -23px;
        top: 5px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: #e9ecef;
        border: 3px solid #fff;
        box-shadow: 0 0 0 2px #e9ecef;
    }

    .timeline-item.active .timeline-marker {
        background: #ffc107;
        box-shadow: 0 0 0 2px #ffc107;
    }

    .timeline-item.completed .timeline-marker {
        background: #198754;
        box-shadow: 0 0 0 2px #198754;
    }

    .timeline-content h6 {
        color: #6c757d;
        font-size: 0.9rem;
    }

    .timeline-item.active .timeline-content h6,
    .timeline-item.completed .timeline-content h6 {
        color: #212529;
        font-weight: 600;
    }
</style>
@endsection 