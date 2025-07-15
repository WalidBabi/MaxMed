@extends('layouts.app')

@section('title', 'Quotation Request Confirmation | MaxMed UAE')
@section('meta_description', 'Your quotation request for ' . $product->name . ' has been submitted to MaxMed UAE. We will respond shortly with pricing and availability.')
@section('meta_robots', 'noindex, follow')

@section('content')
<style>
    .confirmation-container {
        min-height: 80vh;
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        position: relative;
        overflow: hidden;
    }

    .confirmation-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="%23e2e8f0" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
        opacity: 0.3;
        z-index: 0;
    }

    .confirmation-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
        position: relative;
        z-index: 1;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .confirmation-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 35px 60px -12px rgba(0, 0, 0, 0.2);
    }

    .success-header {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        padding: 2rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .success-header::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: pulse 3s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); opacity: 0.3; }
        50% { transform: scale(1.1); opacity: 0.6; }
    }

    .success-icon {
        width: 80px;
        height: 80px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        position: relative;
        z-index: 1;
        animation: iconBounce 0.6s ease-out;
    }

    @keyframes iconBounce {
        0% { transform: scale(0); }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); }
    }

    .success-icon i {
        font-size: 2.5rem;
        color: white;
        animation: checkmark 0.8s ease-out 0.3s both;
    }

    @keyframes checkmark {
        0% { transform: scale(0) rotate(-45deg); }
        50% { transform: scale(1.2) rotate(-45deg); }
        100% { transform: scale(1) rotate(0deg); }
    }

    .confirmation-body {
        padding: 3rem 2rem;
        text-align: center;
    }

    .product-info {
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        border-radius: 15px;
        padding: 1.5rem;
        margin: 2rem 0;
        border-left: 4px solid #10b981;
        position: relative;
        overflow: hidden;
    }

    .product-info::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, #10b981, #059669, #10b981);
        animation: shimmer 2s linear infinite;
    }

    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }

    .product-name {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 0.5rem;
    }

    .product-category {
        color: #64748b;
        font-size: 0.9rem;
    }

    .next-steps {
        background: #f8fafc;
        border-radius: 15px;
        padding: 2rem;
        margin: 2rem 0;
        border: 1px solid #e2e8f0;
    }

    .step-item {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
        padding: 0.75rem;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .step-item:hover {
        background: rgba(16, 185, 129, 0.05);
        transform: translateX(5px);
    }

    .step-number {
        width: 30px;
        height: 30px;
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.9rem;
        margin-right: 1rem;
        flex-shrink: 0;
    }

    .step-text {
        color: #374151;
        font-size: 0.95rem;
    }

    .action-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
        margin-top: 2rem;
    }

    .btn-primary-custom {
        background: linear-gradient(135deg, #0a5694 0%, #171e60 100%);
        color: white;
        border: none;
        padding: 0.75rem 2rem;
        border-radius: 10px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(10, 86, 148, 0.3);
        position: relative;
        overflow: hidden;
    }

    .btn-primary-custom::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }

    .btn-primary-custom:hover::before {
        left: 100%;
    }

    .btn-primary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(10, 86, 148, 0.4);
        color: white;
        text-decoration: none;
    }

    .btn-secondary-custom {
        background: white;
        color: #0a5694;
        border: 2px solid #0a5694;
        padding: 0.75rem 2rem;
        border-radius: 10px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .btn-secondary-custom:hover {
        background: #0a5694;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(10, 86, 148, 0.2);
        text-decoration: none;
    }

    .contact-info {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        border-radius: 15px;
        padding: 1.5rem;
        margin-top: 2rem;
        border-left: 4px solid #3b82f6;
    }

    .contact-info h6 {
        color: #1e40af;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .contact-info p {
        color: #1e3a8a;
        margin-bottom: 0.25rem;
        font-size: 0.9rem;
    }

    .floating-elements {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        pointer-events: none;
        z-index: 0;
    }

    .floating-element {
        position: absolute;
        opacity: 0.1;
        animation: float 6s ease-in-out infinite;
    }

    .floating-element:nth-child(1) {
        top: 10%;
        left: 10%;
        animation-delay: 0s;
    }

    .floating-element:nth-child(2) {
        top: 20%;
        right: 15%;
        animation-delay: 2s;
    }

    .floating-element:nth-child(3) {
        bottom: 20%;
        left: 20%;
        animation-delay: 4s;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(180deg); }
    }

    @media (max-width: 768px) {
        .confirmation-body {
            padding: 2rem 1rem;
        }
        
        .action-buttons {
            flex-direction: column;
            align-items: center;
        }
        
        .btn-primary-custom,
        .btn-secondary-custom {
            width: 100%;
            max-width: 300px;
        }
    }
</style>

<div class="confirmation-container py-5">
    <div class="floating-elements">
        <div class="floating-element">
            <i class="fas fa-flask" style="font-size: 2rem; color: #0a5694;"></i>
        </div>
        <div class="floating-element">
            <i class="fas fa-microscope" style="font-size: 1.5rem; color: #171e60;"></i>
        </div>
        <div class="floating-element">
            <i class="fas fa-dna" style="font-size: 1.8rem; color: #10b981;"></i>
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <!-- Success and Error Messages -->
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <div class="confirmation-card">
                    <div class="success-header">
                        <div class="success-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <h2 class="mb-2">Quotation Request Submitted!</h2>
                        <p class="mb-0 opacity-90">Thank you for your interest in our products</p>
                    </div>

                    <div class="confirmation-body">
                        <div class="product-info">
                            <div class="product-name">{{ $product->name }}</div>
                            @if($product->category)
                            <div class="product-category">{{ $product->category->name }}</div>
                            @endif
                        </div>

                        <p class="text-muted mb-4">
                            We have received your quotation request and our team will review it carefully. 
                            You can expect a response within 24-48 hours with detailed pricing and availability information.
                        </p>

                        <div class="next-steps">
                            <h5 class="mb-3 text-dark">What happens next?</h5>
                            <div class="step-item">
                                <div class="step-number">1</div>
                                <div class="step-text">Our sales team reviews your requirements</div>
                            </div>
                            <div class="step-item">
                                <div class="step-number">2</div>
                                <div class="step-text">We check current stock and pricing</div>
                            </div>
                            <div class="step-item">
                                <div class="step-number">3</div>
                                <div class="step-text">You receive a detailed quotation via email</div>
                            </div>
                            <div class="step-item">
                                <div class="step-number">4</div>
                                <div class="step-text">Follow up call to discuss any questions</div>
                            </div>
                        </div>

                        <div class="contact-info">
                            <h6><i class="fas fa-headset me-2"></i>Need immediate assistance?</h6>
                            <p><i class="fas fa-phone me-2"></i>Call us/whatsapp: +971 55 460 2500</p>
                            <p><i class="fas fa-envelope me-2"></i>Email: sales@maxmedme.com</p>
                        </div>

                        <div class="action-buttons">
                            <a href="{{ route('products.index') }}" class="btn-primary-custom">
                                <i class="fas fa-search me-2"></i>Browse More Products
                            </a>
                            <a href="{{ route('welcome') }}" class="btn-secondary-custom">
                                <i class="fas fa-home me-2"></i>Back to Home
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 