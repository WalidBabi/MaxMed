<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Quote;
use App\Models\Order;
use App\Models\Delivery;
use App\Models\Customer;
use App\Models\Product;
use App\Mail\InvoiceEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices
     */
    public function index(Request $request)
    {
        $query = Invoice::with(['quote', 'order', 'delivery', 'creator', 'payments', 'parentInvoice', 'childInvoices']);

        // Apply filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%");
            });
        }

        // Custom ordering to group related invoices together
        // Order by created_at desc first (most recently created), then by parent_invoice_id, then by type
        $query->orderBy('created_at', 'desc')
              ->orderByRaw('COALESCE(parent_invoice_id, id)')
              ->orderBy('parent_invoice_id', 'asc')
              ->orderBy('type', 'asc'); // 'final' comes before 'proforma' alphabetically, but we want proforma first

        // Load necessary relationships to avoid N+1 queries
        $invoices = $query->with([
            'quote',
            'order.delivery',
            'delivery',
            'parentInvoice.items',
            'childInvoices'
        ])->paginate(20);

        // Calculate totals avoiding double counting
        // - Include proforma invoices that haven't been converted (no child final invoice)
        // - Include final invoices that are standalone (no parent proforma)
        $totalsQuery = Invoice::query();
        
        // Apply same filters for totals calculation
        if ($request->filled('type')) {
            $totalsQuery->where('type', $request->type);
        }
        if ($request->filled('status')) {
            $totalsQuery->where('status', $request->status);
        }
        if ($request->filled('payment_status')) {
            $totalsQuery->where('payment_status', $request->payment_status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $totalsQuery->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%");
            });
        }
        
        // Filter to show only sent final invoices
        $totalsQuery->where('type', 'final')
                   ->where('status', 'sent');
        
        $invoiceTotals = [
            'aed' => $totalsQuery->where('currency', 'AED')->sum('total_amount'),
            'usd' => $totalsQuery->where('currency', 'USD')->sum('total_amount')
        ];

        // Calculate invoice counts by type
        $invoiceCounts = [
            'proforma' => Invoice::where('type', 'proforma')->count(),
            'final' => Invoice::where('type', 'final')->count(),
            'total' => Invoice::count()
        ];

        // All-pages pending count (not limited by pagination)
        $pendingAll = Invoice::where('payment_status', 'pending')->count();

        // Calculate unique paid orders (avoiding double counting proforma + final)
        $paidOrdersCount = Invoice::where('payment_status', 'paid')
            ->where(function($q) {
                $q->where(function($subQ) {
                    // Proforma invoices that haven't been converted
                    $subQ->where('type', 'proforma')
                         ->where('payment_status', 'paid')
                         ->whereNotExists(function($existsQ) {
                             $existsQ->select(DB::raw(1))
                                     ->from('invoices as child')
                                     ->whereRaw('child.parent_invoice_id = invoices.id')
                                     ->where('child.type', 'final');
                         });
                })->orWhere(function($subQ) {
                    // Final invoices that are standalone (no parent)
                    $subQ->where('type', 'final')
                         ->where('payment_status', 'paid')
                         ->whereNull('parent_invoice_id');
                })->orWhere(function($subQ) {
                    // Final invoices that are converted from proforma (have a parent)
                    $subQ->where('type', 'final')
                         ->where('payment_status', 'paid')
                         ->whereNotNull('parent_invoice_id');
                });
            })
            ->count();

        // Get filter options
        $filterOptions = [
            'types' => ['proforma' => 'Proforma', 'final' => 'Final'],
            'statuses' => Invoice::STATUS_OPTIONS,
            'payment_statuses' => Invoice::PAYMENT_STATUS
        ];

        return view('admin.invoices.index', compact('invoices', 'filterOptions', 'invoiceTotals', 'invoiceCounts', 'paidOrdersCount', 'pendingAll'));
    }

    /**
     * Show the form for creating a new invoice
     */
    public function create(Request $request)
    {
        $quote = null;
        $order = null;

        if ($request->filled('quote_id')) {
            $quote = Quote::with('items')->findOrFail($request->quote_id);
        }

        if ($request->filled('order_id')) {
            $order = Order::with('orderItems.product')->findOrFail($request->order_id);
        }

        $customers = Customer::select('id', 'name', 'email', 'company_name')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $products = \App\Models\Product::with(['brand', 'category'])
            ->orderBy('name')
            ->get();

        return view('admin.invoices.create', compact('quote', 'order', 'customers', 'products'));
    }

    /**
     * Convert quote to proforma invoice
     */
    public function convertFromQuote(Quote $quote)
    {
        try {
            DB::transaction(function () use ($quote) {
                // Find customer by name to get additional details
                $customer = \App\Models\Customer::where('name', $quote->customer_name)->first();
                
                // Get billing and shipping addresses with proper fallback
                $billingAddress = 'Billing address not available';
                $shippingAddress = 'Shipping address not available';
                
                if ($customer) {
                    // Use Customer model's accessor methods for cleaner code
                    $billingAddress = $customer->billing_address ?: 'Billing address not available';
                    $shippingAddress = $customer->shipping_address ?: $billingAddress;
                }

                // Create the invoice
                $invoice = Invoice::create([
                    'type' => 'proforma',
                    'is_proforma' => true,
                    'quote_id' => $quote->id,
                    'customer_name' => $quote->customer_name,
                    'billing_address' => $billingAddress,
                    'shipping_address' => $shippingAddress,
                    'invoice_date' => now(),
                    'due_date' => now()->addDays(30),
                    'description' => $quote->subject ?: 'Invoice from Quote ' . $quote->quote_number,
                    'terms_conditions' => $quote->terms_conditions,
                    'notes' => $quote->customer_notes,
                    'sub_total' => $quote->sub_total,
                    'shipping_rate' => $quote->shipping_rate ?? 0,
                    'tax_amount' => $quote->vat_amount ?? 0,
                    'vat_rate' => $quote->vat_rate ?? 0,
                    'customs_clearance_fee' => $quote->customs_clearance_fee ?? 0,
                    'discount_amount' => 0,
                    'total_amount' => $quote->total_amount,
                    'currency' => $quote->currency ?: 'AED', // Default to AED if not set
                    'payment_terms' => $quote->payment_terms ?? 'advance_50',
                    'payment_status' => 'pending',
                    'status' => 'draft',
                    'reference_number' => $quote->reference_number,
                    'created_by' => \Auth::id(),
                ]);

                // Create invoice items
                foreach ($quote->items as $quoteItem) {
                    // Calculate item totals
                    $subtotal = $quoteItem->quantity * $quoteItem->rate;
                    $discountAmount = ($subtotal * ($quoteItem->discount ?? 0)) / 100;
                    $total = $subtotal - $discountAmount;

                    // Get item description, fallback to product name if not set
                    $itemDescription = $quoteItem->item_details;
                    if (empty($itemDescription) && $quoteItem->product) {
                        $itemDescription = $quoteItem->product->name;
                    }
                    if (empty($itemDescription)) {
                        $itemDescription = 'Product #' . $quoteItem->product_id;
                    }

                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'product_id' => $quoteItem->product_id,
                        'description' => $itemDescription,
                        'size' => $quoteItem->size,
                        'quantity' => $quoteItem->quantity,
                        'unit_price' => $quoteItem->rate,
                        'discount_percentage' => $quoteItem->discount ?? 0,
                        'discount_amount' => $discountAmount,
                        'line_total' => $total,
                        'unit_of_measure' => null, // Quote items don't have unit_of_measure
                        'specifications' => $quoteItem->specifications,
                        'sort_order' => $quoteItem->sort_order ?? 0
                    ]);
                }

                // Update quote status
                $quote->update(['status' => 'invoiced']);

                // Calculate and update invoice totals
                $invoice->refresh(); // Reload the invoice with items
                $invoice->calculateTotals();
            });

            return redirect()->route('admin.invoices.index')
                ->with('success', 'Quote converted to invoice successfully!');

        } catch (\Exception $e) {
            \Log::error('Failed to convert quote to invoice', [
                'quote_id' => $quote->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Failed to convert quote to invoice: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created invoice
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:proforma,final',
            'customer_name' => 'required|string|max:255',
            'billing_address' => 'required|string',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'payment_terms' => 'required|in:advance_50,advance_100,on_delivery,net_30,custom',
            'advance_percentage' => 'nullable|numeric|min:0|max:100',
            'shipping_rate' => 'nullable|numeric|min:0',
            'vat_rate' => 'nullable|numeric|min:0|max:100',
            'customs_clearance_fee' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.size' => 'nullable|string|max:100'
        ]);

        try {
            DB::beginTransaction();

            $invoice = Invoice::create([
                'type' => $request->type,
                'customer_name' => $request->customer_name,
                'billing_address' => $request->billing_address,
                'shipping_address' => $request->shipping_address,
                'invoice_date' => $request->invoice_date,
                'due_date' => $request->due_date,
                'description' => $request->description,
                'terms_conditions' => $request->terms_conditions,
                'notes' => $request->notes,
                'payment_terms' => $request->payment_terms,
                'advance_percentage' => $request->advance_percentage,
                'shipping_rate' => $request->shipping_rate ?? 0,
                'vat_rate' => $request->vat_rate ?? 0,
                'customs_clearance_fee' => $request->customs_clearance_fee ?? 0,
                'currency' => 'AED',
                'is_proforma' => $request->type === 'proforma',
                'requires_advance_payment' => in_array($request->payment_terms, ['advance_50', 'advance_100', 'custom']),
                'reference_number' => $request->reference_number,
                'po_number' => $request->po_number,
                'created_by' => Auth::id()
            ]);

            // Create invoice items
            foreach ($request->items as $index => $itemData) {
                // Use line_total from form (which includes discount calculation)
                $quantity = $itemData['quantity'];
                $unitPrice = $itemData['unit_price'];
                $lineTotal = $itemData['line_total'] ?? ($quantity * $unitPrice);
                
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $itemData['product_id'] ?? null,
                    'description' => $itemData['description'] ?? $itemData['item_description'] ?? '',
                    'size' => $itemData['size'] ?? null,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'discount_percentage' => $itemData['discount_percentage'] ?? 0,
                    'discount_amount' => 0, // We'll calculate this in the model's boot method
                    'line_total' => $lineTotal,
                    'unit_of_measure' => $itemData['unit_of_measure'] ?? null,
                    'specifications' => $itemData['specifications'] ?? null,
                    'sort_order' => $index + 1
                ]);
            }

            $invoice->calculateTotals();

            DB::commit();

            return redirect()->route('admin.invoices.show', $invoice)
                ->with('success', 'Invoice created successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to create invoice: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to create invoice: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified invoice
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['items.product.specifications', 'quote', 'order', 'delivery', 'payments', 'creator', 'parentInvoice', 'childInvoices']);
        return view('admin.invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified invoice
     */
    public function edit(Invoice $invoice)
    {
        $invoice->load(['items', 'items.product']);
        
        $customers = Customer::select('id', 'name', 'email', 'company_name')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $products = \App\Models\Product::with(['brand', 'category'])
            ->orderBy('name')
            ->get();

        return view('admin.invoices.edit', compact('invoice', 'customers', 'products'));
    }

    /**
     * Update the specified invoice
     */
    public function update(Request $request, Invoice $invoice)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'billing_address' => 'required|string',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'payment_terms' => 'required|in:advance_50,advance_100,on_delivery,net_30,custom',
            'shipping_rate' => 'nullable|numeric|min:0',
            'vat_rate' => 'nullable|numeric|min:0|max:100',
            'customs_clearance_fee' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1'
        ]);

        try {
            DB::beginTransaction();

            // Preserve existing VAT unless explicitly changed; if vat_rate provided as 0, also zero tax_amount
            $vatRateInput = $request->vat_rate;
            $vatRateNormalized = is_null($vatRateInput) ? $invoice->vat_rate : (float)$vatRateInput;
            $taxAmountToSet = ($vatRateNormalized <= 0) ? 0 : ($invoice->tax_amount ?? 0);

            $invoice->update([
                'customer_name' => $request->customer_name,
                'billing_address' => $request->billing_address,
                'shipping_address' => $request->shipping_address,
                'invoice_date' => $request->invoice_date,
                'due_date' => $request->due_date,
                'description' => $request->description,
                'terms_conditions' => $request->terms_conditions,
                'notes' => $request->notes,
                'payment_terms' => $request->payment_terms,
                'advance_percentage' => $request->advance_percentage,
                'shipping_rate' => $request->shipping_rate ?? 0,
                'vat_rate' => $vatRateNormalized,
                'tax_amount' => $taxAmountToSet,
                'customs_clearance_fee' => $request->customs_clearance_fee ?? 0,
                'reference_number' => $request->reference_number,
                'po_number' => $request->po_number,
                'updated_by' => Auth::id()
            ]);

            // Delete existing items and recreate
            $invoice->items()->delete();

            foreach ($request->items as $index => $itemData) {
                // Use line_total from form (which includes discount calculation)
                $quantity = $itemData['quantity'];
                $unitPrice = $itemData['unit_price'];
                $lineTotal = $itemData['line_total'] ?? ($quantity * $unitPrice);
                
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $itemData['product_id'] ?? null,
                    'description' => $itemData['description'] ?? $itemData['item_description'] ?? '',
                    'size' => $itemData['size'] ?? null,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'discount_percentage' => $itemData['discount_percentage'] ?? 0,
                    'discount_amount' => 0, // We'll calculate this in the model's boot method
                    'line_total' => $lineTotal,
                    'unit_of_measure' => $itemData['unit_of_measure'] ?? null,
                    'specifications' => $itemData['specifications'] ?? null,
                    'sort_order' => $index + 1
                ]);
            }

            $invoice->calculateTotals();

            DB::commit();

            return redirect()->route('admin.invoices.show', $invoice)
                ->with('success', 'Invoice updated successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Failed to update invoice: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified invoice
     */
    public function destroy(Invoice $invoice)
    {
        try {
            // Check if invoice has payments
            if ($invoice->payments()->where('status', 'completed')->exists()) {
                return redirect()->back()->with('error', 'Cannot delete invoice with completed payments.');
            }

            $invoice->delete();

            return redirect()->route('admin.invoices.index')
                ->with('success', 'Invoice deleted successfully!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete invoice: ' . $e->getMessage());
        }
    }

    /**
     * Update invoice status
     */
    public function updateStatus(Request $request, Invoice $invoice)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(Invoice::STATUS_OPTIONS))
        ]);

        $invoice->update([
            'status' => $request->status,
            'updated_by' => Auth::id()
        ]);

        return redirect()->back()->with('success', 'Invoice status updated successfully!');
    }

    /**
     * Convert proforma to final invoice
     */
    public function convertToFinal(Invoice $invoice)
    {
        if (!$invoice->canConvertToFinalInvoice()) {
            return redirect()->back()->with('error', 'Cannot convert this invoice to final invoice.');
        }

        try {
            $finalInvoice = $invoice->convertToFinalInvoice();

            return redirect()->route('admin.invoices.show', $finalInvoice)
                ->with('success', 'Final invoice created successfully from proforma ' . $invoice->invoice_number);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to convert to final invoice: ' . $e->getMessage());
        }
    }

    /**
     * Record payment
     */
    public function recordPayment(Request $request, Invoice $invoice)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:' . implode(',', array_keys(Payment::PAYMENT_METHODS)),
            'payment_date' => 'required|date',
            'transaction_reference' => 'nullable|string|max:255',
            'payment_notes' => 'nullable|string'
        ]);

        // Ensure the payment amount doesn't exceed the remaining balance
        $invoice->calculateTotals(); // Ensure totals are up to date
        $remainingAmount = $invoice->total_amount - $invoice->paid_amount;
        
        if ($request->amount > $remainingAmount) {
            return redirect()->back()
                ->withErrors(['amount' => "Payment amount cannot exceed the remaining balance of {$remainingAmount} {$invoice->currency}"])
                ->withInput();
        }

        try {
            DB::beginTransaction();
            
            Log::info("Recording payment for invoice {$invoice->id}: amount={$request->amount}, current_status={$invoice->status}, current_payment_status={$invoice->payment_status}");
            
            // Ensure invoice status is appropriate before creating payment so hooks see correct state
            $freshBeforePayment = $invoice->fresh();
            if ($freshBeforePayment->type === 'proforma' && $freshBeforePayment->status === 'draft') {
                Log::info("Pre-payment: updating invoice {$freshBeforePayment->id} status from 'draft' to 'sent' to enable workflow");
                $freshBeforePayment->update(['status' => 'sent']);
            }

            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'amount' => $request->amount,
                'currency' => 'AED',
                'payment_method' => $request->payment_method,
                'payment_date' => $request->payment_date,
                'transaction_reference' => $request->transaction_reference,
                'payment_notes' => $request->payment_notes,
                'status' => 'completed',
                'processed_by' => Auth::id(),
                'processed_at' => now()
            ]);

            Log::info("Payment {$payment->id} created successfully for invoice {$invoice->id}");
            
            // Payment::saved hook updates invoice paid_amount/payment_status and triggers automation.
            $freshInvoice = $invoice->fresh();
            Log::info("Post-payment: invoice {$freshInvoice->id} state: status={$freshInvoice->status}, payment_status={$freshInvoice->payment_status}");

            // Check final status
            $finalInvoice = $invoice->fresh();
            Log::info("Final invoice state after workflow: status={$finalInvoice->status}, order_id={$finalInvoice->order_id}");

            DB::commit();

            // Add success message with workflow status
            $message = $this->generatePaymentSuccessMessage($invoice, $finalInvoice);

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to record payment: ' . $e->getMessage());
            Log::error('Payment recording stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Failed to record payment: ' . $e->getMessage());
        }
    }

    /**
     * Send invoice email
     */
    public function sendEmail(Request $request, Invoice $invoice)
    {
        $request->validate([
            'customer_email' => 'required|email',
            'cc_emails' => 'nullable|string'
        ]);

        try {
            $ccEmails = $request->cc_emails ? 
                array_map('trim', explode(',', $request->cc_emails)) : [];

            $emailData = [
                'to_email' => $request->customer_email,
                'cc_emails' => $ccEmails,
                'subject' => ($invoice->type === 'proforma' ? 'Proforma Invoice' : 'Invoice') . ' ' . $invoice->invoice_number,
                'message' => $request->message ?? null
            ];

            Mail::to($request->customer_email)
                ->cc($ccEmails)
                ->send(new InvoiceEmail($invoice, $emailData));

            $previousStatus = $invoice->status;

            // Update email history
            $emailHistory = $invoice->email_history ?? [];
            $emailHistory[] = [
                'sent_at' => now()->toISOString(),
                'to' => $request->customer_email,
                'cc' => $ccEmails,
                'subject' => $emailData['subject']
            ];

            $invoiceUpdate = [
                'email_history' => $emailHistory,
                'sent_at' => now()
            ];

            // Update status to 'sent' if currently 'draft'
            if ($invoice->status === 'draft') {
                $invoiceUpdate['status'] = 'sent';
            }

            $invoice->update($invoiceUpdate);

            // Return JSON response for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Invoice email sent successfully!',
                    'previous_status' => $previousStatus,
                    'new_status' => $invoice->fresh()->status
                ]);
            }

            return redirect()->back()->with('success', 'Invoice email sent successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to send invoice email: ' . $e->getMessage());
            
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
     * Generate PDF
     */
    public function generatePdf(Invoice $invoice)
    {
        $invoice->load(['items.product.specifications', 'delivery', 'parentInvoice', 'order.cashReceipts', 'payments']);
        
        // Get customer data for company name display
        $customer = \App\Models\Customer::where('name', $invoice->customer_name)->first();
        
        $pdf = Pdf::loadView('admin.invoices.pdf', compact('invoice', 'customer'));
        
        return $pdf->download($invoice->invoice_number . '.pdf');
    }

    /**
     * Get customer billing address helper
     */
    private function getCustomerBillingAddress($customerName)
    {
        $customer = Customer::where('name', $customerName)->first();
        
        if ($customer) {
            return $customer->billing_street . "\n" . 
                   $customer->billing_city . ', ' . 
                   $customer->billing_state . ' ' . 
                   $customer->billing_zip;
        }

        return 'Please update customer billing address';
    }

    /**
     * Create order from proforma invoice
     */
    public function createOrder(Invoice $invoice)
    {
        try {
            if ($invoice->type !== 'proforma') {
                return redirect()->back()->with('error', 'Only proforma invoices can be converted to orders.');
            }

            if ($invoice->order_id) {
                return redirect()->back()->with('error', 'This invoice already has an associated order.');
            }

            if (!in_array($invoice->status, ['confirmed', 'sent'])) {
                return redirect()->back()->with('error', 'Invoice must be confirmed or sent before creating an order.');
            }

            DB::beginTransaction();

            // Create order from invoice
            $order = Order::create([
                'user_id' => 1, // Default admin user - you might want to get this from customer
                'proforma_invoice_id' => $invoice->id, // Link to the proforma invoice
                // order_number will be auto-generated by the Order model
                'total_amount' => $invoice->total_amount,
                'currency' => $invoice->currency ?? 'AED',
                'shipping_rate' => $invoice->shipping_rate ?? 0,
                'vat_rate' => $invoice->vat_rate ?? 0,
                'vat_amount' => $invoice->tax_amount ?? 0,
                'customs_clearance_fee' => $invoice->customs_clearance_fee ?? 0,
                'status' => 'processing', // Changed to match orders table enum
                'shipping_address' => $invoice->shipping_address ?: $invoice->billing_address,
                'shipping_city' => 'N/A', // Extract from address if needed
                'shipping_state' => 'N/A',
                'shipping_zipcode' => 'N/A',
                'shipping_phone' => 'N/A',
                'notes' => 'Created from proforma invoice ' . $invoice->invoice_number
            ]);

            // Create order items from invoice items
            foreach ($invoice->items as $invoiceItem) {
                \App\Models\OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $invoiceItem->product_id,
                    'quantity' => $invoiceItem->quantity,
                    'price' => $invoiceItem->product ? $invoiceItem->product->price_aed : $invoiceItem->unit_price,
                    'variation' => $invoiceItem->specifications,
                    'discount_percentage' => $invoiceItem->discount_percentage,
                    'discount_amount' => $invoiceItem->discount_amount
                ]);
            }

            // Link invoice to order
            $invoice->update(['order_id' => $order->id]);

            DB::commit();

            return redirect()->route('admin.orders.show', $order)
                ->with('success', 'Order created successfully from proforma invoice!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to create order from invoice: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to create order: ' . $e->getMessage());
        }
    }

    /**
     * Generate appropriate success message for payment recording
     */
    private function generatePaymentSuccessMessage(Invoice $originalInvoice, Invoice $updatedInvoice): string
    {
        $message = 'Payment recorded successfully!';
        
        // Check if order was created during this payment
        $orderCreatedNow = !$originalInvoice->order_id && $updatedInvoice->order_id;
        
        // Check if order already existed
        $orderAlreadyExisted = $originalInvoice->order_id;
        
        // Special handling for payment terms that can create orders
        $orderCreatingPaymentTerms = ['on_delivery', 'net_30'];
        $isOrderCreatingPaymentTerm = in_array($updatedInvoice->payment_terms, $orderCreatingPaymentTerms);
        
        if ($orderCreatedNow) {
            $message .= ' Order has been automatically created and is now being processed.';
        } elseif ($orderAlreadyExisted && $isOrderCreatingPaymentTerm) {
            $orderNumber = $updatedInvoice->order ? $updatedInvoice->order->order_number : 'N/A';
            
            if ($updatedInvoice->payment_terms === 'on_delivery') {
                $message .= " Order {$orderNumber} is already in progress. Payment will be collected upon delivery.";
            } elseif ($updatedInvoice->payment_terms === 'net_30') {
                $message .= " Order {$orderNumber} is already in progress. Payment recorded for Net 30 terms.";
            }
        } elseif ($orderAlreadyExisted) {
            $orderNumber = $updatedInvoice->order ? $updatedInvoice->order->order_number : 'N/A';
            $message .= " Order {$orderNumber} is already in progress.";
        } else {
            // No order created or exists
            if ($isOrderCreatingPaymentTerm) {
                if ($updatedInvoice->payment_terms === 'on_delivery') {
                    $message .= ' Order creation is pending - please ensure delivery address is complete.';
                } elseif ($updatedInvoice->payment_terms === 'net_30') {
                    $trustLevel = $updatedInvoice->getCustomerTrustLevel();
                    if ($trustLevel === 'high') {
                        $message .= ' Order creation is pending - check invoice and payment status.';
                    } elseif ($trustLevel === 'medium') {
                        $requiredAmount = $updatedInvoice->total_amount * 0.25;
                        if ($updatedInvoice->paid_amount < $requiredAmount) {
                            $message .= ' Order will be created when ' . number_format($requiredAmount, 2) . ' ' . $updatedInvoice->currency . ' (25%) is paid.';
                        } else {
                            $message .= ' Order creation is pending - check invoice status.';
                        }
                    } else {
                        $message .= ' Order will be created when full payment is received.';
                    }
                }
            } else {
                // Other payment terms
                $message .= ' Order creation pending payment requirements.';
            }
        }
        
        return $message;
    }

    /**
     * Link invoice to existing delivery
     */
    public function linkDelivery(Request $request, Invoice $invoice)
    {
        $request->validate([
            'delivery_id' => 'required|exists:deliveries,id'
        ]);

        try {
            $delivery = Delivery::findOrFail($request->delivery_id);
            
            // Check if delivery is already linked to another invoice
            if (Invoice::where('delivery_id', $delivery->id)->exists()) {
                return redirect()->back()->with('error', 'This delivery is already linked to another invoice.');
            }

            $invoice->update(['delivery_id' => $delivery->id]);

            return redirect()->back()->with('success', 'Invoice successfully linked to delivery #' . $delivery->id);

        } catch (\Exception $e) {
            Log::error('Failed to link invoice to delivery: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to link delivery: ' . $e->getMessage());
        }
    }

    /**
     * Get invoice details for AJAX
     */
    public function getDetails(Invoice $invoice)
    {
        // Load items with product details
        $invoice->load('items.product');
        
        $items = $invoice->items->map(function($item) {
            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product ? $item->product->name : $item->item_details,
                'product_description' => $item->product ? $item->product->description : '',
                'specifications' => $item->specifications,
                'size' => $item->size,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'line_total' => $item->line_total,
                'discount_percentage' => $item->discount_percentage ?? 0,
                'product' => $item->product ? [
                    'id' => $item->product->id,
                    'name' => $item->product->name,
                    'description' => $item->product->description,
                    'price_aed' => $item->product->price_aed,
                    'price' => $item->product->price,
                ] : null
            ];
        });

        return response()->json([
            'id' => $invoice->id,
            'invoice_number' => $invoice->invoice_number,
            'customer_name' => $invoice->customer_name,
            'currency' => $invoice->currency,
            'subtotal' => $invoice->subtotal,
            'shipping_rate' => $invoice->shipping_rate,
            'tax_amount' => $invoice->tax_amount,
            'vat_rate' => $invoice->vat_rate,
            'customs_clearance_fee' => $invoice->customs_clearance_fee,
            'discount_amount' => $invoice->discount_amount,
            'total_amount' => $invoice->total_amount,
            'payment_terms' => $invoice->payment_terms,
            'billing_address' => $invoice->billing_address,
            'shipping_address' => $invoice->shipping_address,
            'items' => $items
        ]);
    }
} 