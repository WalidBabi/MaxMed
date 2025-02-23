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
        $orders = auth()->user()->orders()->latest()->paginate(10);
        
        // Add debugging
        Log::info('User ID: ' . auth()->id());
        Log::info('Orders count: ' . $orders->count());

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        if (auth()->user()->cannot('view', $order)) {
            abort(403);
        }

        // Add this line for debugging
        Log::info('Showing order: ' . $order->id);

        return view('orders.show', compact('order'));
    }
}
