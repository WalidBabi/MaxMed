<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "MedicalBusiness",
    "name": "MaxMed UAE",
    "url": "https://maxmedme.com",
    "logo": "{{ asset('Images/MaxTest-logo.png') }}",
    "description": "Leading supplier of medical and laboratory equipment in Dubai, UAE",
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
        "email": "cs@maxmedme.com",
        "availableLanguage": ["English", "Arabic"]
    },
    "areaServed": ["Dubai", "Abu Dhabi", "Sharjah", "UAE"],
    "openingHoursSpecification": {
        "@type": "OpeningHoursSpecification",
        "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"],
        "opens": "09:00",
        "closes": "18:00"
    }
}
</script> 