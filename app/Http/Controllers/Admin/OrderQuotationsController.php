<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\SupplierQuotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderQuotationsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:quotations.view')->only(['index', 'show']);
        $this->middleware('permission:quotations.edit')->only(['update', 'accept', 'reject']);
        $this->middleware('permission:quotations.delete')->only(['destroy']);
    }

    /**
     * Display quotations for an order
     */
    public function index(Order $order)
    {
        $quotations = $order->supplierQuotations()
            ->with(['supplier'])
            ->latest()
            ->get();

        return view('admin.orders.quotations.index', [
            'order' => $order,
            'quotations' => $quotations
        ]);
    }

    /**
     * Approve a quotation
     */
    public function approve(Order $order, SupplierQuotation $quotation)
    {
        try {
            $quotation->approve();
            return redirect()->back()->with('success', 'Quotation approved successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to approve quotation: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to approve quotation.');
        }
    }
} 