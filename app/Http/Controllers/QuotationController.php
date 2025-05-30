<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\QuotationRequest;
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
                ]);
                Log::info('QuotationRequest record created successfully', ['id' => $quotationRequest->id]);
            } catch (\Exception $e) {
                Log::error('Failed to create QuotationRequest: ' . $e->getMessage());
                throw new \Exception('Database error creating quotation request: ' . $e->getMessage());
            }

            // Send email with robust error handling
            try {
                Log::info('Attempting to send email');
                
                // Check if we need to handle a guest user
                $mailTo = 'sales@maxmedme.com';
                
                Mail::to($mailTo)->send(new QuotationRequestMail(
                    $product,
                    $request->quantity,
                    $request->size,
                    $request->requirements,
                    $request->notes,
                    $user // This can be null but we'll handle it in the template
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
            return redirect()->route('quotation.confirmation', ['product' => $product->id])
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
} 