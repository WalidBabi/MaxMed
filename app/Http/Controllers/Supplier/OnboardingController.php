<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SupplierInformation;
use App\Models\SupplierCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

class OnboardingController extends Controller
{
    /**
     * Show the company information form
     */
    public function company()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to access the onboarding process.');
        }

        $user = Auth::user();
        
        if (!$user->isSupplier()) {
            return redirect()->route('login')->with('error', 'Access denied. This area is for suppliers only.');
        }

        $supplierInfo = $user->supplierInformation;
        
        return View::make('supplier.onboarding.company-info', compact('supplierInfo'));
    }

    /**
     * Save company information
     */
    public function storeCompany(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to access the onboarding process.');
        }

        $user = Auth::user();
        
        if (!$user->isSupplier()) {
            return redirect()->route('login')->with('error', 'Access denied. This area is for suppliers only.');
        }

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'business_registration_number' => 'required|string|max:50',
            'tax_registration_number' => 'nullable|string|max:50',
            'trade_license_number' => 'required|string|max:50',
            'business_address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state_province' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'phone_primary' => 'required|string|max:20',
            'website' => [
                'nullable',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    if (!empty($value)) {
                        // Add protocol if missing
                        $url = $value;
                        if (!preg_match('/^https?:\/\//', $url)) {
                            $url = 'http://' . $url;
                        }
                        
                        // Validate the URL format
                        if (!filter_var($url, FILTER_VALIDATE_URL)) {
                            $fail('The website field must be a valid URL.');
                        }
                    }
                }
            ],
            'years_in_business' => 'required|integer|min:0',
            'company_description' => 'required|string|max:1000',
            'primary_contact_name' => 'required|string|max:255',
            'primary_contact_position' => 'required|string|max:255',
            'primary_contact_email' => 'required|email|max:255',
            'primary_contact_phone' => 'required|string|max:20',
            'brand_name' => 'nullable|string|max:255',
            'brand_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        // Process website URL to add protocol if missing
        if (!empty($validated['website'])) {
            if (!preg_match('/^https?:\/\//', $validated['website'])) {
                $validated['website'] = 'http://' . $validated['website'];
            }
        }

        $user = Auth::user();
        $supplierInfo = $user->supplierInformation ?? new SupplierInformation();
        $supplierInfo->fill($validated);
        $supplierInfo->user_id = $user->id;
        $supplierInfo->save();

        // Handle brand name and logo
        $brandName = $request->input('brand_name') ?: $supplierInfo->company_name;
        $brandData = ['name' => $brandName];
        if ($request->hasFile('brand_logo')) {
            $logoFile = $request->file('brand_logo');
            if ($logoFile->isValid()) {
                $logoPath = $logoFile->store('brand-logos', 'public');
                $brandData['logo_url'] = $logoPath;
            }
        }
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
            if (isset($brandData['logo_url'])) {
                $brand->logo_url = $brandData['logo_url'];
                $brand->save();
            }
        }
        $supplierInfo->brand_id = $brand->id;
        $supplierInfo->save();

        return Redirect::route('supplier.onboarding.documents')
            ->with('success', 'Company information saved successfully!');
    }

    /**
     * Show the document upload form
     */
    public function documents()
    {
        $user = Auth::user();
        $supplierInfo = $user->supplierInformation;
        $existingDocuments = $supplierInfo ? $supplierInfo->documents : [];
        
        return View::make('supplier.onboarding.documents', compact('existingDocuments'));
    }

    /**
     * Create a safe company name for file paths
     */
    private function createSafeCompanyName($companyName)
    {
        // Remove special characters, keep only alphanumeric and spaces
        $safeName = preg_replace('/[^a-zA-Z0-9\s]/', '', $companyName);
        // Replace spaces with underscores
        $safeName = str_replace(' ', '_', trim($safeName));
        // Convert to lowercase
        $safeName = strtolower($safeName);
        // Limit length to avoid filesystem issues
        $safeName = Str::limit($safeName, 50, '');
        
        return $safeName;
    }

    /**
     * Save uploaded documents
     */
    public function storeDocuments(Request $request)
    {
        $validated = $request->validate([
            'trade_license_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:20480',
            'tax_certificate_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:20480',
            'company_profile_file' => 'required|file|mimes:pdf|max:40960',
            'certification_files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:20480',
        ]);

        $user = Auth::user();
        $supplierInfo = $user->supplierInformation;

        if (!$supplierInfo || !$supplierInfo->company_name) {
            return redirect()->back()->with('error', 'Please complete company information first.');
        }

        // Create a safe company name for file paths
        $safeCompanyName = $this->createSafeCompanyName($supplierInfo->company_name);

        try {
            // Store documents
            $documents = [];
            foreach ($validated as $type => $file) {
                if ($type !== 'certification_files' && $file) {
                    $path = Storage::disk('supplier_documents')->put(
                        $safeCompanyName . '/' . $type,
                        $file
                    );
                    $documents[$type] = $path;
                }
            }

            // Store certification files if any
            if ($request->hasFile('certification_files')) {
                $certificationPaths = [];
                foreach ($request->file('certification_files') as $file) {
                    $path = Storage::disk('supplier_documents')->put(
                        $safeCompanyName . '/certifications',
                        $file
                    );
                    $certificationPaths[] = $path;
                }
                $documents['certification_files'] = $certificationPaths;
            }

            $supplierInfo->documents = $documents;
            $supplierInfo->save();

            return redirect()->route('supplier.onboarding.categories')
                ->with('success', 'Documents uploaded successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to upload documents. Please try again.')
                ->withInput();
        }
    }

    /**
     * Show the category selection form
     */
    public function categories()
    {
        $categories = \App\Models\Category::whereNull('parent_id')
            ->with('children')
            ->orderBy('name')
            ->get();
            
        return view('supplier.onboarding.categories', compact('categories'));
    }

    /**
     * Save selected categories
     */
    public function storeCategories(Request $request)
    {
        $validated = $request->validate([
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:categories,id',
            'specializations' => 'required|array|min:1',
            'specializations.*' => 'required|string|max:255',
            'suggested_categories' => 'nullable|array',
            'suggested_categories.*' => 'required|string|max:255',
        ]);

        $user = Auth::user();

        // Create category assignments
        foreach ($validated['categories'] as $categoryId) {
            \App\Models\SupplierCategory::create([
                'supplier_id' => $user->id,
                'category_id' => $categoryId,
                'status' => 'pending_approval',
            ]);
        }

        // Save specializations and suggested categories
        $supplierInfo = $user->supplierInformation;
        $supplierInfo->specializations = $validated['specializations'];
        $supplierInfo->suggested_categories = $validated['suggested_categories'] ?? [];
        $supplierInfo->onboarding_completed = true;
        $supplierInfo->onboarding_completed_at = now();
        $supplierInfo->status = SupplierInformation::STATUS_PENDING_APPROVAL;
        $supplierInfo->save();

        return redirect()->route('supplier.dashboard')
            ->with('success', 'Onboarding completed successfully! MaxMed will review your profile and category assignments. Once approved, you can add products to your categories and start receiving inquiries and orders.');
    }
} 