@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">Create New Product</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}"
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
                                        <option value="">Select a category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="description" class="form-label">Description</label>
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
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="price" class="form-label">Price</label>
                                    <input type="number" step="0.01" name="price" id="price" 
                                           value="{{ old('price') }}"
                                           class="form-control">
                                    @error('price')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="stock" class="form-label">Stock</label>
                                    <input type="number" name="stock" id="stock" 
                                           value="{{ old('stock') }}"
                                           class="form-control">
                                    @error('stock')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="price_aed" class="form-label">Price (AED)</label>
                                    <input type="number" step="0.01" name="price_aed" id="price_aed" 
                                           value="{{ old('price_aed') }}"
                                           class="form-control">
                                    @error('price_aed')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="image" class="form-label">Product Image</label>
                                    <input type="file" name="image" id="image" class="form-control">
                                    @error('image')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i> Create Product
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
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
    });
</script>