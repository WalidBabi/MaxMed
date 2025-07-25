<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuotationRequest;
use App\Models\SupplierQuotation;
use App\Models\User;
use App\Models\Product;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\Order;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Notifications\NewQuotationRequestNotification;
use Spatie\Permission\Models\Role;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\View as ViewFacade;
use App\Models\SupplierInquiry;
use App\Models\SupplierCategoryType;
use Illuminate\Http\JsonResponse;
use App\Models\SupplierInquiryItem;

class InquiryController extends Controller
{
    /**
     * Display a listing of inquiries
     */
    public function index(Request $request)
    {
        $query = SupplierInquiry::with(['product', 'items.product', 'supplierResponses.supplier'])
            ->latest();

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
            })->orWhereHas('supplierResponses.supplier', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $inquiries = $query->paginate(10);

        return view('admin.inquiries.index', compact('inquiries'));
    }

    /**
     * Show the form for creating a new inquiry
     */
    public function create(): ViewContract
    {
        $products = Product::with(['specifications', 'brand', 'category'])
            ->orderBy('name')
            ->get();
        
        $categories = Category::orderBy('name')->get();

        return ViewFacade::make('admin.inquiries.create', compact('products', 'categories'));
    }

    /**
     * Store a newly created inquiry
     */
    public function store(Request $request): RedirectResponse
    {
        // Detailed request logging
        Log::info('Raw request data:', [
            'all' => $request->all(),
            'has_items' => $request->has('items'),
            'items_count' => $request->has('items') ? count($request->items) : 0,
            'items_detail' => $request->get('items', [])
        ]);
        
        try {
            // Filter out empty items (items with no product_type or empty product_type)
            $items = $request->get('items', []);
            $filteredItems = array_filter($items, function($item) {
                // Only keep items that have a product_type AND either a product_id (for listed) or product_name (for unlisted)
                if (empty($item['product_type'])) {
                    return false;
                }
                
                if ($item['product_type'] === 'listed') {
                    return !empty($item['product_id']);
                } else {
                    return !empty($item['product_name']);
                }
            });
            
            // Replace the items in the request with filtered items
            $request->merge(['items' => array_values($filteredItems)]);
            
            // Log the filtered items for debugging
            Log::info('Filtered items:', ['filtered_items' => $filteredItems]);
            
            // Base validation rules
            $rules = [
                'requirements' => 'nullable|string',
                'customer_reference' => 'nullable|string|max:255',
                'internal_notes' => 'nullable|string',
                'attachments' => 'nullable|array',
                'attachments.*' => 'file|mimes:pdf|max:10240', // 10MB max per file
                'items' => 'nullable|array',
            ];

            // If no items are provided, we need at least attachments or requirements
            if (empty($filteredItems) && empty($request->file('attachments')) && empty($request->get('requirements'))) {
                $rules['items'] = 'required|array|min:1';
            }

            // Add conditional rules for each item based on product type
            foreach ($filteredItems as $index => $item) {
                $rules["items.{$index}.product_type"] = 'required|in:listed,unlisted';
                $rules["items.{$index}.quantity"] = 'nullable|numeric|min:0';
                $rules["items.{$index}.requirements"] = 'nullable|string';
                $rules["items.{$index}.notes"] = 'nullable|string';
                $rules["items.{$index}.specifications"] = 'nullable|string';
                $rules["items.{$index}.size"] = 'nullable|string|max:255';
                
                if (($item['product_type'] ?? '') === 'listed') {
                    $rules["items.{$index}.product_id"] = 'required|exists:products,id';
                    $rules["items.{$index}.product_name"] = 'nullable|string|max:255';
                } else {
                    $rules["items.{$index}.product_name"] = 'required|string|max:255';
                    $rules["items.{$index}.product_description"] = 'nullable|string';
                    $rules["items.{$index}.product_brand"] = 'nullable|string|max:255';
                    $rules["items.{$index}.product_category"] = 'nullable|string|max:255';
                    $rules["items.{$index}.product_specifications"] = 'nullable|string';
                }
            }
            
            // Add validation for targeting method
            $rules['targeting_method'] = 'required|in:all,categories';
            if ($request->input('targeting_method') === 'categories') {
                $rules['target_categories'] = 'required|array|min:1';
                $rules['target_categories.*'] = 'exists:categories,id';
            }

            $validated = $request->validate($rules);

            Log::info('Validation passed', ['validated_data' => $validated]);

            DB::beginTransaction();
            try {
                $inquiry = new SupplierInquiry();
                
                // Set main inquiry fields
                $inquiry->requirements = $request->requirements;
                $inquiry->customer_reference = $request->customer_reference;
                $inquiry->internal_notes = $request->internal_notes;
                $inquiry->status = 'pending';
                
                // Handle supplier targeting
                if ($request->input('targeting_method') === 'categories') {
                    $inquiry->broadcast_to_all_suppliers = false;
                    $inquiry->target_supplier_categories = $request->input('target_categories');
                } else {
                    $inquiry->broadcast_to_all_suppliers = true;
                    $inquiry->target_supplier_categories = null;
                }
                
                // Handle file uploads
                $attachments = [];
                if ($request->hasFile('attachments')) {
                    foreach ($request->file('attachments') as $file) {
                        $filename = time() . '_' . $file->getClientOriginalName();
                        $path = $file->storeAs('inquiry_attachments', $filename, 'public');
                        $attachments[] = [
                            'original_name' => $file->getClientOriginalName(),
                            'stored_name' => $filename,
                            'path' => $path,
                            'size' => $file->getSize(),
                            'mime_type' => $file->getClientMimeType(),
                            'uploaded_at' => now()->toDateTimeString()
                        ];
                    }
                }
                $inquiry->attachments = $attachments;
                
                Log::info('Creating new inquiry', [
                    'items_count' => count($request->items ?? [])
                ]);

                $inquiry->save();
                
                // Create inquiry items (if any provided)
                if (!empty($request->items)) {
                    foreach ($request->items as $index => $itemData) {
                        $item = new SupplierInquiryItem();
                        $item->supplier_inquiry_id = $inquiry->id;
                        $item->sort_order = $index;
                        
                        if ($itemData['product_type'] === 'listed') {
                            $item->product_id = $itemData['product_id'];
                            $item->product_name = $itemData['product_name'] ?? null;
                            Log::info('Adding listed product item', [
                                'product_id' => $itemData['product_id'],
                                'product_name' => $itemData['product_name'] ?? null,
                                'index' => $index
                            ]);
                        } else {
                            $item->product_name = $itemData['product_name'];
                            $item->product_description = $itemData['product_description'] ?? null;
                            $item->product_brand = $itemData['product_brand'] ?? null;
                            $item->product_category = $itemData['product_category'] ?? null;
                            $item->product_specifications = $itemData['product_specifications'] ?? null;
                            
                            Log::info('Adding unlisted product item', [
                                'product_name' => $itemData['product_name'],
                                'index' => $index
                            ]);
                        }
                        
                        $item->quantity = $itemData['quantity'] ?? null;
                        $item->requirements = $itemData['requirements'] ?? null;
                        $item->notes = $itemData['notes'] ?? null;
                        $item->specifications = $itemData['specifications'] ?? null;
                        $item->size = $itemData['size'] ?? null;
                        
                        $item->save();
                    }
                }
                
                Log::info('Inquiry saved successfully', [
                    'inquiry_id' => $inquiry->id, 
                    'reference_number' => $inquiry->reference_number,
                    'items_count' => count($request->items ?? [])
                ]);

                DB::commit();

                // Broadcast to suppliers after successful save
                try {
                    $inquiry->broadcast();
                    $message = 'Inquiry created and broadcast to suppliers successfully.';
                    Log::info('Inquiry broadcast successful', ['inquiry_id' => $inquiry->id]);
                } catch (\Exception $broadcastError) {
                    Log::error('Failed to broadcast inquiry: ' . $broadcastError->getMessage());
                    $message = 'Inquiry created successfully, but failed to broadcast to suppliers. Please try broadcasting manually.';
                }

                return redirect()
                    ->route('admin.inquiries.index')
                    ->with('success', $message . " Reference: {$inquiry->reference_number}");
                
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Failed to create inquiry: ' . $e->getMessage(), [
                    'request_data' => $request->all(),
                    'exception' => $e->getTraceAsString()
                ]);
                return back()
                    ->withInput()
                    ->with('error', 'Failed to create inquiry. ' . $e->getMessage());
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            throw $e;
        }
    }

    /**
     * Display the specified inquiry
     */
    public function show(SupplierInquiry $inquiry)
    {
        $inquiry->load(['product', 'items.product', 'supplierResponses.supplier', 'quotations']);
        
        // Get smart supplier recommendations for this product
        $smartRecommendations = $this->getSmartSupplierRecommendations($inquiry);

        return view('admin.inquiries.show', compact('inquiry', 'smartRecommendations'));
    }

    /**
     * Get smart supplier recommendations based on product category and performance
     */
    private function getSmartSupplierRecommendations(SupplierInquiry $inquiry)
    {
        // Get the product category ID from either the product or directly from the inquiry
        $productCategoryId = $inquiry->product_id 
            ? $inquiry->product->category_id 
            : $inquiry->product_category_id;
        
        if (!$productCategoryId) {
            return collect(); // Return empty collection if no category ID found
        }
        
        // Get suppliers assigned to this product category with performance data
        $suppliers = User::whereHas('role', function ($query) {
                $query->where('name', 'supplier');
            })
            ->whereHas('activeSupplierCategories', function ($query) use ($productCategoryId) {
                $query->where('category_id', $productCategoryId);
            })
            ->with(['activeSupplierCategories' => function ($query) use ($productCategoryId) {
                $query->where('category_id', $productCategoryId);
            }, 'supplierInformation'])
            ->get()
            ->map(function ($supplier) use ($productCategoryId) {
                $categoryAssignment = $supplier->activeSupplierCategories->first();
                
                // Calculate recommendation score
                $score = $this->calculateSupplierScore($supplier, $categoryAssignment);
                
                return [
                    'supplier' => $supplier,
                    'category_assignment' => $categoryAssignment,
                    'score' => $score,
                    'reasons' => $this->getRecommendationReasons($supplier, $categoryAssignment),
                ];
            })
            ->sortByDesc('score')
            ->take(5);

        return $suppliers;
    }

    /**
     * Calculate supplier recommendation score
     */
    private function calculateSupplierScore($supplier, $categoryAssignment)
    {
        $score = 0;
        
        // Win rate (40% weight)
        $score += ($categoryAssignment->quotation_win_rate ?? 0) * 0.4;
        
        // Response time (25% weight) - faster is better
        $responseTimeScore = max(0, 100 - (($categoryAssignment->avg_response_time_hours ?? 48) / 48 * 100));
        $score += $responseTimeScore * 0.25;
        
        // Customer rating (20% weight)
        $ratingScore = (($categoryAssignment->avg_customer_rating ?? 5) / 5) * 100;
        $score += $ratingScore * 0.2;
        
        // Experience/total quotations (10% weight)
        $experienceScore = min(100, ($categoryAssignment->total_quotations ?? 0) * 2);
        $score += $experienceScore * 0.1;
        
        // Availability bonus (5% weight) - recently active suppliers
        $availabilityScore = $categoryAssignment->last_quotation_at && 
                           $categoryAssignment->last_quotation_at->diffInDays(now()) < 30 ? 100 : 50;
        $score += $availabilityScore * 0.05;
        
        return round($score, 1);
    }

    /**
     * Get recommendation reasons for display
     */
    private function getRecommendationReasons($supplier, $categoryAssignment)
    {
        $reasons = [];
        
        if (($categoryAssignment->quotation_win_rate ?? 0) >= 70) {
            $reasons[] = 'High win rate (' . number_format($categoryAssignment->quotation_win_rate, 1) . '%)';
        }
        
        if (($categoryAssignment->avg_response_time_hours ?? 48) <= 12) {
            $reasons[] = 'Fast response time (' . number_format($categoryAssignment->avg_response_time_hours, 1) . 'h)';
        }
        
        if (($categoryAssignment->avg_customer_rating ?? 5) >= 4.5) {
            $reasons[] = 'Excellent rating (' . number_format($categoryAssignment->avg_customer_rating, 1) . '/5)';
        }
        
        if (($categoryAssignment->total_quotations ?? 0) >= 20) {
            $reasons[] = 'Experienced (' . $categoryAssignment->total_quotations . ' quotations)';
        }
        
        if ($categoryAssignment->last_quotation_at && $categoryAssignment->last_quotation_at->diffInDays(now()) < 7) {
            $reasons[] = 'Recently active';
        }
        
        return $reasons;
    }

    /**
     * Forward inquiry to supplier
     */
    public function forwardToSupplier(Request $request, SupplierInquiry $inquiry)
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
    public function updateStatus(Request $request, SupplierInquiry $inquiry)
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
    public function generateQuote(Request $request, SupplierInquiry $inquiry)
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
                'customer_name' => $inquiry->supplierResponses->first()->supplier->name,
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
                ->with('success', 'Customer quote generated successfully! Review and send to customer.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error generating quote from supplier quotation: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to generate quote.');
        }
    }

    /**
     * Send notification to supplier
     */
    private function sendSupplierNotification(SupplierInquiry $inquiry, User $supplier)
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
    public function cancel(Request $request, SupplierInquiry $inquiry)
    {
        $validated = $request->validate([
            'reason' => 'nullable|string',
        ]);

        $inquiry->update([
            'status' => SupplierInquiry::STATUS_CANCELLED,
            'internal_notes' => ($inquiry->internal_notes ?? '') . "\n\nCancelled: " . ($validated['reason'] ?? 'No reason provided'),
        ]);

        return redirect()->back()->with('success', 'Inquiry cancelled successfully.');
    }

    /**
     * Create purchase order from supplier quotation (after customer accepts quote)
     */
    public function createPurchaseOrder(Request $request, SupplierInquiry $inquiry)
    {
        $validated = $request->validate([
            'supplier_quotation_id' => 'required|exists:supplier_quotations,id',
            'customer_order_id' => 'nullable|exists:orders,id', // Make customer order optional
            'notes' => 'nullable|string',
        ]);

        $supplierQuotation = SupplierQuotation::with(['supplier', 'supplier.supplierInformation'])->find($validated['supplier_quotation_id']);
        $order = $validated['customer_order_id'] ? Order::find($validated['customer_order_id']) : null;
        
        DB::beginTransaction();
        try {
            // Get supplier information or use basic user info
            $supplier = $supplierQuotation->supplier;
            $supplierInfo = $supplier->supplierInformation;
            
            // Create purchase order with or without customer order
            $po = PurchaseOrder::create([
                'order_id' => $order ? $order->id : null, // Optional customer order
                'supplier_id' => $supplier->id,
                'quotation_request_id' => $inquiry->id,
                'supplier_quotation_id' => $supplierQuotation->id,
                'po_date' => now(),
                'delivery_date_requested' => now()->addDays($supplierQuotation->lead_time_days ?? 7),
                
                // Supplier information (not customer info)
                'supplier_name' => $supplierInfo ? $supplierInfo->company_name : $supplier->name,
                'supplier_email' => $supplierInfo ? $supplierInfo->primary_contact_email : $supplier->email,
                'supplier_phone' => $supplierInfo ? $supplierInfo->primary_contact_phone : null,
                'supplier_address' => $supplierInfo ? $supplierInfo->formatted_address : null,
                
                // Order details without customer identification
                'description' => "Purchase Order for Product: {$supplierQuotation->product->name}" . 
                                ($order ? " - Customer Order #{$order->order_number}" : " - Supplier Inquiry"),
                'terms_conditions' => $supplierQuotation->terms_conditions,
                'notes' => $validated['notes'] ?? "Lead time: {$supplierQuotation->lead_time_days} days",
                'currency' => $supplierQuotation->currency,
                'created_by' => auth()->id(),
            ]);

            // Create PO item based on supplier quotation (no customer details)
            PurchaseOrderItem::create([
                'purchase_order_id' => $po->id,
                'product_id' => $supplierQuotation->product_id,
                'item_description' => $supplierQuotation->description ?? $supplierQuotation->product->name,
                'quantity' => $inquiry->quantity,
                'unit_price' => $supplierQuotation->unit_price,
                'line_total' => $supplierQuotation->unit_price * $inquiry->quantity,
                'specifications' => json_encode($supplierQuotation->specifications ?? []),
                'size' => $supplierQuotation->size,
                'sort_order' => 1
            ]);

            $po->calculateTotals();

            // Update quotation request
            $inquiry->update([
                'status' => 'completed',
            ]);

            DB::commit();

            $successMessage = $order 
                ? 'Purchase order created and ready to send to supplier (customer information protected).'
                : 'Purchase order created from supplier inquiry and ready to send to supplier.';

            return redirect()->route('admin.purchase-orders.show', $po)
                ->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error creating purchase order from supplier quotation: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to create purchase order.');
        }
    }

    /**
     * Bulk forward inquiries to suppliers
     */
    public function bulkForward(Request $request)
    {
        $validated = $request->validate([
            'inquiry_ids' => 'required|array|min:1',
            'inquiry_ids.*' => 'exists:quotation_requests,id',
            'supplier_id' => 'required|exists:users,id',
            'internal_notes' => 'nullable|string',
        ]);

        $supplier = User::findOrFail($validated['supplier_id']);
        
        // Verify supplier role
        if (!$supplier->isSupplier()) {
            return redirect()->back()->with('error', 'Selected user is not a supplier.');
        }

        $inquiries = SupplierInquiry::whereIn('id', $validated['inquiry_ids'])
            ->where('status', 'pending')
            ->get();

        if ($inquiries->isEmpty()) {
            return redirect()->back()->with('error', 'No valid pending inquiries found for bulk forwarding.');
        }

        DB::beginTransaction();
        try {
            $successCount = 0;
            
            foreach ($inquiries as $inquiry) {
                $inquiry->update([
                    'supplier_id' => $validated['supplier_id'],
                    'status' => 'forwarded',
                    'forwarded_at' => now(),
                    'internal_notes' => $validated['internal_notes'] ?? $inquiry->internal_notes,
                ]);

                // Send notification to supplier
                $this->sendSupplierNotification($inquiry, $supplier);
                $successCount++;
            }

            DB::commit();

            return redirect()->back()->with('success', 
                "Successfully forwarded {$successCount} " . Str::plural('inquiry', $successCount) . " to {$supplier->name}.");
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error in bulk forwarding inquiries: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to forward inquiries. Please try again.');
        }
    }

    /**
     * Get supplier recommendations for AJAX requests
     */
    public function getSupplierRecommendations(Request $request)
    {
        $categoryId = $request->get('category_id');
        $inquiryId = $request->get('inquiry_id');
        
        if (!$categoryId) {
            return response()->json(['error' => 'Category ID is required'], 400);
        }

        $suppliers = User::whereHas('role', function ($query) {
                $query->where('name', 'supplier');
            })
            ->whereHas('activeSupplierCategories', function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->with(['activeSupplierCategories' => function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            }])
            ->get()
            ->map(function ($supplier) use ($categoryId) {
                $categoryAssignment = $supplier->activeSupplierCategories->first();
                $score = $this->calculateSupplierScore($supplier, $categoryAssignment);
                
                return [
                    'id' => $supplier->id,
                    'name' => $supplier->name,
                    'email' => $supplier->email,
                    'score' => $score,
                    'win_rate' => $categoryAssignment->quotation_win_rate ?? 0,
                    'response_time' => $categoryAssignment->avg_response_time_hours ?? 48,
                    'rating' => $categoryAssignment->avg_customer_rating ?? 5,
                    'total_quotations' => $categoryAssignment->total_quotations ?? 0,
                    'reasons' => $this->getRecommendationReasons($supplier, $categoryAssignment),
                ];
            })
            ->sortByDesc('score')
            ->values();

        return response()->json($suppliers);
    }

    /**
     * Broadcast inquiry to suppliers
     */
    public function broadcast(SupplierInquiry $inquiry)
    {
        try {
            $inquiry->broadcast();
            return redirect()->back()->with('success', 'Inquiry broadcast to suppliers successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to broadcast inquiry: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to broadcast inquiry to suppliers.');
        }
    }

    /**
     * Get status updates for dynamic refresh
     */
    public function getStatusUpdates(Request $request): JsonResponse
    {
        if (!$request->ajax()) {
            return response()->json(['error' => 'Invalid request'], 400);
        }

        $inquiries = SupplierInquiry::with(['supplierResponses'])
            ->get()
            ->map(function ($inquiry) {
                // Only count quoted responses as actual responses
                $actualResponseCount = $inquiry->supplierResponses->where('status', 'quoted')->count();
                $totalSuppliersNotified = $inquiry->supplierResponses->count();
                
                // Count suppliers with different engagement levels
                $emailsSentSuccessfully = $inquiry->supplierResponses->where('email_sent_successfully', true)->count();
                $suppliersClickedEmail = $inquiry->supplierResponses->whereNotNull('viewed_at')->count();
                $emailsFailed = $inquiry->supplierResponses->where('email_sent_successfully', false)->count();
                
                // Additional response counts
                $totalClicks = $suppliersClickedEmail;
                $quotedCount = $inquiry->supplierResponses->where('status', 'quoted')->count();
                $interestedCount = $inquiry->supplierResponses->where('status', 'interested')->count();
                $notInterestedCount = $inquiry->supplierResponses->where('status', 'not_interested')->count();

                return [
                    'id' => $inquiry->id,
                    'reference_number' => $inquiry->reference_number,
                    'status' => $inquiry->status,
                    'responses' => $quotedCount,
                    'total_suppliers' => $totalSuppliersNotified,
                    'emails_sent' => $emailsSentSuccessfully,
                    'emails_clicked' => $suppliersClickedEmail,
                    'emails_failed' => $emailsFailed,
                    'total_clicks' => $totalClicks,
                    'quoted' => $quotedCount,
                    'interested' => $interestedCount,
                    'not_interested' => $notInterestedCount
                ];
            });

        return response()->json(['inquiries' => $inquiries]);
    }
} 