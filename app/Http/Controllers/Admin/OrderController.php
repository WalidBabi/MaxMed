<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function create()
    {
        // Get customers with their associated users
        $customers = \App\Models\Customer::with('user')->get();
        $products = \App\Models\Product::all();
        
        return view('admin.orders.create', compact('customers', 'products'));
    }
    
    public function index()
    {
        $orders = Order::with(['user', 'orderItems.product'])
            ->latest()
            ->paginate(10);
            
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'orderItems.product']);
        return view('admin.orders.show', compact('order'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        // Get the customer to access user_id
        $customer = \App\Models\Customer::findOrFail($validated['customer_id']);

        $order = \App\Models\Order::create([
            'user_id' => $customer->user_id,
            'status' => 'processing',
            'total' => 0, // Will be calculated
        ]);

        $total = 0;
        foreach ($validated['items'] as $item) {
            $product = \App\Models\Product::find($item['product_id']);
            $order->orderItems()->create([
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'price' => $product->price,
            ]);
            $total += $product->price * $item['quantity'];
        }

        $order->update(['total' => $total]);

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Order created successfully');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled'
        ]);

        $order->update([
            'status' => $request->status
        ]);

        return redirect()->back()->with('success', 'Order status updated successfully');
    }
} 