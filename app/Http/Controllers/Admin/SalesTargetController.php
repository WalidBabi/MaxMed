<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalesTarget;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SalesTargetController extends Controller
{
    /**
     * Display a listing of sales targets
     */
    public function index(Request $request)
    {
        $query = SalesTarget::with(['creator', 'assignedTo']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by target type
        if ($request->filled('target_type')) {
            $query->where('target_type', $request->target_type);
        }

        // Filter by period type
        if ($request->filled('period_type')) {
            $query->where('period_type', $request->period_type);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->where('start_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('end_date', '<=', $request->end_date);
        }

        $targets = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.sales-targets.index', compact('targets'));
    }

    /**
     * Show the form for creating a new sales target
     */
    public function create()
    {
        $users = User::where('role_id', 1)->orWhere('role_id', 2)->get(); // Admin and sales roles
        return view('admin.sales-targets.create', compact('users'));
    }

    /**
     * Store a newly created sales target
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'period_type' => 'required|in:daily,weekly,monthly,quarterly,yearly',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'target_amount' => 'required|numeric|min:0',
            'target_type' => 'required|in:revenue,orders,customers,products',
            'assigned_to' => 'nullable|exists:users,id',
            'target_breakdown' => 'nullable|array'
        ]);

        $target = SalesTarget::create([
            'name' => $request->name,
            'description' => $request->description,
            'period_type' => $request->period_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'target_amount' => $request->target_amount,
            'target_type' => $request->target_type,
            'assigned_to' => $request->assigned_to,
            'target_breakdown' => $request->target_breakdown,
            'created_by' => Auth::id()
        ]);

        return redirect()->route('admin.sales-targets.index')
                        ->with('success', 'Sales target created successfully.');
    }

    /**
     * Display the specified sales target
     */
    public function show(SalesTarget $salesTarget)
    {
        $salesTarget->load(['creator', 'assignedTo']);
        
        // Update achieved amount
        $salesTarget->updateAchievedAmount();
        
        return view('admin.sales-targets.show', compact('salesTarget'));
    }

    /**
     * Show the form for editing the specified sales target
     */
    public function edit(SalesTarget $salesTarget)
    {
        $users = User::where('role_id', 1)->orWhere('role_id', 2)->get();
        return view('admin.sales-targets.edit', compact('salesTarget', 'users'));
    }

    /**
     * Update the specified sales target
     */
    public function update(Request $request, SalesTarget $salesTarget)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'period_type' => 'required|in:daily,weekly,monthly,quarterly,yearly',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'target_amount' => 'required|numeric|min:0',
            'target_type' => 'required|in:revenue,orders,customers,products',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'required|in:active,completed,cancelled',
            'target_breakdown' => 'nullable|array'
        ]);

        $salesTarget->update([
            'name' => $request->name,
            'description' => $request->description,
            'period_type' => $request->period_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'target_amount' => $request->target_amount,
            'target_type' => $request->target_type,
            'assigned_to' => $request->assigned_to,
            'status' => $request->status,
            'target_breakdown' => $request->target_breakdown
        ]);

        return redirect()->route('admin.sales-targets.index')
                        ->with('success', 'Sales target updated successfully.');
    }

    /**
     * Remove the specified sales target
     */
    public function destroy(SalesTarget $salesTarget)
    {
        $salesTarget->delete();

        return redirect()->route('admin.sales-targets.index')
                        ->with('success', 'Sales target deleted successfully.');
    }

    /**
     * Update achieved amounts for all active targets
     */
    public function updateAllAchievedAmounts()
    {
        $targets = SalesTarget::active()->get();
        
        foreach ($targets as $target) {
            $target->updateAchievedAmount();
        }

        return redirect()->route('admin.sales-targets.index')
                        ->with('success', 'All target achievements updated successfully.');
    }

    /**
     * Get sales analytics data
     */
    public function analytics()
    {
        // Current month data
        $currentMonth = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();
        
        // Previous month data
        $previousMonth = Carbon::now()->subMonth()->startOfMonth();
        $previousMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        $analytics = [
            'current_month' => [
                'revenue' => $this->getRevenue($currentMonth, $currentMonthEnd),
                'orders' => $this->getOrders($currentMonth, $currentMonthEnd),
                'customers' => $this->getCustomers($currentMonth, $currentMonthEnd),
                'products' => $this->getProducts($currentMonth, $currentMonthEnd)
            ],
            'previous_month' => [
                'revenue' => $this->getRevenue($previousMonth, $previousMonthEnd),
                'orders' => $this->getOrders($previousMonth, $previousMonthEnd),
                'customers' => $this->getCustomers($previousMonth, $previousMonthEnd),
                'products' => $this->getProducts($previousMonth, $previousMonthEnd)
            ],
            'targets' => [
                'active' => SalesTarget::active()->count(),
                'completed' => SalesTarget::where('status', 'completed')->count(),
                'overdue' => SalesTarget::active()->where('end_date', '<', Carbon::now())->count()
            ]
        ];

        // Calculate growth percentages
        $analytics['growth'] = [
            'revenue' => $this->calculateGrowth($analytics['current_month']['revenue'], $analytics['previous_month']['revenue']),
            'orders' => $this->calculateGrowth($analytics['current_month']['orders'], $analytics['previous_month']['orders']),
            'customers' => $this->calculateGrowth($analytics['current_month']['customers'], $analytics['previous_month']['customers']),
            'products' => $this->calculateGrowth($analytics['current_month']['products'], $analytics['previous_month']['products'])
        ];

        return view('admin.sales-targets.analytics', compact('analytics'));
    }

    /**
     * Get revenue for date range
     */
    private function getRevenue($startDate, $endDate)
    {
        return \App\Models\Invoice::whereBetween('invoice_date', [$startDate, $endDate])
                                 ->where('payment_status', '!=', 'cancelled')
                                 ->sum('total_amount');
    }

    /**
     * Get orders for date range
     */
    private function getOrders($startDate, $endDate)
    {
        return \App\Models\Order::whereBetween('created_at', [$startDate, $endDate])
                               ->where('status', '!=', 'cancelled')
                               ->count();
    }

    /**
     * Get customers for date range
     */
    private function getCustomers($startDate, $endDate)
    {
        return \App\Models\Customer::whereBetween('created_at', [$startDate, $endDate])
                                  ->count();
    }

    /**
     * Get products sold for date range
     */
    private function getProducts($startDate, $endDate)
    {
        return \App\Models\OrderItem::whereHas('order', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate])
                  ->where('status', '!=', 'cancelled');
        })->sum('quantity');
    }

    /**
     * Calculate growth percentage
     */
    private function calculateGrowth($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        
        return round((($current - $previous) / $previous) * 100, 2);
    }
} 