// Handle Google One Tap response
function handleCredentialResponse(response) {
    const container = document.getElementById('google-one-tap-container');
    if (container) container.style.display = 'none';
    
    const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
    if (!csrfTokenElement) {
        console.error('CSRF token not found');
        if (container) container.style.display = 'block';
        return;
    }
    
    const csrfToken = csrfTokenElement.getAttribute('content');
    
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
    if (!container) {
        console.log('Google One Tap container not found');
        return;
    }

    // Check if we're in development mode
    const isDevelopment = window.location.hostname === 'localhost' || 
                         window.location.hostname === '127.0.0.1' ||
                         window.location.hostname.includes('localhost');

    // Make sure the Google API is loaded
    if (!window.google || !window.google.accounts || !window.google.accounts.id) {
        console.error('Google One Tap API not loaded');
        return;
    }

    // Get client ID from data attribute
    const gIdOnload = document.querySelector('#g_id_onload');
    if (!gIdOnload || !gIdOnload.dataset.client_id) {
        console.error('Google One Tap client ID not found');
        return;
    }

    // Configure Google One Tap with development-friendly settings
    const config = {
        client_id: gIdOnload.dataset.client_id,
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

    try {
        window.google.accounts.id.initialize(config);
        
        // Show the One Tap UI
        window.google.accounts.id.prompt((notification) => {
            if (notification.isNotDisplayed()) {
                console.log('One Tap prompt was not displayed:', notification.getNotDisplayedReason());
                if (container) container.style.display = 'block';
            } else if (notification.isSkippedMoment()) {
                console.log('One Tap prompt was skipped:', notification.getSkippedReason());
                if (container) container.style.display = 'block';
            } else {
                if (container) container.style.display = 'block';
            }
        });

        // Show the container after a short delay if not shown by the prompt
        setTimeout(() => {
            if (container && container.style.display !== 'block') {
                container.style.display = 'block';
            }
        }, 1000);
    } catch (error) {
        console.error('Error initializing Google One Tap:', error);
        if (container) container.style.display = 'block';
    }
}

// Check if user is authenticated using meta tag
function isUserAuthenticated() {
    const meta = document.querySelector('meta[name="user-authenticated"]');
    return meta && meta.content === 'true';
}

// Hide the Google One Tap container
function hideGoogleOneTapContainer() {
    const container = document.getElementById('google-one-tap-container');
    if (container) {
        container.style.animation = 'fadeOut 0.3s ease-out forwards';
        setTimeout(() => {
            container.style.display = 'none';
            // Remove click outside event listener if it exists
            if (typeof handleClickOutside === 'function') {
                document.removeEventListener('click', handleClickOutside);
            }
        }, 300);
    }
}

// Show the Google One Tap container
function showGoogleOneTapContainer() {
    const container = document.getElementById('google-one-tap-container');
    if (container) {
        container.style.display = 'block';
        container.style.animation = 'slideIn 0.3s ease-out forwards';
        
        // Add click outside to close
        setTimeout(() => {
            if (typeof handleClickOutside === 'function') {
                document.addEventListener('click', handleClickOutside);
            }
        }, 100);
    }
}

// Handle clicks outside the container
function handleClickOutside(event) {
    const container = document.getElementById('google-one-tap-container');
    const button = document.querySelector('.g_id_signin iframe');
    
    if (container && !container.contains(event.target) && 
        button && !button.contains(event.target)) {
        hideGoogleOneTapContainer();
    }
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
