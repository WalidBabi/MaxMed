<div id="cookieConsent" class="fixed bottom-6 left-0 right-0 mx-auto max-w-xl bg-white p-4 shadow-xl rounded-xl border border-gray-200 z-50 hidden" style="">
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="flex-1 text-gray-800 text-sm md:text-base">
            We use cookies to enhance your experience. By continuing to visit this site you agree to our use of cookies.
            <a href="{{ route('privacy.policy') }}" class="text-blue-600 hover:underline ml-1">Learn more</a>
        </div>
        <div class="flex mt-3 md:mt-0">
            <button id="acceptCookies" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold shadow hover:bg-blue-700 transition">
                Accept All
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const cookieConsent = document.getElementById('cookieConsent');
    const acceptBtn = document.getElementById('acceptCookies');

    // Check if user has already given consent
    const existingConsent = document.cookie.split('; ').find(row => row.startsWith('cookie_consent='));
    
    if (!existingConsent) {
        cookieConsent.classList.remove('hidden');
    }

    // Handle accept button
    acceptBtn.addEventListener('click', function() {
        // Set cookie with explicit attributes for better compatibility
        document.cookie = 'cookie_consent=accepted; path=/; SameSite=Lax; max-age=' + (60 * 60 * 24 * 365);
        
        // Debug: Verify cookie was set
        console.log('Cookie set:', document.cookie);
        console.log('Cookie consent check:', document.cookie.split('; ').find(row => row.startsWith('cookie_consent=')));
        
        cookieConsent.classList.add('hidden');
        
        // Send consent to backend
        fetch('/api/user-behavior/track', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({
                event_type: 'cookie_consent',
                page_url: window.location.href,
                event_data: {
                    consent: 'accepted',
                    timestamp: new Date().toISOString()
                }
            })
        }).then(response => {
            console.log('Consent tracking response:', response.status);
        }).catch(error => console.log('Consent tracking failed:', error));
        
        // Start tracking with a small delay to ensure cookie is set
        setTimeout(() => {
            if (window.startUserBehaviorTracking) {
                console.log('Starting user behavior tracking...');
                window.startUserBehaviorTracking();
            } else {
                console.log('startUserBehaviorTracking function not found');
            }
        }, 100);
    });
});
</script>
@endpush
