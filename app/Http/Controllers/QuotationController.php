<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\QuotationRequest;
use App\Models\ContactSubmission;
use App\Mail\QuotationRequest as QuotationRequestMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class QuotationController extends Controller
{
    /**
     * Handle direct URL access to quotation/ID which should be redirected
     */
    public function redirect(Request $request, $productId)
    {
        try {
            $product = Product::findOrFail($productId);
            return redirect()->route('product.show', $product)->setStatusCode(301);
        } catch (\Exception $e) {
            return redirect()->route('products.index')->setStatusCode(301);
        }
    }
  
    public function form(Product $product)
    {
        // Share SEO information with the view
        View::share('needsNoIndex', true);
        return view('quotation.form', compact('product'));
    }

    public function store(Request $request)
    {
        
        try {
            // Validate request
            $validatedData = $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1',
                'size' => 'nullable|string|max:50',
                'requirements' => 'nullable|string',
                'notes' => 'nullable|string',
                'delivery_timeline' => 'nullable|string|in:urgent,standard,flexible',
                // Contact information for guest users
                'contact_name' => 'required_without:user_id|nullable|string|max:255',
                'contact_email' => 'required_without:user_id|nullable|email|max:255',
                'contact_phone' => 'nullable|string|max:20',
                'contact_company' => 'nullable|string|max:255',
            ]);
            
            Log::info('Validation passed', $validatedData);
            
            // Find product
            try {
                $product = Product::findOrFail($request->product_id);
                Log::info('Product found', ['product_id' => $product->id]);
            } catch (\Exception $e) {
                Log::error('Product not found: ' . $e->getMessage());
                throw new \Exception('Unable to find the requested product: ' . $e->getMessage());
            }
            
            $user = Auth::user();
            Log::info('User authentication checked', ['user_id' => $user ? $user->id : 'null']);
            
            // Create quotation request record
            try {
                Log::info('Creating QuotationRequest record');
                $quotationRequest = QuotationRequest::create([
                    'product_id' => $product->id,
                    'user_id' => $request->input('user_id', 0),
                    'quantity' => $request->quantity,
                    'size' => $request->size,
                    'requirements' => $request->requirements,
                    'notes' => $request->notes,
                    'delivery_timeline' => $request->delivery_timeline,
                    'status' => 'pending', // Set initial status for workflow
                ]);
                Log::info('QuotationRequest record created successfully', ['id' => $quotationRequest->id]);
            } catch (\Exception $e) {
                Log::error('Failed to create QuotationRequest: ' . $e->getMessage());
                throw new \Exception('Database error creating quotation request: ' . $e->getMessage());
            }

            // Create contact submission for CRM tracking (for guest users or when contact info provided)
            if (!$user || $request->input('user_id', 0) == 0 || $request->filled('contact_name')) {
                try {
                    Log::info('Creating ContactSubmission for CRM tracking');
                    
                    // Use provided contact info or fallback to user data
                    $contactName = $request->contact_name ?? ($user ? $user->name : 'Guest User');
                    $contactEmail = $request->contact_email ?? ($user ? $user->email : 'guest@quotation.request');
                    $contactPhone = $request->contact_phone;
                    $contactCompany = $request->contact_company;
                    
                    // Build detailed message for CRM
                    $timelineText = '';
                    switch ($request->delivery_timeline) {
                        case 'urgent':
                            $timelineText = 'Urgent (1-2 weeks)';
                            break;
                        case 'standard':
                            $timelineText = 'Standard (3-4 weeks)';
                            break;
                        case 'flexible':
                            $timelineText = 'Flexible (1-2 months)';
                            break;
                    }
                    
                    $message = "Product Quotation Request:\n\n" .
                              "Product: {$product->name}\n" .
                              "SKU: {$product->sku}\n" .
                              "Quantity: {$request->quantity}\n" .
                              ($request->size ? "Size: {$request->size}\n" : "") .
                              ($timelineText ? "Delivery Timeline: {$timelineText}\n" : "") .
                              ($contactCompany ? "Company: {$contactCompany}\n" : "") .
                              ($contactPhone ? "Phone: {$contactPhone}\n" : "") .
                              ($request->requirements ? "\nSpecific Requirements:\n{$request->requirements}\n" : "") .
                              ($request->notes ? "\nAdditional Notes:\n{$request->notes}\n" : "") .
                              "\n--- End of Quotation Request ---";
                    
                    $contactData = [
                        'name' => $contactName,
                        'email' => $contactEmail,
                        'phone' => $contactPhone,
                        'company' => $contactCompany,
                        'subject' => 'Product Quotation Request',
                        'message' => $message,
                        'status' => 'converted_to_inquiry',
                        'converted_to_inquiry_id' => $quotationRequest->id,
                        'lead_potential' => $this->assessLeadPotential($request),
                    ];
                    
                    $contactSubmission = ContactSubmission::create($contactData);
                    Log::info('ContactSubmission created successfully', ['id' => $contactSubmission->id]);
                } catch (\Exception $e) {
                    // Log but don't fail the entire process
                    Log::error('Failed to create ContactSubmission for CRM: ' . $e->getMessage());
                }
            }

            // Send email with robust error handling
            try {
                Log::info('Attempting to send email');
                
                // Set email recipient based on environment
                $mailTo = app()->environment('production') ? 'sales@maxmedme.com' : 'wbabi@localhost.com';
                
                Mail::to($mailTo)->send(new QuotationRequestMail(
                    $product,
                    $request->quantity,
                    $request->size,
                    $request->requirements,
                    $request->notes,
                    $user, // This can be null but we'll handle it in the template
                    $request->delivery_timeline,
                    $request->contact_name ?? ($user ? $user->name : null),
                    $request->contact_email ?? ($user ? $user->email : null),
                    $request->contact_phone,
                    $request->contact_company
                ));
                Log::info('Email sent successfully');
            } catch (\Exception $emailError) {
                // Log but continue - don't throw the error to allow form to complete even if email fails
                Log::error('Email sending failed: ' . $emailError->getMessage());
                Log::error('Email error details: ', [
                    'file' => $emailError->getFile(),
                    'line' => $emailError->getLine(),
                    'trace' => $emailError->getTraceAsString()
                ]);
                // Don't throw - just continue
            }

            // Redirect with correct parameter name
            return redirect()->route('quotation.confirmation', ['product' => $product->slug])
                            ->with('success', 'Your quotation request has been sent successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to process quotation request: ' . $e->getMessage());
            Log::error('Error details: ', [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return with more detailed error for debugging
            return back()->withInput()
                        ->with('error', 'We encountered an error processing your request. Please try again later.')
                        ->withErrors(['debug_error' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function confirmation(Product $product)
    {
        // Share SEO information with the view
        View::share('needsNoIndex', true);
        return view('quotation.confirmation', compact('product'));
    }

    /**
     * Assess lead potential based on request data
     */
    private function assessLeadPotential(Request $request)
    {
        $score = 0;
        
        // Company provided increases potential
        if ($request->filled('contact_company')) {
            $score += 30;
        }
        
        // Phone provided shows higher engagement
        if ($request->filled('contact_phone')) {
            $score += 20;
        }
        
        // Urgent timeline indicates immediate need
        if ($request->delivery_timeline === 'urgent') {
            $score += 25;
        } elseif ($request->delivery_timeline === 'standard') {
            $score += 15;
        }
        
        // Higher quantity suggests bulk purchase
        $quantity = (int) $request->quantity;
        if ($quantity >= 100) {
            $score += 25;
        } elseif ($quantity >= 10) {
            $score += 15;
        } elseif ($quantity >= 5) {
            $score += 10;
        }
        
        // Detailed requirements show serious interest
        if (strlen($request->requirements ?? '') > 50) {
            $score += 15;
        }
        
        // Convert score to rating
        if ($score >= 80) {
            return 'hot';
        } elseif ($score >= 50) {
            return 'warm';
        } else {
            return 'cold';
        }
    }
} 