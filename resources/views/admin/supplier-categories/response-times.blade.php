@extends('admin.layouts.app')

@section('title', 'Supplier Response Times')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.supplier-categories.index') }}">Supplier Categories</a></li>
                    <li class="breadcrumb-item active">Response Times</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0 text-gray-800">Supplier Response Times</h1>
            <p class="text-muted mb-0">Monitor supplier response performance across all categories</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.supplier-categories.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Categories
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Average Response Time
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($suppliers->flatMap->activeSupplierCategories->avg('avg_response_time_hours'), 1) }}h
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Fastest Responders
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $suppliers->where('activeSupplierCategories.0.avg_response_time_hours', '<=', 4)->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bolt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Slow Responders
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $suppliers->where('activeSupplierCategories.0.avg_response_time_hours', '>', 24)->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Suppliers
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $suppliers->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Response Times Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Supplier Response Times by Category</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="responseTimesTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Supplier</th>
                            <th>Email</th>
                            <th>Categories</th>
                            <th>Avg Response Time</th>
                            <th>Performance</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($suppliers as $supplier)
                            @php
                                $avgResponseTime = $supplier->activeSupplierCategories->avg('avg_response_time_hours');
                                $responseTimeClass = $avgResponseTime <= 4 ? 'text-success' : ($avgResponseTime <= 12 ? 'text-warning' : 'text-danger');
                                $performanceClass = $avgResponseTime <= 4 ? 'bg-success' : ($avgResponseTime <= 12 ? 'bg-warning' : 'bg-danger');
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-3">
                                            @if($supplier->profile_photo)
                                                <img src="{{ asset('storage/' . $supplier->profile_photo) }}" 
                                                     class="rounded-circle" width="40" height="40" alt="{{ $supplier->name }}">
                                            @else
                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" 
                                                     style="width: 40px; height: 40px;">
                                                    {{ strtoupper(substr($supplier->name, 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="font-weight-bold">{{ $supplier->name }}</div>
                                            <small class="text-muted">{{ $supplier->supplierInformation->company_name ?? 'No company' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $supplier->email }}</td>
                                <td>
                                    @if($supplier->activeSupplierCategories->count() > 0)
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($supplier->activeSupplierCategories as $assignment)
                                                <span class="badge bg-light text-dark">
                                                    {{ $assignment->category->name }}
                                                    <small class="text-muted">({{ number_format($assignment->avg_response_time_hours, 1) }}h)</small>
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted">No categories assigned</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="font-weight-bold {{ $responseTimeClass }}">
                                        {{ $avgResponseTime ? number_format($avgResponseTime, 1) . 'h' : 'No data' }}
                                    </span>
                                </td>
                                <td>
                                    @if($avgResponseTime)
                                        <span class="badge {{ $performanceClass }} text-white">
                                            @if($avgResponseTime <= 4)
                                                Excellent
                                            @elseif($avgResponseTime <= 12)
                                                Good
                                            @else
                                                Needs Improvement
                                            @endif
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">No Data</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.supplier-categories.show', $supplier) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <a href="{{ route('admin.supplier-categories.edit', $supplier) }}" 
                                           class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Response Time Guidelines -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Response Time Guidelines</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="text-center p-3 border rounded">
                        <div class="text-success mb-2">
                            <i class="fas fa-bolt fa-2x"></i>
                        </div>
                        <h5 class="text-success">Excellent (≤ 4 hours)</h5>
                        <p class="text-muted mb-0">Suppliers who respond within 4 hours</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center p-3 border rounded">
                        <div class="text-warning mb-2">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                        <h5 class="text-warning">Good (4-12 hours)</h5>
                        <p class="text-muted mb-0">Suppliers who respond within 12 hours</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center p-3 border rounded">
                        <div class="text-danger mb-2">
                            <i class="fas fa-hourglass-half fa-2x"></i>
                        </div>
                        <h5 class="text-danger">Needs Improvement (> 12 hours)</h5>
                        <p class="text-muted mb-0">Suppliers who take longer than 12 hours</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#responseTimesTable').DataTable({
        order: [[3, 'asc']], // Sort by response time column
        pageLength: 25,
        language: {
            search: "Search suppliers:",
            lengthMenu: "Show _MENU_ suppliers per page",
            info: "Showing _START_ to _END_ of _TOTAL_ suppliers",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        }
    });
});
</script>
@endpush
@endsection 