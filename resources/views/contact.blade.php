@extends('layouts.app')
 
@section('title', 'Contact MaxMed - Medical & Laboratory Equipment Supplier Middle East & Africa')

@section('meta_description', 'Get in touch with MaxMed. Call us at +971 55 460 2500 or email sales@maxmedme.com. Leading supplier of premium medical and laboratory equipment across the Middle East, Africa, and global markets.')

@section('meta_keywords', 'contact MaxMed, MaxMed phone number, MaxMed email, medical equipment supplier contact, laboratory equipment supplier Middle East, diagnostic supplies Africa, healthcare solutions contact, medical equipment inquiry, laboratory technology support, global medical equipment supplier')
 
@push('scripts')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "ContactPage",
  "name": "Contact MaxMed UAE",
  "description": "Contact MaxMed for medical and laboratory equipment in the UAE and Middle East. Call +971 55 460 2500 or email sales@maxmedme.com",
  "mainEntityOfPage": {
    "@type": "WebPage",
    "@id": "https://maxmedme.com/contact"
  },
  "provider": {
    "@type": "MedicalBusiness",
    "name": "MaxMed UAE",
    "telephone": "+971 55 460 2500",
    "email": "sales@maxmedme.com",
    "url": "https://maxmedme.com",
    "areaServed": ["Dubai", "Abu Dhabi", "Sharjah", "Ajman", "UAE", "GCC"]
  },
  "contactPoint": [
    {
      "@type": "ContactPoint",
      "telephone": "+971 55 460 2500",
      "contactType": "customer service",
      "email": "sales@maxmedme.com",
      "availableLanguage": ["English", "Arabic"]
    }
  ]
}
</script>
@endpush

