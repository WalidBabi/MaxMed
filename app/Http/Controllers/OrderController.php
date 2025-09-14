<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:orders.view_own')->only(['index', 'show']);
        $this->middleware('permission:orders.create')->only(['create', 'store']);
        $this->middleware('permission:orders.edit')->only(['edit', 'update']);
    }

    /**
     * Display the user's orders
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get orders for the current user
        $orders = Order::where('user_id', $user->id)
            ->with(['items.product', 'quotations.supplier'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('orders.index', compact('orders'));
    }
    
    /**
     * Display a specific order
     */
    public function show(Order $order)
    {
        $user = Auth::user();
        
        // Ensure user can only view their own orders
        if ($order->user_id !== $user->id) {
            abort(403, 'You are not authorized to view this order.');
        }
        
        $order->load(['items.product', 'quotations.supplier']);
        
        return view('orders.show', compact('order'));
    }
}
