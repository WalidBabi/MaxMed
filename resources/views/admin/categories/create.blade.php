@extends('admin.layouts.app')

@section('content')
<style>
    .create-category-container {
        margin-top: 20px;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .btn-create {
        margin-top: 20px;
    }
    .custom-file-input {
        border: 1px solid #ced4da;
        border-radius: 4px;
        padding: 5px;
        width: 100%;
    }
    .custom-file-label {
        margin-top: 5px;
        font-size: 0.9rem;
        color: #6c757d;
    }
</style>

<div class="container create-category-container">
    <div class="card">
        <div class="card-header">
            <h1>Create Category</h1>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="name">Category Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="parent_id">Parent Category</label>
                    <small class="form-text text-muted">Select a parent category to nest this category under it. Leave empty for a top-level category.</small>
                    <select class="form-control" id="parent_id" name="parent_id">
                        <option value="">None</option>
                        @foreach($categories as $parentCategory)
                            <option value="{{ $parentCategory->id }}">{{ $parentCategory->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="image">Category Image</label>
                    <input type="file" class="custom-file-input" id="image" name="image">
                    <small class="custom-file-label">Choose an image file (optional)</small>
                </div>
                <button type="submit" class="btn btn-success btn-create">Create</button>
            </form>
        </div>
    </div>
</div>
@endsection 