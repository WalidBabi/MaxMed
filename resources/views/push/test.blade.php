@extends('layouts.app')

@push('head')
<script>
    // Disable user behavior tracking on push test page
    window.userBehaviorTrackingDisabled = true;
</script>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold mb-4">Push Notification Test</h1>
            
            <div class="mb-6">
                <div class="p-4 bg-blue-50 rounded-lg mb-4">
                    <p class="text-sm text-gray-700"><strong>Your active subscriptions:</strong> <span id="subCount">{{ $subscriptionCount }}</span></p>
                    @if($userId)
                        <p class="text-sm text-gray-700"><strong>User ID:</strong> {{ $userId }}</p>
                    @endif
                    <p class="text-sm text-gray-700 mt-2"><strong>Notification Permission:</strong> <span id="permissionStatus">Checking...</span></p>
                    <p class="text-sm text-gray-700"><strong>Service Worker:</strong> <span id="swStatus">Checking...</span></p>
                    <p class="text-sm text-gray-700"><strong>Status:</strong> <span id="subscriptionStatus">Initializing...</span></p>
                </div>

                @if($subscriptionCount === 0)
                    <div class="p-4 bg-yellow-50 rounded-lg mb-4">
                        <p class="text-sm text-yellow-800">
                            <strong>No subscriptions found.</strong> Make sure you've allowed notifications in your browser. 
                            The page will attempt to subscribe automatically when you interact with it (tap, scroll, etc.).
                        </p>
                    </div>
                @endif
                
                <div id="permissionDeniedHelp" class="hidden p-4 bg-red-50 rounded-lg mb-4 border border-red-200">
                    <p class="text-sm font-semibold text-red-900 mb-2">⚠️ Notifications are currently blocked</p>
                    <p class="text-sm text-red-800 mb-3">To enable push notifications, you need to allow notifications in your browser settings:</p>
                    <div class="text-sm text-red-700 space-y-2">
                        <p><strong>On Mobile:</strong></p>
                        <ul class="list-disc list-inside ml-2 space-y-1">
                            <li><strong>Chrome (Android):</strong> Menu → Settings → Site Settings → Notifications → Allow</li>
                            <li><strong>Safari (iOS):</strong> Settings → Safari → Notifications → Allow</li>
                            <li><strong>Firefox (Android):</strong> Menu → Settings → Notifications → Allow</li>
                        </ul>
                        <p class="mt-2"><strong>On Desktop:</strong></p>
                        <ul class="list-disc list-inside ml-2 space-y-1">
                            <li><strong>Chrome:</strong> Click the lock/info icon in address bar → Site settings → Notifications → Allow</li>
                            <li><strong>Firefox:</strong> Click the lock icon → More Information → Permissions → Notifications → Allow</li>
                            <li><strong>Safari:</strong> Safari → Settings for This Website → Notifications → Allow</li>
                        </ul>
                        <p class="mt-3 text-red-800 font-medium">After enabling notifications, refresh this page.</p>
                    </div>
                    <button type="button" onclick="window.location.reload()" class="mt-3 px-4 py-2 bg-red-600 text-white text-sm rounded-md hover:bg-red-700">
                        Refresh Page After Enabling
                    </button>
                </div>
                
                <div id="pwaInfo" class="hidden p-4 bg-blue-50 rounded-lg mb-4 border border-blue-200">
                    <p class="text-sm font-semibold text-blue-900 mb-2">ℹ️ App Mode Detected</p>
                    <p class="text-sm text-blue-800 mb-2">
                        This site is running as an installed app (PWA). Push notifications work the same way in app mode.
                    </p>
                    <p class="text-sm text-blue-700">
                        If notifications are blocked, you may need to enable them in the app settings rather than browser settings.
                    </p>
                </div>
            </div>

            <form id="testForm" class="space-y-4">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input type="text" id="title" name="title" value="MaxMed" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="body" class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                    <input type="text" id="body" name="body" value="Test notification from MaxMed!" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="url" class="block text-sm font-medium text-gray-700 mb-1">URL (when clicked)</label>
                    <input type="text" id="url" name="url" value="/" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed" {{ $subscriptionCount === 0 ? 'disabled' : '' }}>
                    Send Test Notification
                </button>
            </form>

            <div id="result" class="mt-4 hidden"></div>
        </div>

        <div class="mt-6 bg-gray-50 rounded-lg p-4">
            <h2 class="text-lg font-semibold mb-2">Instructions</h2>
            <ol class="list-decimal list-inside space-y-1 text-sm text-gray-700">
                <li>Make sure you've allowed notifications in your browser</li>
                <li>If this is the first visit, refresh the page to register your device</li>
                <li>Fill in the form above and click "Send Test Notification"</li>
                <li>You should receive a notification even if the browser tab is closed</li>
            </ol>
        </div>
    </div>
