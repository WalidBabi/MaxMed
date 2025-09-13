<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\Order;
use App\Models\Product;
use App\Models\SupplierPayment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of purchase orders
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        
        $query = PurchaseOrder::with(['order', 'order.delivery']);
        
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        $purchaseOrders = $query->latest()->paginate(15);
        
        // Get status counts for tabs
        $statusCounts = [
            'all' => PurchaseOrder::count(),
            'draft' => PurchaseOrder::where('status', 'draft')->count(),
            'sent_to_supplier' => PurchaseOrder::where('status', 'sent_to_supplier')->count(),
            'acknowledged' => PurchaseOrder::where('status', 'acknowledged')->count(),
            'in_production' => PurchaseOrder::where('status', 'in_production')->count(),
            'completed' => PurchaseOrder::where('status', 'completed')->count(),
        ];

        return view('admin.purchase-orders.index', compact('purchaseOrders', 'statusCounts', 'status'));
    }

    /**
     * Show the form for creating a new purchase order
     */
    public function create(Request $request)
    {
        // Get orders that don't have purchase orders yet
        $availableOrders = Order::whereDoesntHave('purchaseOrder')
            ->latest()
            ->get();

        // Get suppliers (users with supplier role) with their information
        $suppliers = User::whereHas('role', function($q) {
            $q->where('name', 'supplier');
        })
            ->with('supplierInformation')
            ->get();

        // Get products for the dropdown
        try {
            $products = Product::with(['brand', 'specifications'])
                ->orderBy('name')
                ->get();
        } catch (\Exception $e) {
            // If there's an error with products, set to empty collection
            $products = collect([]);
            \Log::error('Error loading products for purchase order create: ' . $e->getMessage());
        }

        $selectedOrder = null;
        if ($request->has('order_id')) {
            $selectedOrder = Order::with(['items.product'])->find($request->order_id);
        }

        return view('admin.purchase-orders.create', compact('availableOrders', 'selectedOrder', 'suppliers', 'products'));
    }

    /**
     * Store a newly created purchase order
     */
    public function store(Request $request)
    {
        $validationRules = [
            'order_id' => 'nullable|exists:orders,id',
            'supplier_type' => 'required|in:existing,new',
            'currency' => 'required|in:AED,USD,CYN',
            'delivery_date_requested' => 'required|date|after:today',  
            'description' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
            'notes' => 'nullable|string',
            'special_instructions' => 'nullable|string',
            'payment_terms' => 'required|string|max:255',
            'shipping_method' => 'required|string|max:255',
            'sub_total' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'shipping_cost' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'proforma_attachment' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240', // 10MB max
        ];

        // Add conditional validation based on supplier type
        if ($request->supplier_type === 'existing') {
            $validationRules['supplier_id'] = 'required|exists:users,id';
        } else {
            $validationRules['supplier_name'] = 'required|string|max:255';
            $validationRules['supplier_email'] = 'nullable|email|max:255';
            $validationRules['supplier_phone'] = 'nullable|string|max:50';
            $validationRules['supplier_address'] = 'nullable|string';
        }

        // Add items validation for standalone purchase orders
        if (!$request->order_id) {
            $validationRules['items'] = 'required|array|min:1';
            $validationRules['items.*.product_id'] = 'required|exists:products,id';
            $validationRules['items.*.quantity'] = 'required|numeric|min:0.01';
            $validationRules['items.*.unit_price'] = 'required|numeric|min:0';
            $validationRules['items.*.discount_percentage'] = 'nullable|numeric|min:0|max:100';
        }

        // Custom validation messages
        $validationMessages = [
            'supplier_type.required' => 'Please select a supplier type (existing or new).',
            'supplier_type.in' => 'Supplier type must be either existing or new.',
            'currency.required' => 'Please select a currency.',
            'currency.in' => 'Currency must be AED, USD, or CYN.',
            'delivery_date_requested.required' => 'Please select a delivery date.',
            'delivery_date_requested.date' => 'Please enter a valid delivery date.',
            'delivery_date_requested.after' => 'Delivery date must be after today.',
            'supplier_id.required' => 'Please select a supplier.',
            'supplier_id.exists' => 'The selected supplier does not exist.',
            'supplier_name.required' => 'Please enter the supplier name.',
            'supplier_name.max' => 'Supplier name cannot exceed 255 characters.',
            'supplier_email.email' => 'Please enter a valid email address.',
            'supplier_email.max' => 'Email address cannot exceed 255 characters.',
            'supplier_phone.max' => 'Phone number cannot exceed 50 characters.',
            'sub_total.required' => 'Sub total is required.',
            'sub_total.numeric' => 'Sub total must be a valid number.',
            'sub_total.min' => 'Sub total cannot be negative.',
            'tax_amount.numeric' => 'Tax amount must be a valid number.',
            'tax_amount.min' => 'Tax amount cannot be negative.',
            'shipping_cost.numeric' => 'Shipping cost must be a valid number.',
            'shipping_cost.min' => 'Shipping cost cannot be negative.',
            'total_amount.required' => 'Total amount is required.',
            'total_amount.numeric' => 'Total amount must be a valid number.',
            'total_amount.min' => 'Total amount cannot be negative.',
            'payment_terms.required' => 'Please select payment terms.',
            'payment_terms.max' => 'Payment terms cannot exceed 255 characters.',
            'shipping_method.required' => 'Please select a shipping method.',
            'shipping_method.max' => 'Shipping method cannot exceed 255 characters.',
            'proforma_attachment.file' => 'Please upload a valid file.',
            'proforma_attachment.mimes' => 'File must be a PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, or PNG.',
            'proforma_attachment.max' => 'File size cannot exceed 10MB.',
            'order_id.exists' => 'The selected order does not exist.',
            'items.required' => 'Please add at least one item to the purchase order.',
            'items.array' => 'Items must be provided as a list.',
            'items.min' => 'Please add at least one item to the purchase order.',
            'items.*.product_id.required' => 'Please select a product for each item.',
            'items.*.product_id.exists' => 'The selected product does not exist.',
            'items.*.quantity.required' => 'Please enter a quantity for each item.',
            'items.*.quantity.numeric' => 'Quantity must be a valid number.',
            'items.*.quantity.min' => 'Quantity must be greater than 0.',
            'items.*.unit_price.required' => 'Please enter a unit price for each item.',
            'items.*.unit_price.numeric' => 'Unit price must be a valid number.',
            'items.*.unit_price.min' => 'Unit price cannot be negative.',
            'items.*.discount_percentage.numeric' => 'Discount percentage must be a valid number.',
            'items.*.discount_percentage.min' => 'Discount percentage cannot be negative.',
            'items.*.discount_percentage.max' => 'Discount percentage cannot exceed 100%.',
        ];

        $request->validate($validationRules, $validationMessages);

        try {
            DB::beginTransaction();

            $order = null;
            $po = null;

            // Check if order_id is provided (linking to existing customer order)
            if ($request->order_id) {
                $order = Order::findOrFail($request->order_id);
                
                // Check if PO already exists for this order
                if ($order->purchaseOrder) {
                    return redirect()->back()->with('error', 'Purchase order already exists for this order.');
                }

                // Create PO from existing order
                $po = PurchaseOrder::createFromOrder($order);
            } else {
                // Create standalone PO without customer order
                $po = PurchaseOrder::create([
                    'order_id' => null,
                    'po_date' => now()->format('Y-m-d'),
                    'status' => PurchaseOrder::STATUS_DRAFT,
                    'payment_status' => PurchaseOrder::PAYMENT_STATUS_PENDING,
                    'created_by' => Auth::id(),
                ]);
            }
            
            // Handle file upload
            $attachments = [];
            if ($request->hasFile('proforma_attachment')) {
                $file = $request->file('proforma_attachment');
                $filename = 'proforma_' . time() . '_' . $file->getClientOriginalName();
                $attachmentPath = $file->storeAs('purchase-orders/attachments', $filename, 'public');
                $attachments[] = [
                    'type' => 'proforma_invoice',
                    'filename' => $file->getClientOriginalName(),
                    'path' => $attachmentPath,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'uploaded_at' => now()->toISOString()
                ];
            }

            // Prepare supplier data based on selection type
            $supplierData = [
                'delivery_date_requested' => $request->delivery_date_requested,
                'description' => $request->description,
                'terms_conditions' => $request->terms_conditions,
                'notes' => $request->notes,
                'special_instructions' => $request->special_instructions,
                'payment_terms' => $request->payment_terms,
                'shipping_method' => $request->shipping_method,
                'currency' => $request->currency,
                'sub_total' => $request->sub_total,
                'tax_amount' => $request->tax_amount ?? 0,
                'shipping_cost' => $request->shipping_cost ?? 0,
                'total_amount' => $request->total_amount,
                'attachments' => !empty($attachments) ? json_encode($attachments) : null,
                'updated_by' => Auth::id()
            ];

            if ($request->supplier_type === 'existing') {
                // Get supplier information from database
                $supplier = User::with('supplierInformation')->findOrFail($request->supplier_id);
                $supplierData['supplier_id'] = $supplier->id;
                $supplierData['supplier_name'] = $supplier->supplierInformation->company_name ?? $supplier->name;
                $supplierData['supplier_email'] = $supplier->email;
                $supplierData['supplier_phone'] = $supplier->supplierInformation->phone_primary ?? '';
                $supplierData['supplier_address'] = $supplier->supplierInformation->business_address ?? '';
            } else {
                // Use manually entered supplier information (no customer info included)
                $supplierData['supplier_name'] = $request->supplier_name;
                $supplierData['supplier_email'] = $request->supplier_email;
                $supplierData['supplier_phone'] = $request->supplier_phone;
                $supplierData['supplier_address'] = $request->supplier_address;
            }

            // Update PO with supplier data (no customer information included)
            $po->update($supplierData);

            // Process items for standalone POs (when no order_id is provided)
            if (!$request->order_id && $request->has('items')) {
                foreach ($request->items as $itemData) {
                    if (!empty($itemData['product_id']) && !empty($itemData['quantity']) && !empty($itemData['unit_price'])) {
                        $quantity = (float) $itemData['quantity'];
                        $unitPrice = (float) $itemData['unit_price'];
                        $discountPercentage = (float) ($itemData['discount_percentage'] ?? 0);
                        
                        $subtotal = $quantity * $unitPrice;
                        $discountAmount = ($subtotal * $discountPercentage) / 100;
                        $totalAmount = $subtotal - $discountAmount;

                        $po->items()->create([
                            'product_id' => $itemData['product_id'],
                            'quantity' => $quantity,
                            'unit_price' => $unitPrice,
                            // Only set price_type for AED/USD. For CYN (or others), keep null so editing rules remain valid
                            'price_type' => in_array(strtoupper($request->currency), ['AED','USD']) ? strtolower($request->currency) : null,
                            'discount_percentage' => $discountPercentage,
                            'discount_amount' => $discountAmount,
                            'line_total' => $totalAmount,
                            'item_description' => $itemData['item_description'] ?? '',
                            'specifications' => $itemData['specifications'] ?? '',
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.purchase-orders.show', $po)
                ->with('success', 'Purchase order created successfully without customer information disclosure.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create purchase order: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to create purchase order.');
        }
    }

    /**
     * Display the specified purchase order
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        // Load relationships, but conditionally load order.items.product only if order exists
        $with = ['items.product', 'payments'];
        
        if ($purchaseOrder->order_id) {
            $with[] = 'order.items.product';
        }
        
        $purchaseOrder->load($with);
        return view('admin.purchase-orders.show', compact('purchaseOrder'));
    }

    /**
     * Show the form for editing the specified purchase order
     */
    public function edit(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['items.product']);
        
        // Get products for the dropdown
        try {
            $products = Product::with(['brand', 'specifications'])
                ->orderBy('name')
                ->get();
        } catch (\Exception $e) {
            // If there's an error with products, set to empty collection
            $products = collect([]);
            \Log::error('Error loading products for purchase order edit: ' . $e->getMessage());
        }
        
        return view('admin.purchase-orders.edit', compact('purchaseOrder', 'products'));
    }

    /**
     * Update the specified purchase order
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $request->validate([
            'supplier_name' => 'required|string|max:255',
            'supplier_email' => 'nullable|email|max:255',
            'supplier_phone' => 'nullable|string|max:50',
            'currency' => 'required|in:AED,USD,CYN',
            'delivery_date_requested' => 'required|date',
            'description' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
            'notes' => 'nullable|string',
            'sub_total' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.item_description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.price_type' => 'nullable|string|in:aed,usd',
            'items.*.discount_percentage' => 'nullable|numeric|min:0|max:100',
            'items.*.specifications' => 'nullable|string',
            'items.*.size' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,gif|max:10240', // 10MB max
            'removed_attachments' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Handle attachments
            $currentAttachments = is_array($purchaseOrder->attachments) ? $purchaseOrder->attachments : [];
            
            // Remove attachments that were marked for deletion
            if ($request->filled('removed_attachments')) {
                $removedIndexes = array_map('intval', explode(',', $request->removed_attachments));
                foreach ($removedIndexes as $index) {
                    if (isset($currentAttachments[$index])) {
                        // Delete the file from storage
                        $attachment = $currentAttachments[$index];
                        if (isset($attachment['path'])) {
                            \Storage::disk('public')->delete($attachment['path']);
                        }
                        unset($currentAttachments[$index]);
                    }
                }
                // Re-index the array
                $currentAttachments = array_values($currentAttachments);
            }
            
            // Handle new file uploads
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $filename = 'attachment_' . time() . '_' . $file->getClientOriginalName();
                    $attachmentPath = $file->storeAs('purchase-orders/attachments', $filename, 'public');
                    
                    $currentAttachments[] = [
                        'type' => 'attachment',
                        'filename' => $file->getClientOriginalName(),
                        'path' => $attachmentPath,
                        'size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'uploaded_at' => now()->toISOString()
                    ];
                }
            }

            // Update purchase order basic info
            $purchaseOrder->update([
                'supplier_name' => $request->supplier_name,
                'supplier_email' => $request->supplier_email,
                'supplier_phone' => $request->supplier_phone,
                'currency' => $request->currency,
                'delivery_date_requested' => $request->delivery_date_requested,
                'description' => $request->description,
                'terms_conditions' => $request->terms_conditions,
                'notes' => $request->notes,
                'sub_total' => $request->sub_total,
                'total_amount' => $request->total_amount,
                'attachments' => !empty($currentAttachments) ? $currentAttachments : null,
                'updated_by' => Auth::id()
            ]);

            // Delete existing items and create new ones
            $purchaseOrder->items()->delete();

            // Process new items
            foreach ($request->items as $index => $itemData) {
                $quantity = (float) $itemData['quantity'];
                $unitPrice = (float) $itemData['unit_price'];
                $discountPercentage = (float) ($itemData['discount_percentage'] ?? 0);
                
                $subtotal = $quantity * $unitPrice;
                $discountAmount = ($subtotal * $discountPercentage) / 100;
                $totalAmount = $subtotal - $discountAmount;

                $purchaseOrder->items()->create([
                    'product_id' => $itemData['product_id'] ?: null,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'price_type' => $itemData['price_type'] ?? null,
                    'discount_percentage' => $discountPercentage,
                    'discount_amount' => $discountAmount,
                    'line_total' => $totalAmount,
                    'item_description' => $itemData['item_description'],
                    'specifications' => $itemData['specifications'] ?? '',
                    'size' => $itemData['size'] ?? '',
                ]);
            }

            DB::commit();

            return redirect()
                ->route('admin.purchase-orders.show', $purchaseOrder)
                ->with('success', 'Purchase order updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update purchase order: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update purchase order.');
        }
    }

    /**
     * Send email for purchase order from index page
     */
    public function sendEmail(Request $request, PurchaseOrder $purchaseOrder)
    {
        $request->validate([
            'supplier_email' => 'required|email',
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

            // Send email to supplier
            Mail::to($request->supplier_email)->send(new \App\Mail\PurchaseOrderEmail($purchaseOrder, $ccEmails));
            
            $previousStatus = $purchaseOrder->status;
            
            // Update status to sent_to_supplier if email was sent successfully
            if ($purchaseOrder->status === 'draft') {
                $purchaseOrder->update(['status' => 'sent_to_supplier']);
            }
            
            // Return JSON response for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Purchase order email sent successfully!',
                    'previous_status' => $previousStatus,
                    'new_status' => $purchaseOrder->fresh()->status
                ]);
            }
            
            return redirect()->back()->with('success', 'Purchase order email sent successfully to ' . $request->supplier_email . '!');
        } catch (\Exception $e) {
            Log::error('Failed to send purchase order email: ' . $e->getMessage());
            
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
     * Mark purchase order as acknowledged
     */
    public function markAsAcknowledged(PurchaseOrder $purchaseOrder)
    {
        try {
            $purchaseOrder->markAsAcknowledged();
            return redirect()->back()->with('success', 'Purchase order marked as acknowledged.');

        } catch (\Exception $e) {
            Log::error('Failed to acknowledge purchase order: ' . $e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update purchase order status
     */
    public function updateStatus(Request $request, PurchaseOrder $purchaseOrder)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(PurchaseOrder::$statuses))
        ]);

        try {
            $oldStatus = $purchaseOrder->status;
            $newStatus = $request->status;

            $purchaseOrder->update([
                'status' => $newStatus,
                'updated_by' => Auth::id()
            ]);

            // Update timestamps based on status
            if ($newStatus === PurchaseOrder::STATUS_SENT_TO_SUPPLIER && !$purchaseOrder->sent_to_supplier_at) {
                $purchaseOrder->update(['sent_to_supplier_at' => now()]);
            }

            if ($newStatus === PurchaseOrder::STATUS_ACKNOWLEDGED && !$purchaseOrder->acknowledged_at) {
                $purchaseOrder->update(['acknowledged_at' => now()]);
            }

            Log::info("Purchase Order {$purchaseOrder->po_number} status changed from {$oldStatus} to {$newStatus}");

            return redirect()->back()->with('success', 'Purchase order status updated successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to update purchase order status: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update purchase order status.');
        }
    }

    /**
     * Create payment for purchase order
     */
    public function createPayment(Request $request, PurchaseOrder $purchaseOrder)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $purchaseOrder->getRemainingAmount(),
            'payment_method' => 'required|in:' . implode(',', array_keys(SupplierPayment::$paymentMethods)),
            'payment_date' => 'required|date',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'bank_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'transaction_id' => 'nullable|string|max:255',
        ]);

        try {
            $payment = SupplierPayment::create([
                'purchase_order_id' => $purchaseOrder->id,
                'order_id' => $purchaseOrder->order_id,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'payment_date' => $request->payment_date,
                'reference_number' => $request->reference_number,
                'notes' => $request->notes,
                'bank_name' => $request->bank_name,
                'account_number' => $request->account_number,
                'transaction_id' => $request->transaction_id,
                'status' => SupplierPayment::STATUS_PENDING,
                'created_by' => Auth::id()
            ]);

            return redirect()->back()->with('success', 'Payment record created successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to create supplier payment: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to create payment record.');
        }
    }

    /**
     * Generate PDF
     */
    public function generatePdf(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['items.product', 'order', 'supplier']);
        
        $pdf = Pdf::loadView('admin.purchase-orders.pdf', compact('purchaseOrder'));
        
        return $pdf->download($purchaseOrder->po_number . '.pdf');
    }

    /**
     * Delete purchase order
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        try {
            if ($purchaseOrder->status !== PurchaseOrder::STATUS_DRAFT) {
                return redirect()->back()->with('error', 'Only draft purchase orders can be deleted.');
            }

            $poNumber = $purchaseOrder->po_number;
            $purchaseOrder->delete();

            return redirect()
                ->route('admin.purchase-orders.index')
                ->with('success', "Purchase order {$poNumber} deleted successfully.");

        } catch (\Exception $e) {
            Log::error('Failed to delete purchase order: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete purchase order.');
        }
    }

    /**
     * Get order items for AJAX request
     */
    public function getOrderItems(Order $order)
    {
        try {
            $orderItems = $order->items()->with('product.specifications')->get();
            
            $items = $orderItems->map(function ($item) {
                $product = $item->product;
                $specifications = [];
                
                if ($product && $product->specifications) {
                    $specifications = $product->specifications->map(function ($spec) {
                        return $spec->display_name . ': ' . $spec->formatted_value;
                    })->toArray();
                }
                
                return [
                    'product_id' => $item->product_id,
                    'item_description' => $product ? $product->name : 'Unknown Product',
                    'quantity' => (float) $item->quantity,
                    'unit_price' => (float) $item->price,
                    'specifications' => $specifications,
                    'variation' => $item->variation ?? '',
                    'discount_percentage' => (float) ($item->discount_percentage ?? 0),
                    'discount_amount' => (float) ($item->discount_amount ?? 0),
                    'line_total' => (float) $item->line_total,
                    'product_name' => $product ? $product->name : 'Unknown Product',
                    'product_description' => $product ? $product->description : '',
                    'procurement_price_aed' => $product ? ($product->procurement_price_aed ?? $product->price_aed ?? $product->price ?? 0) : 0,
                    'procurement_price_usd' => $product ? ($product->procurement_price_usd ?? $product->price ?? 0) : 0,
                    'has_size_options' => $product ? ($product->has_size_options ?? false) : false,
                    'size_options' => $product ? ($product->size_options ?? []) : []
                ];
            });
            
            return response()->json([
                'success' => true,
                'items' => $items,
                'order' => [
                    'order_number' => $order->order_number,
                    'customer_name' => $order->getCustomerName(),
                    'total_amount' => $order->total_amount,
                    'currency' => $order->currency ?? 'AED',
                    'vat_rate' => $order->vat_rate ?? 0,
                    'vat_amount' => $order->vat_amount ?? 0,
                    'shipping_rate' => $order->shipping_rate ?? 0
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to get order items: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load order items'
            ], 500);
        }
    }
} 