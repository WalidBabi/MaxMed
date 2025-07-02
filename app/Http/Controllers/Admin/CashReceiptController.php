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
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'order_id' => 'nullable|exists:orders,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:' . implode(',', array_keys(CashReceipt::$paymentMethods)),
            'description' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
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
                'currency' => 'AED', // Default to AED as per memory
                'payment_date' => $request->payment_date,
                'payment_method' => $request->payment_method,
                'description' => $request->description ?: ($order ? "Cash payment for Order #{$order->order_number}" : "Cash payment from {$customer->name}"),
                'notes' => $request->notes,
                'customer_name' => $customer->name,
                'customer_email' => $customer->email,
                'customer_phone' => $customer->phone,
                'customer_address' => $customer->getFullAddress(),
                'status' => CashReceipt::STATUS_ISSUED,
                'created_by' => Auth::id(),
            ]);

            // If linked to order, update order status if it's still pending
            if ($order && $order->status === Order::STATUS_PENDING) {
                $order->update(['status' => Order::STATUS_PROCESSING]);
                Log::info("Updated order {$order->order_number} status to processing after cash payment");
            }

            DB::commit();

            return redirect()->route('admin.cash-receipts.show', $cashReceipt)
                ->with('success', 'Cash receipt created successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to create cash receipt: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to create cash receipt: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified cash receipt
     */
    public function show(CashReceipt $cashReceipt)
    {
        $cashReceipt->load(['customer', 'order', 'creator']);
        
        return view('admin.cash-receipts.show', compact('cashReceipt'));
    }

    /**
     * Generate and download PDF receipt
     */
    public function downloadPdf(CashReceipt $cashReceipt)
    {
        $cashReceipt->load(['customer', 'order']);
        
        $pdf = Pdf::loadView('admin.cash-receipts.pdf', compact('cashReceipt'));
        
        return $pdf->download("cash-receipt-{$cashReceipt->receipt_number}.pdf");
    }

    /**
     * Cancel a cash receipt
     */
    public function cancel(CashReceipt $cashReceipt)
    {
        try {
            if ($cashReceipt->status === CashReceipt::STATUS_CANCELLED) {
                return redirect()->back()->with('error', 'Receipt is already cancelled.');
            }

            $cashReceipt->update([
                'status' => CashReceipt::STATUS_CANCELLED,
                'updated_by' => Auth::id(),
            ]);

            return redirect()->back()->with('success', 'Receipt cancelled successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to cancel cash receipt: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to cancel receipt.');
        }
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

            return redirect()->route('admin.cash-receipts.show', $cashReceipt)
                ->with('success', 'Cash receipt created successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to create quick cash receipt: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to create cash receipt.');
        }
    }
} 