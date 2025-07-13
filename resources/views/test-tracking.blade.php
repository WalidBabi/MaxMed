@extends('layouts.app')

@section('title', 'Test User Behavior Tracking')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">User Behavior Tracking Test</h1>
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-2xl font-semibold mb-4">Test Instructions</h2>
            <p class="mb-4">This page is designed to test user behavior tracking. Please:</p>
            <ol class="list-decimal list-inside space-y-2 mb-6">
                <li>Accept cookies if the consent banner appears</li>
                <li>Click on various elements below</li>
                <li>Scroll up and down the page</li>
                <li>Fill out the test form</li>
                <li>Check the browser console for tracking logs</li>
            </ol>
            
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="font-semibold text-blue-800 mb-2">Tracking Status</h3>
                <p class="text-blue-700" id="trackingStatus">Checking tracking status...</p>
            </div>
        </div>

        <!-- Test Buttons -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-2xl font-semibold mb-4">Test Click Tracking</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <button id="testButton1" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                    Test Button 1
                </button>
                <button id="testButton2" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                    Test Button 2
                </button>
                <button id="testButton3" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">
                    Test Button 3
                </button>
            </div>
        </div>

        <!-- Test Form -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-2xl font-semibold mb-4">Test Form Interactions</h2>
            <form id="testForm" class="space-y-4">
                <div>
                    <label for="testName" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input type="text" id="testName" name="name" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label for="testEmail" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" id="testEmail" name="email" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label for="testMessage" class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                    <textarea id="testMessage" name="message" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded hover:bg-purple-700 transition">
                    Submit Test Form
                </button>
            </form>
        </div>

        <!-- Scroll Test Area -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-2xl font-semibold mb-4">Test Scroll Tracking</h2>
            <p class="mb-4">Scroll through this content to test scroll depth tracking:</p>
            
            <div class="space-y-4 h-96 overflow-y-auto border border-gray-200 rounded-lg p-4">
                <div class="bg-gray-50 p-4 rounded">
                    <h3 class="font-semibold">Section 1</h3>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                </div>
                <div class="bg-gray-50 p-4 rounded">
                    <h3 class="font-semibold">Section 2</h3>
                    <p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
                </div>
                <div class="bg-gray-50 p-4 rounded">
                    <h3 class="font-semibold">Section 3</h3>
                    <p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>
                </div>
                <div class="bg-gray-50 p-4 rounded">
                    <h3 class="font-semibold">Section 4</h3>
                    <p>Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                </div>
                <div class="bg-gray-50 p-4 rounded">
                    <h3 class="font-semibold">Section 5</h3>
                    <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium.</p>
                </div>
            </div>
        </div>

        <!-- Test Links -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-2xl font-semibold mb-4">Test Link Clicks</h2>
            <div class="space-y-2">
                <a href="#" class="block text-blue-600 hover:underline">Test Link 1</a>
                <a href="#" class="block text-blue-600 hover:underline">Test Link 2</a>
                <a href="#" class="block text-blue-600 hover:underline">Test Link 3</a>
            </div>
        </div>

        <!-- Console Output -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-semibold mb-4">Tracking Console</h2>
            <div id="consoleOutput" class="bg-gray-900 text-green-400 p-4 rounded-lg font-mono text-sm h-64 overflow-y-auto">
                <div>Waiting for tracking events...</div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const consoleOutput = document.getElementById('consoleOutput');
    const trackingStatus = document.getElementById('trackingStatus');
    
    // Check if tracking is enabled
    function checkTrackingStatus() {
        const hasConsent = document.cookie.split('; ').find(row => row.startsWith('cookie_consent='));
        if (hasConsent && hasConsent.includes('accepted')) {
            trackingStatus.innerHTML = '<span class="text-green-600">✓ Tracking enabled (cookies accepted)</span>';
        } else if (hasConsent && hasConsent.includes('denied')) {
            trackingStatus.innerHTML = '<span class="text-red-600">✗ Tracking disabled (cookies rejected)</span>';
        } else {
            trackingStatus.innerHTML = '<span class="text-yellow-600">? Waiting for cookie consent</span>';
        }
    }
    
    // Log to console output
    function logToConsole(message) {
        const timestamp = new Date().toLocaleTimeString();
        const logEntry = document.createElement('div');
        logEntry.innerHTML = `[${timestamp}] ${message}`;
        consoleOutput.appendChild(logEntry);
        consoleOutput.scrollTop = consoleOutput.scrollHeight;
    }
    
    // Check initial status
    checkTrackingStatus();
    
    // Monitor for tracking events
    const originalFetch = window.fetch;
    window.fetch = function(...args) {
        if (args[0].includes('/api/user-behavior/track')) {
            logToConsole('📡 Sending tracking event to server...');
        }
        return originalFetch.apply(this, args);
    };
    
    // Monitor for user behavior tracker
    if (window.userBehaviorTracker) {
        logToConsole('✅ UserBehaviorTracker initialized');
        
        // Override trackEvent to log locally
        const originalTrackEvent = window.userBehaviorTracker.trackEvent;
        window.userBehaviorTracker.trackEvent = function(eventData) {
            logToConsole(`🎯 Event tracked: ${eventData.event_type}`);
            return originalTrackEvent.call(this, eventData);
        };
    } else {
        logToConsole('❌ UserBehaviorTracker not found');
    }
    
    // Test form submission
    document.getElementById('testForm').addEventListener('submit', function(e) {
        e.preventDefault();
        logToConsole('📝 Form submitted');
        alert('Form submitted! Check the tracking data.');
    });
    
    // Test button clicks
    document.querySelectorAll('button[id^="testButton"]').forEach(button => {
        button.addEventListener('click', function() {
            logToConsole(`🔘 Button clicked: ${this.textContent}`);
        });
    });
    
    // Test link clicks
    document.querySelectorAll('a[href="#"]').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            logToConsole(`🔗 Link clicked: ${this.textContent}`);
        });
    });
    
    // Monitor scroll events
    let scrollTimeout;
    window.addEventListener('scroll', function() {
        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(() => {
            const scrollDepth = Math.round((window.pageYOffset / (document.documentElement.scrollHeight - window.innerHeight)) * 100);
            logToConsole(`📜 Scroll depth: ${scrollDepth}%`);
        }, 150);
    });
    
    // Check status periodically
    setInterval(checkTrackingStatus, 5000);
});
</script>
@endsection 