<div id="cookieConsent" class="fixed bottom-0 left-0 right-0 bg-white shadow-2xl border-t-2 border-blue-600 z-50 hidden" style="backdrop-filter: blur(10px); transform: translateY(100%); transition: transform 0.3s ease;">
    <div class="container mx-auto px-4 py-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex-1 text-gray-800 text-sm leading-relaxed">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 mt-1">
                        <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 mb-1">We use cookies to enhance your experience</p>
                        <p class="text-gray-600 text-xs sm:text-sm">By continuing to visit this site you agree to our use of cookies. 
                            <a href="{{ route('privacy.policy') }}" class="text-blue-600 hover:underline font-medium">Learn more</a>
                        </p>
                    </div>
                </div>
            </div>
            <div class="flex gap-3 w-full sm:w-auto">
                <button id="rejectCookies" class="flex-1 sm:flex-none bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-semibold shadow-sm hover:bg-gray-300 transition-colors duration-200 text-sm sm:text-base min-h-[44px]">
                    Reject
                </button>
                <button id="acceptCookies" class="flex-1 sm:flex-none bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold shadow-sm hover:bg-blue-700 transition-colors duration-200 text-sm sm:text-base min-h-[44px]">
                    Accept All
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

    // Check if user has already given consent
    const existingConsent = document.cookie.split('; ').find(row => row.startsWith('cookie_consent='));
    
    if (!existingConsent) {
        // Add a small delay to ensure smooth appearance
        setTimeout(() => {
            cookieConsent.classList.remove('hidden');
            cookieConsent.style.transform = 'translateY(0)';
        }, 500);
    }

    // Handle accept button
    acceptBtn.addEventListener('click', function() {
        // Set cookie with explicit attributes for better compatibility
        document.cookie = 'cookie_consent=accepted; path=/; SameSite=Lax; max-age=' + (60 * 60 * 24 * 365);
        
        // Smooth hide animation
        cookieConsent.style.transform = 'translateY(100%)';
        setTimeout(() => {
            cookieConsent.classList.add('hidden');
        }, 300);
        
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

    // Handle reject button
    rejectBtn.addEventListener('click', function() {
        document.cookie = 'cookie_consent=denied; path=/; SameSite=Lax; max-age=' + (60 * 60 * 24 * 30);
        
        // Smooth hide animation
        cookieConsent.style.transform = 'translateY(100%)';
        setTimeout(() => {
            cookieConsent.classList.add('hidden');
        }, 300);
        
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
                    consent: 'denied',
                    timestamp: new Date().toISOString()
                }
            })
        }).catch(error => console.log('Consent tracking failed:', error));
    });
});
</script>
@endpush
