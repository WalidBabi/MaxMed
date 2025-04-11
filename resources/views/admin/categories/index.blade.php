@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="page-header mb-4">
        <h1 class="page-title">Categories</h1>
        <p class="page-description text-muted">Manage product categories and subcategories</p>
    </div>

    <div class="d-flex justify-content-end mb-4">
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Create Category
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Category List</h5>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Level</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $topCategories = \App\Models\Category::whereNull('parent_id')->with('subcategories.subcategories')->get();
                    @endphp
                    
                    @foreach($topCategories as $category)
                    <tr>
                        <td><strong>{{ $category->name }}</strong></td>
                        <td><span class="level-indicator">Level 1</span></td>
                        <td>
                            <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                            <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" style="display:inline;">
                                @csrf 
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this category?')">
                                    <i class="fas fa-trash me-1"></i>Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    
                    @foreach($category->subcategories as $subcategory)
                    <tr class="subcategory-row">
                        <td>
                            <i class="fas fa-angle-right me-2 text-primary"></i>
                            {{ $subcategory->name }}
                        </td>
                        <td><span class="level-indicator">Level 2</span></td>
                        <td>
                            <a href="{{ route('admin.categories.edit', $subcategory->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                            <form action="{{ route('admin.categories.destroy', $subcategory->id) }}" method="POST" style="display:inline;">
                                @csrf 
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this subcategory?')">
                                    <i class="fas fa-trash me-1"></i>Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    
                    @foreach($subcategory->subcategories as $subsubcategory)
                    <tr class="subsubcategory-row">
                        <td>
                            <i class="fas fa-angle-double-right me-2 text-primary"></i>
                            {{ $subsubcategory->name }}
                        </td>
                        <td><span class="level-indicator">Level 3</span></td>
                        <td>
                            <a href="{{ route('admin.categories.edit', $subsubcategory->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                            <form action="{{ route('admin.categories.destroy', $subsubcategory->id) }}" method="POST" style="display:inline;">
                                @csrf 
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this sub-subcategory?')">
                                    <i class="fas fa-trash me-1"></i>Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                    
                    @endforeach
                    
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .level-indicator {
        color: #ffffff;
        font-size: 0.7rem;
        display: inline-block;
        padding: 2px 8px;
        background: var(--main-color);
        border-radius: 20px;
        margin-right: 5px;
    }
    
    .subcategory-row {
        background-color: rgba(0, 0, 0, 0.02);
    }
    
    .subcategory-row td:first-child {
        padding-left: 2rem;
    }
    
    .subsubcategory-row {
        background-color: rgba(0, 0, 0, 0.04);
    }
    
    .subsubcategory-row td:first-child {
        padding-left: 3.5rem;
    }
    
    .table {
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .table thead th {
        border-bottom: 2px solid rgba(0, 0, 0, 0.05);
        font-weight: 600;
        color: #495057;
    }
    
    .table tbody tr {
        transition: all 0.2s;
    }
    
    .table tbody tr:hover {
        background-color: rgba(10, 86, 148, 0.05);
    }
    
    .table td, .table th {
        padding: 0.75rem;
        vertical-align: middle;
        border-top: 1px solid rgba(0, 0, 0, 0.05);
    }
</style>
@endsection 