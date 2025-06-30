@extends('admin.layouts.app')

@section('title', 'Send Supplier Invitation')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Send Supplier Invitation</h1>
                <p class="text-gray-600 mt-2">Invite a new supplier to join MaxMed's partner network</p>
            </div>
            <div>
                <a href="{{ route('admin.supplier-invitations.index') }}" 
                   class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Back to List
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('admin.supplier-invitations.store') }}" class="max-w-2xl mx-auto space-y-8">
        @csrf

        <!-- Supplier Details Section -->
        <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="h-5 w-5 text-indigo-600 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                    Supplier Information
                </h3>
            </div>
            
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Supplier Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name') }}"
                               required
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('name') border-red-300 @enderror"
                               placeholder="Enter supplier contact name">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               value="{{ old('email') }}"
                               required
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('email') border-red-300 @enderror"
                               placeholder="supplier@company.com">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Company Name -->
                <div>
                    <label for="company_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Company Name <span class="text-gray-400">(Optional)</span>
                    </label>
                    <input type="text" 
                           name="company_name" 
                           id="company_name" 
                           value="{{ old('company_name') }}"
                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('company_name') border-red-300 @enderror"
                           placeholder="Enter company name">
                    @error('company_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Custom Message Section -->
        <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="h-5 w-5 text-indigo-600 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.875 14.25l1.214 1.942a2.25 2.25 0 001.908 1.058h2.006c.776 0 1.497-.4 1.908-1.058l1.214-1.942M2.41 9h4.636a2.25 2.25 0 011.872 1.002l.164.246a2.25 2.25 0 001.872 1.002h2.092a2.25 2.25 0 001.872-1.002l.164-.246A2.25 2.25 0 0116.954 9h4.636M2.41 9a2.25 2.25 0 00-.16.832V12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 12V9.832c0-.287-.055-.57-.16-.832M2.41 9a2.25 2.25 0 01.382-.632l3.285-3.832a2.25 2.25 0 011.708-.786h8.43c.657 0 1.281.287 1.709.786l3.284 3.832c.163.19.291.404.382.632M4.5 20.25h15A2.25 2.25 0 0021.75 18v-2.625c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125V18a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                    Personal Message
                </h3>
            </div>
            
            <div class="p-6">
                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                        Custom Message <span class="text-gray-400">(Optional)</span>
                    </label>
                    <textarea name="message" 
                              id="message" 
                              rows="4"
                              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('message') border-red-300 @enderror"
                              placeholder="Add a personal message to include in the invitation email (optional)">{{ old('message') }}</textarea>
                    @error('message')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="text-sm text-gray-500 mt-2">
                        This message will be included in the invitation email to provide context or specific instructions.
                    </p>
                </div>
            </div>
        </div>

        <!-- Preview Section -->
        <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="h-5 w-5 text-indigo-600 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                    </svg>
                    What happens next?
                </h3>
            </div>
            
            <div class="p-6">
                <div class="space-y-3">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-6 h-6 bg-indigo-600 rounded-full flex items-center justify-center text-white text-xs font-bold">1</div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-gray-700">The supplier will receive an invitation email with a unique onboarding link</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-6 h-6 bg-indigo-600 rounded-full flex items-center justify-center text-white text-xs font-bold">2</div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-gray-700">They can set up their account and complete their supplier profile directly</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-6 h-6 bg-indigo-600 rounded-full flex items-center justify-center text-white text-xs font-bold">3</div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-gray-700">Once verified, they'll be assigned to relevant product categories</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-6 h-6 bg-indigo-600 rounded-full flex items-center justify-center text-white text-xs font-bold">4</div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-gray-700">They can start receiving quotation requests and managing orders</p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-blue-800">Invitation expires in 7 days</h4>
                            <p class="text-sm text-blue-700 mt-1">The supplier must register within 7 days, or you'll need to resend the invitation.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end space-x-3">
            <button type="button" 
                    onclick="previewInvitation()"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Preview Email
            </button>
            <button type="submit" 
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Send Invitation
            </button>
        </div>
    </form>
</div>

<!-- Preview Modal -->
<div id="preview-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity hidden z-50">
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                <div class="absolute right-0 top-0 hidden pr-4 pt-4 sm:block">
                    <button type="button" onclick="closePreview()" class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        <span class="sr-only">Close</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                        <h3 class="text-base font-semibold leading-6 text-gray-900">Email Preview</h3>
                        <div class="mt-4">
                            <div id="preview-content" class="mt-2 max-h-[60vh] overflow-y-auto"></div>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="closePreview()" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function previewInvitation() {
    const name = document.getElementById('name').value || '[Supplier Name]';
    const email = document.getElementById('email').value || '[Email Address]';
    const company = document.getElementById('company_name').value || '';
    const message = document.getElementById('message').value || '';
    
    const previewContent = `
        <div class="text-sm text-gray-700">
            <p class="mb-4"><strong>To:</strong> ${email}</p>
            <p class="mb-4"><strong>Subject:</strong> Invitation to Join MaxMed as a Supplier Partner</p>
            
            <div class="border-t pt-4">
                <h4 class="font-medium mb-2">Email Content:</h4>
                <div class="bg-gray-50 p-4 rounded border">
                    <h3 class="text-lg font-semibold text-indigo-600 mb-3">🤝 Supplier Partnership Invitation</h3>
                    <p class="mb-3">Welcome ${name}!</p>
                    <p class="mb-3">We're excited to invite you to join MaxMed as a trusted supplier partner. ${company ? `Your expertise at <strong>${company}</strong> makes you an ideal candidate for our supplier network.` : 'Your expertise in your field makes you an ideal candidate for our supplier network.'}</p>
                    
                    ${message ? `
                    <div class="bg-blue-50 p-3 rounded mb-3">
                        <p class="text-blue-800"><strong>Personal Message:</strong></p>
                        <p class="text-blue-700">${message}</p>
                    </div>
                    ` : ''}
                    
                    <div class="mt-4 p-3 bg-yellow-50 rounded">
                        <p class="font-medium text-yellow-800">Ready to Get Started?</p>
                        <p class="text-yellow-700 text-sm">Click the button below to set up your supplier account and complete your profile.</p>
                        <div class="mt-2">
                            <a href="{{ route('supplier.invitation.onboarding', ['token' => 'PREVIEW-TOKEN']) }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                                🚀 Complete Supplier Onboarding
                            </a>
                        </div>
                        <p class="mt-2 text-sm text-yellow-700">
                            <strong>Note:</strong> This is a preview. The actual email will contain a unique onboarding link.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('preview-content').innerHTML = previewContent;
    document.getElementById('preview-modal').classList.remove('hidden');
}

function closePreview() {
    document.getElementById('preview-modal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('preview-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePreview();
    }
});
</script>
@endsection 