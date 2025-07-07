<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Delivery;
use App\Models\SupplierQuotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewQuotationNotification;
use App\Models\PurchaseOrder;

class OrderController extends Controller
{
    /**
     * Display orders for supplier processing
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $viewType = $request->get('view', 'pipeline'); // Default to pipeline view
        
        // Get supplier's active category IDs
        $supplierCategoryIds = Auth::user()
            ->supplierCategories()
            ->where('status', 'active')
            ->pluck('category_id');

        // Get orders based on status for pagination
        $orders = $this->getOrdersForStatus($status, $supplierCategoryIds)->paginate(15);
        
        // Get status counts for tabs
        $statusCounts = [
            'all' => $this->getOrdersForStatus('all', $supplierCategoryIds)->count(),
            'awaiting_quotations' => $this->getOrdersForStatus('awaiting_quotations', $supplierCategoryIds)->count(),
            'quotations_received' => $this->getOrdersForStatus('quotations_received', $supplierCategoryIds)->count(),
            'processing' => $this->getOrdersForStatus('processing', $supplierCategoryIds)->count(),
            'shipped' => $this->getOrdersForStatus('shipped', $supplierCategoryIds)->count(),
            'delivered' => $this->getOrdersForStatus('delivered', $supplierCategoryIds)->count(),
            'completed' => $this->getOrdersForStatus('completed', $supplierCategoryIds)->count(),
        ];

        // For pipeline view, get orders grouped by status
        $ordersGrouped = [];
        if ($viewType === 'pipeline') {
            $statuses = ['awaiting_quotations', 'quotations_received', 'processing', 'shipped', 'delivered', 'completed'];
            foreach ($statuses as $pipelineStatus) {
                $ordersGrouped[$pipelineStatus] = $this->getOrdersForStatus($pipelineStatus, $supplierCategoryIds)->get();
            }
        }

        // Get all orders for table view
        $allOrders = collect();
        if ($viewType === 'table') {
            $allOrders = $this->getOrdersForStatus($status, $supplierCategoryIds)->get();
        }

        return view('supplier.orders.index', compact(
            'orders', 
            'statusCounts', 
            'status', 
            'viewType', 
            'ordersGrouped', 
            'allOrders'
        ));
    }

    /**
     * Get orders query for a specific status
     */
    private function getOrdersForStatus($status, $supplierCategoryIds)
    {
        $baseQuery = Order::with(['items.product', 'supplierQuotations' => function($q) {
            $q->where('supplier_id', Auth::id());
        }]);

        switch ($status) {
            case 'awaiting_quotations':
                return $baseQuery->where(function($q) use ($supplierCategoryIds) {
                    $q->whereHas('items.product', function($productQ) use ($supplierCategoryIds) {
                        $productQ->whereIn('category_id', $supplierCategoryIds);
                    });
                })
                ->where(function($q) {
                    $q->where('status', Order::STATUS_AWAITING_QUOTATIONS)
                      ->orWhere(function($subQ) {
                          $subQ->where('status', 'pending')
                               ->where('requires_quotation', true);
                      });
                })
                ->whereHas('supplierQuotations', function($sq) {
                    $sq->where('supplier_id', Auth::id())
                        ->where('status', 'submitted');
                })
                ->latest();

            case 'quotations_received':
                return $baseQuery->where(function($q) use ($supplierCategoryIds) {
                    $q->where(function($subQ) use ($supplierCategoryIds) {
                        $subQ->whereHas('items.product', function($productQ) use ($supplierCategoryIds) {
                            $productQ->whereIn('category_id', $supplierCategoryIds);
                        });
                    })
                    ->orWhere(function($subQ) {
                        $subQ->whereHas('supplierQuotations', function($quotationQ) {
                            $quotationQ->where('supplier_id', Auth::id());
                        });
                    });
                })
                ->where('status', Order::STATUS_QUOTATIONS_RECEIVED)
                ->where('requires_quotation', true)
                ->whereHas('supplierQuotations', function($sq) {
                    $sq->where('supplier_id', Auth::id())
                        ->where('status', 'submitted');
                })
                ->latest();

            case 'processing':
                return $baseQuery->where(function($q) use ($supplierCategoryIds) {
                    $q->where(function($subQ) use ($supplierCategoryIds) {
                        $subQ->whereHas('items.product', function($productQ) use ($supplierCategoryIds) {
                            $productQ->whereIn('category_id', $supplierCategoryIds);
                        });
                    })
                    ->orWhere(function($subQ) {
                        $subQ->whereHas('supplierQuotations', function($quotationQ) {
                            $quotationQ->where('supplier_id', Auth::id())
                                ->where('status', SupplierQuotation::STATUS_APPROVED);
                        });
                    });
                })
                ->where(function($q) {
                    $q->where('status', Order::STATUS_APPROVED)
                      ->orWhere('status', Order::STATUS_PROCESSING)
                      ->orWhere(function($subQ) {
                          $subQ->where('status', 'pending')
                               ->where('requires_quotation', false);
                      });
                })
                ->latest();

            case 'shipped':
                return $baseQuery->where('status', Order::STATUS_SHIPPED)
                ->where(function($q) use ($supplierCategoryIds) {
                    $q->where(function($subQ) use ($supplierCategoryIds) {
                        $subQ->whereHas('items.product', function($productQ) use ($supplierCategoryIds) {
                            $productQ->whereIn('category_id', $supplierCategoryIds);
                        });
                    })
                    ->orWhere(function($subQ) {
                        $subQ->whereHas('supplierQuotations', function($sq) {
                            $sq->where('supplier_id', Auth::id());
                        });
                    });
                })
                ->latest();

            case 'delivered':
                return $baseQuery->where('status', Order::STATUS_DELIVERED)
                ->where(function($q) use ($supplierCategoryIds) {
                    $q->where(function($subQ) use ($supplierCategoryIds) {
                        $subQ->whereHas('items.product', function($productQ) use ($supplierCategoryIds) {
                            $productQ->whereIn('category_id', $supplierCategoryIds);
                        });
                    })
                    ->orWhere(function($subQ) {
                        $subQ->whereHas('supplierQuotations', function($sq) {
                            $sq->where('supplier_id', Auth::id());
                        });
                    });
                })
                ->latest();

            case 'completed':
                return $baseQuery->where('status', Order::STATUS_COMPLETED)
                ->where(function($q) use ($supplierCategoryIds) {
                    $q->where(function($subQ) use ($supplierCategoryIds) {
                        $subQ->whereHas('items.product', function($productQ) use ($supplierCategoryIds) {
                            $productQ->whereIn('category_id', $supplierCategoryIds);
                        });
                    })
                    ->orWhere(function($subQ) {
                        $subQ->whereHas('supplierQuotations', function($sq) {
                            $sq->where('supplier_id', Auth::id());
                        });
                    });
                })
                ->latest();

            case 'all':
            default:
                return $baseQuery->where(function($q) use ($supplierCategoryIds) {
                    $q->where(function($subQ) use ($supplierCategoryIds) {
                        // Show orders with products in supplier's categories
                        $subQ->whereHas('items.product', function($productQ) use ($supplierCategoryIds) {
                            $productQ->whereIn('category_id', $supplierCategoryIds);
                        });
                    })
                    ->orWhere(function($subQ) {
                        // Show orders that have quotations from this supplier
                        $subQ->whereHas('supplierQuotations', function($sq) {
                            $sq->where('supplier_id', Auth::id());
                        });
                    });
                })
                ->latest();
        }
    }

