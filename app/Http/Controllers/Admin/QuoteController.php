<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\Customer;
use App\Mail\QuoteEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class QuoteController extends Controller
{
    /**
     * Display a listing of quotes
     */
    public function index()
    {
        // Additional security check
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $quotes = Quote::with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.quotes.index', compact('quotes'));
    }

    /**
     * Show the form for creating a new quote
     */
    public function create()
    {
        $customers = \App\Models\Customer::select('id', 'name', 'email', 'company_name')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
            
        $products = \App\Models\Product::with(['brand', 'category', 'specifications'])
            ->orderBy('name')
            ->get();
            
        return view('admin.quotes.create', compact('customers', 'products'));
    }

    /**
     * Store a newly created quote
     */
    public function store(Request $request)
    {
        // Check if user is authenticated and is admin
        if (!auth()->check()) {
            \Log::warning('QuoteController store: User not authenticated');
            return redirect()->route('login')->with('error', 'Please log in to continue.');
        }
        
        if (!auth()->user()->isAdmin()) {
            \Log::warning('QuoteController store: User is not admin', ['user_id' => auth()->id()]);
            abort(403, 'Unauthorized access.');
        }
        
        \Log::info('QuoteController store: Starting validation', [
            'user_id' => auth()->id(),
            'customer_id' => $request->input('customer_id')
        ]);

        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'reference_number' => 'nullable|string|max:255',
            'quote_date' => 'required|date',
            'expiry_date' => 'required|date|after:quote_date',
            'salesperson' => 'nullable|string|max:255',
            'subject' => 'nullable|string',
            'customer_notes' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
            'status' => 'required|in:draft,sent,invoiced',
            'currency' => 'required|string|in:AED,USD',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.item_details' => 'required|string',
            'items.*.specifications' => 'nullable|string',
            'items.*.size' => 'nullable|string|max:50',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0|max:100',
            'attachments.*' => 'nullable|file|max:10240' // 10MB max
        ]);

        // Get customer name from the customer record
        $customer = \App\Models\Customer::findOrFail($request->customer_id);
        
        $quote = Quote::create([
            'customer_name' => $customer->name,
            'reference_number' => $request->reference_number,
            'quote_date' => $request->quote_date,
            'expiry_date' => $request->expiry_date,
            'salesperson' => $request->salesperson,
            'subject' => $request->subject,
            'customer_notes' => $request->customer_notes,
            'terms_conditions' => $request->terms_conditions,
            'status' => $request->status,
            'currency' => $request->currency,
            'created_by' => Auth::id(),
        ]);

        // Handle attachments
        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('quote-attachments', 'public');
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path
                ];
            }
            $quote->update(['attachments' => $attachments]);
        }

        // Create quote items
        foreach ($request->items as $index => $itemData) {
            QuoteItem::create([
                'quote_id' => $quote->id,
                'product_id' => $itemData['product_id'],
                'item_details' => $itemData['item_details'],
                'specifications' => $itemData['specifications'] ?? null,
                'size' => $itemData['size'] ?? null,
                'quantity' => $itemData['quantity'],
                'rate' => $itemData['rate'],
                'discount' => $itemData['discount'] ?? 0,
                'sort_order' => $index + 1,
            ]);
        }

        $quote->calculateTotals();

        return redirect()->route('admin.quotes.index')
            ->with('success', 'Quote created successfully!');
    }

    /**
     * Display the specified quote
     */
    public function show(Quote $quote)
    {
        $quote->load(['items.product.specifications', 'creator']);
        return view('admin.quotes.show', compact('quote'));
    }

    /**
     * Show the form for editing the specified quote
     */
    public function edit(Quote $quote)
    {
        $quote->load('items');
        $customers = \App\Models\Customer::select('id', 'name', 'email', 'company_name')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
            
        $products = \App\Models\Product::with(['brand', 'category', 'specifications'])
            ->orderBy('name')
            ->get();
            
        return view('admin.quotes.edit', compact('quote', 'customers', 'products'));
    }

    /**
     * Update the specified quote
     */
    public function update(Request $request, Quote $quote)
    {
        try {
            \Log::info('QuoteController update: Starting update for quote', [
                'quote_id' => $quote->id,
                'quote_number' => $quote->quote_number
            ]);

            $request->validate([
                'customer_name' => 'required|string|max:255',
                'reference_number' => 'nullable|string|max:255',
                'quote_date' => 'required|date',
                'expiry_date' => 'required|date|after:quote_date',
                'salesperson' => 'nullable|string|max:255',
                'subject' => 'nullable|string',
                'customer_notes' => 'nullable|string',
                'terms_conditions' => 'nullable|string',
                'status' => 'required|in:draft,sent,invoiced',
                'currency' => 'required|string|in:AED,USD',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'nullable|exists:products,id',
                'items.*.item_details' => 'required|string',
                'items.*.size' => 'nullable|string|max:50',
                'items.*.quantity' => 'required|numeric|min:0',
                'items.*.rate' => 'required|numeric|min:0',
                'items.*.discount' => 'nullable|numeric|min:0|max:100',
                'attachments.*' => 'nullable|file|max:10240'
            ]);

            \Log::info('QuoteController update: Validation passed');

            // Use database transaction to ensure data integrity
            \DB::transaction(function () use ($request, $quote) {
                // Update quote basic information
                $quote->update([
                    'customer_name' => $request->customer_name,
                    'reference_number' => $request->reference_number,
                    'quote_date' => $request->quote_date,
                    'expiry_date' => $request->expiry_date,
                    'salesperson' => $request->salesperson,
                    'subject' => $request->subject,
                    'customer_notes' => $request->customer_notes,
                    'terms_conditions' => $request->terms_conditions,
                    'status' => $request->status,
                    'currency' => $request->currency,
                ]);

                \Log::info('QuoteController update: Quote basic info updated');

                // Handle new attachments
                $existingAttachments = $quote->attachments ?? [];
                if ($request->hasFile('attachments')) {
                    foreach ($request->file('attachments') as $file) {
                        $path = $file->store('quote-attachments', 'public');
                        $existingAttachments[] = [
                            'name' => $file->getClientOriginalName(),
                            'path' => $path
                        ];
                    }
                    $quote->update(['attachments' => $existingAttachments]);
                    \Log::info('QuoteController update: Attachments updated');
                }

                // Delete existing items and create new ones
                \Log::info('QuoteController update: Deleting existing items');
                $quote->items()->delete();
                
                \Log::info('QuoteController update: Creating new items', [
                    'item_count' => count($request->items)
                ]);
                
                foreach ($request->items as $index => $itemData) {
                    $quoteItem = QuoteItem::create([
                        'quote_id' => $quote->id,
                        'product_id' => $itemData['product_id'] ?? null,
                        'item_details' => $itemData['item_details'],
                        'specifications' => $itemData['specifications'] ?? null,
                        'size' => $itemData['size'] ?? null,
                        'quantity' => $itemData['quantity'],
                        'rate' => $itemData['rate'],
                        'discount' => $itemData['discount'] ?? 0,
                        'sort_order' => $index + 1,
                    ]);
                    
                    \Log::info('QuoteController update: Item created', [
                        'item_id' => $quoteItem->id,
                        'product_id' => $quoteItem->product_id
                    ]);
                }

                // Recalculate totals
                $quote->calculateTotals();
                \Log::info('QuoteController update: Totals calculated');
            });

            \Log::info('QuoteController update: Quote updated successfully', [
                'quote_id' => $quote->id
            ]);

            return redirect()->route('admin.quotes.index')
                ->with('success', 'Quote updated successfully!');

        } catch (\Exception $e) {
            \Log::error('QuoteController update: Error updating quote', [
                'quote_id' => $quote->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update quote: ' . $e->getMessage());
        }
    }

    /**
     * Send email for quote from index page
     */
    public function sendEmail(Request $request, Quote $quote)
    {
        $request->validate([
            'customer_email' => 'required|email',
            'cc_emails' => 'nullable|string'
        ]);

        try {
            // Parse CC emails
            $ccEmails = [];
            if ($request->filled('cc_emails')) {
                $ccEmails = array_filter(
                    array_map('trim', explode(',', $request->cc_emails)),
                    function($email) {
                        return filter_var($email, FILTER_VALIDATE_EMAIL);
                    }
                );
            }

            // Find or create customer by email
            $customer = Customer::where('email', $request->customer_email)->first();
            if (!$customer) {
                // Create a minimal customer record for sending email
                $customer = new Customer();
                $customer->email = $request->customer_email;
                $customer->name = $quote->customer_name; // Use quote's customer name
            }

            Mail::to($request->customer_email)->send(new QuoteEmail($quote, $customer, $ccEmails));
            
            $previousStatus = $quote->status;
            
            // Update status to sent if email was sent successfully
            if ($quote->status === 'draft') {
                $quote->update(['status' => 'sent']);
            }
            
            // Return JSON response for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Quote email sent successfully!',
                    'previous_status' => $previousStatus,
                    'new_status' => $quote->fresh()->status
                ]);
            }
            
            return redirect()->back()->with('success', 'Quote email sent successfully to ' . $request->customer_email . '!');
        } catch (\Exception $e) {
            Log::error('Failed to send quote email: ' . $e->getMessage());
            
            // Return JSON response for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send email: ' . $e->getMessage()
                ]);
            }
            
            return redirect()->back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified quote
     */
    public function destroy(Quote $quote)
    {
        // Delete attachments from storage
        if ($quote->attachments) {
            foreach ($quote->attachments as $attachment) {
                Storage::disk('public')->delete($attachment['path']);
            }
        }

        $quote->delete();

        return redirect()->route('admin.quotes.index')
            ->with('success', 'Quote deleted successfully!');
    }

    /**
     * Generate PDF for the quote
     */
    public function generatePdf(Quote $quote)
    {
        $quote->load(['items.product.specifications']);
        
        // Get customer data to show company name in PDF
        $customer = \App\Models\Customer::where('name', $quote->customer_name)->first();
        
        $pdf = Pdf::loadView('admin.quotes.pdf', compact('quote', 'customer'));
        
        return $pdf->download($quote->quote_number . '.pdf');
    }

    /**
     * Update quote status
     */
    public function updateStatus(Request $request, Quote $quote)
    {
        $request->validate([
            'status' => 'required|in:draft,sent,invoiced'
        ]);

        $quote->update(['status' => $request->status]);

        return redirect()->back()
            ->with('success', 'Quote status updated successfully!');
    }

    /**
     * Remove attachment
     */
    public function removeAttachment(Request $request, Quote $quote)
    {
        $attachmentIndex = $request->attachment_index;
        $attachments = $quote->attachments ?? [];
        
        if (isset($attachments[$attachmentIndex])) {
            // Delete file from storage
            Storage::disk('public')->delete($attachments[$attachmentIndex]['path']);
            
            // Remove from array
            unset($attachments[$attachmentIndex]);
            $attachments = array_values($attachments); // Reindex array
            
            $quote->update(['attachments' => $attachments]);
        }

        return redirect()->back()
            ->with('success', 'Attachment removed successfully!');
    }

    /**
     * Convert quote to proforma invoice
     */
    public function convertToProforma(Quote $quote)
    {
        Log::info('QuoteController convertToProforma: Starting conversion', [
            'quote_id' => $quote->id,
            'quote_status' => $quote->status,
            'items_count' => $quote->items->count(),
            'user_id' => Auth::id()
        ]);

        // Check if quote can be converted
        if ($quote->status === 'invoiced') {
            Log::warning('QuoteController convertToProforma: Quote already invoiced', ['quote_id' => $quote->id]);
            return redirect()->route('admin.quotes.index')
                ->with('error', 'This quote has already been converted to an invoice.');
        }

        // Check if quote has items
        if ($quote->items->count() === 0) {
            Log::warning('QuoteController convertToProforma: Quote has no items', ['quote_id' => $quote->id]);
            return redirect()->route('admin.quotes.index')
                ->with('error', 'Cannot convert quote to invoice. Quote has no items.');
        }

        try {
            Log::info('QuoteController convertToProforma: Beginning transaction', ['quote_id' => $quote->id]);
            DB::beginTransaction();

            // Find customer by name to get billing and shipping addresses
            Log::info('QuoteController convertToProforma: Looking for customer', [
                'quote_id' => $quote->id,
                'customer_name' => $quote->customer_name,
                'customer_name_length' => strlen($quote->customer_name)
            ]);
            
            $customer = Customer::where('name', $quote->customer_name)->first();
            
            Log::info('QuoteController convertToProforma: Customer lookup result', [
                'quote_id' => $quote->id,
                'customer_found' => $customer ? true : false,
                'customer_id' => $customer ? $customer->id : null,
                'customer_name_from_db' => $customer ? $customer->name : null
            ]);
            
            if (!$customer) {
                Log::error('QuoteController convertToProforma: Customer not found', [
                    'quote_id' => $quote->id,
                    'customer_name' => $quote->customer_name
                ]);
                return redirect()->route('admin.quotes.index')
                    ->with('error', 'Customer not found. Please ensure the customer exists in the system.');
            }

            // Get billing and shipping addresses with proper null handling
            $billingAddress = 'Billing address not available';
            $shippingAddress = 'Shipping address not available';
            
            try {
                // Use Customer model's accessor methods for cleaner code
                if ($customer) {
                    $billingAddress = $customer->billing_address ?: 'Billing address not available';
                    $shippingAddress = $customer->shipping_address ?: $billingAddress;
                }
                
                Log::info('QuoteController convertToProforma: Address processing successful', [
                    'quote_id' => $quote->id,
                    'billing_address_length' => strlen($billingAddress),
                    'shipping_address_length' => strlen($shippingAddress)
                ]);
                
            } catch (\Exception $e) {
                Log::error('QuoteController convertToProforma: Error processing addresses', [
                    'quote_id' => $quote->id,
                    'error' => $e->getMessage()
                ]);
                // Keep the default fallback values
            }

            // Create proforma invoice
            $invoice = \App\Models\Invoice::create([
                'type' => 'proforma',
                'is_proforma' => true,
                'quote_id' => $quote->id,
                'customer_name' => $quote->customer_name,
                'billing_address' => $billingAddress,
                'shipping_address' => $shippingAddress,
                'invoice_date' => now(),
                'due_date' => now()->addDays(30),
                'description' => $quote->subject,
                'terms_conditions' => $quote->terms_conditions,
                'notes' => $quote->customer_notes,
                'sub_total' => $quote->sub_total,
                'tax_amount' => $quote->tax_amount ?? 0,
                'discount_amount' => $quote->discount_amount ?? 0,
                'total_amount' => $quote->total_amount,
                'currency' => $quote->currency ?: 'AED', // Default to AED if not set
                'payment_status' => 'pending',
                'payment_terms' => 'advance_50', // Default to 50% advance
                'status' => 'draft',
                'reference_number' => $quote->reference_number,
                'created_by' => Auth::id(),
            ]);

            // Copy quote items to invoice items
            foreach ($quote->items as $quoteItem) {
                Log::info("Converting quote item {$quoteItem->id}: product_id={$quoteItem->product_id}, item_details={$quoteItem->item_details}");
                
                // Calculate values
                $subtotal = $quoteItem->quantity * $quoteItem->rate;
                $discountAmount = ($subtotal * ($quoteItem->discount ?? 0)) / 100;
                $lineTotal = $subtotal - $discountAmount;
                $tax = 0; // Default tax to 0, can be updated later if needed
                $total = $lineTotal + $tax;
                
                // Get item description, fallback to product name if not set
                $description = $quoteItem->item_details;
                if (empty($description) && $quoteItem->product) {
                    $description = $quoteItem->product->name;
                }
                if (empty($description)) {
                    $description = 'Product #' . $quoteItem->product_id;
                }
                
                $invoiceItemData = [
                    'invoice_id' => $invoice->id,
                    'product_id' => $quoteItem->product_id,
                    'description' => $description,
                    'size' => $quoteItem->size,
                    'quantity' => $quoteItem->quantity,
                    'unit_price' => $quoteItem->rate,
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'total' => $total,
                    'discount_percentage' => $quoteItem->discount ?? 0,
                    'discount_amount' => $discountAmount,
                    'line_total' => $lineTotal,
                    'unit_of_measure' => null, // Quote items don't have unit_of_measure
                    'specifications' => $quoteItem->specifications,
                    'sort_order' => $quoteItem->sort_order,
                ];
                
                Log::info("Creating invoice item with data:", $invoiceItemData);
                
                $invoiceItem = \App\Models\InvoiceItem::create($invoiceItemData);
                
                Log::info("Created invoice item {$invoiceItem->id}: product_id={$invoiceItem->product_id}, subtotal={$invoiceItem->subtotal}, total={$invoiceItem->total}");
            }

            // Update quote status
            $quote->update(['status' => 'invoiced']);

            DB::commit();
            
            Log::info('QuoteController convertToProforma: Conversion successful', [
                'quote_id' => $quote->id,
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number
            ]);

            return redirect()->route('admin.invoices.show', $invoice)
                ->with('success', 'Quote has been successfully converted to proforma invoice: ' . $invoice->invoice_number);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to convert quote to proforma invoice', [
                'quote_id' => $quote->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('admin.quotes.index')
                ->with('error', 'Failed to convert quote to proforma invoice. Please try again.');
        }
    }
} 