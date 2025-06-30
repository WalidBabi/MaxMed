@extends('admin.layouts.app')

@section('title', 'Supplier Invitation Details')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    <div class="sm:flex sm:justify-between sm:items-center mb-8">
        <!-- Left: Title -->
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl font-bold text-gray-900">Supplier Invitation Details</h1>
            <p class="text-gray-600 mt-2">View and manage invitation information</p>
        </div>

        <!-- Right: Actions -->
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

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Invitation Information -->
        <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="h-5 w-5 text-indigo-600 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                    Invitation Information
                </h3>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600">Status</label>
                    <span class="mt-1 block">
                        @switch($invitation->status)
                            @case('pending')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Pending
                                </span>
                                @break
                            @case('accepted')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Accepted
                                </span>
                                @break
                            @case('expired')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Expired
                                </span>
                                @break
                            @default
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ ucfirst($invitation->status) }}
                                </span>
                        @endswitch
                    </span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Contact Name</label>
                    <span class="mt-1 block text-gray-900">{{ $invitation->contact_name }}</span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Email</label>
                    <span class="mt-1 block text-gray-900">{{ $invitation->email }}</span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Company Name</label>
                    <span class="mt-1 block text-gray-900">{{ $invitation->company_name ?: 'Not specified' }}</span>
                </div>
            </div>
        </div>

        <!-- Additional Details -->
        <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="h-5 w-5 text-indigo-600 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                    </svg>
                    Additional Details
                </h3>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600">Invited By</label>
                    <span class="mt-1 block text-gray-900">{{ $invitation->inviter->name ?? 'System' }}</span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Invitation Sent</label>
                    <span class="mt-1 block text-gray-900">{{ $invitation->created_at->format('M d, Y H:i') }}</span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Expires At</label>
                    <span class="mt-1 block text-gray-900">{{ $invitation->expires_at->format('M d, Y H:i') }}</span>
                </div>
                @if($invitation->custom_message)
                <div>
                    <label class="block text-sm font-medium text-gray-600">Custom Message</label>
                    <span class="mt-1 block text-gray-900 whitespace-pre-line">{{ $invitation->custom_message }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    @if($invitation->status === 'pending')
    <div class="mt-8">
        <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="h-5 w-5 text-indigo-600 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM12.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM18.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                    </svg>
                    Actions
                </h3>
            </div>
            <div class="p-6">
                <div class="flex space-x-4">
                    <form action="{{ route('admin.supplier-invitations.resend', $invitation) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Resend Invitation
                        </button>
                    </form>
                    
                    <form action="{{ route('admin.supplier-invitations.cancel', $invitation) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" 
                                onclick="return confirm('Are you sure you want to cancel this invitation?')">
                            Cancel Invitation
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection 