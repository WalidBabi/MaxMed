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
            $orders = Auth::user()->orders()->latest()->paginate(10);
            
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
            if (Auth::user()->cannot('view', $order)) {
                abort(403);
            }

            // Add this line for debugging
            Log::info('Showing order: ' . $order->id);

            return view('orders.show', compact('order'));
        } catch (\Exception $e) {
            Log::error('Error viewing order: ' . $e->getMessage());
            return redirect()->route('orders.index')->with('error', 'There was an error viewing this order. Please try again later.');
        }
    }
}
