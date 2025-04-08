<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\QuotationRequest;
use App\Mail\QuotationRequest as QuotationRequestMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class QuotationController extends Controller
{
  
    public function form(Product $product)
    {
        return view('quotation.form', compact('product'));
    }

    public function store(Request $request)
    {
        dd($request->all());
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'requirements' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $product = Product::findOrFail($request->product_id);
        $user = Auth::user();
        
        try {
            Log::info('Starting quotation request process');
            
            // Create quotation request record
            Log::info('Creating QuotationRequest record');
            $quotationRequest = QuotationRequest::create([
                'product_id' => $product->id,
                'user_id' => $user ? $user->id : null,
                'quantity' => $request->quantity,
                'requirements' => $request->requirements,
                'notes' => $request->notes,
            ]);
            Log::info('QuotationRequest record created successfully', ['id' => $quotationRequest->id]);

            // Send email with robust error handling
            try {
                Mail::to('cs@maxmedme.com')->send(new QuotationRequestMail(
                    $product,
                    $request->quantity,
                    $request->requirements,
                    $request->notes,
                    $user
                ));
                Log::info('Email sent successfully');
            } catch (\Exception $emailError) {
                Log::error('Email sending failed: ' . $emailError->getMessage());
                throw $emailError; // Rethrow to be caught by the outer catch block
            }

            // Redirect with correct parameter name
            return redirect()->route('quotation.confirmation', ['product' => $product->id])
                            ->with('success', 'Your quotation request has been sent successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to process quotation request: ' . $e->getMessage());
            
            // Return with more detailed error for debugging
            return back()->withInput()
                        ->with('error', 'Error: ' . $e->getMessage())
                        ->withErrors(['debug_error' => 'Error occurred at: ' . $e->getFile() . ' line ' . $e->getLine()]);
        }
    }

    public function confirmation(Product $product)
    {
        return view('quotation.confirmation', compact('product'));
    }
} 