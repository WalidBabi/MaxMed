@extends('admin.layouts.app')

@section('title', 'Supplier Categories - ' . $supplier->name)

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.supplier-categories.index') }}">Supplier Categories</a></li>
                    <li class="breadcrumb-item active">{{ $supplier->name }}</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0 text-gray-800">{{ $supplier->name }}</h1>
            <p class="text-muted mb-0">{{ $supplier->email }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.supplier-categories.create', ['supplier_id' => $supplier->id]) }}" 
               class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Assign New Category
            </a>
        </div>
    </div>

    <!-- Performance Overview -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card border-left-primary h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Overall Score
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
        <div class="col-md-2">
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
        <div class="col-md-2">
            <div class="card border-left-warning h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Won Quotations
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $performanceData['won_quotations'] }}</div>
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
        <div class="col-md-3">
            <div class="card border-left-secondary h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Customer Rating
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $performanceData['avg_rating'] ? number_format($performanceData['avg_rating'], 1) . '/5' : 'N/A' }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-star fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Assigned Categories -->
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Assigned Categories</h6>
                </div>
                <div class="card-body p-0">
                    @if($supplier->supplierCategories->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Category</th>
                                        <th>Status</th>
                                        <th>Performance</th>
                                        <th>Settings</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($supplier->supplierCategories as $assignment)
                                        <tr>
                                            <td>
                                                <div class="fw-bold">{{ $assignment->category->name }}</div>
                                                @if($assignment->notes)
                                                    <div class="text-muted small">{{ Str::limit($assignment->notes, 60) }}</div>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge {{ $assignment->status_badge_class }}">
                                                    {{ ucfirst(str_replace('_', ' ', $assignment->status)) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column small">
                                                    <div>
                                                        <span class="text-muted">Score:</span> 
                                                        <span class="fw-bold">{{ number_format($assignment->performance_score, 1) }}%</span>
                                                    </div>
                                                    <div>
                                                        <span class="text-muted">Win Rate:</span> 
                                                        <span class="text-success">{{ number_format($assignment->quotation_win_rate, 1) }}%</span>
                                                        <span class="text-muted">({{ $assignment->won_quotations }}/{{ $assignment->total_quotations }})</span>
                                                    </div>
                                                    <div>
                                                        <span class="text-muted">Response:</span> 
                                                        <span class="text-info">{{ number_format($assignment->avg_response_time_hours, 1) }}h</span>
                                                    </div>
                                                    <div>
                                                        <span class="text-muted">Rating:</span> 
                                                        <span class="text-warning">{{ number_format($assignment->avg_customer_rating, 1) }}/5</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="small">
                                                    @if($assignment->minimum_order_value)
                                                        <div><span class="text-muted">Min Order:</span> AED {{ number_format($assignment->minimum_order_value) }}</div>
                                                    @endif
                                                    @if($assignment->lead_time_days)
                                                        <div><span class="text-muted">Lead Time:</span> {{ $assignment->lead_time_days }} days</div>
                                                    @endif
                                                    @if($assignment->commission_rate)
                                                        <div><span class="text-muted">Commission:</span> {{ $assignment->commission_rate }}%</div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                        Actions
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('admin.supplier-categories.edit', $assignment) }}">
                                                                <i class="fas fa-edit me-2"></i> Edit
                                                            </a>
                                                        </li>
                                                        @if($assignment->status === 'inactive')
                                                            <li>
                                                                <form action="{{ route('admin.supplier-categories.update', $assignment) }}" method="POST" style="display: inline;">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <input type="hidden" name="status" value="active">
                                                                    <button type="submit" class="dropdown-item text-success">
                                                                        <i class="fas fa-check me-2"></i> Activate
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        @else
                                                            <li>
                                                                <form action="{{ route('admin.supplier-categories.update', $assignment) }}" method="POST" style="display: inline;">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <input type="hidden" name="status" value="inactive">
                                                                    <button type="submit" class="dropdown-item text-warning">
                                                                        <i class="fas fa-pause me-2"></i> Deactivate
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        @endif
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <form action="{{ route('admin.supplier-categories.destroy', $assignment) }}" 
                                                                  method="POST" 
                                                                  onsubmit="return confirm('Are you sure you want to remove this category assignment?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger">
                                                                    <i class="fas fa-trash me-2"></i> Remove
                                                                </button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-tags fa-3x text-gray-300 mb-3"></i>
                            <h5 class="text-muted">No categories assigned</h5>
                            <p class="text-muted">This supplier hasn't been assigned to any categories yet.</p>
                            <a href="{{ route('admin.supplier-categories.create', ['supplier_id' => $supplier->id]) }}" 
                               class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Assign First Category
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Supplier Info -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Supplier Information</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Name:</strong> {{ $supplier->name }}
                    </div>
                    <div class="mb-3">
                        <strong>Email:</strong> {{ $supplier->email }}
                    </div>
                    <div class="mb-3">
                        <strong>Role:</strong> {{ $supplier->role ? $supplier->role->display_name : 'No Role' }}
                    </div>
                    <div class="mb-3">
                        <strong>Total Products:</strong> {{ $supplier->supplierProducts->count() }}
                    </div>
                    <div class="mb-3">
                        <strong>Member Since:</strong> {{ formatDubaiDate($supplier->created_at, 'M j, Y') }}
                    </div>
                </div>
            </div>

            <!-- Quick Assign -->
            @if($availableCategories->isNotEmpty())
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Quick Assign Category</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.supplier-categories.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="supplier_id" value="{{ $supplier->id }}">
                            
                            <div class="mb-3">
                                <label class="form-label">Category</label>
                                <select name="category_id" class="form-select" required>
                                    <option value="">Select Category</option>
                                    @foreach($availableCategories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select" required>
                                    <option value="active">Active</option>
                                    <option value="pending_approval">Pending Approval</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                <i class="fas fa-plus me-1"></i> Assign Category
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Recent Activity -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Activity</h6>
                </div>
                <div class="card-body">
                    @if($supplier->supplierCategories->where('last_quotation_at', '!=', null)->isNotEmpty())
                        <div class="timeline">
                            @foreach($supplier->supplierCategories->where('last_quotation_at', '!=', null)->sortByDesc('last_quotation_at')->take(5) as $assignment)
                                <div class="timeline-item mb-3">
                                    <div class="timeline-marker bg-primary"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">{{ $assignment->category->name }}</h6>
                                        <p class="text-muted mb-0 small">
                                            Last quotation: {{ $assignment->last_quotation_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center">No recent activity</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 20px;
}

.timeline-item {
    position: relative;
}

.timeline-marker {
    position: absolute;
    left: -25px;
    top: 5px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
}

.timeline::before {
    content: '';
    position: absolute;
    left: -21px;
    top: 0;
    bottom: 0;
    width: 2px;
    background-color: #e3e6f0;
}

.avatar-sm {
    width: 2rem;
    height: 2rem;
}
</style>
@endpush
@endsection 