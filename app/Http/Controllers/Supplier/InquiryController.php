<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\QuotationRequest;
use App\Models\SupplierQuotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InquiryController extends Controller
{
    /**
     * Display inquiries for the current supplier
     */
    public function index(Request $request)
    {
        $supplierId = auth()->id();
        
        $query = QuotationRequest::with(['product', 'user'])
            ->forSupplier($supplierId)
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by supplier response
        if ($request->filled('supplier_response')) {
            $query->where('supplier_response', $request->supplier_response);
        }

        $inquiries = $query->paginate(15);

        return view('supplier.inquiries.index', compact('inquiries'));
    }

    /**
     * Show specific inquiry details
     */
    public function show(QuotationRequest $inquiry)
    {
        // Check if this inquiry belongs to the current supplier
        if ($inquiry->supplier_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this inquiry.');
        }

        $inquiry->load(['product', 'user', 'supplierQuotations' => function ($query) {
            $query->where('supplier_id', auth()->id());
        }]);

        return view('supplier.inquiries.show', compact('inquiry'));
    }

    /**
     * Respond to inquiry - Not Available
     */
    public function respondNotAvailable(Request $request, QuotationRequest $inquiry)
    {
        // Check authorization
        if ($inquiry->supplier_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this inquiry.');
        }

        $validated = $request->validate([
            'reason' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $inquiry->update([
                'supplier_response' => 'not_available',
                'supplier_responded_at' => now(),
                'status' => 'supplier_responded',
                'supplier_notes' => $validated['reason'] ?? 'Product not available',
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Response submitted successfully. Customer will be notified.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error submitting not available response: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to submit response.');
        }
    }

    /**
     * Show quotation form
     */
    public function quotationForm(QuotationRequest $inquiry)
    {
        // Check authorization
        if ($inquiry->supplier_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this inquiry.');
        }

        $inquiry->load(['product', 'user']);

        // Check if quotation already exists
        $existingQuotation = SupplierQuotation::where('quotation_request_id', $inquiry->id)
            ->where('supplier_id', auth()->id())
            ->first();

        return view('supplier.inquiries.quotation-form', compact('inquiry', 'existingQuotation'));
    }

    /**
     * Store or update supplier quotation
     */
    public function storeQuotation(Request $request, QuotationRequest $inquiry)
    {
        // Check authorization
        if ($inquiry->supplier_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this inquiry.');
        }

        $validated = $request->validate([
            'unit_price' => 'required|numeric|min:0',
            'currency' => 'required|in:AED,USD,EUR',
            'minimum_quantity' => 'required|integer|min:1',
            'lead_time_days' => 'nullable|integer|min:1',
            'valid_until' => 'required|date|after:today',
            'size' => 'nullable|string',
            'description' => 'nullable|string',
            'supplier_notes' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
            'specifications' => 'nullable|array',
            'submit' => 'boolean', // Whether to submit or save as draft
        ]);

        DB::beginTransaction();
        try {
            // Check if quotation already exists
            $quotation = SupplierQuotation::where('quotation_request_id', $inquiry->id)
                ->where('supplier_id', auth()->id())
                ->first();

            $quotationData = [
                'quotation_request_id' => $inquiry->id,
                'supplier_id' => auth()->id(),
                'product_id' => $inquiry->product_id,
                'unit_price' => $validated['unit_price'],
                'currency' => $validated['currency'],
                'minimum_quantity' => $validated['minimum_quantity'],
                'lead_time_days' => $validated['lead_time_days'],
                'valid_until' => $validated['valid_until'],
                'size' => $validated['size'] ?? $inquiry->size,
                'description' => $validated['description'],
                'supplier_notes' => $validated['supplier_notes'],
                'terms_conditions' => $validated['terms_conditions'],
                'specifications' => $validated['specifications'],
                'status' => $request->boolean('submit') ? 'submitted' : 'draft',
                'submitted_at' => $request->boolean('submit') ? now() : null,
            ];

            if ($quotation) {
                $quotation->update($quotationData);
            } else {
                $quotation = SupplierQuotation::create($quotationData);
            }

            // Update inquiry status if quotation is submitted
            if ($request->boolean('submit')) {
                $inquiry->update([
                    'supplier_response' => 'available',
                    'supplier_responded_at' => now(),
                    'status' => 'supplier_responded',
                    'supplier_notes' => 'Quotation submitted: ' . $quotation->quotation_number,
                ]);
            }

            DB::commit();

            $message = $request->boolean('submit') 
                ? 'Quotation submitted successfully. MaxMed will review and create customer quote.'
                : 'Quotation saved as draft. You can continue editing before submission.';

            return redirect()->route('supplier.inquiries.show', $inquiry)
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error saving supplier quotation: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to save quotation.');
        }
    }

    /**
     * Submit draft quotation
     */
    public function submitQuotation(SupplierQuotation $quotation)
    {
        // Check authorization
        if ($quotation->supplier_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this quotation.');
        }

        if ($quotation->status !== 'draft') {
            return redirect()->back()->with('error', 'Only draft quotations can be submitted.');
        }

        DB::beginTransaction();
        try {
            $quotation->update([
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);

            // Update inquiry status
            $inquiry = $quotation->quotationRequest;
            $inquiry->update([
                'supplier_response' => 'available',
                'supplier_responded_at' => now(),
                'status' => 'supplier_responded',
                'supplier_notes' => 'Quotation submitted: ' . $quotation->quotation_number,
            ]);

            DB::commit();

            return redirect()->route('supplier.inquiries.show', $inquiry)
                ->with('success', 'Quotation submitted successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error submitting quotation: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to submit quotation.');
        }
    }
} 