<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashReceipt;
use App\Models\Order;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class CashReceiptController extends Controller
{
    /**
     * Display a listing of cash receipts
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $search = $request->get('search');
        
        $query = CashReceipt::with(['customer', 'order', 'creator']);
        
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('receipt_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        $cashReceipts = $query->latest()->paginate(15);
        
        // Get status counts
        $statusCounts = [
            'all' => CashReceipt::count(),
            'issued' => CashReceipt::where('status', 'issued')->count(),
            'draft' => CashReceipt::where('status', 'draft')->count(),
            'cancelled' => CashReceipt::where('status', 'cancelled')->count(),
        ];

        // Get total values by currency
        $totalValues = [
            'aed' => CashReceipt::where('currency', 'AED')->sum('amount'),
            'usd' => CashReceipt::where('currency', 'USD')->sum('amount'),
        ];

        return view('admin.cash-receipts.index', compact('cashReceipts', 'status', 'statusCounts', 'totalValues', 'search'));
    }

    /**
     * Show the form for creating a new cash receipt
     */
    public function create(Request $request)
    {
        $orderId = $request->get('order_id');
        $order = $orderId ? Order::with('customer')->find($orderId) : null;
        
        $customers = Customer::orderBy('name')->get();
        $orders = Order::with('customer')->whereDoesntHave('cashReceipts')->latest()->take(50)->get();

        return view('admin.cash-receipts.create', compact('order', 'customers', 'orders'));
    }

    /**
     * Store a newly created cash receipt
     */
    public function store(Request $request)
    {
        // Debug logging
        Log::info('Cash receipt store method called', [
            'user_id' => auth()->id(),
            'request_data' => $request->all(),
            'method' => $request->method(),
            'url' => $request->url()
        ]);

        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:255',
            'customer_address' => 'nullable|string|max:1000',
            'order_id' => 'nullable|exists:orders,id',
            'amount' => 'required|numeric|min:0.01',
            'receipt_date' => 'required|date',
            'payment_method' => 'required|in:' . implode(',', array_keys(CashReceipt::$paymentMethods)),
            'currency' => 'required|in:AED,CNY,USD,EUR',
            'description' => 'nullable|string|max:1000',
            'reference_number' => 'nullable|string|max:255',
            'status' => 'required|in:draft,issued',
        ]);

        try {
            DB::beginTransaction();
            
            $customer = Customer::findOrFail($request->customer_id);
            $order = $request->order_id ? Order::find($request->order_id) : null;
            
            // Create receipt
            $cashReceipt = CashReceipt::create([
                'order_id' => $request->order_id,
                'customer_id' => $customer->id,
                'amount' => $request->amount,
                'currency' => $request->currency,
                'payment_date' => $request->receipt_date,
                'payment_method' => $request->payment_method,
                'description' => $request->description ?: ($order ? "Cash payment for Order #{$order->order_number}" : "Cash payment from {$customer->name}"),
                'notes' => null,
                'reference_number' => $request->reference_number,
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'customer_address' => $request->customer_address,
                'status' => $request->status,
                'created_by' => Auth::id(),
            ]);

            // If linked to order, update order status if it's still pending
            if ($order && $order->status === Order::STATUS_PENDING) {
                $order->update(['status' => Order::STATUS_PROCESSING]);
                Log::info("Updated order {$order->order_number} status to processing after cash payment");
            }

            DB::commit();

            return redirect()->route('admin.cash-receipts.index')
                ->with('success', 'Cash receipt created successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to create cash receipt: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'request_data' => $request->all(),
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Failed to create cash receipt: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified cash receipt
     */
    public function show(CashReceipt $cashReceipt)
    {
        try {
            // Load all necessary relationships
            $cashReceipt->load([
                'customer', 
                'order.customer', 
                'order.items.product',
                'creator',
                'updater'
            ]);
            
            // Log the access for debugging
            Log::info('Cash receipt show accessed', [
                'receipt_id' => $cashReceipt->id,
                'receipt_number' => $cashReceipt->receipt_number,
                'user_id' => auth()->id(),
                'user_email' => auth()->user()?->email
            ]);
            
            return view('admin.cash-receipts.show', compact('cashReceipt'));
            
        } catch (\Exception $e) {
            Log::error('Error displaying cash receipt', [
                'receipt_id' => $cashReceipt->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('admin.cash-receipts.index')
                ->with('error', 'Unable to display cash receipt: ' . $e->getMessage());
        }
    }

    /**
     * Generate and download PDF receipt
     */
    public function downloadPdf(CashReceipt $cashReceipt)
    {
        $cashReceipt->load([
            'customer', 
            'order.customer', 
            'order.items.product.brand',
            'order.items.product.specifications',
            'order.proformaInvoice',
            'creator'
        ]);
        
        // Get customer information - try direct relationship first, then from order
        $customer = $cashReceipt->customer;
        if (!$customer && $cashReceipt->order && $cashReceipt->order->customer) {
            $customer = $cashReceipt->order->customer;
        }
        
        // If still no customer found, try to find by name as fallback
        if (!$customer && $cashReceipt->customer_name) {
            $customer = Customer::where('name', $cashReceipt->customer_name)->first();
        }
        
        $pdf = Pdf::loadView('admin.cash-receipts.pdf', compact('cashReceipt', 'customer'));
        
        return $pdf->download("cash-receipt-{$cashReceipt->receipt_number}.pdf");
    }

    /**
     * Quick create receipt from order
     */
    public function quickCreate(Order $order)
    {
        try {
            if ($order->cashReceipts()->where('status', CashReceipt::STATUS_ISSUED)->exists()) {
                return redirect()->back()->with('error', 'Cash receipt already exists for this order.');
            }

            $cashReceipt = CashReceipt::createFromOrder($order);

            // Update order status if it's still pending
            if ($order->status === Order::STATUS_PENDING) {
                $order->update(['status' => Order::STATUS_PROCESSING]);
            }

            return redirect()->route('admin.cash-receipts.index')
                ->with('success', 'Cash receipt created successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to create quick cash receipt: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to create cash receipt.');
        }
    }

    /**
     * Delete the specified cash receipt
     */
    public function destroy(CashReceipt $cashReceipt)
    {
        try {
            $cashReceipt->delete();
            return redirect()->route('admin.cash-receipts.index')
                ->with('success', 'Cash receipt deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to delete cash receipt: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete receipt.');
        }
    }

    /**
     * Send email for cash receipt from index page
     */
    public function sendEmail(Request $request, CashReceipt $cashReceipt)
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
                $customer->name = $cashReceipt->customer_name; // Use receipt's customer name
            }

            Mail::to($request->customer_email)->send(new \App\Mail\CashReceiptEmail($cashReceipt, $customer, $ccEmails));
            
            $previousStatus = $cashReceipt->status;
            
            // Update status to issued if email was sent successfully and it was draft
            if ($cashReceipt->status === 'draft') {
                $cashReceipt->update(['status' => 'issued']);
            }
            
            // Return JSON response for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cash receipt email sent successfully!',
                    'previous_status' => $previousStatus,
                    'new_status' => $cashReceipt->fresh()->status
                ]);
            }
            
            return redirect()->back()->with('success', 'Cash receipt email sent successfully to ' . $request->customer_email . '!');
        } catch (\Exception $e) {
            Log::error('Failed to send cash receipt email: ' . $e->getMessage());
            
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
} 