</div>

<script>
// Debug and subscription status on test page
(function() {
    const updateStatus = (id, text, color = 'text-gray-700') => {
        const el = document.getElementById(id);
        if (el) {
            el.textContent = text;
            el.className = `text-sm ${color}`;
        }
    };

    const checkStatus = async () => {
        // Check notification permission
        if ('Notification' in window) {
            const perm = Notification.permission;
            const permText = perm === 'granted' ? '✅ Granted' : perm === 'denied' ? '❌ Denied (blocked - check browser settings)' : '⏳ Default (not asked)';
            const permColor = perm === 'granted' ? 'text-green-600' : perm === 'denied' ? 'text-red-600' : 'text-yellow-600';
            updateStatus('permissionStatus', permText, permColor);
            
            // Show help message if denied
            const helpDiv = document.getElementById('permissionDeniedHelp');
            if (helpDiv) {
                if (perm === 'denied') {
                    helpDiv.classList.remove('hidden');
                } else {
                    helpDiv.classList.add('hidden');
                }
            }
        } else {
            updateStatus('permissionStatus', '❌ Not supported', 'text-red-600');
        }

        // Check service worker
        if ('serviceWorker' in navigator) {
            try {
                const registration = await navigator.serviceWorker.getRegistration();
                if (registration) {
                    updateStatus('swStatus', '✅ Registered', 'text-green-600');
                    
                    // Check for subscription
                    const subscription = await registration.pushManager.getSubscription();
                    if (subscription) {
                        updateStatus('subscriptionStatus', '✅ Has browser subscription', 'text-green-600');
                    } else {
                        updateStatus('subscriptionStatus', '⚠️ No browser subscription yet', 'text-yellow-600');
                    }
                } else {
                    updateStatus('swStatus', '⚠️ Not registered', 'text-yellow-600');
                    updateStatus('subscriptionStatus', '⚠️ Service worker not ready', 'text-yellow-600');
                }
            } catch (e) {
                updateStatus('swStatus', '❌ Error checking', 'text-red-600');
                updateStatus('subscriptionStatus', '❌ Error: ' + e.message, 'text-red-600');
            }
        } else {
            updateStatus('swStatus', '❌ Not supported', 'text-red-600');
            updateStatus('subscriptionStatus', '❌ Service workers not supported', 'text-red-600');
        }
        
        // Check if running as PWA
        if (window.matchMedia && window.matchMedia('(display-mode: standalone)').matches) {
            const pwaInfo = document.getElementById('pwaInfo');
            if (pwaInfo) {
                pwaInfo.classList.remove('hidden');
            }
        }
    };

    // Check status on load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', checkStatus);
    } else {
        checkStatus();
    }

    // Try to subscribe immediately if permission is granted (only if not already done)
    // Check subscription count first - if > 0, don't auto-subscribe (already done)
    const subscriptionCountEl = document.getElementById('subCount');
    const currentCount = subscriptionCountEl ? parseInt(subscriptionCountEl.textContent) || 0 : 0;
    
    if ('serviceWorker' in navigator && 'PushManager' in window && currentCount === 0) {
        window.addEventListener('load', async () => {
            try {
                // Wait for service worker and layout code
                setTimeout(async () => {
                    // Check if we already handled this (prevent multiple attempts)
                    if (sessionStorage.getItem('pushSubscriptionAttempted')) {
                        return; // Already attempted, don't try again
                    }
                    
                    if (Notification.permission === 'granted') {
                        updateStatus('subscriptionStatus', '🔄 Attempting to subscribe...', 'text-blue-600');
                        sessionStorage.setItem('pushSubscriptionAttempted', 'true');
                        
                        // Check if subscription already exists in browser
                        try {
                            const registration = await navigator.serviceWorker.ready;
                            let subscription = await registration.pushManager.getSubscription();
                            
                            if (subscription) {
                                updateStatus('subscriptionStatus', '🔄 Found browser subscription, saving to database...', 'text-blue-600');
                                
                                // Save existing subscription to database
                                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                                
                                // Convert to base64url format
                                const uint8ArrayToBase64Url = (array) => {
                                    const base64 = btoa(String.fromCharCode.apply(null, array));
                                    return base64.replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
                                };
                                
                                const subscriptionData = subscription.toJSON && typeof subscription.toJSON === 'function' 
                                    ? subscription.toJSON()
                                    : {
                                        endpoint: subscription.endpoint,
                                        keys: {
                                            p256dh: uint8ArrayToBase64Url(new Uint8Array(subscription.getKey('p256dh'))),
                                            auth: uint8ArrayToBase64Url(new Uint8Array(subscription.getKey('auth')))
                                        }
                                    };
                                
                                const response = await fetch('/push/subscribe', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'X-CSRF-TOKEN': csrf
                                    },
                                    body: JSON.stringify(subscriptionData)
                                });
                                
                                if (response.ok) {
                                    updateStatus('subscriptionStatus', '✅ Subscription saved! Refreshing...', 'text-green-600');
                                    // Only reload once to prevent infinite loop
                                    if (!sessionStorage.getItem('pushSubscriptionReloaded')) {
                                        sessionStorage.setItem('pushSubscriptionReloaded', 'true');
                                        setTimeout(() => window.location.reload(), 1500);
                                    } else {
                                        // Already reloaded, just update status
                                        updateStatus('subscriptionStatus', '✅ Subscription saved successfully!', 'text-green-600');
                                        const countEl = document.getElementById('subCount');
                                        if (countEl) countEl.textContent = '1';
                                    }
                                } else {
                                    const errorData = await response.json().catch(() => ({ error: 'Unknown error', message: 'Unknown error' }));
                                    const errorMsg = errorData.message || errorData.error || 'Unknown error';
                                    console.error('[Push] Failed to save subscription:', errorData);
                                    updateStatus('subscriptionStatus', '❌ Failed to save: ' + errorMsg, 'text-red-600');
                                }
                            } else {
                                // No subscription yet, try calling the global function if available
                                if (window.subscribeToPush) {
                                    updateStatus('subscriptionStatus', '🔄 Calling subscription function...', 'text-blue-600');
                                    window.subscribeToPush().catch(e => {
                                        updateStatus('subscriptionStatus', '⚠️ Subscription attempt: ' + e.message, 'text-yellow-600');
                                    });
                                } else {
                                    updateStatus('subscriptionStatus', '⚠️ No subscription found. Interact with page to subscribe.', 'text-yellow-600');
                                }
                            }
                        } catch (e) {
                            updateStatus('subscriptionStatus', '❌ Error: ' + e.message, 'text-red-600');
                            console.error('Subscription check error:', e);
                        }
                    } else if (Notification.permission === 'denied') {
                        updateStatus('subscriptionStatus', '❌ Permission denied. Please enable notifications in browser settings (see instructions below).', 'text-red-600');
                    } else {
                        updateStatus('subscriptionStatus', '⏳ Permission not granted. Interact with page to request permission.', 'text-yellow-600');
                    }
                }, 2000);
            } catch (e) {
                updateStatus('subscriptionStatus', '❌ Error: ' + e.message, 'text-red-600');
            }
        });
    }
    
    // Periodically check if permission changes (in case user enables it in settings)
    if ('Notification' in window && Notification.permission === 'denied') {
        setInterval(() => {
            if (Notification.permission === 'granted') {
                updateStatus('subscriptionStatus', '✅ Permission granted! Attempting subscription...', 'text-green-600');
                // Permission was enabled, try to subscribe
                if (window.subscribeToPush) {
                    window.subscribeToPush().catch(e => {
                        console.error('Subscription attempt after permission change:', e);
                    });
                }
            }
        }, 3000); // Check every 3 seconds
    }
})();
</script>

