@extends('supplier.layouts.app')

@section('title', 'My Product Categories')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">My Product Categories</h1>
            <p class="text-muted mb-0">Categories you're authorized to supply products for</p>
        </div>
        <div>
            <span class="badge bg-primary">{{ $activeCategories->count() }} Active Categories</span>
        </div>
    </div>

    <!-- Performance Overview -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Overall Performance
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($performanceData['overall_score'], 1) }}%
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-success h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Quotations
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $performanceData['total_quotations'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-invoice fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-warning h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Win Rate
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $performanceData['total_quotations'] > 0 ? number_format(($performanceData['won_quotations'] / $performanceData['total_quotations']) * 100, 1) : 0 }}%
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-trophy fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-info h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Avg Response Time
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $performanceData['avg_response_time'] ? number_format($performanceData['avg_response_time'], 1) . 'h' : 'N/A' }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Categories Grid -->
    @if($activeCategories->isNotEmpty())
        <div class="row">
            @foreach($activeCategories as $assignment)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-header bg-gradient-primary text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 text-white">{{ $assignment->category->name }}</h6>
                                <span class="badge badge-light">
                                    {{ number_format($assignment->performance_score, 1) }}%
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Performance Metrics -->
                            <div class="row text-center mb-3">
                                <div class="col-4">
                                    <div class="border-right">
                                        <div class="h6 mb-0 text-primary">{{ $assignment->total_quotations }}</div>
                                        <div class="small text-muted">Total</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="border-right">
                                        <div class="h6 mb-0 text-success">{{ $assignment->won_quotations }}</div>
                                        <div class="small text-muted">Won</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="h6 mb-0 text-warning">{{ number_format($assignment->quotation_win_rate, 1) }}%</div>
                                    <div class="small text-muted">Win Rate</div>
                                </div>
                            </div>

                            <!-- Category Details -->
                            <div class="mb-3">
                                @if($assignment->minimum_order_value)
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Min Order:</span>
                                        <span class="fw-bold">AED {{ number_format($assignment->minimum_order_value) }}</span>
                                    </div>
                                @endif
                                @if($assignment->lead_time_days)
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Lead Time:</span>
                                        <span class="fw-bold">{{ $assignment->lead_time_days }} days</span>
                                    </div>
                                @endif
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Response Time:</span>
                                    <span class="fw-bold">{{ number_format($assignment->avg_response_time_hours, 1) }}h</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Customer Rating:</span>
                                    <span class="fw-bold">{{ number_format($assignment->avg_customer_rating, 1) }}/5</span>
                                </div>
                            </div>

                            <!-- Recent Activity -->
                            @if($assignment->last_quotation_at)
                                <div class="text-center">
                                    <small class="text-muted">
                                        Last quotation: {{ $assignment->last_quotation_at->diffForHumans() }}
                                    </small>
                                </div>
                            @else
                                <div class="text-center">
                                    <small class="text-muted">No quotations yet</small>
                                </div>
                            @endif

                            <!-- Notes -->
                            @if($assignment->notes)
                                <div class="mt-3 pt-3 border-top">
                                    <small class="text-muted">
                                        <strong>Notes:</strong> {{ $assignment->notes }}
                                    </small>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Performance Indicator -->
                        <div class="card-footer bg-transparent">
                            <div class="progress" style="height: 5px;">
                                @php
                                    $score = $assignment->performance_score;
                                    $color = $score >= 80 ? 'success' : ($score >= 60 ? 'warning' : 'danger');
                                @endphp
                                <div class="progress-bar bg-{{ $color }}" 
                                     role="progressbar" 
                                     style="width: {{ $score }}%"
                                     aria-valuenow="{{ $score }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                </div>
                            </div>
                            <div class="text-center mt-1">
                                <small class="text-{{ $color }}">
                                    @if($score >= 80)
                                        Excellent Performance
                                    @elseif($score >= 60)
                                        Good Performance
                                    @else
                                        Needs Improvement
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Performance Tips -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-left-info">
                    <div class="card-body">
                        <h5 class="card-title text-info">
                            <i class="fas fa-lightbulb me-2"></i>Performance Tips
                        </h5>
                        <div class="row">
                            <div class="col-md-4">
                                <h6 class="text-primary">Improve Response Time</h6>
                                <ul class="small text-muted">
                                    <li>Check emails regularly for new quotation requests</li>
                                    <li>Set up email notifications for urgent requests</li>
                                    <li>Respond within 24 hours when possible</li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <h6 class="text-success">Increase Win Rate</h6>
                                <ul class="small text-muted">
                                    <li>Provide competitive pricing</li>
                                    <li>Include detailed product specifications</li>
                                    <li>Offer flexible payment terms</li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <h6 class="text-warning">Customer Satisfaction</h6>
                                <ul class="small text-muted">
                                    <li>Deliver products on time</li>
                                    <li>Provide excellent customer service</li>
                                    <li>Maintain product quality standards</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- No Categories Assigned -->
        <div class="card shadow">
            <div class="card-body text-center py-5">
                <i class="fas fa-tags fa-3x text-gray-300 mb-3"></i>
                <h4 class="text-muted">No Categories Assigned</h4>
                <p class="text-muted mb-4">
                    You haven't been assigned to any product categories yet. 
                    Please contact the admin team to get assigned to categories you can supply.
                </p>
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="card border-left-primary">
                            <div class="card-body">
                                <h6 class="text-primary">How to get assigned to categories:</h6>
                                <ol class="small text-muted mb-0">
                                    <li>Contact MaxMed administration team</li>
                                    <li>Provide information about products you supply</li>
                                    <li>Wait for approval and category assignment</li>
                                    <li>Start receiving quotation requests</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('styles')
<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.bg-gradient-primary {
    background: linear-gradient(45deg, #4e73df, #224abe);
}

.card-header.bg-gradient-primary {
    color: white !important;
}

.card:hover {
    transform: translateY(-2px);
    transition: transform 0.2s ease-in-out;
}
</style>
@endpush
@endsection 