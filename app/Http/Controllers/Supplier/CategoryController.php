<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    /**
     * Display supplier's assigned categories
     */
    public function index()
    {
        $user = Auth::user();
        
        // Allow super admins and suppliers to access this page
        if (!$user->isSupplier() && !$user->hasPermission('dashboard.view')) {
            abort(403, 'Access denied. You must be a supplier or admin to access this page.');
        }

        // For admins viewing supplier pages, we'll show demo data or empty state
        if ($user->hasPermission('dashboard.view') && !$user->isSupplier()) {
            // Show empty state for admins
            $activeCategories = collect([]);
            $performanceData = [
                'overall_score' => 0,
                'total_quotations' => 0,
                'won_quotations' => 0,
                'avg_response_time' => 0,
                'avg_rating' => 5.0,
            ];
        } else {
            // Get active category assignments for suppliers
            $activeCategories = $user->activeSupplierCategories()
                ->with('category')
                ->orderBy('created_at', 'desc')
                ->get();

            // Calculate performance metrics
            $performanceData = [
                'overall_score' => $user->overall_performance_score,
                'total_quotations' => $activeCategories->sum('total_quotations'),
                'won_quotations' => $activeCategories->sum('won_quotations'),
                'avg_response_time' => $activeCategories->avg('avg_response_time_hours'),
                'avg_rating' => $activeCategories->avg('avg_customer_rating'),
            ];
        }

        return view('supplier.categories.index', compact('activeCategories', 'performanceData'));
    }

    /**
     * Show detailed performance for a specific category
     */
    public function show($categoryId)
    {
        $user = Auth::user();
        
        // Allow super admins and suppliers to access this page
        if (!$user->isSupplier() && !$user->hasPermission('dashboard.view')) {
            abort(403, 'Access denied. You must be a supplier or admin to access this page.');
        }

        // For admins, redirect to admin supplier categories page
        if ($user->hasPermission('dashboard.view') && !$user->isSupplier()) {
            return redirect()->route('admin.supplier-categories.index')
                ->with('info', 'Admins should use the admin supplier categories management page.');
        }

        // Get the specific category assignment
        $assignment = $user->supplierCategories()
            ->with('category')
            ->where('category_id', $categoryId)
            ->firstOrFail();

        // Get quotation history for this category
        $recentQuotations = \App\Models\QuotationRequest::where('supplier_id', $user->id)
            ->whereHas('product', function($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            })
            ->with(['product', 'lead'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Calculate monthly performance trends
        $monthlyPerformance = $this->getMonthlyPerformance($user->id, $categoryId);

        return view('supplier.categories.show', compact(
            'assignment', 'recentQuotations', 'monthlyPerformance'
        ));
    }

    /**
     * Get monthly performance data for charts
     */
    private function getMonthlyPerformance($supplierId, $categoryId)
    {
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();

            $quotations = \App\Models\QuotationRequest::where('supplier_id', $supplierId)
                ->whereHas('product', function($q) use ($categoryId) {
                    $q->where('category_id', $categoryId);
                })
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->get();

            $months[] = [
                'month' => $date->format('M Y'),
                'total_quotations' => $quotations->count(),
                'won_quotations' => $quotations->where('status', 'completed')->count(),
                'win_rate' => $quotations->count() > 0 
                    ? ($quotations->where('status', 'completed')->count() / $quotations->count()) * 100 
                    : 0,
            ];
        }

        return $months;
    }

    /**
     * Request assignment to a new category
     */
    public function requestAssignment(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'notes' => 'required|string|max:1000',
            'estimated_lead_time' => 'nullable|integer|min:1|max:365',
            'minimum_order_value' => 'nullable|numeric|min:0',
        ]);

        $user = Auth::user();
        
        // Allow super admins and suppliers to access this functionality
        if (!$user->isSupplier() && !$user->hasPermission('dashboard.view')) {
            abort(403, 'Access denied. You must be a supplier or admin to access this page.');
        }

        // Admins should not use this function
        if ($user->hasPermission('dashboard.view') && !$user->isSupplier()) {
            return redirect()->route('admin.supplier-categories.index')
                ->with('warning', 'Admins should assign categories through the admin interface.');
        }

        // Check if already assigned or has pending request
        $existing = $user->supplierCategories()
            ->where('category_id', $validated['category_id'])
            ->first();

        if ($existing) {
            $status = $existing->status;
            $message = match($status) {
                'active' => 'You are already assigned to this category.',
                'pending_approval' => 'You already have a pending request for this category.',
                'inactive' => 'Your assignment to this category is inactive. Please contact admin.',
                default => 'Category assignment already exists.'
            };
            
            return redirect()->back()->with('warning', $message);
        }

        // Create new assignment request
        \App\Models\SupplierCategory::create([
            'supplier_id' => $user->id,
            'category_id' => $validated['category_id'],
            'status' => 'pending_approval',
            'notes' => $validated['notes'],
            'lead_time_days' => $validated['estimated_lead_time'],
            'minimum_order_value' => $validated['minimum_order_value'],
            'assigned_by' => null, // Will be set when approved
            'assigned_at' => null,
        ]);

        return redirect()->back()
            ->with('success', 'Category assignment request submitted. You will be notified when it is reviewed.');
    }
} 