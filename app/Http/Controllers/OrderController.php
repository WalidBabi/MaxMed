<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function index()
    {
        try {
            $orders = Order::where('user_id', Auth::id())->latest()->paginate(10);
            
            // Add debugging
            Log::info('User ID: ' . Auth::id());
            Log::info('Orders count: ' . $orders->count());

            return view('orders.index', compact('orders'));
        } catch (\Exception $e) {
            Log::error('Error accessing orders: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'There was an error accessing your orders. Please try again later.');
        }
    }

    public function show(Order $order)
    {
        try {
            // Check if user owns this order
            if ($order->user_id !== Auth::id()) {
                abort(403);
            }

            // Load the order with its items, products, and feedback
            $order->load(['items.product', 'feedback']);

            // Debug logging
            Log::info('Showing order: ' . $order->id);
            Log::info('Order items count: ' . $order->items->count());
            foreach($order->items as $item) {
                Log::info('Item ID: ' . $item->id . ', Product ID: ' . $item->product_id . ', Quantity: ' . $item->quantity . ', Price: ' . $item->price);
                if($item->product) {
                    Log::info('Product Name: ' . $item->product->name);
                } else {
                    Log::info('Product not found for item ID: ' . $item->id);
                }
            }

            return view('orders.show', compact('order'));
        } catch (\Exception $e) {
            Log::error('Error viewing order: ' . $e->getMessage());
            return redirect()->route('orders.index')->with('error', 'There was an error viewing this order. Please try again later.');
        }
    }
}
