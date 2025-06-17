<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuotationRequest;
use App\Models\SupplierQuotation;
use App\Models\User;
use App\Models\Product;
use App\Models\Quote;
use App\Models\QuoteItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class InquiryController extends Controller
{
    /**
     * Display a listing of inquiries
     */
    public function index(Request $request)
    {
        $query = QuotationRequest::with(['product', 'user', 'supplier'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by supplier response
        if ($request->filled('supplier_response')) {
            $query->where('supplier_response', $request->supplier_response);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            })->orWhereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $inquiries = $query->paginate(15);

        return view('admin.inquiries.index', compact('inquiries'));
    }

    /**
     * Show the form for creating a new inquiry
     */
    public function create()
    {
        $products = Product::all();
        $customers = User::whereDoesntHave('roles')->orWhereHas('roles', function ($query) {
            $query->where('name', '!=', 'supplier');
        })->get();

        return view('admin.inquiries.create', compact('products', 'customers'));
    }

    /**
     * Store a newly created inquiry
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'user_id' => 'required|exists:users,id',
            'quantity' => 'required|integer|min:1',
            'size' => 'nullable|string',
            'requirements' => 'nullable|string',
            'notes' => 'nullable|string',
            'internal_notes' => 'nullable|string',
        ]);

        $inquiry = QuotationRequest::create($validated);

        return redirect()->route('admin.inquiries.show', $inquiry)
            ->with('success', 'Inquiry created successfully.');
    }

    /**
     * Display the specified inquiry
     */
    public function show(QuotationRequest $inquiry)
    {
        $inquiry->load(['product', 'user', 'supplier', 'supplierQuotations.supplier', 'generatedQuote']);
        
        // Get available suppliers for this product
        $availableSuppliers = User::whereHas('roles', function ($query) {
            $query->where('name', 'supplier');
        })->get();

        return view('admin.inquiries.show', compact('inquiry', 'availableSuppliers'));
    }

    /**
     * Forward inquiry to supplier
     */
    public function forwardToSupplier(Request $request, QuotationRequest $inquiry)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:users,id',
            'internal_notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $inquiry->update([
                'supplier_id' => $validated['supplier_id'],
                'status' => 'forwarded',
                'forwarded_at' => now(),
                'internal_notes' => $validated['internal_notes'] ?? $inquiry->internal_notes,
            ]);

            // Send email notification to supplier
            $supplier = User::find($validated['supplier_id']);
            $this->sendSupplierNotification($inquiry, $supplier);

            DB::commit();

            return redirect()->back()->with('success', 'Inquiry forwarded to supplier successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error forwarding inquiry to supplier: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to forward inquiry to supplier.');
        }
    }

    /**
     * Update inquiry status
     */
    public function updateStatus(Request $request, QuotationRequest $inquiry)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,forwarded,supplier_responded,quote_created,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $inquiry->update([
            'status' => $validated['status'],
            'internal_notes' => $validated['notes'] ?? $inquiry->internal_notes,
        ]);

        return redirect()->back()->with('success', 'Inquiry status updated successfully.');
    }

    /**
     * Generate customer quote from supplier quotation
     */
    public function generateQuote(Request $request, QuotationRequest $inquiry)
    {
        $validated = $request->validate([
            'supplier_quotation_id' => 'required|exists:supplier_quotations,id',
            'markup_percentage' => 'required|numeric|min:0|max:100',
            'additional_notes' => 'nullable|string',
        ]);

        $supplierQuotation = SupplierQuotation::find($validated['supplier_quotation_id']);
        
        DB::beginTransaction();
        try {
            // Calculate customer price with markup
            $supplierPrice = $supplierQuotation->unit_price;
            $markupAmount = ($supplierPrice * $validated['markup_percentage']) / 100;
            $customerPrice = $supplierPrice + $markupAmount;

            // Create customer quote
            $quote = Quote::create([
                'customer_name' => $inquiry->user->name,
                'quote_date' => Carbon::today(),
                'expiry_date' => Carbon::today()->addDays(30),
                'salesperson' => auth()->user()->name,
                'subject' => 'Quotation for ' . $inquiry->product->name,
                'customer_notes' => $validated['additional_notes'],
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);

            // Create quote item
            QuoteItem::create([
                'quote_id' => $quote->id,
                'product_id' => $inquiry->product_id,
                'description' => $supplierQuotation->description ?? $inquiry->product->name,
                'quantity' => $inquiry->quantity,
                'unit_price' => $customerPrice,
                'amount' => $customerPrice * $inquiry->quantity,
                'size' => $supplierQuotation->size ?? $inquiry->size,
                'sort_order' => 1,
            ]);

            // Update quote totals
            $quote->calculateTotals();

            // Update inquiry status
            $inquiry->update([
                'status' => 'quote_created',
                'generated_quote_id' => $quote->id,
            ]);

            // Update supplier quotation status
            $supplierQuotation->update(['status' => 'accepted']);

            DB::commit();

            return redirect()->route('admin.quotes.show', $quote)
                ->with('success', 'Customer quote generated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error generating customer quote: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to generate customer quote.');
        }
    }

    /**
     * Send notification to supplier
     */
    private function sendSupplierNotification(QuotationRequest $inquiry, User $supplier)
    {
        try {
            // Here you would implement the email notification
            // For now, we'll just log it
            Log::info('Supplier notification sent', [
                'inquiry_id' => $inquiry->id,
                'supplier_id' => $supplier->id,
                'product' => $inquiry->product->name,
            ]);

            // You can implement actual email sending here using Mail facade
            /*
            Mail::to($supplier->email)->send(new SupplierInquiryNotification($inquiry));
            */
        } catch (\Exception $e) {
            Log::error('Failed to send supplier notification: ' . $e->getMessage());
        }
    }

    /**
     * Cancel inquiry
     */
    public function cancel(Request $request, QuotationRequest $inquiry)
    {
        $validated = $request->validate([
            'reason' => 'nullable|string',
        ]);

        $inquiry->update([
            'status' => 'cancelled',
            'internal_notes' => ($inquiry->internal_notes ?? '') . "\n\nCancelled: " . ($validated['reason'] ?? 'No reason provided'),
        ]);

        return redirect()->back()->with('success', 'Inquiry cancelled successfully.');
    }
} 