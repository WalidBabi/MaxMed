@extends('admin.layouts.app')

@section('title', 'Edit Profile')

@section('content')
<!-- Header -->
<div class="mb-8">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Edit Profile</h1>
            <p class="text-gray-600 mt-2">Update your account information and settings</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('profile.show') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Profile
            </a>
        </div>
    </div>
</div>
<!-- Profile Avatar -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-5">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <h3 class="text-lg font-semibold text-gray-900">Profile Picture</h3>
    </div>
    <div class="p-6 text-center">
        <div class="mx-auto h-24 w-24 rounded-full mb-4 overflow-hidden border-4 border-gray-100">
            @if($user->profile_photo)
            <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
            @else
            <div class="h-full w-full bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center text-white text-2xl font-bold">
                {{ $user->profile_display }}
            </div>
            @endif
        </div>

        @if($user->profile_photo)
        <form method="post" action="{{ route('profile.photo.remove') }}" class="mt-3">
            @csrf
            @method('delete')
            <button type="submit" onclick="return confirm('Are you sure you want to remove your profile photo?')" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Remove Photo
            </button>
        </form>
        @endif

        @if (session('status') === 'photo-removed')
        <p class="mt-2 text-sm text-green-600">Photo removed successfully.</p>
        @endif
    </div>
</div>
<div class="grid grid-cols-1 xl:grid-cols-4 gap-8 ">
    <!-- Profile Information Form -->
    <div class="xl:col-span-3 space-y-6">
        <!-- Update Profile Information -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">Profile Information</h3>
                <p class="text-sm text-gray-600 mt-1">Update your account's profile information and email address</p>
            </div>
            <form method="post" action="{{ route('profile.update') }}" class="p-6" enctype="multipart/form-data">
                @csrf
                @method('patch')

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                        <input id="name" name="name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
                        @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <div class="mt-1 block w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-md shadow-sm text-sm text-gray-600">
                            {{ $user->email }}
                            @if($user->email_verified_at)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 ml-2">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Verified
                            </span>
                            @endif
                        </div>
                        <p class="mt-2 text-sm text-gray-500">Contact support to change your email address</p>
                    </div>

                    <div>
                        <label for="profile_photo" class="block text-sm font-medium text-gray-700 mb-2">Profile Photo</label>
                        <input type="file" id="profile_photo" name="profile_photo" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" onchange="previewImage(this)">
                        @error('profile_photo')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-sm text-gray-500">JPG, PNG, GIF, WEBP up to 2MB</p>

                        <!-- Photo preview -->
                        <div id="photo-preview" class="mt-3 hidden">
                            <img id="preview-image" class="h-20 w-20 rounded-full object-cover border-2 border-gray-200" alt="Preview">
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end mt-6">
                    @if (session('status') === 'profile-updated')
                    <p class="text-sm text-green-600 mr-4">Saved successfully.</p>
                    @endif

                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>

        <!-- Update Password -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">Update Password</h3>
                <p class="text-sm text-gray-600 mt-1">Ensure your account is using a long, random password to stay secure</p>
            </div>
            <form method="post" action="{{ route('password.update') }}" class="p-6">
                @csrf
                @method('put')

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                        <input id="current_password" name="current_password" type="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" autocomplete="current-password">
                        @error('current_password', 'updatePassword')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                        <input id="password" name="password" type="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" autocomplete="new-password">
                        @error('password', 'updatePassword')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" autocomplete="new-password">
                        @error('password_confirmation', 'updatePassword')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end mt-6">
                    @if (session('status') === 'password-updated')
                    <p class="text-sm text-green-600 mr-4">Password updated successfully.</p>
                    @endif

                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Update Password
                    </button>
                </div>
            </form>
        </div>

        <!-- Delete Account -->
        <div class="bg-white rounded-lg shadow-sm border border-red-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-red-200 bg-red-50">
                <h3 class="text-lg font-semibold text-red-900">Delete Account</h3>
                <p class="text-sm text-red-600 mt-1">Once your account is deleted, all of its resources and data will be permanently deleted</p>
            </div>
            <div class="p-6">
                <p class="text-sm text-gray-600 mb-4">
                    Before deleting your account, please download any data or information that you wish to retain.
                </p>

                <button
                    x-data=""
                    x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Delete Account
                </button>
            </div>
        </div>
    </div>

    <!-- Profile Sidebar -->
    <div class="space-y-6">


    </div>
</div>

<!-- Profile Sidebar -->
<div class="xl:col-span-1 space-y-6">



</div>
</div>

<!-- Delete Account Modal -->
<div x-data="{ show: false }" x-on:open-modal.window="$event.detail == 'confirm-user-deletion' ? show = true : null" x-on:close-modal.window="$event.detail == 'confirm-user-deletion' ? show = false : null" x-show="show" class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50" style="display: none;">
    <div x-show="show" class="fixed inset-0 transform transition-all" x-on:click="show = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
    </div>

    <div x-show="show" class="mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-md sm:mx-auto" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-gray-900">
                Are you sure you want to delete your account?
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.
            </p>

            <div class="mt-6">
                <label for="password" class="sr-only">Password</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    placeholder="Password" />
                @error('password', 'userDeletion')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6 flex justify-end">
                <button type="button" x-on:click="show = false" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                    Cancel
                </button>

                <button type="submit" class="ml-3 inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Delete Account
                </button>
            </div>
        </form>
    </div>
</div>

@if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
<form id="send-verification" method="post" action="{{ route('verification.send') }}">
    @csrf
</form>
@endif
</div>

<script>
    function previewImage(input) {
        const previewDiv = document.getElementById('photo-preview');
        const previewImage = document.getElementById('preview-image');

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                previewImage.src = e.target.result;
                previewDiv.classList.remove('hidden');
            }

            reader.readAsDataURL(input.files[0]);
        } else {
            previewDiv.classList.add('hidden');
        }
    }
</script>
@endsection