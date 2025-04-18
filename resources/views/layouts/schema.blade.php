<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "MedicalBusiness",
    "name": "MaxMed UAE",
    "url": "https://maxmedme.com",
    "logo": "{{ asset('Images/MaxTest-logo.png') }}",
    "description": "Leading supplier of laboratory equipment and medical supplies in Dubai, UAE. We provide high-quality scientific instruments, diagnostic tools, and lab technology for healthcare and research facilities.",
    "address": {
        "@type": "PostalAddress",
        "addressCountry": "UAE",
        "addressLocality": "Dubai",
        "streetAddress": "{{ config('app.address') }}"
    },
    "geo": {
        "@type": "GeoCoordinates",
        "latitude": "25.2048",
        "longitude": "55.2708"
    },
    "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+971 55 460 2500",
        "contactType": "customer service",
        "email": "sales@maxmedme.com",
        "availableLanguage": ["English", "Arabic"]
    },
    "areaServed": ["Dubai", "Abu Dhabi", "Sharjah", "Ajman", "UAE", "GCC"],
    "openingHoursSpecification": {
        "@type": "OpeningHoursSpecification",
        "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"],
        "opens": "09:00",
        "closes": "18:00"
    },
    "sameAs": [
        "https://www.facebook.com/maxmeduae",
        "https://www.linkedin.com/company/maxmeduae",
        "https://www.instagram.com/maxmeduae"
    ],
    "keywords": "laboratory equipment Dubai, medical supplies UAE, scientific instruments, lab technology, diagnostic equipment, research lab supplies, hospital equipment, medical laboratory setup",
    "makesOffer": [
        {
            "@type": "Offer",
            "itemOffered": {
                "@type": "Product",
                "name": "Laboratory Equipment",
                "description": "High-quality laboratory equipment for medical and research facilities"
            }
        },
        {
            "@type": "Offer",
            "itemOffered": {
                "@type": "Product",
                "name": "Scientific Instruments",
                "description": "Precision scientific instruments for laboratory analysis and research"
            }
        },
        {
            "@type": "Offer",
            "itemOffered": {
                "@type": "Product",
                "name": "Medical Supplies",
                "description": "Essential medical supplies for hospitals and healthcare facilities"
            }
        },
        {
            "@type": "Offer",
            "itemOffered": {
                "@type": "Product",
                "name": "Diagnostic Equipment",
                "description": "Advanced diagnostic tools and equipment for medical testing"
            }
        }
    ]
}
</script> 