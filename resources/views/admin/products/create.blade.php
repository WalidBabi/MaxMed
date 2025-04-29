@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Create New Product</h1>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Back to Products
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
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
                                        <span class="input-group-text bg-light">
                                            <i class="fas fa-box"></i>
                                        </span>
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
                                        <span class="input-group-text bg-light">
                                            <i class="fas fa-tags"></i>
                                        </span>
                                        <select name="category_id" id="category_id" class="form-select" required>
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
                                    <label for="brand_id" class="form-label fw-medium">Brand</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fas fa-trademark"></i>
                                        </span>
                                        <select name="brand_id" id="brand_id" class="form-select">
                                            <option value="">Select a brand</option>
                                            @foreach(App\Models\Brand::orderBy('name')->get() as $brand)
                                                <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                                    {{ $brand->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('brand_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Pricing Information -->
                            <div class="col-12 mt-4">
                                <h5 class="mb-3 text-primary">
                                    <i class="fas fa-dollar-sign me-2"></i>Pricing Information
                                </h5>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="price" class="form-label fw-medium">Price (USD) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">$</span>
                                        <input type="number" step="0.01" name="price" id="price" 
                                               value="{{ old('price') }}"
                                               class="form-control" required>
                                    </div>
                                    @error('price')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="price_aed" class="form-label fw-medium">Price (AED) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">AED</span>
                                        <input type="number" step="0.01" name="price_aed" id="price_aed" 
                                               value="{{ old('price_aed') }}"
                                               class="form-control" required>
                                    </div>
                                    @error('price_aed')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="stock" class="form-label fw-medium">Stock Quantity <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fas fa-boxes"></i>
                                        </span>
                                        <input type="number" name="stock" id="stock" 
                                               value="{{ old('stock') }}"
                                               class="form-control" required>
                                    </div>
                                    @error('stock')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
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
                                    <div class="mb-2">
                                        <small class="text-muted">
                                            Add normal product description first, then add technical specifications using the "PRODUCT PARAMETERS" format.
                                            <button type="button" class="btn btn-sm btn-outline-secondary ms-2" data-bs-toggle="modal" data-bs-target="#parametersHelpModal">
                                                <i class="fas fa-question-circle"></i> Format Help
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-primary ms-1" id="insertParametersTemplate">
                                                <i class="fas fa-plus-circle"></i> Insert Template
                                            </button>
                                        </small>
                                    </div>
                                    <textarea name="description" id="description" rows="8"
                                              class="form-control">{{ old('description') }}</textarea>
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

                            <div class="col-12" id="size_options_container" style="{{ old('has_size_options') ? '' : 'display: none;' }}">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-12">
                                                <p class="text-muted">Add the available size options for this product.</p>
                                            </div>
                                        </div>
                                        <div id="size_options_list">
                                            @if(old('size_options'))
                                                @foreach(old('size_options') as $index => $option)
                                                <div class="row mb-2 size-option-row">
                                                    <div class="col-md-10">
                                                        <div class="input-group">
                                                            <span class="input-group-text bg-light">
                                                                <i class="fas fa-ruler-combined"></i>
                                                            </span>
                                                            <input type="text" name="size_options[]" class="form-control" value="{{ $option }}" placeholder="Size option (e.g., Small, Medium, Large)">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="button" class="btn btn-danger remove-size-option">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                @endforeach
                                            @else
                                                <div class="row mb-2 size-option-row">
                                                    <div class="col-md-10">
                                                        <div class="input-group">
                                                            <span class="input-group-text bg-light">
                                                                <i class="fas fa-ruler-combined"></i>
                                                            </span>
                                                            <input type="text" name="size_options[]" class="form-control" placeholder="Size option (e.g., Small, Medium, Large)">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="button" class="btn btn-danger remove-size-option">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <button type="button" class="btn btn-outline-primary" id="add_size_option">
                                                    <i class="fas fa-plus"></i> Add Another Size Option
                                                </button>
                                            </div>
                                        </div>
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

<!-- Parameters Help Modal -->
<div class="modal fade" id="parametersHelpModal" tabindex="-1" aria-labelledby="parametersHelpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="parametersHelpModalLabel">Product Parameters Format Help</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>To add technical specifications to a product, include them in the following format after your regular product description:</p>
                
                <pre class="bg-light p-3 border rounded"><code>PRODUCT PARAMETERS
Parameter Name 1
Value 1
Parameter Name 2
Value 2
...</code></pre>

                <p>Example:</p>
                <pre class="bg-light p-3 border rounded"><code>PRODUCT PARAMETERS
Cat.No.
8032220100
Motion type
Orbital Shaker
Amplitude
10mm
Speed range
100-500rpm</code></pre>

                <p>Important guidelines:</p>
                <ul>
                    <li>Start with the header "PRODUCT PARAMETERS" on its own line</li>
                    <li>Each parameter name and value should be on separate lines</li>
                    <li>Keep parameter names consistent across similar products when possible</li>
                    <li>Parameters will be automatically formatted as a table on the product page</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Got it</button>
            </div>
        </div>
    </div>
</div>

<style>
    .form-control:focus, .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
    }

    .input-group-text {
        background-color: #f8f9fa;
        border-color: #dee2e6;
    }

    .card {
        border-radius: 0.5rem;
    }

    .btn {
        border-radius: 0.375rem;
    }

    .form-check-input:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    .form-label {
        color: #495057;
    }

    .text-primary {
        color: #0d6efd !important;
    }

    .btn-outline-primary {
        color: #0d6efd;
        border-color: #0d6efd;
    }

    .btn-outline-primary:hover {
        background-color: #0d6efd;
        color: #fff;
    }

    .btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
    }

    .btn-danger:hover {
        background-color: #bb2d3b;
        border-color: #b02a37;
    }

    .invalid-feedback {
        font-size: 0.875rem;
    }

    .text-muted {
        color: #6c757d !important;
    }

    .shadow-sm {
        box-shadow: 0 .125rem .25rem rgba(0,0,0,.075) !important;
    }

    .border-0 {
        border: 0 !important;
    }
</style>

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

        // Parameters template insertion
        const insertTemplateBtn = document.getElementById('insertParametersTemplate');
        const descriptionTextarea = document.getElementById('description');
        
        if (insertTemplateBtn && descriptionTextarea) {
            insertTemplateBtn.addEventListener('click', function() {
                const template = `

PRODUCT PARAMETERS
Cat.No.

Motion type

Amplitude

Speed range

Timer display

Time settings range

Max. load capacity

Motor type

Power

Voltage

Frequency

Dimensions

Weight

`;
                // Append the template to existing content or insert at cursor position
                const currentPosition = descriptionTextarea.selectionStart;
                const currentContent = descriptionTextarea.value;
                
                descriptionTextarea.value = 
                    currentContent.substring(0, currentPosition) + 
                    template + 
                    currentContent.substring(currentPosition);
                
                // Focus back on textarea
                descriptionTextarea.focus();
                descriptionTextarea.selectionStart = 
                    currentPosition + template.indexOf('Cat.No.') + 'Cat.No.'.length + 1;
                descriptionTextarea.selectionEnd = descriptionTextarea.selectionStart;
            });
        }
        
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
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-ruler-combined"></i>
                            </span>
                            <input type="text" name="size_options[]" class="form-control" placeholder="Size option (e.g., Small, Medium, Large)">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger remove-size-option">
                            <i class="fas fa-trash"></i>
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
@endsection