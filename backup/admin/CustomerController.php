<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = Customer::latest()->paginate(10);
        return view('admin.customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::doesntHave('customer')
            ->select('id', 'name', 'email')
            ->get();
            
        return view('admin.customers.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:customers,email',
            'phone' => 'nullable|string|max:50',
            'company_name' => 'nullable|string|max:255',
            'tax_id' => 'nullable|string|max:100',
            'billing_street' => 'nullable|string|max:255',
            'billing_city' => 'nullable|string|max:100',
            'billing_state' => 'nullable|string|max:100',
            'billing_zip' => 'nullable|string|max:20',
            'billing_country' => 'nullable|string|max:100',
            'shipping_street' => 'nullable|string|max:255',
            'shipping_city' => 'nullable|string|max:100',
            'shipping_state' => 'nullable|string|max:100',
            'shipping_zip' => 'nullable|string|max:20',
            'shipping_country' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // If user_id is provided, ensure it doesn't already have a customer
        if (!empty($validated['user_id'])) {
            $existingCustomer = Customer::where('user_id', $validated['user_id'])->first();
            if ($existingCustomer) {
                return back()->withErrors(['user_id' => 'This user is already associated with another customer.']);
            }
        }

        Customer::create($validated);

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        $customer->load('user');
        
        // Calculate customer statistics
        $stats = [
            'total_orders' => $customer->user_id ? \App\Models\Order::where('user_id', $customer->user_id)->count() : 0,
            
            'total_spent' => $customer->user_id ? \App\Models\Order::where('user_id', $customer->user_id)->sum('total_amount') : 0,
            
            'total_quotes' => \App\Models\Quote::where('customer_name', $customer->name)->count(),
            
            'total_invoices' => \App\Models\Invoice::where('customer_name', $customer->name)->count(),
            
            'pending_invoices' => \App\Models\Invoice::where('customer_name', $customer->name)
                ->where('payment_status', 'pending')->count(),
                
            'overdue_invoices' => \App\Models\Invoice::where('customer_name', $customer->name)
                ->where('payment_status', 'overdue')->count(),
        ];
        
        // Get recent orders
        $recentOrders = collect();
        if ($customer->user_id) {
            $recentOrders = \App\Models\Order::where('user_id', $customer->user_id)
                ->with(['orderItems.product'])
                ->latest()
                ->limit(5)
                ->get();
        }
        
        // Get recent quotes
        $recentQuotes = \App\Models\Quote::where('customer_name', $customer->name)
            ->latest()
            ->limit(5)
            ->get();
            
        // Get recent invoices  
        $recentInvoices = \App\Models\Invoice::where('customer_name', $customer->name)
            ->latest()
            ->limit(5)
            ->get();
        
        return view('admin.customers.show', compact('customer', 'stats', 'recentOrders', 'recentQuotes', 'recentInvoices'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        $users = User::whereDoesntHave('customer', function($query) use ($customer) {
                $query->where('id', '!=', $customer->id);
            })
            ->orWhere('id', $customer->user_id)
            ->select('id', 'name', 'email')
            ->get();
            
        return view('admin.customers.edit', compact('customer', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:customers,email,' . $customer->id,
            'phone' => 'nullable|string|max:50',
            'company_name' => 'nullable|string|max:255',
            'tax_id' => 'nullable|string|max:100',
            'billing_street' => 'nullable|string|max:255',
            'billing_city' => 'nullable|string|max:100',
            'billing_state' => 'nullable|string|max:100',
            'billing_zip' => 'nullable|string|max:20',
            'billing_country' => 'nullable|string|max:100',
            'shipping_street' => 'nullable|string|max:255',
            'shipping_city' => 'nullable|string|max:100',
            'shipping_state' => 'nullable|string|max:100',
            'shipping_zip' => 'nullable|string|max:20',
            'shipping_country' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // If user_id is provided and different from current, ensure it doesn't already have a customer
        if (isset($validated['user_id']) && $validated['user_id'] != $customer->user_id) {
            $existingCustomer = Customer::where('user_id', $validated['user_id'])->first();
            if ($existingCustomer) {
                return back()->withErrors(['user_id' => 'This user is already associated with another customer.']);
            }
        }

        $customer->update($validated);

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();
        
        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer deleted successfully.');
    }

    /**
     * Get customer by name for email modal
     */
    public function getByName($name)
    {
        $customer = Customer::where('name', urldecode($name))->first();
        
        if ($customer) {
            return response()->json([
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email
            ]);
        }
        
        return response()->json(['email' => null]);
    }

    /**
     * Get customer details by ID for invoice forms
     */
    public function getCustomerDetails($id)
    {
        $customer = Customer::find($id);
        
        if ($customer) {
            return response()->json([
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
                'company_name' => $customer->company_name,
                'billing_address' => $customer->billing_address,
                'shipping_address' => $customer->shipping_address,
                'phone' => $customer->phone,
            ]);
        }
        
        return response()->json(['error' => 'Customer not found'], 404);
    }
}
