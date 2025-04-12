@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Brands Management</h1>
        <a href="{{ route('admin.brands.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New Brand
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">All Brands</h5>
        </div>
        <div class="card-body">
            @if($brands->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th width="50">ID</th>
                                <th width="80">Logo</th>
                                <th>Name</th>
                                <th width="100">Featured</th>
                                <th width="100">Sort Order</th>
                                <th width="120">Products</th>
                                <th width="150">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($brands as $brand)
                                <tr>
                                    <td>{{ $brand->id }}</td>
                                    <td>
                                        @if($brand->logo_url)
                                            <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}" 
                                                 class="img-thumbnail" style="max-height: 50px;">
                                        @else
                                            <span class="text-muted">No logo</span>
                                        @endif
                                    </td>
                                    <td>{{ $brand->name }}</td>
                                    <td>
                                        @if($brand->is_featured)
                                            <span class="badge bg-success">Yes</span>
                                        @else
                                            <span class="badge bg-secondary">No</span>
                                        @endif
                                    </td>
                                    <td>{{ $brand->sort_order }}</td>
                                    <td>{{ $brand->products()->count() }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.brands.edit', $brand) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.brands.destroy', $brand) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('Are you sure you want to delete this brand?');"
                                                  class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <p class="text-muted mb-0">No brands found. Create your first brand to get started.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 