@section('content')
<style>
    /* Enhanced Hero Section */
    .contact-hero {
        position: relative;
        background-color: #171e60;
        color: white;
        padding: 5rem 0;
        overflow: hidden;
    }
    
    .contact-hero::before {
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
    
    .contact-hero::after {
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
        font-size: 3rem;

        margin-bottom: 1rem;
        opacity: 0;
        transform: translateY(20px);
        animation: fadeUp 0.8s ease forwards 0.2s;
        position: relative;
        z-index: 10;
    }
    
    .hero-subtitle {
        font-size: 1rem;
        opacity: 0;
        transform: translateY(20px);
        animation: fadeUp 0.8s ease forwards 0.4s;
        position: relative;
        z-index: 10;
    }
    
    /* Contact Section */
    .contact-section {
        padding: 5rem 0;
        background-color: #f8f9fa;
    }
    
    .contact-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        transition: all 0.4s ease;
        height: 100%;
    }
    
    .contact-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
    }
    
    .contact-form-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        position: relative;
        display: inline-block;
    }
    
    .contact-form-title::after {
        content: '';
        position: absolute;
        width: 60%;
        height: 3px;
        background: linear-gradient(to right, #171e60, #0a5694);
        bottom: -10px;
        left: 0;
        border-radius: 2px;
    }
    
    /* Form Styling */
    .form-group {
        margin-bottom: 1.5rem;
        opacity: 0;
        transform: translateY(15px);
    }
    
    .form-label {
        display: block;
        margin-bottom: 0.75rem;
        font-weight: 500;
        font-size: 0.875rem;
        color: #333;
    }
    
    .form-control {
        width: 100%;
        padding: 0.875rem 1.25rem;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background-color: #f8fafc;
        font-size: 1rem;
        transition: all 0.3s ease;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #0a5694;
        box-shadow: 0 0 0 3px rgba(10, 86, 148, 0.15);
        background-color: white;
    }
    
    .form-select {
        width: 100%;
        padding: 0.875rem 1.25rem;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background-color: #f8fafc;
        font-size: 1rem;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23333333'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 1rem center;
        background-size: 1.25rem;
        transition: all 0.3s ease;
    }
    
    .form-select:focus {
        outline: none;
        border-color: #0a5694;
        box-shadow: 0 0 0 3px rgba(10, 86, 148, 0.15);
        background-color: white;
    }
    
    .textarea-control {
        min-height: 120px;
        resize: vertical;
    }
    
    .submit-btn {
        width: 100%;
        padding: 1rem 1.5rem;
        background: linear-gradient(to right, #171e60, #0a5694);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        opacity: 0;
        transform: translateY(15px);
    }
    
    .submit-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(23, 30, 96, 0.3);
    }
    
    .submit-btn::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 5px;
        height: 5px;
        background: rgba(255, 255, 255, 0.5);
        opacity: 0;
        border-radius: 100%;
        transform: scale(1, 1) translate(-50%);
        transform-origin: 50% 50%;
    }
    
    .submit-btn:hover::after {
        animation: ripple 1s ease-out;
    }
    
    @keyframes ripple {
        0% {
            transform: scale(0, 0);
            opacity: 0.5;
        }
        100% {
            transform: scale(20, 20);
            opacity: 0;
        }
    }
    
    /* Contact Info Styling */
    .info-card {
        background: white;
        border-radius: 12px;
        padding: 1.75rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
        border-left: 4px solid #171e60;
        transition: all 0.3s ease;
        opacity: 0;
        transform: translateX(20px);
    }
    
    .info-card:hover {
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        border-left-color: #0a5694;
    }
    
    .info-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
    }
    
    .info-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: rgba(23, 30, 96, 0.1);
        color: #171e60;
        margin-right: 1rem;
        transition: all 0.3s ease;
    }
    
    .info-card:hover .info-icon {
        background-color: #171e60;
        color: white;
        transform: scale(1.1);
    }
    
    .info-text {
        color: #555;
        line-height: 1.7;
        font-size: 0.875rem;
    }
    
    .info-link {
        color: #0a5694;
        font-weight: 500;
        transition: all 0.2s ease;
        display: inline-block;
    }
    
    .info-link:hover {
        color: #171e60;
        transform: translateX(3px);
    }
    
    /* Success Message */
    .success-message {
        background-color: #d1fae5;
        border: 1px solid #10b981;
        color: #065f46;
        padding: 1rem;
        border-radius: 8px;
        margin-top: 1.5rem;
        display: flex;
        align-items: center;
        opacity: 0;
        transform: translateY(10px);
        animation: fadeUp 0.5s ease forwards;
    }
    
    .success-icon {
        margin-right: 0.75rem;
        font-size: 1.25rem;
        color: #10b981;
    }
    
    /* Error Message */
    .error-message {
        background-color: #fee2e2; /* Light red */
        border: 1px solid #ef4444; /* Red */
        color: #b91c1c; /* Darker red */
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem; /* Add some space below */
        opacity: 0;
        transform: translateY(10px);
        animation: fadeUp 0.5s ease forwards;
    }
    
    .error-message ul {
        margin: 0;
        padding-left: 1.5rem;
    }
    
    .error-icon {
        margin-right: 0.75rem;
        font-size: 1.25rem;
        color: #ef4444; /* Red */
    }
    
    /* Animations */
    @keyframes fadeUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes fadeRight {
        from {
            opacity: 0;
            transform: translateX(20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    /* Responsive styles */
    @media (max-width: 992px) {
        .hero-title {
            font-size: 2.5rem;
        }
        
        .contact-section {
            padding: 4rem 0;
        }
    }
    
    @media (max-width: 768px) {
        .hero-title {
            font-size: 2rem;
        }
        
        .hero-subtitle {
            font-size: 1.1rem;
        }
        
        .contact-section {
            padding: 3rem 0;
        }
        
        .contact-info {
            margin-top: 2.5rem;
        }
    }
    
    @media (max-width: 576px) {
        .contact-hero {
            padding: 3.5rem 0;
        }
        
        .hero-title {
            font-size: 1.75rem;
        }
        
        .form-group, .info-card {
            margin-bottom: 1.25rem;
        }
        
        /* Add these new styles */
        input, select, textarea {
            font-size: 16px !important; /* Prevents zoom on iOS */
            padding: 12px !important;
            margin-bottom: 15px;
            border-radius: 8px;
        }
        
        .action-button, button[type="submit"] {
            width: 100%;
            padding: 14px;
            margin-top: 10px;
        }
    }
</style>
<!-- Hero Section -->
<div class="contact-hero">
    <div class="max-w-7xl mx-auto px-4">
        <h1 class="hero-title">Contact Us</h1>
        <p class="hero-subtitle">Get in touch with our team for any inquiries or support</p>
    </div>
</div>

<!-- Contact Section -->
<div class="contact-section">
    <div class="max-w-7xl mx-auto px-4">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
            <!-- Contact Form -->
            <div x-data="{ 
                submitted: false,
                formActive: true,
                initForm() {
                    const formGroups = document.querySelectorAll('.form-group');
                    const submitBtn = document.querySelector('.submit-btn');
                    
                    formGroups.forEach((group, index) => {
                        setTimeout(() => {
                            group.style.opacity = '1';
                            group.style.transform = 'translateY(0)';
                        }, 100 + (index * 100));
                    });
                    
                    setTimeout(() => {
                        submitBtn.style.opacity = '1';
                        submitBtn.style.transform = 'translateY(0)';
                    }, 100 + (formGroups.length * 100));
                }
            }" x-init="initForm()">
                <div class="contact-card p-8">
                    <h2 class="contact-form-title">Send us a Message</h2>
                    
                    {{-- Success Message --}}
                    @if(session('success'))
                        <div class="success-message">
                            <div class="success-icon">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div>
                                {{ session('success') }}
                            </div>
                        </div>
                    @endif

                    {{-- Error Message --}}
                    @if(session('error'))
                        <div class="error-message">
                            <div class="error-icon">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                {{ session('error') }}
                            </div>
                        </div>
                    @endif

                    {{-- Validation Errors --}}
                    @if($errors->any())
                        <div class="error-message">
                            <div class="error-icon">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <strong>Please fix the following errors:</strong>
                                <ul>
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                    
                    <form action="{{ route('contact.submit') }}" method="POST" x-data="{ submitting: false }" @submit.prevent="
                        submitting = true;
                        $event.target.submit();
                    ">
                        @csrf
                        <div class="form-group" style="transition: all 0.4s ease;">
                            <label class="form-label" for="name">Name</label>
                            <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>
                        <div class="form-group" style="transition: all 0.4s ease;">
                            <label class="form-label" for="email">Email</label>
                            <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required>
                        </div>
                        <div class="form-group" style="transition: all 0.4s ease;">
                            <label class="form-label" for="phone">Phone (Optional)</label>
                            <input type="tel" id="phone" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="+971 50 123 4567">
                        </div>
                        <div class="form-group" style="transition: all 0.4s ease;">
                            <label class="form-label" for="company">Company (Optional)</label>
                            <input type="text" id="company" name="company" class="form-control" value="{{ old('company') }}" placeholder="Your company name">
                        </div>
                        <div class="form-group" style="transition: all 0.4s ease;">
                            <label class="form-label" for="subject">Subject</label>
                            <select id="subject" name="subject" class="form-select" required>
                                <option value="">Select a subject</option>
                                <option value="sales inquiry" {{ old('subject') == 'sales inquiry' ? 'selected' : '' }}>Sales Inquiry</option>
                                <option value="technical support" {{ old('subject') == 'technical support' ? 'selected' : '' }}>Technical Support</option>
                                <option value="service request" {{ old('subject') == 'service request' ? 'selected' : '' }}>Service Request</option>
                                <option value="partnership" {{ old('subject') == 'partnership' ? 'selected' : '' }}>Partnership</option>
                                <option value="other" {{ old('subject') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="form-group" style="transition: all 0.4s ease;">
                            <label class="form-label" for="message">Message</label>
                            <textarea id="message" name="message" class="form-control textarea-control" required>{{ old('message') }}</textarea>
                        </div>

                        <!-- Hidden field for recipient email -->
                        <input type="hidden" name="recipient" value="{{ app()->environment('production') ? 'sales@maxmedme.com' : 'wbabi@localhost.com' }}">
                        
                        {{-- reCAPTCHA v2 - Only show in production --}}
                        @if(app()->environment('production'))
                        <div class="form-group" style="transition: all 0.4s ease;">
                           <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                        </div>
                        @else
                        {{-- Hidden field for development environment --}}
                        <input type="hidden" name="g-recaptcha-response" value="dev-bypass">
                        @endif
                        
                        <button type="submit" class="submit-btn" style="transition: all 0.4s ease;"
                                x-bind:disabled="submitting">
                            <span x-show="!submitting">Send Message</span>
                            <span x-show="submitting">Sending...</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="contact-info">
                <div x-data="{
                    initInfo() {
                        const infoCards = document.querySelectorAll('.info-card');
                        
                        infoCards.forEach((card, index) => {
                            setTimeout(() => {
                                card.style.opacity = '1';
                                card.style.transform = 'translateX(0)';
                            }, 100 + (index * 150));
                        });
                    }
                }" x-init="initInfo()">
                    <!-- Office Location -->
                    <div class="info-card" style="transition: all 0.4s ease;">
                        <h3 class="info-title">
                            <div class="info-icon">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            Office Location
                        </h3>
                        <p class="info-text">
                            MAXMED SCIENTIFIC & LABORATORY EQUIPMENT TRADING CO. L.L.C<br>
                            Dubai Al Barsha<br>
                            Dubai, United Arab Emirates
                        </p>
                    </div>

                    <!-- Contact Details -->
                    <div class="info-card" style="transition: all 0.4s ease;">
                        <h3 class="info-title">
                            <div class="info-icon">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </div>
                            Contact Details
                        </h3>
                        <div class="space-y-3">
                            <p class="info-text">
                                Email: <a href="mailto:sales@maxmedme.com" class="info-link">sales@maxmedme.com</a>
                            </p>
                            <p class="info-text">
                                Phone: <a href="tel:+97155460250" class="info-link">+971 55 460 2500</a>
                            </p>
                        </div>
                    </div>

                    <!-- Business Hours -->
                    <div class="info-card" style="transition: all 0.4s ease;">
                        <h3 class="info-title">
                            <div class="info-icon">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            Business Hours
                        </h3>
                        <div class="space-y-2">
                            <p class="info-text">Monday - Friday: 9:00 AM - 6:00 PM</p>
                            <p class="info-text">Saturday: 10:00 AM - 2:00 PM</p>
                            <p class="info-text">Sunday: Closed</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // This script is only used if Alpine.js is not loading properly
        setTimeout(function() {
            document.querySelectorAll('.form-group').forEach((group, index) => {
                setTimeout(() => {
                    group.style.opacity = '1';
                    group.style.transform = 'translateY(0)';
                }, 100 + (index * 100));
            });
            
            setTimeout(() => {
                const submitBtn = document.querySelector('.submit-btn');
                if (submitBtn) {
                    submitBtn.style.opacity = '1';
                    submitBtn.style.transform = 'translateY(0)';
                }
            }, 500);
            
            document.querySelectorAll('.info-card').forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateX(0)';
                }, 100 + (index * 150));
            });
        }, 100);

        // Auto-hide success message after 8 seconds
        const successMessage = document.querySelector('.success-message');
        if (successMessage) {
            setTimeout(() => {
                successMessage.style.transition = 'opacity 0.5s ease';
                successMessage.style.opacity = '0';
                setTimeout(() => {
                    successMessage.style.display = 'none';
                }, 500);
            }, 8000);
        }

        // Auto-hide error message after 10 seconds
        const errorMessage = document.querySelector('.error-message');
        if (errorMessage) {
            setTimeout(() => {
                errorMessage.style.transition = 'opacity 0.5s ease';
                errorMessage.style.opacity = '0';
                setTimeout(() => {
                    errorMessage.style.display = 'none';
                }, 500);
            }, 10000);
        }
    });
</script>

{{-- Add reCAPTCHA script only in production --}}
@if(app()->environment('production'))
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endif

@endsection 