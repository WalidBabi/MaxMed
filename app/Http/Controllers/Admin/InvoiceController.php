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
use App\Mail\InvoiceEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices
     */
    public function index(Request $request)
    {
        $query = Invoice::with(['quote', 'order', 'delivery', 'creator', 'payments']);

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

        $invoices = $query->latest()->paginate(20);

        // Get filter options
        $filterOptions = [
            'types' => ['proforma' => 'Proforma', 'final' => 'Final'],
            'statuses' => Invoice::STATUS_OPTIONS,
            'payment_statuses' => Invoice::PAYMENT_STATUS
        ];

        return view('admin.invoices.index', compact('invoices', 'filterOptions'));
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
            ->select('id', 'name', 'description', 'price', 'price_aed', 'brand_id', 'category_id')
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
            DB::beginTransaction();

            // Check if quote already has an invoice
            if ($quote->invoices()->exists()) {
                return redirect()->back()->with('error', 'This quote has already been converted to an invoice.');
            }

            // Create proforma invoice from quote
            $invoice = Invoice::create([
                'type' => 'proforma',
                'quote_id' => $quote->id,
                'customer_name' => $quote->customer_name,
                'billing_address' => $this->getCustomerBillingAddress($quote->customer_name),
                'invoice_date' => now(),
                'due_date' => now()->addDays(30),
                'description' => $quote->subject ?: 'Proforma Invoice from Quote ' . $quote->quote_number,
                'terms_conditions' => $quote->terms_conditions,
                'notes' => $quote->customer_notes,
                'sub_total' => $quote->sub_total,
                'total_amount' => $quote->total_amount,
                'payment_terms' => 'advance_50', // Default to 50% advance
                'status' => 'draft',
                'is_proforma' => true,
                'requires_advance_payment' => true,
                'reference_number' => $quote->reference_number,
                'created_by' => Auth::id()
            ]);

            // Copy quote items to invoice items
            foreach ($quote->items as $index => $quoteItem) {
                // Calculate line total to ensure accuracy
                $subtotal = $quoteItem->quantity * $quoteItem->rate;
                $discountAmount = ($subtotal * $quoteItem->discount) / 100;
                $lineTotal = $subtotal - $discountAmount;
                
                // Log for debugging
                Log::info("Converting quote item {$quoteItem->id}: product_id={$quoteItem->product_id}, item_details={$quoteItem->item_details}");
                
                // Create invoice item data array
                $invoiceItemData = [
                    'invoice_id' => $invoice->id,
                    'product_id' => $quoteItem->product_id, // Ensure product_id is transferred
                    'item_description' => $quoteItem->item_details,
                    'quantity' => $quoteItem->quantity,
                    'unit_price' => $quoteItem->rate,
                    'discount_percentage' => $quoteItem->discount,
                    'discount_amount' => $discountAmount,
                    'line_total' => $lineTotal,
                    'sort_order' => $index + 1
                ];
                
                // Debug log the data being passed to create
                Log::info("Creating invoice item with data:", $invoiceItemData);
                
                $invoiceItem = InvoiceItem::create($invoiceItemData);
                
                // Log the created invoice item to verify product_id transfer
                Log::info("Created invoice item {$invoiceItem->id}: product_id={$invoiceItem->product_id}");
                
                // Refresh from database and check again
                $invoiceItem->refresh();
                Log::info("After refresh - invoice item {$invoiceItem->id}: product_id={$invoiceItem->product_id}");
            }

            // Update quote status
            $quote->update(['status' => 'invoiced']);

            DB::commit();

            return redirect()->route('admin.invoices.show', $invoice)
                ->with('success', 'Proforma invoice created successfully from quote ' . $quote->quote_number);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to convert quote to invoice: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to convert quote to invoice: ' . $e->getMessage());
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
            'items' => 'required|array|min:1',
            'items.*.item_description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_percentage' => 'nullable|numeric|min:0|max:100'
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
                'is_proforma' => $request->type === 'proforma',
                'requires_advance_payment' => in_array($request->payment_terms, ['advance_50', 'advance_100', 'custom']),
                'reference_number' => $request->reference_number,
                'po_number' => $request->po_number,
                'created_by' => Auth::id()
            ]);

            // Create invoice items
            foreach ($request->items as $index => $itemData) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $itemData['product_id'] ?? null,
                    'item_description' => $itemData['item_description'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'discount_percentage' => $itemData['discount_percentage'] ?? 0,
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
        $invoice->load(['items', 'quote', 'order', 'delivery', 'payments', 'creator', 'parentInvoice', 'childInvoices']);
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
            ->select('id', 'name', 'description', 'price', 'price_aed', 'brand_id', 'category_id')
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
            'items' => 'required|array|min:1'
        ]);

        try {
            DB::beginTransaction();

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
                'reference_number' => $request->reference_number,
                'po_number' => $request->po_number,
                'updated_by' => Auth::id()
            ]);

            // Delete existing items and recreate
            $invoice->items()->delete();

            foreach ($request->items as $index => $itemData) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $itemData['product_id'] ?? null,
                    'item_description' => $itemData['item_description'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'discount_percentage' => $itemData['discount_percentage'] ?? 0,
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

        try {
            DB::beginTransaction();
            
            Log::info("Recording payment for invoice {$invoice->id}: amount={$request->amount}, current_status={$invoice->status}, current_payment_status={$invoice->payment_status}");
            
            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'payment_date' => $request->payment_date,
                'transaction_reference' => $request->transaction_reference,
                'payment_notes' => $request->payment_notes,
                'status' => 'completed',
                'processed_by' => Auth::id(),
                'processed_at' => now()
            ]);

            Log::info("Payment {$payment->id} created successfully for invoice {$invoice->id}");
            
            // Ensure invoice status is appropriate for workflow automation
            $freshInvoice = $invoice->fresh();
            if ($freshInvoice->type === 'proforma' && $freshInvoice->status === 'draft') {
                Log::info("Updating invoice {$freshInvoice->id} status from 'draft' to 'sent' to enable workflow");
                $freshInvoice->update(['status' => 'sent']);
            }
            
            // Manually trigger workflow automation to ensure it runs
            $freshInvoice = $invoice->fresh();
            Log::info("Triggering workflow automation for invoice {$freshInvoice->id}: status={$freshInvoice->status}, payment_status={$freshInvoice->payment_status}");
            $freshInvoice->handleWorkflowAutomation();

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
        $invoice->load('items');
        
        $pdf = Pdf::loadView('admin.invoices.pdf', compact('invoice'));
        
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
                'order_number' => 'ORD-' . str_pad(Order::max('id') + 1, 6, '0', STR_PAD_LEFT),
                'total_amount' => $invoice->total_amount,
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
                    'variation' => $invoiceItem->specifications
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
} 