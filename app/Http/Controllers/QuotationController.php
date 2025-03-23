<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class QuotationController extends Controller
{
    public function request(Product $product)
    {
        return view('quotation.form', compact('product'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'requirements' => 'nullable|string',
            'delivery_timeline' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        // Get product details
        $product = Product::find($validated['product_id']);

        // Send email
        Mail::send('emails.quotation-request', [
            'product' => $product,
            'quantity' => $validated['quantity'],
            'requirements' => $validated['requirements'],
            'delivery_timeline' => $validated['delivery_timeline'],
            'notes' => $validated['notes'],
            'user' => auth()->user()
        ], function($message) use ($product) {
            $message->to('cs@maxmedme.com')
                   ->subject('New Quotation Request - ' . $product->name);
        });

        return redirect()->back()
            ->with('success', 'Your quotation request has been sent successfully! Our team will contact you shortly.');
    }

    public function form(Product $product)
    {
        return view('quotation.form', compact('product'));
    }
} 