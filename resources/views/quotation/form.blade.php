@extends('layouts.app')

@section('title', 'Request Quotation for ' . $product->name . ' | MaxMed UAE')
@section('meta_description', 'Request a quotation for ' . $product->name . ' from MaxMed UAE, supplier of high-quality laboratory equipment in Dubai.')
@section('meta_robots', 'index, follow')

@section('content')
<style>
    /* Enhanced Quotation Form Styling */
    .quotation-hero {
        position: relative;
        background: linear-gradient(135deg, #171e60 0%, #0a5694 100%);
        color: white;
        padding: 4rem 0;
        overflow: hidden;
    }
    
    .quotation-hero::before {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 200px;
        height: 200px;
        border-radius: 50%;
        background: rgba(10, 86, 148, 0.3);
        filter: blur(80px);
    }
    
    .quotation-hero::after {
        content: '';
        position: absolute;
        bottom: -80px;
        left: -80px;
        width: 300px;
        height: 300px;
        border-radius: 50%;
        background: rgba(10, 86, 148, 0.2);
        filter: blur(80px);
    }
    
    .hero-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
        position: relative;
        z-index: 10;
    }
    
    .hero-subtitle {
        font-size: 1.1rem;
        opacity: 0.9;
        position: relative;
        z-index: 10;
    }
    
    .quotation-section {
        padding: 4rem 0;
        background-color: #f8f9fa;
        min-height: 80vh;
    }
    
    .quotation-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        transition: all 0.4s ease;
        max-width: 800px;
        margin: 0 auto;
    }
    
    .quotation-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
    }
    
    .card-header {
        background: linear-gradient(135deg, #171e60 0%, #0a5694 100%);
        color: white;
        padding: 2rem;
        text-align: center;
    }
    
    .card-header h2 {
        font-size: 1.5rem;
        font-weight: 600;
        margin: 0;
    }
    
    .product-info {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 16px;
        padding: 2rem;
        margin: 1.5rem auto 0;
        max-width: 400px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        color: #333;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .product-details {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
        text-align: center;
    }
    
    .product-image {
        flex-shrink: 0;
    }
    
    .product-img {
        width: 100px;
        height: 100px;
        object-fit: contain;
        border-radius: 12px;
        background: white;
        border: 2px solid #e2e8f0;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        margin: 0 auto;
    }
    
    .product-placeholder {
        width: 100px;
        height: 100px;
        background: #f8f9fa;
        border-radius: 12px;
        border: 2px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6b7280;
        font-size: 2rem;
        margin: 0 auto;
    }
    
    .product-text {
        flex: 1;
        width: 100%;
    }
    
    .product-name {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #1f2937;
        line-height: 1.4;
    }
    
    .product-details-info {
        margin-bottom: 1.5rem;
    }
    
    .product-category,
    .product-spec {
        display: flex;
        align-items: center;
        font-size: 0.85rem;
        color: #6b7280;
        margin-bottom: 0.5rem;
        line-height: 1.4;
    }
    
    .product-category i,
    .product-spec i {
        color: #9ca3af;
        width: 16px;
        text-align: center;
    }
    
    .product-category .label,
    .product-spec .label {
        font-weight: 500;
        margin-right: 0.25rem;
        color: #4b5563;
    }
    
    .product-category .value,
    .product-spec .value {
        font-weight: 600;
        color: #1f2937;
    }
    
    .product-action {
        margin-top: 0.5rem;
    }
    
    .view-product-btn {
        display: inline-flex;
        align-items: center;
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(5, 150, 105, 0.25);
        border: none;
    }
    
    .view-product-btn:hover {
        background: linear-gradient(135deg, #047857 0%, #065f46 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(5, 150, 105, 0.35);
        color: white;
        text-decoration: none;
    }
    
    .view-product-btn:active {
        transform: translateY(0);
        box-shadow: 0 2px 8px rgba(5, 150, 105, 0.3);
    }
    
    /* Form Styling */
    .form-section {
        padding: 2rem;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        font-size: 0.9rem;
        color: #333;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .form-control {
        width: 100%;
        padding: 1rem 1.25rem;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        background-color: #f8fafc;
        font-size: 1rem;
        transition: all 0.3s ease;
        font-family: inherit;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #0a5694;
        box-shadow: 0 0 0 3px rgba(10, 86, 148, 0.15);
        background-color: white;
        transform: translateY(-1px);
    }
    
    .form-control::placeholder {
        color: #94a3b8;
        font-style: italic;
    }
    
    .textarea-control {
        min-height: 120px;
        resize: vertical;
        font-family: inherit;
    }
    
    .size-display {
        background: linear-gradient(135deg, #e0f2fe 0%, #b3e5fc 100%);
        border: 2px solid #0a5694;
        border-radius: 8px;
        padding: 1rem;
        color: #0a5694;
        font-weight: 600;
        text-align: center;
        margin-bottom: 1rem;
    }
    
    .size-display i {
        margin-right: 0.5rem;
        color: #0a5694;
    }
    
    /* Submit Button */
    .submit-btn {
        width: 100%;
        padding: 1.25rem 2rem;
        background: linear-gradient(135deg, #171e60 0%, #0a5694 100%);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .submit-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(23, 30, 96, 0.4);
    }
    
    .submit-btn:active {
        transform: translateY(-1px);
    }
    
    .submit-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }
    
    .submit-btn:hover::before {
        left: 100%;
    }
    
    /* Alert Styling */
    .alert {
        padding: 1rem 1.5rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        border-left: 4px solid;
        font-weight: 500;
    }
    
    .alert-success {
        background-color: #d1fae5;
        border-left-color: #10b981;
        color: #065f46;
    }
    
    .alert-danger {
        background-color: #fee2e2;
        border-left-color: #ef4444;
        color: #b91c1c;
    }
    
    .alert-info {
        background-color: #dbeafe;
        border-left-color: #3b82f6;
        color: #1e40af;
    }
    
    /* reCAPTCHA Styling */
    .g-recaptcha {
        margin: 1rem 0;
        display: flex;
        justify-content: center;
    }
    
    .g-recaptcha > div {
        transform: scale(1);
        transform-origin: 0 0;
    }
    
    @media (max-width: 480px) {
        .g-recaptcha > div {
            transform: scale(0.9);
        }
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .hero-title {
            font-size: 2rem;
        }
        
        .quotation-section {
            padding: 2rem 0;
        }
        
        .card-header,
        .form-section {
            padding: 1.5rem;
        }
        
        .view-product-btn {
            width: 100%;
            justify-content: center;
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
        }
        
        .product-info {
            margin: 1rem 1rem 0;
            padding: 1.5rem;
            max-width: none;
        }
        
        .product-details {
            gap: 0.75rem;
        }
        
        .product-img,
        .product-placeholder {
            width: 80px;
            height: 80px;
        }
        
        .product-name {
            font-size: 1.1rem;
        }
        
        .product-details-info {
            margin-bottom: 1rem;
        }
        
        .product-category,
        .product-spec {
            font-size: 0.8rem;
            margin-bottom: 0.4rem;
        }
        
        .view-product-btn {
            width: 100%;
            justify-content: center;
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
        }
        
        .form-control {
            font-size: 16px; /* Prevents zoom on iOS */
        }
    }
</style>

<!-- Hero Section -->
<div class="quotation-hero">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <h1 class="hero-title">Request Quotation</h1>
        <p class="hero-subtitle">Get competitive pricing for premium medical equipment</p>
    </div>
</div>

<!-- Quotation Section -->
<div class="quotation-section">
    <div class="max-w-7xl mx-auto px-4">
        <div class="quotation-card">
            <!-- Card Header -->
            <div class="card-header">
                <h2>Quotation Request</h2>
                <div class="product-info">
                    <div class="product-details">
                        <div class="product-image">
                            @if($product->primaryImage)
                                <img src="{{ $product->primaryImage->image_url }}" alt="{{ $product->name }}" class="product-img">
                            @elseif($product->image_url)
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="product-img">
                            @elseif($product->images && $product->images->count() > 0)
                                <img src="{{ $product->images->first()->image_url }}" alt="{{ $product->name }}" class="product-img">
                            @else
                                <div class="product-placeholder">
                                    <i class="fas fa-box"></i>
                                </div>
                            @endif
                        </div>
                        <div class="product-text">
                            <div class="product-name">{{ $product->name }}</div>
                            <div class="product-details-info">
                                @if($product->category)
                                    <div class="product-category">
                                        <i class="fas fa-folder mr-1"></i>
                                        <span class="label">Category:</span>
                                        <span class="value">{{ $product->category->name }}</span>
                                    </div>
                                @endif
                                @php
                                    $keySpecs = $product->specifications()
                                        ->where('show_on_listing', true)
                                        ->orderBy('sort_order')
                                        ->limit(2)
                                        ->get();
                                @endphp
                                @if($keySpecs->count() > 0)
                                    @foreach($keySpecs as $spec)
                                        <div class="product-spec">
                                            <i class="fas fa-cog mr-1"></i>
                                            <span class="label">{{ $spec->display_name }}:</span>
                                            <span class="value">{{ $spec->formatted_value }}</span>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <div class="product-action">
                                <a href="{{ route('product.show', $product) }}" class="view-product-btn">
                                    <i class="fas fa-eye mr-2"></i>
                                    View Product Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Form Section -->
            <div class="form-section">
            <!-- Success and Error Messages -->
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
            </div>
            @endif

            <!-- Display validation errors -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong>Please correct the following errors:</strong>
                    <ul class="mt-2 mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

                <!-- Size & Model Selection Display -->
                @if(request('size') || request('model'))
                <div class="size-display">
                    @if(request('size'))
                    <div class="mb-2">
                        <i class="fas fa-ruler-combined"></i>
                        <strong>Selected Size:</strong> {{ request('size') }}
                    </div>
                    @endif
                    @if(request('model'))
                    <div>
                        <i class="fas fa-tag"></i>
                        <strong>Selected Model:</strong> {{ request('model') }}
                    </div>
                    @endif
                </div>
                @endif

                <!-- Quotation Form -->
                    <form action="{{ route('quotation.store') }}" method="POST" id="quotationForm">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="user_id" value="{{ auth()->check() ? auth()->id() : 0 }}">
                        <input type="hidden" name="size" value="{{ request('size') }}" id="size-input">
                        <input type="hidden" name="model" value="{{ request('model') }}" id="model-input">

                    <!-- Contact Information Section -->
                    @if(!auth()->check())
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <h3 class="text-lg font-medium text-blue-900 mb-4">
                            <i class="fas fa-user mr-2"></i>Contact Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="form-group">
                                <label for="contact_name" class="form-label">
                                    <i class="fas fa-user mr-2"></i>Full Name
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="contact_name" 
                                       name="contact_name" 
                                       required 
                                       value="{{ old('contact_name') }}"
                                       placeholder="Your full name">
                            </div>

                            <div class="form-group">
                                <label for="contact_email" class="form-label">
                                    <i class="fas fa-envelope mr-2"></i>Email Address
                                </label>
                                <input type="email" 
                                       class="form-control" 
                                       id="contact_email" 
                                       name="contact_email" 
                                       required 
                                       value="{{ old('contact_email') }}"
                                       placeholder="your.email@company.com">
                            </div>

                            <div class="form-group">
                                <label for="contact_phone" class="form-label">
                                    <i class="fas fa-phone mr-2"></i>Phone Number
                                </label>
                                <input type="tel" 
                                       class="form-control" 
                                       id="contact_phone" 
                                       name="contact_phone" 
                                       value="{{ old('contact_phone') }}"
                                       placeholder="+971 50 123 4567">
                            </div>

                            <div class="form-group">
                                <label for="contact_company" class="form-label">
                                    <i class="fas fa-building mr-2"></i>Company Name
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="contact_company" 
                                       name="contact_company" 
                                       value="{{ old('contact_company') }}"
                                       placeholder="Your company name">
                            </div>
                            </div>
                        </div>
                        @endif

                    <!-- Product & Quantity Information -->
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            <i class="fas fa-box mr-2"></i>Product Requirements
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="form-group">
                                <label for="quantity" class="form-label">
                                    <i class="fas fa-hashtag mr-2"></i>Quantity Required
                                </label>
                                <input type="number" 
                                       class="form-control" 
                                       id="quantity" 
                                       name="quantity" 
                                       required 
                                       min="1"
                                       value="{{ old('quantity', 1) }}"
                                       placeholder="Enter quantity needed">
                        </div>

                            <div class="form-group">
                                <label for="delivery_timeline" class="form-label">
                                    <i class="fas fa-clock mr-2"></i>Delivery Timeline
                                </label>
                                <select class="form-control" id="delivery_timeline" name="delivery_timeline">
                                    <option value="">Select timeline</option>
                                    <option value="urgent" {{ old('delivery_timeline') == 'urgent' ? 'selected' : '' }}>Urgent (1-2 weeks)</option>
                                    <option value="standard" {{ old('delivery_timeline') == 'standard' ? 'selected' : '' }}>Standard (3-4 weeks)</option>
                                    <option value="flexible" {{ old('delivery_timeline') == 'flexible' ? 'selected' : '' }}>Flexible (1-2 months)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="requirements" class="form-label">
                            <i class="fas fa-list-ul mr-2"></i>Specific Requirements
                        </label>
                        <textarea class="form-control textarea-control" 
                                  id="requirements" 
                                  name="requirements" 
                                  rows="4"
                                  placeholder="Please specify any technical requirements, certifications needed, or customizations...">{{ old('requirements') }}</textarea>
                        </div>

                    <div class="form-group">
                        <label for="notes" class="form-label">
                            <i class="fas fa-sticky-note mr-2"></i>Additional Notes
                        </label>
                        <textarea class="form-control textarea-control" 
                                  id="notes" 
                                  name="notes" 
                                  rows="3"
                                  placeholder="Any additional information, questions, or special requests...">{{ old('notes') }}</textarea>
                        </div>

                    {{-- reCAPTCHA v2 - Only show in production --}}
                    @if(app()->environment('production'))
                    <div class="form-group">
                        <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                        @error('g-recaptcha-response')
                            <div class="text-red-600 text-sm mt-1">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>
                    @else
                    {{-- Hidden field for development environment --}}
                    <input type="hidden" name="g-recaptcha-response" value="dev-bypass">
                    @endif

                    <button type="submit" class="submit-btn" id="submitBtn">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Submit Quote Request
                    </button>
                    </form>
            </div>
        </div>
    </div>
</div>

                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const form = document.getElementById('quotationForm');
    const submitBtn = document.getElementById('submitBtn');
                        
    // Add loading state to submit button
                        form.addEventListener('submit', function(e) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Submitting Request...';
        

        
        // Re-enable button after 5 seconds in case of issues
        setTimeout(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Submit Quote Request';
        }, 5000);
    });
    
    // Auto-resize textareas
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(textarea => {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    });
    
    // Add floating label effect
    const inputs = document.querySelectorAll('.form-control');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        
        input.addEventListener('blur', function() {
            if (!this.value) {
                this.parentElement.classList.remove('focused');
            }
        });
        
        // Check if input has value on page load
        if (input.value) {
            input.parentElement.classList.add('focused');
                            }
                        });
                    });
                    </script>

{{-- Add reCAPTCHA script only in production --}}
@if(app()->environment('production'))
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endif

@endsection