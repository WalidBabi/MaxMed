@extends('layouts.app')

@section('title', 'Test Cookie Clear')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">Cookie Consent Test</h1>
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-2xl font-semibold mb-4">Current Cookies</h2>
            <div id="cookieList" class="bg-gray-100 p-4 rounded-lg font-mono text-sm">
                Loading cookies...
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-2xl font-semibold mb-4">Actions</h2>
            <div class="space-y-4">
                <button id="clearCookies" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">
                    Clear All Cookies
                </button>
                <button id="reloadPage" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                    Reload Page
                </button>
                <button id="checkConsent" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                    Check Consent Status
                </button>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-semibold mb-4">Debug Info</h2>
            <div id="debugInfo" class="bg-gray-100 p-4 rounded-lg font-mono text-sm">
                <div>Page loaded at: <span id="loadTime"></span></div>
                <div>Cookie consent banner should appear below if no consent is set.</div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cookieList = document.getElementById('cookieList');
    const debugInfo = document.getElementById('debugInfo');
    const loadTime = document.getElementById('loadTime');
    
    // Set load time
    loadTime.textContent = new Date().toLocaleString();
    
    // Display current cookies
    function displayCookies() {
        const cookies = document.cookie.split(';').map(cookie => cookie.trim());
        if (cookies.length === 0 || (cookies.length === 1 && cookies[0] === '')) {
            cookieList.innerHTML = '<div class="text-gray-500">No cookies found</div>';
        } else {
            cookieList.innerHTML = cookies.map(cookie => `<div>${cookie}</div>`).join('');
        }
    }
    
    // Clear all cookies
    document.getElementById('clearCookies').addEventListener('click', function() {
        const cookies = document.cookie.split(';');
        cookies.forEach(cookie => {
            const eqPos = cookie.indexOf('=');
            const name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
            document.cookie = name + '=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/';
        });
        displayCookies();
        alert('All cookies cleared! Reload the page to see the consent banner.');
    });
    
    // Reload page
    document.getElementById('reloadPage').addEventListener('click', function() {
        window.location.reload();
    });
    
    // Check consent status
    document.getElementById('checkConsent').addEventListener('click', function() {
        const consent = document.cookie.split('; ').find(row => row.startsWith('cookie_consent='));
        if (consent) {
            alert('Consent found: ' + consent);
        } else {
            alert('No consent cookie found');
        }
    });
    
    // Initial display
    displayCookies();
    
    // Check for consent banner
    setTimeout(() => {
        const banner = document.getElementById('cookieConsent');
        if (banner) {
            debugInfo.innerHTML += `<div class="mt-2">Banner element found: ${banner.classList.contains('hidden') ? 'Hidden' : 'Visible'}</div>`;
        } else {
            debugInfo.innerHTML += `<div class="mt-2 text-red-600">Banner element NOT found!</div>`;
        }
    }, 1000);
});
</script>
@endsection 