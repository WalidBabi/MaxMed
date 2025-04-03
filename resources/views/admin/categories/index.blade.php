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
    .subcategory {
        margin-left: 30px;
    }
    .subsubcategory {
        margin-left: 60px;
    }
    .level-indicator {
        color: #6c757d;
        font-size: 0.8rem;
        display: inline-block;
        padding: 2px 6px;
        background: #e9ecef;
        border-radius: 4px;
        margin-right: 5px;
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
                            <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-primary btn-sm">Edit</a>
                            <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" style="display:inline;">
                                @csrf 
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this category?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    
                    @foreach($category->subcategories as $subcategory)
                    <tr class="subcategory">
                        <td>
                            <i class="fas fa-angle-right me-2"></i>
                            {{ $subcategory->name }}
                        </td>
                        <td><span class="level-indicator">Level 2</span></td>
                        <td>
                            <a href="{{ route('admin.categories.edit', $subcategory->id) }}" class="btn btn-primary btn-sm">Edit</a>
                            <form action="{{ route('admin.categories.destroy', $subcategory->id) }}" method="POST" style="display:inline;">
                                @csrf 
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this subcategory?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    
                    @foreach($subcategory->subcategories as $subsubcategory)
                    <tr class="subsubcategory">
                        <td>
                            <i class="fas fa-angle-double-right me-2"></i>
                            {{ $subsubcategory->name }}
                        </td>
                        <td><span class="level-indicator">Level 3</span></td>
                        <td>
                            <a href="{{ route('admin.categories.edit', $subsubcategory->id) }}" class="btn btn-primary btn-sm">Edit</a>
                            <form action="{{ route('admin.categories.destroy', $subsubcategory->id) }}" method="POST" style="display:inline;">
                                @csrf 
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this sub-subcategory?')">Delete</button>
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
@endsection 