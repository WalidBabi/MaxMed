<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function create()
    {
        // Get ALL customers (both with and without user accounts)
        $customers = \App\Models\Customer::with('user')->get();
        $products = \App\Models\Product::all();
        
        return view('admin.orders.create', compact('customers', 'products'));
    }
    
    public function index()
    {
        $orders = Order::with(['user', 'customer', 'orderItems.product'])
            ->latest()
            ->paginate(10);
            
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'customer', 'orderItems.product']);
        return view('admin.orders.show', compact('order'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'currency' => 'required|in:USD,AED',
            'proforma_invoice_id' => 'nullable|exists:invoices,id',
            'shipping_rate' => 'nullable|numeric|min:0',
            'vat_rate' => 'nullable|numeric|min:0',
            'vat_amount' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.item_details' => 'required|string',
            'items.*.specifications' => 'nullable|string',
            'items.*.size' => 'nullable|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0|max:100',
            'requires_quotation' => 'boolean'
        ]);

        // Filter out items with zero quantities and validate we have at least one item
        $validItems = array_filter($validated['items'], function($item) {
            return isset($item['quantity']) && $item['quantity'] > 0;
        });

        if (empty($validItems)) {
            return back()->withErrors(['items' => 'Please add at least one item with quantity greater than 0.'])->withInput();
        }

        // Get the customer
        $customer = \App\Models\Customer::with('user')->findOrFail($validated['customer_id']);
        
        // Determine user_id for the order
        $userId = $this->getUserIdForOrder($customer);

        // Start transaction
        DB::beginTransaction();

        try {
            $order = \App\Models\Order::create([
                'user_id' => $userId,
                'customer_id' => $customer->id,
                'proforma_invoice_id' => $validated['proforma_invoice_id'] ?? null,
                'currency' => $validated['currency'],
                'status' => \App\Models\Order::STATUS_PENDING, // Start with pending status
                'total_amount' => 0, // Will be calculated
                'shipping_rate' => $validated['shipping_rate'] ?? 0,
                'vat_rate' => $validated['vat_rate'] ?? 0,
                'vat_amount' => $validated['vat_amount'] ?? 0,
                'shipping_address' => $customer->shipping_street ?: $customer->billing_street ?: 'Admin Created Order',
                'shipping_city' => $customer->shipping_city ?: $customer->billing_city ?: 'N/A',
                'shipping_state' => $customer->shipping_state ?: $customer->billing_state ?: 'N/A',
                'shipping_zipcode' => $customer->shipping_zip ?: $customer->billing_zip ?: '00000',
                'shipping_phone' => $customer->phone ?: 'N/A',
                'notes' => "Customer: {$customer->name}" . ($customer->user ? '' : ' (No User Account)'),
                'requires_quotation' => $validated['requires_quotation'] ?? false
            ]);

            $total = 0;
            foreach ($validItems as $itemData) {
                $product = \App\Models\Product::find($itemData['product_id']);
                if ($product) {
                    // Calculate item totals using quote-like logic
                    $quantity = (float) $itemData['quantity'];
                    $rate = (float) $itemData['rate'];
                    $discount = (float) ($itemData['discount'] ?? 0);
                    
                    $subtotal = $quantity * $rate;
                    $discountAmount = ($subtotal * $discount) / 100;
                    $finalPrice = $subtotal - $discountAmount;
                    
                    // Build variation string from specifications and size
                    $variation = [];
                    if (!empty($itemData['specifications'])) {
                        $variation[] = 'Specs: ' . $itemData['specifications'];
                    }
                    if (!empty($itemData['size'])) {
                        $variation[] = 'Size: ' . $itemData['size'];
                    }
                    $variationString = implode(' | ', $variation);
                    
                    $order->orderItems()->create([
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'price' => $rate, // Unit price
                        'variation' => $variationString ?: null,
                        'discount_percentage' => $discount,
                        'discount_amount' => $discountAmount,
                    ]);
                    
                    $total += $finalPrice;
                }
            }

            // Calculate final total including shipping and VAT
            $finalTotal = $total + ($validated['shipping_rate'] ?? 0) + ($validated['vat_amount'] ?? 0);
            $order->update(['total_amount' => $finalTotal]);

            // If order requires quotation, update status accordingly
            if ($order->requires_quotation) {
                $order->update([
                    'status' => \App\Models\Order::STATUS_QUOTATIONS_RECEIVED,
                    'quotation_status' => \App\Models\Order::QUOTATION_STATUS_PENDING
                ]);
            }

            DB::commit();

            return redirect()->route('admin.orders.show', $order)
                ->with('success', 'Order created successfully for ' . $customer->name);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to create order: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to create order. Please try again.'])->withInput();
        }
    }

    /**
     * Get or create a user ID for the order
     * 
     * CHOOSE YOUR APPROACH:
     * - Option 1: Guest User (current implementation)
     * - Option 2: Auto-create user accounts (see autoCreateUserForCustomer method)
     */
    private function getUserIdForOrder($customer)
    {
        // If customer has a user account, use it
        if ($customer->user_id && $customer->user) {
            return $customer->user_id;
        }

        // OPTION 1: Use guest user (current implementation)
        return $this->getGuestUserId();
        
        // OPTION 2: Auto-create user account (uncomment to use)
        // return $this->autoCreateUserForCustomer($customer);
    }

    /**
     * OPTION 1: Get or create the guest user for customers without accounts
     */
    private function getGuestUserId()
    {
        $guestUser = User::firstOrCreate(
            ['email' => 'guest@maxmedme.com'],
            [
                'name' => 'Guest Customer',
                'password' => bcrypt('random_password_' . time()),
            ]
        );

        return $guestUser->id;
    }

    /**
     * OPTION 2: Auto-create a user account for the customer
     */
    private function autoCreateUserForCustomer($customer)
    {
        // Create a user account for the customer
        $user = User::create([
            'name' => $customer->name,
            'email' => $customer->email ?: ($customer->name . '@noemail.local'),
            'password' => bcrypt(Str::random(16)), // Random password
            'is_admin' => false,
        ]);

        // Link the customer to the new user
        $customer->update(['user_id' => $user->id]);

        return $user->id;
    }

    public function updateStatus(Request $request, Order $order)
    {
        try {
            $request->validate([
                'status' => 'required|string'
            ]);

            $order->update([
                'status' => $request->status
            ]);

            return redirect()->back()->with('success', 'Order status updated successfully');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to update order status: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update order status. Please try again.');
        }
    }

    public function destroy(Order $order)
    {
        try {
            // Delete associated order items first
            $order->orderItems()->delete();
            
            // Delete the order
            $order->delete();
            
            return redirect()->route('admin.orders.index')
                ->with('success', 'Order deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete order. Please try again.');
        }
    }
} 