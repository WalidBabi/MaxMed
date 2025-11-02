@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold mb-4">Push Notification Test</h1>
            
            <div class="mb-6">
                <div class="p-4 bg-blue-50 rounded-lg mb-4">
                    <p class="text-sm text-gray-700"><strong>Subscriptions found:</strong> {{ $subscriptionCount }}</p>
                    @if($userId)
                        <p class="text-sm text-gray-700"><strong>User ID:</strong> {{ $userId }}</p>
                    @endif
                </div>

                @if($subscriptionCount === 0)
                    <div class="p-4 bg-yellow-50 rounded-lg mb-4">
                        <p class="text-sm text-yellow-800">
                            <strong>No subscriptions found.</strong> Make sure you've allowed notifications in your browser. 
                            Refresh the page to register your device.
                        </p>
                    </div>
                @endif
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

