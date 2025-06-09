@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Customers</h1>
        <a href="{{ route('admin.customers.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New Customer
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Company</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.customers.show', $customer) }}" class="text-decoration-none">
                                        {{ $customer->name }}
                                    </a>
                                </td>
                                <td>{{ $customer->company_name ?? 'N/A' }}</td>
                                <td>{{ $customer->email ?? 'N/A' }}</td>
                                <td>{{ $customer->phone ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ $customer->is_active ? 'success' : 'secondary' }}">
                                        {{ $customer->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this customer?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">No customers found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($customers->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $customers->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