    /**
     * Show order details and delivery management
     */
    public function show(Order $order)
    {
        // Load order with related data
        $order->load(['delivery', 'items.product.category', 'customer', 'user', 'supplierQuotations' => function($q) {
            $q->where('supplier_id', Auth::id());
        }]);
        
        // Check if supplier has access to this order
        $supplierCategoryIds = Auth::user()
            ->supplierCategories()
            ->where('status', 'active')
            ->pluck('category_id');
            
        $orderCategoryIds = $order->items->pluck('product.category_id')->unique();
        
        $hasAccess = $orderCategoryIds->intersect($supplierCategoryIds)->isNotEmpty() ||
                    $order->supplierQuotations->isNotEmpty();
        
        if (!$hasAccess) {
            return redirect()->route('supplier.orders.index')
                ->with('error', 'You do not have access to this order.');
        }

        return view('supplier.orders.show', compact('order'));
    }

    /**
     * Mark order as being processed by supplier
     * This changes delivery status from 'pending' to 'processing'
     */
    public function markAsProcessing(Order $order)
    {
        try {
            $delivery = $order->delivery;
            
            if (!$delivery) {
                return redirect()->back()->with('error', 'No delivery found for this order.');
            }

            if ($delivery->status !== 'pending') {
                return redirect()->back()->with('error', 'Order can only be marked as processing from pending status.');
            }

            $delivery->update([
                'status' => 'processing',
                'processed_by_supplier_at' => now(),
                'supplier_notes' => 'Order marked as processing by supplier'
            ]);

            Log::info("Order {$order->order_number} marked as processing by supplier. Delivery ID: {$delivery->id}");

            return redirect()->back()->with('success', 'Order marked as processing successfully! Your dedication ensures quality healthcare delivery.');

        } catch (\Exception $e) {
            Log::error('Failed to mark order as processing: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update order status.');
        }
    }

