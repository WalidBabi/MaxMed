<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CrmLead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Customer::query();

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Country filter
        if ($request->filled('country')) {
            $query->where(function ($q) use ($request) {
                $q->where('billing_country', 'like', "%{$request->country}%")
                  ->orWhere('shipping_country', 'like', "%{$request->country}%");
            });
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        $allowedSorts = ['name', 'email', 'company_name', 'created_at', 'updated_at'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->latest();
        }

        $customers = $query->paginate(15)->appends($request->query());

        // Get filter data for dropdowns
        $countries = Customer::select('billing_country')
            ->distinct()
            ->whereNotNull('billing_country')
            ->where('billing_country', '!=', '')
            ->orderBy('billing_country')
            ->pluck('billing_country')
            ->merge(
                Customer::select('shipping_country')
                    ->distinct()
                    ->whereNotNull('shipping_country')
                    ->where('shipping_country', '!=', '')
                    ->orderBy('shipping_country')
                    ->pluck('shipping_country')
            )
            ->unique()
            ->sort()
            ->values();

        $stats = [
            'total' => Customer::count(),
            'active' => Customer::where('is_active', true)->count(),
            'inactive' => Customer::where('is_active', false)->count(),
            'with_company' => Customer::whereNotNull('company_name')->where('company_name', '!=', '')->count(),
        ];

        return view('crm.customers.index', compact('customers', 'countries', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::doesntHave('customer')
            ->select('id', 'name', 'email')
            ->get();
            
        return view('crm.customers.create', compact('users'));
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

        return redirect()->route('crm.customers.index')
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
        
        return view('crm.customers.show', compact('customer', 'stats', 'recentOrders', 'recentQuotes', 'recentInvoices'));
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
            
        return view('crm.customers.edit', compact('customer', 'users'));
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

        return redirect()->route('crm.customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();
        
        return redirect()->route('crm.customers.index')
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

    /**
     * Convert an existing customer into a CRM lead (or reset existing lead)
     */
    public function convertToLead(Request $request, Customer $customer)
    {
        // Require an email to associate lead uniquely
        if (empty($customer->email)) {
            return redirect()->back()->with('error', 'Customer has no email. Please add an email before converting to a lead.');
        }

        // Split customer name
        $nameParts = preg_split('/\s+/', trim($customer->name), 2);
        $firstName = $nameParts[0] ?? 'Unknown';
        $lastName = $nameParts[1] ?? 'Customer';

        // Try to find existing lead by email (email is unique in crm_leads)
        $lead = CrmLead::where('email', $customer->email)->first();

        if ($lead) {
            // Reset pipeline to new inquiry and update key fields
            $lead->update([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'mobile' => $lead->mobile ?: $customer->phone,
                'phone' => $lead->phone ?: $customer->phone,
                'company_name' => $customer->company_name ?: $lead->company_name,
                'status' => 'new_inquiry',
                'priority' => $lead->priority ?: 'medium',
                'source' => $lead->source ?: 'other',
                'expected_close_date' => now()->addDays(30),
                'assigned_to' => Auth::id(),
            ]);

            // Log activity
            try {
                $lead->logActivity('note', 'Lead re-opened from customer', 'Customer requested a new enquiry; lead reset to New Inquiry.');
            } catch (\Throwable $e) {
                // Silent fail on activity logging
            }
        } else {
            // Create a fresh lead from this customer
            $lead = CrmLead::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $customer->email,
                'mobile' => $customer->phone,
                'phone' => $customer->phone,
                'company_name' => $customer->company_name ?: 'Unknown Company',
                'status' => 'new_inquiry',
                'source' => 'other',
                'priority' => 'medium',
                'estimated_value' => null,
                'notes' => 'Lead created from existing customer #' . $customer->id,
                'expected_close_date' => now()->addDays(30),
                'last_contacted_at' => null,
                'assigned_to' => Auth::id(),
            ]);

            try {
                $lead->logActivity('note', 'Lead created from customer', 'Lead created from customer profile for a new enquiry.');
            } catch (\Throwable $e) {
                // Silent fail on activity logging
            }
        }

        return redirect()->route('crm.leads.show', $lead)->with('success', 'Lead is ready.');
    }
} 