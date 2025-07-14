{{-- FAQ Section Component for Rich Snippets --}}
@props(['faqs' => [], 'title' => 'Frequently Asked Questions'])

<div class="faq-section bg-light py-5">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="text-center mb-5">{{ $title }}</h2>
                
                <div class="accordion" id="faqAccordion">
                    @foreach($faqs as $index => $faq)
                    <div class="accordion-item">
                        <h3 class="accordion-header" id="faq-heading-{{ $index }}">
                            <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" type="button" 
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#faq-collapse-{{ $index }}" 
                                    aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" 
                                    aria-controls="faq-collapse-{{ $index }}">
                                {{ $faq['question'] }}
                            </button>
                        </h3>
                        <div id="faq-collapse-{{ $index }}" 
                             class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" 
                             aria-labelledby="faq-heading-{{ $index }}" 
                             data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                {{ $faq['answer'] }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

{{-- FAQ Schema for Rich Snippets --}}
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
        @foreach($faqs as $index => $faq)
        {
            "@type": "Question",
            "name": "{{ $faq['question'] }}",
            "acceptedAnswer": {
                "@type": "Answer",
                "text": "{{ $faq['answer'] }}"
            }
        }@if(!$loop->last),@endif
        @endforeach
    ]
}
</script>

<style>
.faq-section {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.accordion-item {
    border: none;
    margin-bottom: 1rem;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.accordion-button {
    background-color: white;
    border: none;
    padding: 1.25rem;
    font-weight: 600;
    color: var(--brand-main);
    font-size: 1.1rem;
    line-height: 1.4;
}

.accordion-button:not(.collapsed) {
    background-color: var(--brand-main);
    color: white;
    box-shadow: none;
}

.accordion-button:focus {
    box-shadow: 0 0 0 0.25rem rgba(23, 30, 96, 0.25);
}

.accordion-button::after {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23171e60'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
}

.accordion-button:not(.collapsed)::after {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23ffffff'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
}

.accordion-body {
    padding: 1.5rem;
    background-color: white;
    color: #495057;
    line-height: 1.6;
    font-size: 1rem;
}

/* Mobile Optimizations */
@media (max-width: 768px) {
    .accordion-button {
        padding: 1rem;
        font-size: 1rem;
    }
    
    .accordion-body {
        padding: 1.25rem;
        font-size: 0.95rem;
    }
    
    .faq-section {
        padding: 3rem 0;
    }
}

/* Hover Effects */
.accordion-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    transition: all 0.3s ease;
}

/* Accessibility */
.accordion-button:focus-visible {
    outline: 2px solid var(--brand-main);
    outline-offset: 2px;
}

/* Print Styles */
@media print {
    .accordion-button {
        background-color: white !important;
        color: black !important;
    }
    
    .accordion-collapse {
        display: block !important;
    }
}
</style> 