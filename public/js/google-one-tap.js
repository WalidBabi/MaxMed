// Handle Google One Tap response
function handleCredentialResponse(response) {
    const container = document.getElementById('google-one-tap-container');
    if (container) container.style.display = 'none';
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch('/google/one-tap', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            'g_csrf_token': csrfToken,
            'credential': response.credential
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.redirect) {
            window.location.href = data.redirect;
        } else if (data.error) {
            throw new Error(data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (container) container.style.display = 'block';
        alert('Failed to sign in with Google. Please try again.');
    });
}

// Initialize Google One Tap
function initializeOneTap() {
    const container = document.getElementById('google-one-tap-container');
    if (!container) return;

    // Check if we're in development mode
    const isDevelopment = window.location.hostname === 'localhost' || 
                         window.location.hostname === '127.0.0.1' ||
                         window.location.hostname.includes('localhost');

    // Make sure the Google API is loaded
    if (!window.google || !window.google.accounts || !window.google.accounts.id) {
        console.error('Google One Tap API not loaded');
        return;
    }

    // Configure Google One Tap with development-friendly settings
    const config = {
        client_id: document.querySelector('#g_id_onload').dataset.client_id,
        callback: handleCredentialResponse,
        context: 'signin',
        ux_mode: 'popup',
        itp_support: true
    };

    // In development, add additional configuration
    if (isDevelopment) {
        config.auto_select = false;
        config.cancel_on_tap_outside = false;
        config.auto_prompt = false;
        console.log('🚀 Google One Tap: Development mode detected');
    }

    window.google.accounts.id.initialize(config);
    
    // Show the One Tap UI
    window.google.accounts.id.prompt((notification) => {
        if (notification.isNotDisplayed()) {
            console.log('One Tap prompt was not displayed:', notification.getNotDisplayedReason());
            container.style.display = 'block';
        } else if (notification.isSkippedMoment()) {
            console.log('One Tap prompt was skipped:', notification.getSkippedReason());
            container.style.display = 'block';
        } else {
            container.style.display = 'block';
        }
    });

    // Show the container after a short delay if not shown by the prompt
    setTimeout(() => {
        if (container && container.style.display !== 'block') {
            container.style.display = 'block';
        }
    }, 1000);
}

// Check if user is authenticated using meta tag
function isUserAuthenticated() {
    const meta = document.querySelector('meta[name="user-authenticated"]');
    return meta && meta.content === 'true';
}

// Initialize when the page loads
document.addEventListener('DOMContentLoaded', function() {
    // Only initialize if user is not authenticated
    if (!isUserAuthenticated()) {
        // Small delay to ensure Google API is loaded
        const checkGoogle = setInterval(() => {
            if (window.google && window.google.accounts && window.google.accounts.id) {
                clearInterval(checkGoogle);
                initializeOneTap();
            }
        }, 100);
        
        // Fallback in case the Google API takes too long
        setTimeout(() => {
            if (!window.google || !window.google.accounts || !window.google.accounts.id) {
                console.error('Google API not loaded after timeout');
                const container = document.getElementById('google-one-tap-container');
                if (container) container.style.display = 'block';
            }
        }, 3000);
    }
});