    /**
     * Mark order back to pending status
     * This changes delivery status from 'processing' to 'pending'
     */
    public function markAsPending(Order $order)
    {
        try {
            $delivery = $order->delivery;
            
            if (!$delivery) {
                return redirect()->back()->with('error', 'No delivery found for this order.');
            }

            if ($delivery->status !== 'processing') {
                return redirect()->back()->with('error', 'Order can only be marked as pending from processing status.');
            }

            $delivery->update([
                'status' => 'pending',
                'supplier_notes' => 'Order marked back to pending by supplier'
            ]);

            Log::info("Order {$order->order_number} marked as pending by supplier. Delivery ID: {$delivery->id}");

            return redirect()->back()->with('success', 'Order marked as pending successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to mark order as pending: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update order status.');
        }
    }

    /**
     * Submit documents and mark as sent to carrier
     * This changes delivery status from 'processing' to 'in_transit'
     * Once shipped, the order cannot be reverted back to processing
     */
    public function submitDocuments(Request $request, Order $order)
    {
        $request->validate([
            'packing_list' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'commercial_invoice' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'carrier' => 'required|string|max:255',
            'tracking_number' => 'required|string|max:255',
            'shipping_cost' => 'nullable|numeric|min:0',
            'supplier_notes' => 'nullable|string|max:1000'
        ]);

        try {
            $delivery = $order->delivery;
            
            if (!$delivery) {
                return redirect()->back()->with('error', 'No delivery found for this order.');
            }

            if ($delivery->status !== 'processing') {
                return redirect()->back()->with('error', 'Order must be in processing status to submit documents.');
            }

            // Upload packing list
            $packingListPath = null;
            if ($request->hasFile('packing_list')) {
                $packingListPath = $request->file('packing_list')->store('deliveries/packing-lists', 'public');
            }

            // Upload commercial invoice
            $commercialInvoicePath = null;
            if ($request->hasFile('commercial_invoice')) {
                $commercialInvoicePath = $request->file('commercial_invoice')->store('deliveries/commercial-invoices', 'public');
            }

            // Update delivery with documents and carrier info
            $delivery->update([
                'status' => 'in_transit',
                'carrier' => $request->carrier,
                'tracking_number' => $request->tracking_number,
                'shipping_cost' => $request->shipping_cost ?? 0,
                'packing_list_file' => $packingListPath,
                'commercial_invoice_file' => $commercialInvoicePath,
                'sent_to_carrier_at' => now(),
                'shipped_at' => now(),
                'supplier_notes' => $request->supplier_notes
            ]);

            // Also update the order status to 'shipped'
            $order->update([
                'status' => 'shipped'
            ]);

            Log::info("Order {$order->order_number} documents submitted, delivery marked as in_transit, and order status changed to shipped. Delivery ID: {$delivery->id}");

            // Trigger invoice conversion if applicable
            $delivery->autoConvertToFinalInvoice();

            return redirect()->back()->with('success', 'The product is now on the way to MaxMed. Your partnership makes healthcare excellence possible.');

        } catch (\Exception $e) {
            Log::error('Failed to submit documents: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to submit documents: ' . $e->getMessage());
        }
    }

