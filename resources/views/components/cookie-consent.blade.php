<div id="cookieConsent" class="fixed bottom-0 left-0 right-0 bg-gray-100 p-4 shadow-lg z-50 hidden">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row justify-between items-center">
            <div class="mb-4 md:mb-0 md:mr-4">
                <p class="text-gray-800">
                    We use cookies to enhance your experience. By continuing to visit this site you agree to our use of cookies.
                    <a href="{{ route('privacy.policy') }}" class="text-blue-600 hover:underline">Learn more</a>
                </p>
            </div>
            <div class="flex space-x-4">
                <button id="acceptCookies" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                    Accept All
                </button>
                <button id="rejectCookies" class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300 transition">
                    Reject
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const cookieConsent = document.getElementById('cookieConsent');
    const acceptBtn = document.getElementById('acceptCookies');
    const rejectBtn = document.getElementById('rejectCookies');

    // For debugging - always show the banner
    console.log('Cookie consent banner should be visible now');
    cookieConsent.classList.remove('hidden');
    
    // Original code (commented out for now)
    // if (!document.cookie.split('; ').find(row => row.startsWith('cookie_consent='))) {
    //     cookieConsent.classList.remove('hidden');
    // }

    // Handle accept button
    acceptBtn.addEventListener('click', function() {
        document.cookie = 'cookie_consent=accepted; path=/; max-age=' + (60 * 60 * 24 * 365);
        cookieConsent.classList.add('hidden');
    });

    // Handle reject button
    rejectBtn.addEventListener('click', function() {
        document.cookie = 'cookie_consent=denied; path=/; max-age=' + (60 * 60 * 24 * 30);
        cookieConsent.classList.add('hidden');
    });
});
</script>
@endpush
