@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Edit Product</h1>
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
                    <form action="{{ route('supplier.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                        @csrf
                        @method('PUT')
                        
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
                                        <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}"
                                               class="form-control rounded-input" required>
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
                                        <select name="category_id" id="category_id" class="form-select rounded-input" required>
                                            <option value="">Select a category</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" 
                                                    {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
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
                                        <input type="text" id="brand" class="form-control rounded-input" value="Yooning" readonly>
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
                                              class="form-control rounded-input" required 
                                              placeholder="Add normal product description first, then add technical specifications using the 'PRODUCT PARAMETERS' format.">{{ old('description', $product->description) }}</textarea>
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
                                        <input class="form-check-input" type="checkbox" id="has_size_options" name="has_size_options" value="1" 
                                            {{ old('has_size_options', $product->has_size_options) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-medium" for="has_size_options">Enable size options</label>
                                    </div>
                                    @error('has_size_options')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12" id="size-options-container" style="display: {{ old('has_size_options', $product->has_size_options) ? 'block' : 'none' }};">
                                <div class="form-group">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <label class="form-label fw-medium">Size Options</label>
                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-btn" id="add-size-option">
                                            <i class="fas fa-plus me-1"></i> Add Size Option
                                        </button>
                                    </div>
                                    <div id="size-options-list">
                                        @if(old('size_options', $product->size_options))
                                            @foreach(old('size_options', $product->size_options) as $index => $size)
                                                @if($size)
                                                    <div class="input-group mb-2 size-option-row">
                                                        <input type="text" name="size_options[]" class="form-control rounded-input" value="{{ $size }}" placeholder="Enter size option (e.g., Small, Medium, Large)">
                                                        <button type="button" class="btn btn-outline-danger rounded-btn remove-size-option">
                                                            <i class="fas fa-trash me-1"></i> Remove
                                                        </button>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Dynamic Specifications Section -->
                            <div class="col-12 mt-4">
                                <h5 class="mb-3 text-primary">
                                    <i class="fas fa-cogs me-2"></i>Product Specifications
                                </h5>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <p class="text-muted mb-3">Product specifications based on the selected category.</p>
                                    <div id="specifications-container" class="space-y-4">
                                        @if(isset($templates) && !empty($templates))
                                            @foreach($templates as $categoryName => $specs)
                                                <div class="card border-0 shadow-sm mb-4">
                                                    <div class="card-header bg-light">
                                                        <h6 class="mb-0 text-primary">
                                                            <i class="fas fa-list-ul me-2"></i>{{ $categoryName }}
                                                        </h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row g-3">
                                                            @foreach($specs as $spec)
                                                                @php
                                                                    $fieldName = "specifications[{$spec['key']}]";
                                                                    $fieldId = "spec_{$spec['key']}";
                                                                    $currentValue = $existingSpecs[$spec['key']]->specification_value ?? '';
                                                                    $required = $spec['required'] ? 'required' : '';
                                                                    $requiredMark = $spec['required'] ? '<span class="text-danger">*</span>' : '';
                                                                @endphp
                                                                
                                                                <div class="col-md-6">
                                                                    <label for="{{ $fieldId }}" class="form-label fw-medium">
                                                                        {!! $spec['name'] . ' ' . $requiredMark !!}
                                                                        @if($spec['unit'])
                                                                            <span class="text-muted small">({{ $spec['unit'] }})</span>
                                                                        @endif
                                                                    </label>
                                                                    
                                                                    @if($spec['type'] === 'select')
                                                                        <select name="{{ $fieldName }}" id="{{ $fieldId }}" {{ $required }}
                                                                                class="form-select rounded-input">
                                                                            <option value="">Select {{ $spec['name'] }}</option>
                                                                            @foreach($spec['options'] as $option)
                                                                                <option value="{{ $option }}" {{ $currentValue == $option ? 'selected' : '' }}>
                                                                                    {{ $option }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    @elseif($spec['type'] === 'textarea')
                                                                        <textarea name="{{ $fieldName }}" id="{{ $fieldId }}" {{ $required }}
                                                                                  class="form-control rounded-input"
                                                                                  rows="3" placeholder="Enter {{ strtolower($spec['name']) }}">{{ $currentValue }}</textarea>
                                                                    @elseif($spec['type'] === 'boolean')
                                                                        <div class="form-check">
                                                                            <input type="checkbox" name="{{ $fieldName }}" id="{{ $fieldId }}" value="1" {{ $required }}
                                                                                   {{ $currentValue == '1' || $currentValue == 'Yes' ? 'checked' : '' }}
                                                                                   class="form-check-input">
                                                                            <label for="{{ $fieldId }}" class="form-check-label">
                                                                                Yes
                                                                            </label>
                                                                        </div>
                                                                    @else
                                                                        @php
                                                                            $inputType = $spec['type'] === 'decimal' ? 'number' : $spec['type'];
                                                                            $step = $spec['type'] === 'decimal' ? 'step="0.01"' : '';
                                                                        @endphp
                                                                        <input type="{{ $inputType }}" name="{{ $fieldName }}" id="{{ $fieldId }}" {{ $required }} {{ $step }}
                                                                               value="{{ $currentValue }}"
                                                                               class="form-control rounded-input"
                                                                               placeholder="Enter {{ strtolower($spec['name']) }}">
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="alert alert-info rounded-input">
                                                <i class="fas fa-info-circle me-2"></i>
                                                No specifications available for this category.
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Current Images Display -->
                            @if($product->images->count() > 0)
                                <div class="col-12 mt-4">
                                    <h5 class="mb-3 text-primary">
                                        <i class="fas fa-images me-2"></i>Current Images
                                    </h5>
                                </div>

                                <div class="col-12">
                                    <div class="row g-3" id="current-images">
                                        @foreach($product->images as $image)
                                            <div class="col-md-3 image-item" data-image-id="{{ $image->id }}">
                                                <div class="card">
                                                    <div class="position-relative">
                                                        <img src="{{ $image->image_url }}" alt="Product Image" class="card-img-top" style="height: 150px; object-fit: cover;">
                                                        @if($image->is_primary)
                                                            <span class="badge bg-success position-absolute top-0 start-0 m-2">Primary</span>
                                                        @endif
                                                        <div class="position-absolute top-0 end-0 m-2">
                                                            <div class="btn-group-vertical">
                                                                @if(!$image->is_primary)
                                                                    <button type="button" class="btn btn-sm btn-success mb-1 rounded-btn set-primary" data-image-id="{{ $image->id }}">
                                                                        <i class="fas fa-star me-1"></i> Set Primary
                                                                    </button>
                                                                @endif
                                                                <button type="button" class="btn btn-sm btn-danger rounded-btn delete-image" data-image-id="{{ $image->id }}">
                                                                    <i class="fas fa-trash me-1"></i> Delete
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <input type="hidden" name="delete_images" id="delete_images" value="">
                                    <input type="hidden" name="primary_image_id" id="primary_image_id" value="">
                                </div>
                            @endif

                            <!-- Product Images -->
                            <div class="col-12 mt-4">
                                <h5 class="mb-3 text-primary">
                                    <i class="fas fa-images me-2"></i>Add New Images
                                </h5>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="image" class="form-label fw-medium">New Primary Product Image</label>
                                    <div class="input-group">
                                        <input type="file" name="image" id="image" class="form-control rounded-input">
                                    </div>
                                    <small class="text-muted">Upload only if you want to replace the current primary image. Recommended size: 800x800px, Max file size: 5MB</small>
                                    @error('image')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="additional_images" class="form-label fw-medium">Additional Product Images</label>
                                    <div class="input-group">
                                        <input type="file" name="additional_images[]" id="additional_images" class="form-control rounded-input" multiple>
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
                                        <input type="file" name="specification_image" id="specification_image" class="form-control rounded-input">
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

                            @if($product->pdf_file)
                                <div class="col-12">
                                    <div class="alert alert-info rounded-input">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="fas fa-file-pdf me-2"></i>
                                                Current PDF: <a href="{{ asset('storage/' . $product->pdf_file) }}" target="_blank" class="btn btn-sm btn-outline-primary border border-primary">View PDF</a>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="delete_pdf" name="delete_pdf" value="1">
                                                <label class="form-check-label text-danger" for="delete_pdf">
                                                    Delete current PDF
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="pdf_file" class="form-label fw-medium">
                                        @if($product->pdf_file)
                                            Replace Product PDF
                                        @else
                                            Product PDF
                                        @endif
                                    </label>
                                    <div class="input-group">
                                        <input type="file" name="pdf_file" id="pdf_file" class="form-control rounded-input" accept=".pdf">
                                    </div>
                                    <small class="text-muted">Upload a PDF file containing product documentation or specifications</small>
                                    @error('pdf_file')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="col-12 mt-4">
                                <div class="d-flex gap-3">
                                    <button type="submit" class="btn btn-primary btn-lg px-4 rounded-btn">
                                        <i class="fas fa-save me-2"></i> Update Product
                                    </button>
                                    <a href="{{ route('supplier.products.index') }}" class="btn btn-outline-secondary btn-lg px-4 rounded-btn">
                                        <i class="fas fa-times me-2"></i> Cancel
                                    </a>
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
    // Category change handler for dynamic specifications
    const categorySelect = document.getElementById('category_id');
    const specificationsContainer = document.getElementById('specifications-container');
    
    if (categorySelect && specificationsContainer) {
        categorySelect.addEventListener('change', function() {
            const categoryId = this.value;
            if (categoryId) {
                loadSpecifications(categoryId);
            } else {
                specificationsContainer.innerHTML = '<div class="alert alert-info rounded-input"><i class="fas fa-info-circle me-2"></i>Select a category to see specification fields.</div>';
            }
        });
    }

    // Function to load specifications based on category
    function loadSpecifications(categoryId) {
        fetch(`/supplier/api/category-specifications/${categoryId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.templates) {
                    renderSpecifications(data.templates);
                } else {
                    specificationsContainer.innerHTML = '<div class="alert alert-info rounded-input"><i class="fas fa-info-circle me-2"></i>No specifications available for this category.</div>';
                }
            })
            .catch(error => {
                console.error('Error loading specifications:', error);
                specificationsContainer.innerHTML = '<div class="alert alert-danger rounded-input"><i class="fas fa-exclamation-triangle me-2"></i>Error loading specifications. Please try again.</div>';
            });
    }

    // Function to render specification fields
    function renderSpecifications(templates) {
        let html = '';
        
        Object.entries(templates).forEach(([categoryName, specs]) => {
            html += `
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 text-primary">
                            <i class="fas fa-list-ul me-2"></i>${categoryName}
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
            `;
            
            specs.forEach(spec => {
                const fieldName = `specifications[${spec.key}]`;
                const fieldId = `spec_${spec.key}`;
                const required = spec.required ? 'required' : '';
                const requiredMark = spec.required ? '<span class="text-danger">*</span>' : '';
                
                html += `
                    <div class="col-md-6">
                        <label for="${fieldId}" class="form-label fw-medium">
                            ${spec.name} ${requiredMark}
                            ${spec.unit ? `<span class="text-muted small">(${spec.unit})</span>` : ''}
                        </label>
                `;
                
                if (spec.type === 'select') {
                    html += `
                        <select name="${fieldName}" id="${fieldId}" ${required}
                                class="form-select rounded-input">
                            <option value="">Select ${spec.name}</option>
                    `;
                    spec.options.forEach(option => {
                        html += `<option value="${option}">${option}</option>`;
                    });
                    html += `</select>`;
                } else if (spec.type === 'textarea') {
                    html += `
                        <textarea name="${fieldName}" id="${fieldId}" ${required}
                                  class="form-control rounded-input"
                                  rows="3" placeholder="Enter ${spec.name.toLowerCase()}"></textarea>
                    `;
                } else if (spec.type === 'boolean') {
                    html += `
                        <div class="form-check">
                            <input type="checkbox" name="${fieldName}" id="${fieldId}" value="1" ${required}
                                   class="form-check-input">
                            <label for="${fieldId}" class="form-check-label">
                                Yes
                            </label>
                        </div>
                    `;
                } else {
                    const inputType = spec.type === 'decimal' ? 'number' : spec.type;
                    const step = spec.type === 'decimal' ? 'step="0.01"' : '';
                    html += `
                        <input type="${inputType}" name="${fieldName}" id="${fieldId}" ${required} ${step}
                               class="form-control rounded-input"
                               placeholder="Enter ${spec.name.toLowerCase()}">
                    `;
                }
                
                html += `</div>`;
            });
            
            html += `
                        </div>
                    </div>
                </div>
            `;
        });
        
        specificationsContainer.innerHTML = html;
    }

    // Size options functionality
    const hasSizeOptions = document.getElementById('has_size_options');
    const sizeOptionsContainer = document.getElementById('size-options-container');
    const addSizeOption = document.getElementById('add-size-option');
    const sizeOptionsList = document.getElementById('size-options-list');

    hasSizeOptions.addEventListener('change', function() {
        sizeOptionsContainer.style.display = this.checked ? 'block' : 'none';
    });

    addSizeOption.addEventListener('click', function() {
        const div = document.createElement('div');
        div.className = 'input-group mb-2 size-option-row';
        div.innerHTML = `
            <input type="text" name="size_options[]" class="form-control rounded-input" placeholder="Enter size option (e.g., Small, Medium, Large)">
            <button type="button" class="btn btn-outline-danger rounded-btn remove-size-option">
                <i class="fas fa-trash me-1"></i> Remove
            </button>
        `;
        sizeOptionsList.appendChild(div);
    });

    // Remove size option
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-size-option')) {
            e.target.closest('.size-option-row').remove();
        }
    });

    // Image management
    let imagesToDelete = [];
    let primaryImageId = null;

    // Delete image functionality
    document.querySelectorAll('.delete-image').forEach(button => {
        button.addEventListener('click', function() {
            const imageId = this.dataset.imageId;
            const imageItem = this.closest('.image-item');
            
            if (confirm('Are you sure you want to delete this image?')) {
                imagesToDelete.push(imageId);
                document.getElementById('delete_images').value = imagesToDelete.join(',');
                imageItem.style.opacity = '0.5';
                imageItem.style.pointerEvents = 'none';
                this.innerHTML = '<i class="fas fa-check me-1"></i> Deleted';
                this.classList.remove('btn-danger');
                this.classList.add('btn-success');
                this.disabled = true;
            }
        });
    });

    // Set primary image functionality
    document.querySelectorAll('.set-primary').forEach(button => {
        button.addEventListener('click', function() {
            const imageId = this.dataset.imageId;
            
            // Remove primary badge from all images
            document.querySelectorAll('.badge.bg-success').forEach(badge => {
                badge.remove();
            });
            
            // Add primary badge to selected image
            const imageItem = this.closest('.image-item');
            const img = imageItem.querySelector('img');
            const badge = document.createElement('span');
            badge.className = 'badge bg-success position-absolute top-0 start-0 m-2';
            badge.textContent = 'Primary';
            img.parentNode.appendChild(badge);
            
            // Hide all set-primary buttons and show for others
            document.querySelectorAll('.set-primary').forEach(btn => {
                btn.style.display = 'block';
            });
            this.style.display = 'none';
            
            // Set the primary image ID
            document.getElementById('primary_image_id').value = imageId;
        });
    });

    // Form validation
    const form = document.querySelector('.needs-validation');
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    });
});
</script>

<style>
/* Custom rounded styles */
.rounded-input {
    border-radius: 0.75rem !important;
    border: 1px solid #dee2e6;
    transition: all 0.3s ease;
}

.rounded-input:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    border-radius: 0.75rem !important;
}

.rounded-btn {
    border-radius: 0.75rem !important;
    transition: all 0.3s ease;
}

.rounded-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.image-item img {
    transition: opacity 0.3s ease;
    border-radius: 0.75rem;
}

.btn-group-vertical .btn {
    border-radius: 0.5rem !important;
    margin-bottom: 0.25rem;
    font-size: 0.8rem;
    padding: 0.375rem 0.5rem;
    white-space: nowrap;
}

.size-option-row {
    animation: fadeIn 0.3s ease-in;
}

.size-option-row .form-control {
    border-top-right-radius: 0 !important;
    border-bottom-right-radius: 0 !important;
}

.size-option-row .btn {
    border-top-left-radius: 0 !important;
    border-bottom-left-radius: 0 !important;
    border-left: 0;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.form-group {
    margin-bottom: 1.5rem;
}

.input-group-text {
    background-color: #f8f9fa;
    border-color: #dee2e6;
    border-radius: 0.75rem 0 0 0.75rem;
}

.card-img-top {
    border-radius: 0.75rem 0.75rem 0 0;
}

.position-absolute .btn {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.alert {
    border-radius: 0.75rem !important;
}

/* Form select styling */
.form-select.rounded-input {
    border-radius: 0.75rem !important;
}

/* Textarea styling */
textarea.rounded-input {
    border-radius: 0.75rem !important;
    resize: vertical;
}

/* File input styling */
input[type="file"].rounded-input {
    border-radius: 0.75rem !important;
    padding: 0.5rem 0.75rem;
}

/* Checkbox styling */
.form-check-input:focus {
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}
</style>
@endsection 