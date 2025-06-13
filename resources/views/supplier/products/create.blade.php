@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Create New Product</h1>
                <a href="{{ route('supplier.products.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Back to My Products
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('supplier.products.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                        @csrf
                        
                        <div class="row g-4">
                            <!-- Basic Information -->
                            <div class="col-12">
                                <h5 class="mb-3 text-primary">
                                    <i class="fas fa-info-circle me-2"></i>Basic Information
                                </h5>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-label fw-medium">Product Name <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                       
                                        <input type="text" name="name" id="name" value="{{ old('name') }}"
                                               class="form-control" required>
                                    </div>
                                    @error('name')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="category_id" class="form-label fw-medium">Category <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                     
                                        <select name="category_id" id="category_id" class="form-select" style="border-radius: 0.375rem;" required>
                                            <option value="">Select a category</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('category_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="brand" class="form-label fw-medium">Brand</label>
                                    <div class="input-group">
                                       
                                        <input type="text" id="brand" class="form-control" value="Yooning" readonly>
                                    </div>
                                    <small class="text-muted">Brand is automatically set to Yooning for all supplier products</small>
                                </div>
                            </div>

                            <!-- Product Description -->
                            <div class="col-12 mt-4">
                                <h5 class="mb-3 text-primary">
                                    <i class="fas fa-align-left me-2"></i>Product Description
                                </h5>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="description" class="form-label fw-medium">Description <span class="text-danger">*</span></label>
                                    <textarea name="description" id="description" rows="5"
                                              class="form-control" required 
                                              placeholder="Add normal product description first, then add technical specifications using the 'PRODUCT PARAMETERS' format.">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Size Options -->
                            <div class="col-12 mt-4">
                                <h5 class="mb-3 text-primary">
                                    <i class="fas fa-ruler me-2"></i>Size Options
                                </h5>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="has_size_options" name="has_size_options" value="1" {{ old('has_size_options') ? 'checked' : '' }}>
                                        <label class="form-check-label fw-medium" for="has_size_options">Enable size options</label>
                                    </div>
                                    @error('has_size_options')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12" id="size-options-container" style="display: none;">
                                <div class="form-group">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <label class="form-label fw-medium">Size Options</label>
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="add-size-option">
                                            <i class="fas fa-plus"></i> Add Size Option
                                        </button>
                                    </div>
                                    <div id="size-options-list">
                                        <!-- Size options will be added here -->
                                    </div>
                                </div>
                            </div>

                            <!-- Product Images -->
                            <div class="col-12 mt-4">
                                <h5 class="mb-3 text-primary">
                                    <i class="fas fa-images me-2"></i>Product Images
                                </h5>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="image" class="form-label fw-medium">Primary Product Image <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="file" name="image" id="image" class="form-control" required>
                                    </div>
                                    <small class="text-muted">Recommended size: 800x800px, Max file size: 5MB</small>
                                    @error('image')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="additional_images" class="form-label fw-medium">Additional Product Images</label>
                                    <div class="input-group">
                                        <input type="file" name="additional_images[]" id="additional_images" class="form-control" multiple>
                                    </div>
                                    <small class="text-muted">You can select multiple images (hold Ctrl or Cmd while selecting)</small>
                                    @error('additional_images')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    @error('additional_images.*')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="specification_image" class="form-label fw-medium">Product Specification Image</label>
                                    <div class="input-group">
                                        <input type="file" name="specification_image" id="specification_image" class="form-control">
                                    </div>
                                    <small class="text-muted">Upload an image showing product specifications or technical details</small>
                                    @error('specification_image')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Product Documentation -->
                            <div class="col-12 mt-4">
                                <h5 class="mb-3 text-primary">
                                    <i class="fas fa-file-pdf me-2"></i>Product Documentation
                                </h5>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="pdf_file" class="form-label fw-medium">Product PDF</label>
                                    <div class="input-group">
                                        <input type="file" name="pdf_file" id="pdf_file" class="form-control" accept=".pdf">
                                    </div>
                                    <small class="text-muted">Upload a PDF file containing product documentation or specifications</small>
                                    @error('pdf_file')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>



                            <!-- Submit Button -->
                            <div class="col-12 mt-4">
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="fas fa-plus me-2"></i> Create Product
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // Size options functionality
    const hasSizeOptions = document.getElementById('has_size_options');
    const sizeOptionsContainer = document.getElementById('size-options-container');
    const addSizeOptionBtn = document.getElementById('add-size-option');
    const sizeOptionsList = document.getElementById('size-options-list');

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
                    <div class="input-group">
                     
                        <input type="text" name="size_options[]" class="form-control" placeholder="Size option (e.g., Small, Medium, Large)">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger remove-size-option">
                        <i class="fas fa-trash">Delete</i>
                    </button>
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

<style>
.form-group {
    margin-bottom: 1.5rem;
    border-radius: 0.375rem;
}

.input-group-text {
border-radius: 0.375rem;
}

.form-control {
  border-radius: 0.375rem;
}

.form-control:focus {
    border-color: #0d6efd;
    border-radius: 0.375rem;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

.alert-info {
    background-color: #d1ecf1;
    border-color: #bee5eb;
    color: #0c5460;
    border-radius: 0.375rem;
}
</style>
@endsection 