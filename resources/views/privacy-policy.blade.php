@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">Privacy Policy</h1>
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-2xl font-semibold mb-4">Cookie Policy</h2>
            <p class="mb-4">
                Our website uses cookies to enhance your experience. By using our website, you consent to our use of cookies in accordance with this policy.
            </p>

            <h3 class="text-xl font-semibold mt-6 mb-2">What Are Cookies</h3>
            <p class="mb-4">
                Cookies are small text files that are stored on your computer or mobile device when you visit our website. They help us provide a better user experience and allow us to analyze how our site is used.
            </p>

            <h3 class="text-xl font-semibold mt-6 mb-2">How We Use Cookies</h3>
            <ul class="list-disc pl-6 mb-4 space-y-2">
                <li><strong>Essential Cookies:</strong> These are necessary for the website to function and cannot be switched off.</li>
                <li><strong>Analytics Cookies:</strong> These help us understand how visitors interact with our website by collecting and reporting information anonymously.</li>
                <li><strong>Preference Cookies:</strong> These enable the website to remember information that changes the way the site behaves or looks.</li>
            </ul>

            <h3 class="text-xl font-semibold mt-6 mb-2">Managing Cookies</h3>
            <p class="mb-4">
                You can manage your cookie preferences through your browser settings. However, please note that disabling certain cookies may affect the functionality of our website.
            </p>

            <div class="mt-8 pt-6 border-t border-gray-200">
                <p>Last updated: {{ nowDubai('F d, Y') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