    /**
     * Download packing list file
     */
    public function downloadPackingList(Order $order)
    {
        $delivery = $order->delivery;
        
        if (!$delivery || !$delivery->packing_list_file) {
            return redirect()->back()->with('error', 'Packing list not found.');
        }

        $filePath = storage_path('app/public/' . $delivery->packing_list_file);
        
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'Packing list file not found.');
        }

        return response()->download($filePath, 'packing-list-' . $order->order_number . '.pdf');
    }

    /**
     * Download commercial invoice file
     */
    public function downloadCommercialInvoice(Order $order)
    {
        $delivery = $order->delivery;
        
        if (!$delivery || !$delivery->commercial_invoice_file) {
            return redirect()->back()->with('error', 'Commercial invoice not found.');
        }

        $filePath = storage_path('app/public/' . $delivery->commercial_invoice_file);
        
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'Commercial invoice file not found.');
        }

        return response()->download($filePath, 'commercial-invoice-' . $order->order_number . '.pdf');
    }

    /**
     * Update delivery status (for testing purposes)
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,in_transit,delivered,cancelled'
        ]);

        try {
            $delivery = $order->delivery;
            
            if (!$delivery) {
                return redirect()->back()->with('error', 'No delivery found for this order.');
            }

            $oldStatus = $delivery->status;
            $newStatus = $request->status;

            $delivery->update(['status' => $newStatus]);

            // Update timestamps based on status
            if ($newStatus === 'processing' && !$delivery->processed_by_supplier_at) {
                $delivery->update(['processed_by_supplier_at' => now()]);
            }

            if ($newStatus === 'in_transit' && !$delivery->sent_to_carrier_at) {
                $delivery->update([
                    'sent_to_carrier_at' => now(),
                    'shipped_at' => now()
                ]);
            }

            if ($newStatus === 'delivered' && !$delivery->delivered_at) {
                $delivery->update(['delivered_at' => now()]);
            }

            Log::info("Delivery {$delivery->id} status changed from {$oldStatus} to {$newStatus}");

            return redirect()->back()->with('success', 'Delivery status updated successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to update delivery status: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update delivery status.');
        }
    }

    /**
     * Show quotation form for an order
     */
    public function showQuotationForm(Order $order)
    {
        // Check if this order requires quotation and is in the correct state
        if (!$order->requires_quotation) {
            return redirect()->route('supplier.orders.show', $order)
                ->with('error', 'This order does not require quotations.');
        }

        if (!in_array($order->status, [Order::STATUS_AWAITING_QUOTATIONS, Order::STATUS_QUOTATIONS_RECEIVED, 'pending'])) {
            return redirect()->route('supplier.orders.show', $order)
                ->with('error', 'This order is not accepting quotations at this time.');
        }

        // Check if supplier has already submitted a quotation
        $existingQuotation = SupplierQuotation::where('order_id', $order->id)
            ->where('supplier_id', auth()->id())
            ->first();

        if ($existingQuotation) {
            return redirect()->route('supplier.orders.show', $order)
                ->with('error', 'You have already submitted a quotation for this order.');
        }

        $order->load(['items.product']);

        return view('supplier.orders.quotation', compact('order'));
    }

    /**
     * Submit quotation for an order
     */
    public function submitQuotation(Request $request, Order $order)
    {
        // Validate request
        $validated = $request->validate([
            'total_amount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'shipping_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Check if this order requires quotation and is in the correct state
        if (!$order->requires_quotation) {
            return redirect()->route('supplier.orders.show', $order)
                ->with('error', 'This order does not require quotations.');
        }

        if (!in_array($order->status, [Order::STATUS_AWAITING_QUOTATIONS, Order::STATUS_QUOTATIONS_RECEIVED, 'pending'])) {
            return redirect()->route('supplier.orders.show', $order)
                ->with('error', 'This order is not accepting quotations at this time.');
        }

        // Check if supplier has already submitted a quotation
        $existingQuotation = SupplierQuotation::where('order_id', $order->id)
            ->where('supplier_id', auth()->id())
            ->first();

        if ($existingQuotation) {
            return redirect()->route('supplier.orders.show', $order)
                ->with('error', 'You have already submitted a quotation for this order.');
        }

        DB::beginTransaction();
        try {
            // Verify the order still exists and is accessible
            $orderExists = Order::where('id', $order->id)->first();
            if (!$orderExists) {
                throw new \Exception("Order #{$order->id} no longer exists in the database.");
            }

            // Create the quotation
            $quotation = SupplierQuotation::create([
                'order_id' => $order->id,
                'supplier_id' => auth()->id(),
                'total_amount' => $validated['total_amount'],
                'currency' => $validated['currency'],
                'shipping_cost' => $validated['shipping_cost'] ?? null,
                'notes' => $validated['notes'],
                'status' => 'submitted'
            ]);

            // Update order quotation status
            $order->updateQuotationStatus();

            // Notify admin about new quotation
            $admins = User::whereHas('role', function($query) {
                $query->where('name', 'admin');
            })->get();

            // Send styled email to admins for order quotations
            foreach ($admins as $admin) {
                // For order quotations, we'll create a simpler email since it's different from inquiry quotations
                \Mail::to($admin->email)->send(new \App\Mail\NewQuotationSubmitted($quotation));
            }

            Notification::send($admins, new NewQuotationNotification($quotation));

            DB::commit();

            Log::info("Supplier quotation submitted for order {$order->order_number}. Quotation ID: {$quotation->id}");

            return redirect()->route('supplier.orders.show', $order)
                ->with('success', 'Your quotation has been submitted successfully. You will be notified when it is reviewed.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to submit quotation: ' . $e->getMessage());
            
            // Provide more specific error messages
            if (str_contains($e->getMessage(), 'foreign key constraint fails')) {
                return redirect()->route('supplier.orders.index')
                    ->with('error', 'The order you are trying to quote no longer exists. Please check the orders list.');
            }
            
            return redirect()->back()
                ->with('error', 'Failed to submit quotation. Please try again.')
                ->withInput();
        }
    }

    /**
     * Show supplier's quotation history for an order
     */
    public function quotationHistory(Order $order)
    {
        $quotations = SupplierQuotation::where('order_id', $order->id)
            ->where('supplier_id', auth()->id())
            ->latest()
            ->get();

        return view('supplier.orders.quotation-history', compact('order', 'quotations'));
    }

    /**
     * Display purchase orders for the supplier
     */
    public function purchaseOrders(Request $request)
    {
        $status = $request->get('status', 'all');
        
        $query = PurchaseOrder::where('supplier_id', auth()->id())
            ->with(['order', 'items.product', 'supplierQuotation']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $purchaseOrders = $query->latest()->paginate(15);
        
        // Get status counts
        $statusCounts = [
            'all' => PurchaseOrder::where('supplier_id', auth()->id())->count(),
            'sent_to_supplier' => PurchaseOrder::where('supplier_id', auth()->id())->where('status', 'sent_to_supplier')->count(),
            'acknowledged' => PurchaseOrder::where('supplier_id', auth()->id())->where('status', 'acknowledged')->count(),
            'in_production' => PurchaseOrder::where('supplier_id', auth()->id())->where('status', 'in_production')->count(),
            'shipped' => PurchaseOrder::where('supplier_id', auth()->id())->where('status', 'shipped')->count(),
        ];

        return view('supplier.purchase-orders.index', compact('purchaseOrders', 'statusCounts', 'status'));
    }

    /**
     * Show purchase order details
     */
    public function showPurchaseOrder(PurchaseOrder $purchaseOrder)
    {
        // Check if supplier has access to this purchase order
        if ($purchaseOrder->supplier_id !== auth()->id()) {
            return redirect()->route('supplier.purchase-orders.index')
                ->with('error', 'You do not have access to this purchase order.');
        }

        $purchaseOrder->load(['order', 'items.product', 'supplierQuotation', 'payments']);

        return view('supplier.purchase-orders.show', compact('purchaseOrder'));
    }

    /**
     * Acknowledge purchase order receipt
     */
    public function acknowledgePurchaseOrder(PurchaseOrder $purchaseOrder)
    {
        // Check if supplier has access to this purchase order
        if ($purchaseOrder->supplier_id !== auth()->id()) {
            return redirect()->route('supplier.purchase-orders.index')
                ->with('error', 'You do not have access to this purchase order.');
        }

        if ($purchaseOrder->status !== PurchaseOrder::STATUS_SENT_TO_SUPPLIER) {
            return redirect()->back()
                ->with('error', 'Purchase order can only be acknowledged when status is "Sent to Supplier".');
        }

        $purchaseOrder->update([
            'status' => PurchaseOrder::STATUS_ACKNOWLEDGED,
            'acknowledged_at' => now()
        ]);

        Log::info("Purchase order {$purchaseOrder->po_number} acknowledged by supplier {$purchaseOrder->supplier->name}");

        return redirect()->back()
            ->with('success', 'Purchase order acknowledged successfully! You can now start production.');
    }
} 