<script>
document.getElementById('testForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const button = e.target.querySelector('button[type="submit"]');
    const resultDiv = document.getElementById('result');
    const originalText = button.textContent;
    
    button.disabled = true;
    button.textContent = 'Sending...';
    resultDiv.classList.add('hidden');
    
    const formData = new FormData(e.target);
    const data = {
        title: formData.get('title'),
        body: formData.get('body'),
        url: formData.get('url')
    };
    
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    try {
        const response = await fetch('/push/test', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrf
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (response.ok) {
            resultDiv.className = 'mt-4 p-4 bg-green-50 rounded-lg';
            resultDiv.innerHTML = `<p class="text-green-800"><strong>Success!</strong> Sent to ${result.sent} device(s).</p>`;
        } else {
            resultDiv.className = 'mt-4 p-4 bg-red-50 rounded-lg';
            resultDiv.innerHTML = `<p class="text-red-800"><strong>Error:</strong> ${result.message || 'Failed to send notification'}</p>`;
        }
    } catch (error) {
        resultDiv.className = 'mt-4 p-4 bg-red-50 rounded-lg';
        resultDiv.innerHTML = `<p class="text-red-800"><strong>Error:</strong> ${error.message}</p>`;
    } finally {
        resultDiv.classList.remove('hidden');
        button.disabled = false;
        button.textContent = originalText;
    }
});
</script>
@endsection

