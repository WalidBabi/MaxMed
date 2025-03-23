@extends('admin.layouts.app')

@section('content')
<style>
    .categories-container {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
    }
    .btn-create-category {
        margin-bottom: 15px;
    }
</style>


<div class="container-fluid py-4 categories-container">
    <h1>Categories</h1>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-create-category">Create Category</a>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Category List</h5>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $category)
                    <tr>
                        <td>{{ $category->name }}</td>
                        <td>
                            <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this category?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 