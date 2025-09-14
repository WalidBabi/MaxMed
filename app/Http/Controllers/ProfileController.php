<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Profile management is generally allowed for all authenticated users
        // No additional permission checks needed for basic profile operations
    }

    /**
     * Display the user's profile form.
     */
    public function show(Request $request): View
    {
        $user = $request->user();
        $user->load('role');
        
        // Ensure permissions are properly loaded
        if ($user->role) {
            $user->role->load('permissions');
        }
        
        // Determine the correct view based on user role
        if ($user->role && $user->role->name === 'supplier') {
            return view('profile.supplier-show', [
                'user' => $user,
            ]);
        }
        
        return view('profile.show', [
            'user' => $user,
        ]);
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user()->load(['role', 'role.permissions']);
        
        // Determine the correct view based on user role
        if ($user->role && $user->role->name === 'supplier') {
            return view('profile.supplier-edit', [
                'user' => $user,
            ]);
        }
        
        return view('profile.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        
        // Debug logging
        Log::info('Profile update request', [
            'user_id' => $user->id,
            'has_file' => $request->hasFile('profile_photo'),
            'request_data' => $request->except(['profile_photo']),
        ]);
        
        // Validate and update basic information
        $validated = $request->validated();
        $user->fill($request->except(['profile_photo']));

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            try {
                // Validate file
                $file = $request->file('profile_photo');
                Log::info('Profile photo upload attempt', [
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'file_type' => $file->getMimeType(),
                    'is_valid' => $file->isValid(),
                ]);
                
                if ($file->isValid()) {
                    // Delete old photo if exists
                    if ($user->profile_photo) {
                        Storage::disk('public')->delete($user->profile_photo);
                        Log::info('Deleted old profile photo', ['path' => $user->profile_photo]);
                    }

                    // Store new photo
                    $path = $file->store('profile-photos', 'public');
                    $user->profile_photo = $path;
                    Log::info('Stored new profile photo', ['path' => $path]);
                } else {
                    Log::error('Profile photo file is not valid');
                    return Redirect::route('profile.edit')->withErrors(['profile_photo' => 'The uploaded file is not valid.']);
                }
            } catch (\Exception $e) {
                Log::error('Profile photo upload failed', ['error' => $e->getMessage()]);
                return Redirect::route('profile.edit')->withErrors(['profile_photo' => 'Failed to upload profile photo. Please try again.']);
            }
        }

        // Handle brand name and logo for suppliers
        if ($user->role && $user->role->name === 'supplier') {
            $supplierInfo = $user->supplierInformation;
            if ($supplierInfo) {
                $brandName = $request->input('brand_name') ?: $supplierInfo->company_name;
                $brandData = ['name' => $brandName];

                // Handle brand logo upload
                if ($request->hasFile('brand_logo')) {
                    $logoFile = $request->file('brand_logo');
                    if ($logoFile->isValid()) {
                        $logoPath = $logoFile->store('brands', 'public');
                        $brandData['logo_url'] = 'https://maxmedme.com/storage/' . $logoPath;
                    }
                }

                // Create or update brand
                $brand = null;
                if ($supplierInfo->brand_id) {
                    $brand = \App\Models\Brand::find($supplierInfo->brand_id);
                    if ($brand) {
                        $brand->fill($brandData);
                        $brand->save();
                    }
                }
                if (!$brand) {
                    $brand = \App\Models\Brand::firstOrCreate(['name' => $brandName], $brandData);
                    // If logo was uploaded, update it
                    if (isset($brandData['logo_url'])) {
                        $brand->logo_url = $brandData['logo_url'];
                        $brand->save();
                    }
                }
                // Set brand_id in supplier_information
                $supplierInfo->brand_id = $brand->id;
                $supplierInfo->save();
            }
        }

        $user->save();
        Log::info('Profile updated successfully', ['user_id' => $user->id]);

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Remove the user's profile photo.
     */
    public function removePhoto(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
            $user->profile_photo = null;
            $user->save();
        }

        return Redirect::route('profile.edit')->with('status', 'photo-removed');
    }
} 