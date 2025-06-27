@extends('layouts.app')

@section('title', 'Dental Consumables UAE - Dental Supplies Dubai | MaxMed')
@section('meta_description', '🦷 Premium dental consumables UAE! Impression materials, dental burs, disposables & more ✅ Same-day delivery Dubai ☎️ +971 55 460 2500')
@section('meta_keywords', 'dental consumables UAE, dental supplies Dubai, dental materials, impression materials UAE, dental burs Dubai, disposable dental items')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <!-- Hero Section -->
            <div class="bg-gradient-info text-white rounded-lg p-5 mb-5">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h1 class="display-4 fw-bold mb-3">🦷 Dental Consumables UAE</h1>
                        <p class="lead mb-4">Complete range of dental consumables and supplies in UAE. From impression materials to dental burs, we supply quality dental products to clinics, hospitals, and dental laboratories.</p>
                        <div class="d-flex flex-wrap gap-3">
                            <a href="#contact-form" class="btn btn-light btn-lg">
                                <i class="fas fa-phone me-2"></i>Order Now
                            </a>
                            <a href="#dental-products" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-tooth me-2"></i>View Catalog
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-4 text-center">
                        <div class="position-relative">
                            <img src="{{ asset('Images/dental-consumables-hero.jpg') }}" 
                                 alt="Dental Consumables UAE" 
                                 class="img-fluid rounded shadow-lg"
                                 style="max-height: 300px;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Why Choose MaxMed for Dental Supplies -->
            <div class="row mb-5">
                <div class="col-12">
                    <h2 class="text-center mb-4">Why Choose MaxMed for Dental Consumables?</h2>
                    <div class="row g-4">
                        <div class="col-md-3 text-center">
                            <div class="feature-icon mb-3">
                                <i class="fas fa-truck-fast fa-3x text-primary"></i>
                            </div>
                            <h5>Same-Day Delivery</h5>
                            <p class="text-muted">Quick delivery across Dubai and UAE</p>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="feature-icon mb-3">
                                <i class="fas fa-certificate fa-3x text-success"></i>
                            </div>
                            <h5>Quality Assured</h5>
                            <p class="text-muted">FDA approved and CE marked products</p>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="feature-icon mb-3">
                                <i class="fas fa-dollar-sign fa-3x text-info"></i>
                            </div>
                            <h5>Competitive Prices</h5>
                            <p class="text-muted">Best prices for bulk orders</p>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="feature-icon mb-3">
                                <i class="fas fa-users fa-3x text-warning"></i>
                            </div>
                            <h5>Expert Support</h5>
                            <p class="text-muted">Professional dental team assistance</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Categories -->
            <div id="dental-products" class="mb-5">
                <h2 class="text-center mb-4">Dental Consumables Categories</h2>
                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center">
                                <i class="fas fa-syringe fa-3x text-primary mb-3"></i>
                                <h5 class="card-title">Impression Materials</h5>
                                <ul class="list-unstyled text-start">
                                    <li>✅ Alginate impression materials</li>
                                    <li>✅ Silicone impression compounds</li>
                                    <li>✅ Polyether impression materials</li>
                                    <li>✅ Impression trays & accessories</li>
                                </ul>
                                <a href="#contact-form" class="btn btn-primary">Order Now</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center">
                                <i class="fas fa-tools fa-3x text-success mb-3"></i>
                                <h5 class="card-title">Dental Burs & Tools</h5>
                                <ul class="list-unstyled text-start">
                                    <li>✅ Diamond burs</li>
                                    <li>✅ Carbide burs</li>
                                    <li>✅ Surgical burs</li>
                                    <li>✅ Polishing instruments</li>
                                </ul>
                                <a href="#contact-form" class="btn btn-success">Order Now</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center">
                                <i class="fas fa-mask fa-3x text-info mb-3"></i>
                                <h5 class="card-title">Disposable Items</h5>
                                <ul class="list-unstyled text-start">
                                    <li>✅ Surgical masks & gloves</li>
                                    <li>✅ Disposable cups & tips</li>
                                    <li>✅ Dental bibs & covers</li>
                                    <li>✅ Sterilization pouches</li>
                                </ul>
                                <a href="#contact-form" class="btn btn-info">Order Now</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Product Categories -->
            <div class="row g-4 mb-5">
                <div class="col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <i class="fas fa-capsules fa-3x text-warning mb-3"></i>
                            <h5 class="card-title">Dental Materials</h5>
                            <ul class="list-unstyled text-start">
                                <li>✅ Composite resins</li>
                                <li>✅ Dental cement</li>
                                <li>✅ Bonding agents</li>
                                <li>✅ Temporary materials</li>
                            </ul>
                            <a href="#contact-form" class="btn btn-warning">Order Now</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <i class="fas fa-spray-can fa-3x text-danger mb-3"></i>
                            <h5 class="card-title">Oral Care Products</h5>
                            <ul class="list-unstyled text-start">
                                <li>✅ Fluoride treatments</li>
                                <li>✅ Mouth rinses</li>
                                <li>✅ Desensitizing agents</li>
                                <li>✅ Whitening products</li>
                            </ul>
                            <a href="#contact-form" class="btn btn-danger">Order Now</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <i class="fas fa-first-aid fa-3x text-secondary mb-3"></i>
                            <h5 class="card-title">Infection Control</h5>
                            <ul class="list-unstyled text-start">
                                <li>✅ Disinfectants</li>
                                <li>✅ Sterilization indicators</li>
                                <li>✅ Surface barriers</li>
                                <li>✅ Hand sanitizers</li>
                            </ul>
                            <a href="#contact-form" class="btn btn-secondary">Order Now</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Popular Brands -->
            <div class="bg-light rounded-lg p-5 mb-5">
                <h2 class="text-center mb-4">Popular Dental Brands We Supply</h2>
                <div class="row text-center">
                    <div class="col-md-2 mb-3">
                        <div class="brand-logo p-3">
                            <strong>3M ESPE</strong>
                        </div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <div class="brand-logo p-3">
                            <strong>Dentsply</strong>
                        </div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <div class="brand-logo p-3">
                            <strong>GC</strong>
                        </div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <div class="brand-logo p-3">
                            <strong>Ivoclar</strong>
                        </div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <div class="brand-logo p-3">
                            <strong>Kerr</strong>
                        </div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <div class="brand-logo p-3">
                            <strong>Ultradent</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div id="contact-form" class="bg-info text-white rounded-lg p-5">
                <div class="row">
                    <div class="col-lg-6">
                        <h3 class="mb-4">Order Dental Consumables Today!</h3>
                        <div class="mb-3">
                            <i class="fas fa-phone me-2"></i>
                            <strong>Call: +971 55 460 2500</strong>
                        </div>
                        <div class="mb-3">
                            <i class="fas fa-envelope me-2"></i>
                            <strong>Email: sales@maxmedme.com</strong>
                        </div>
                        <div class="mb-3">
                            <i class="fas fa-clock me-2"></i>
                            <strong>Same-Day Delivery Dubai</strong>
                        </div>
                        <div class="mb-3">
                            <i class="fas fa-percentage me-2"></i>
                            <strong>Bulk Discounts Available</strong>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <form class="bg-white text-dark p-4 rounded">
                            <h5 class="mb-3">Quick Order Form</h5>
                            <div class="mb-3">
                                <input type="text" class="form-control" placeholder="Clinic/Practice Name" required>
                            </div>
                            <div class="mb-3">
                                <input type="text" class="form-control" placeholder="Contact Person" required>
                            </div>
                            <div class="mb-3">
                                <input type="email" class="form-control" placeholder="Email Address" required>
                            </div>
                            <div class="mb-3">
                                <input type="tel" class="form-control" placeholder="Phone Number" required>
                            </div>
                            <div class="mb-3">
                                <select class="form-select" required>
                                    <option value="">Select Category</option>
                                    <option value="impression">Impression Materials</option>
                                    <option value="burs">Dental Burs & Tools</option>
                                    <option value="disposables">Disposable Items</option>
                                    <option value="materials">Dental Materials</option>
                                    <option value="oral-care">Oral Care Products</option>
                                    <option value="infection-control">Infection Control</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <textarea class="form-control" rows="3" placeholder="Product Requirements & Quantities"></textarea>
                            </div>
                            <button type="submit" class="btn btn-info w-100">
                                <i class="fas fa-shopping-cart me-2"></i>Submit Order Request
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- FAQ Section -->
            <div class="mt-5">
                <h2 class="text-center mb-4">Dental Consumables FAQ</h2>
                <div class="accordion" id="dentalFAQ">
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#dfaq1">
                                Do you offer bulk discounts for dental consumables?
                            </button>
                        </h3>
                        <div id="dfaq1" class="accordion-collapse collapse show" data-bs-parent="#dentalFAQ">
                            <div class="accordion-body">
                                Yes, we offer attractive bulk discounts for dental practices and clinics. Contact us for volume pricing on your regular consumables.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#dfaq2">
                                What is your delivery time across UAE?
                            </button>
                        </h3>
                        <div id="dfaq2" class="accordion-collapse collapse" data-bs-parent="#dentalFAQ">
                            <div class="accordion-body">
                                We offer same-day delivery in Dubai and next-day delivery across UAE for most dental consumables in stock.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#dfaq3">
                                Are all products FDA approved?
                            </button>
                        </h3>
                        <div id="dfaq3" class="accordion-collapse collapse" data-bs-parent="#dentalFAQ">
                            <div class="accordion-body">
                                Yes, all our dental consumables are from FDA approved manufacturers and carry appropriate certifications including CE marking where applicable.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('structured-data')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Product",
    "name": "Dental Consumables",
    "description": "Complete range of dental consumables and supplies in UAE. Impression materials, dental burs, disposables and more for dental practices.",
    "brand": {
        "@type": "Brand",
        "name": "MaxMed UAE"
    },
    "offers": {
        "@type": "Offer",
        "availability": "https://schema.org/InStock",
        "priceCurrency": "AED",
        "seller": {
            "@type": "Organization",
            "name": "MaxMed UAE",
            "telephone": "+971 55 460 2500",
            "address": {
                "@type": "PostalAddress",
                "addressCountry": "AE",
                "addressRegion": "Dubai"
            }
        }
    },
    "category": "Dental Supplies",
    "keywords": "dental consumables UAE, dental supplies Dubai, impression materials, dental burs, disposable dental items"
}
</script>
@endpush

@endsection 