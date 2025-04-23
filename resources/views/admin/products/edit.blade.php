@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">Edit Product</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}"
                                           class="form-control">
                                    @error('name')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="category_id" class="form-label">Category</label>
                                    <select name="category_id" id="category_id" class="form-select">
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" 
                                                {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="brand_id" class="form-label">Brand</label>
                                    <select name="brand_id" id="brand_id" class="form-select">
                                        <option value="">Select a brand</option>
                                        @foreach(App\Models\Brand::orderBy('name')->get() as $brand)
                                            <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
                                                {{ $brand->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('brand_id')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" id="description" rows="3"
                                              class="form-control">{{ old('description', $product->description) }}</textarea>
                                    @error('description')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="price" class="form-label">Price</label>
                                    <input type="number" step="0.01" name="price" id="price" 
                                           value="{{ old('price', $product->price) }}"
                                           class="form-control">
                                    @error('price')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="price_aed" class="form-label">Price (AED)</label>
                                    <input type="number" step="0.01" name="price_aed" id="price_aed" 
                                           value="{{ old('price_aed', $product->price_aed) }}"
                                           class="form-control">
                                    @error('price_aed')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="inventory_quantity" class="form-label">Number of Stock</label>
                                    
                                    <input type="number" name="inventory_quantity" id="inventory_quantity" 
                                           value="{{ old('inventory_quantity', $product->inventory->quantity) }}"
                                           class="form-control" min="0">
                                    @error('inventory_quantity')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-check form-switch mt-4">
                                        <input class="form-check-input" type="checkbox" id="has_size_options" name="has_size_options" value="1" {{ old('has_size_options', $product->has_size_options) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="has_size_options">Enable size options</label>
                                    </div>
                                    @error('has_size_options')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12" id="size_options_container" style="{{ old('has_size_options', $product->has_size_options) ? '' : 'display: none;' }}">
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">Size Options</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-12">
                                                <p class="text-muted">Add the available size options for this product.</p>
                                            </div>
                                        </div>
                                        <div id="size_options_list">
                                            @php
                                                $sizeOptions = old('size_options', $product->size_options ?? []);
                                                if (!is_array($sizeOptions) && !empty($sizeOptions)) {
                                                    // Convert JSON string to array if needed
                                                    $sizeOptions = json_decode($sizeOptions, true) ?? [];
                                                }
                                            @endphp
                                            
                                            @if(count($sizeOptions) > 0)
                                                @foreach($sizeOptions as $option)
                                                <div class="row mb-2 size-option-row">
                                                    <div class="col-md-10">
                                                        <input type="text" name="size_options[]" class="form-control" value="{{ $option }}" placeholder="Size option (e.g., Small, Medium, Large)">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="button" class="btn btn-danger remove-size-option"><i class="fas fa-trash"></i></button>
                                                    </div>
                                                </div>
                                                @endforeach
                                            @else
                                                <div class="row mb-2 size-option-row">
                                                    <div class="col-md-10">
                                                        <input type="text" name="size_options[]" class="form-control" placeholder="Size option (e.g., Small, Medium, Large)">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="button" class="btn btn-danger remove-size-option"><i class="fas fa-trash"></i></button>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <button type="button" class="btn btn-secondary" id="add_size_option">
                                                    <i class="fas fa-plus"></i> Add Another Size Option
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="image" class="form-label">Primary Product Image</label>
                                    @if($product->image)
                                        <div class="mb-3">
                                            <img src="{{ Storage::url($product->image) }}" 
                                                 alt="Current product image" 
                                                 class="img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;">
                                        </div>
                                    @endif
                                    <input type="file" name="image" id="image" class="form-control">
                                    @error('image')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="additional_images" class="form-label">Additional Product Images</label>
                                    
                                    @if($product->images->count() > 0)
                                        <div class="row mb-3">
                                            @foreach($product->images as $image)
                                                <div class="col-md-3 col-sm-4 col-6 mb-3 position-relative">
                                                    <img src="{{ $image->image_url }}" 
                                                         alt="Product image" 
                                                         class="img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;">
                                                    <div class="mt-2 d-flex">
                                                        <button type="button" class="btn btn-sm btn-outline-danger me-2 delete-image" 
                                                                data-image-id="{{ $image->id }}">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-primary set-primary-image {{ $image->is_primary ? 'active' : '' }}"
                                                                data-image-id="{{ $image->id }}" {{ $image->is_primary ? 'disabled' : '' }}>
                                                            <i class="fas fa-star"></i> {{ $image->is_primary ? 'Primary' : 'Set Primary' }}
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="mb-3">
                                            <input type="hidden" name="delete_images" id="delete_images">
                                            <input type="hidden" name="primary_image_id" id="primary_image_id">
                                        </div>
                                    @endif

                                    <input type="file" name="additional_images[]" id="additional_images" class="form-control" multiple>
                                    <small class="text-muted">You can select multiple images (hold Ctrl or Cmd while selecting)</small>
                                    @error('additional_images')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                    @error('additional_images.*')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="specification_image" class="form-label">Product Specification Image</label>
                                    
                                    @php
                                        $specImage = $product->images()->whereNotNull('specification_image_url')->first();
                                    @endphp
                                    
                                    @if($specImage)
                                        <div class="mb-3">
                                            <img src="{{ $specImage->specification_image_url }}" 
                                                 alt="Product specification image" 
                                                 class="img-thumbnail" style="max-width: 300px;">
                                            <div class="mt-2">
                                                <span class="badge bg-info">Current specification image</span>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <input type="file" name="specification_image" id="specification_image" class="form-control">
                                    <small class="text-muted">Upload an image showing product specifications or technical details</small>
                                    @error('specification_image')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Update Product
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Image deletion
    const deleteButtons = document.querySelectorAll('.delete-image');
    const deleteImagesInput = document.getElementById('delete_images');
    let imagesToDelete = [];

    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const imageId = this.getAttribute('data-image-id');
            const imageContainer = this.closest('.col-md-3');
            
            // Add image ID to the list of images to delete
            imagesToDelete.push(imageId);
            deleteImagesInput.value = imagesToDelete.join(',');
            
            // Visually hide the image
            imageContainer.style.opacity = '0.3';
            this.disabled = true;
            
            // Show a message that the image will be deleted on save
            const message = document.createElement('div');
            message.classList.add('badge', 'bg-danger', 'position-absolute', 'top-0', 'end-0');
            message.innerHTML = 'Will be deleted';
            imageContainer.appendChild(message);
        });
    });
    
    // Primary image setting
    const primaryButtons = document.querySelectorAll('.set-primary-image');
    const primaryImageInput = document.getElementById('primary_image_id');

    primaryButtons.forEach(button => {
        button.addEventListener('click', function() {
            const imageId = this.getAttribute('data-image-id');
            
            // Reset all buttons
            primaryButtons.forEach(btn => {
                btn.classList.remove('active');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-star"></i> Set Primary';
            });
            
            // Set this button as active
            this.classList.add('active');
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-star"></i> Primary';
            
            // Set the primary image ID
            primaryImageInput.value = imageId;
        });
    });
    
    // Size options handling
    const hasSizeOptions = document.getElementById('has_size_options');
    const sizeOptionsContainer = document.getElementById('size_options_container');
    const addSizeOptionBtn = document.getElementById('add_size_option');
    const sizeOptionsList = document.getElementById('size_options_list');

    if (hasSizeOptions && sizeOptionsContainer) {
        hasSizeOptions.addEventListener('change', function() {
            if (this.checked) {
                sizeOptionsContainer.style.display = 'block';
            } else {
                sizeOptionsContainer.style.display = 'none';
            }
        });
    }

    if (addSizeOptionBtn && sizeOptionsList) {
        addSizeOptionBtn.addEventListener('click', function() {
            const newRow = document.createElement('div');
            newRow.className = 'row mb-2 size-option-row';
            newRow.innerHTML = `
                <div class="col-md-10">
                    <input type="text" name="size_options[]" class="form-control" placeholder="Size option (e.g., Small, Medium, Large)">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger remove-size-option"><i class="fas fa-trash"></i></button>
                </div>
            `;
            sizeOptionsList.appendChild(newRow);
            
            // Attach event listener to the new remove button
            const removeBtn = newRow.querySelector('.remove-size-option');
            if (removeBtn) {
                removeBtn.addEventListener('click', function() {
                    newRow.remove();
                });
            }
        });
    }

    // Attach event listeners to existing remove buttons
    document.querySelectorAll('.remove-size-option').forEach(button => {
        button.addEventListener('click', function() {
            this.closest('.size-option-row').remove();
        });
    });
});
</script>
