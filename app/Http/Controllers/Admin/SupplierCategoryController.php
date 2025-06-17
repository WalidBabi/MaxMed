<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Category;
use App\Models\SupplierCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SupplierCategoryController extends Controller
{
    /**
     * Display supplier category management dashboard
     */
    public function index(Request $request)
    {
        $query = SupplierCategory::with(['supplier', 'category', 'assignedBy'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by supplier
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('supplier', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhereHas('category', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $assignments = $query->paginate(15);

        // Get filter options
        $categories = Category::orderBy('name')->get();
        $suppliers = User::whereHas('role', function ($q) {
            $q->where('name', 'supplier');
        })->orderBy('name')->get();

        // Get summary statistics
        $stats = [
            'total_assignments' => SupplierCategory::count(),
            'active_assignments' => SupplierCategory::where('status', 'active')->count(),
            'pending_assignments' => SupplierCategory::where('status', 'pending_approval')->count(),
            'total_suppliers' => $suppliers->count(),
            'suppliers_with_assignments' => SupplierCategory::distinct('supplier_id')->count(),
        ];

        return view('admin.supplier-categories.index', compact(
            'assignments', 'categories', 'suppliers', 'stats'
        ));
    }

    /**
     * Show detailed view of supplier categories
     */
    public function show($supplierId)
    {
        $supplier = User::with(['supplierCategories.category', 'supplierProducts.category'])
            ->findOrFail($supplierId);

        if (!$supplier->isSupplier()) {
            abort(404, 'User is not a supplier.');
        }

        // Get categories not yet assigned
        $assignedCategoryIds = $supplier->supplierCategories->pluck('category_id');
        $availableCategories = Category::whereNotIn('id', $assignedCategoryIds)
            ->orderBy('name')
            ->get();

        // Performance metrics
        $performanceData = [
            'overall_score' => $supplier->overall_performance_score,
            'total_quotations' => $supplier->supplierCategories->sum('total_quotations'),
            'won_quotations' => $supplier->supplierCategories->sum('won_quotations'),
            'avg_response_time' => $supplier->supplierCategories->avg('avg_response_time_hours'),
            'avg_rating' => $supplier->supplierCategories->avg('avg_customer_rating'),
        ];

        return view('admin.supplier-categories.show', compact(
            'supplier', 'availableCategories', 'performanceData'
        ));
    }

    /**
     * Show form to assign categories to supplier
     */
    public function create(Request $request)
    {
        $suppliers = User::whereHas('role', function ($q) {
            $q->where('name', 'supplier');
        })->orderBy('name')->get();

        $categories = Category::orderBy('name')->get();

        $selectedSupplierId = $request->get('supplier_id');
        $selectedCategoryId = $request->get('category_id');

        return view('admin.supplier-categories.create', compact(
            'suppliers', 'categories', 'selectedSupplierId', 'selectedCategoryId'
        ));
    }

    /**
     * Store new supplier category assignment
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:active,inactive,pending_approval',
            'minimum_order_value' => 'nullable|numeric|min:0',
            'lead_time_days' => 'nullable|integer|min:1|max:365',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Check if assignment already exists
        $existing = SupplierCategory::where('supplier_id', $validated['supplier_id'])
            ->where('category_id', $validated['category_id'])
            ->first();

        if ($existing) {
            return redirect()->back()
                ->withErrors(['category_id' => 'This supplier is already assigned to this category.'])
                ->withInput();
        }

        // Verify supplier role
        $supplier = User::findOrFail($validated['supplier_id']);
        if (!$supplier->isSupplier()) {
            return redirect()->back()
                ->withErrors(['supplier_id' => 'Selected user is not a supplier.'])
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $assignment = SupplierCategory::create([
                'supplier_id' => $validated['supplier_id'],
                'category_id' => $validated['category_id'],
                'status' => $validated['status'],
                'minimum_order_value' => $validated['minimum_order_value'],
                'lead_time_days' => $validated['lead_time_days'],
                'commission_rate' => $validated['commission_rate'],
                'notes' => $validated['notes'],
                'assigned_by' => auth()->id(),
                'assigned_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('admin.supplier-categories.show', $supplier->id)
                ->with('success', 'Supplier assigned to category successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error creating supplier category assignment: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to assign supplier to category.')
                ->withInput();
        }
    }

    /**
     * Show form to edit supplier category assignment
     */
    public function edit(SupplierCategory $supplierCategory)
    {
        $supplierCategory->load(['supplier', 'category']);
        
        return view('admin.supplier-categories.edit', compact('supplierCategory'));
    }

    /**
     * Update supplier category assignment
     */
    public function update(Request $request, SupplierCategory $supplierCategory)
    {
        $validated = $request->validate([
            'status' => 'required|in:active,inactive,pending_approval',
            'minimum_order_value' => 'nullable|numeric|min:0',
            'lead_time_days' => 'nullable|integer|min:1|max:365',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);

        $supplierCategory->update($validated);

        return redirect()->route('admin.supplier-categories.show', $supplierCategory->supplier_id)
            ->with('success', 'Supplier category assignment updated successfully.');
    }

    /**
     * Remove supplier category assignment
     */
    public function destroy(SupplierCategory $supplierCategory)
    {
        $supplierId = $supplierCategory->supplier_id;
        $supplierCategory->delete();

        return redirect()->route('admin.supplier-categories.show', $supplierId)
            ->with('success', 'Supplier category assignment removed successfully.');
    }

    /**
     * Bulk update status for multiple assignments
     */
    public function bulkUpdateStatus(Request $request)
    {
        $validated = $request->validate([
            'assignment_ids' => 'required|array',
            'assignment_ids.*' => 'exists:supplier_categories,id',
            'status' => 'required|in:active,inactive,pending_approval',
        ]);

        SupplierCategory::whereIn('id', $validated['assignment_ids'])
            ->update(['status' => $validated['status']]);

        return redirect()->back()
            ->with('success', 'Bulk status update completed successfully.');
    }

    /**
     * Get suppliers by category (AJAX endpoint)
     */
    public function getSuppliersByCategory(Request $request)
    {
        $categoryId = $request->get('category_id');
        
        if (!$categoryId) {
            return response()->json(['suppliers' => []]);
        }

        $suppliers = User::whereHas('supplierCategories', function ($q) use ($categoryId) {
            $q->where('category_id', $categoryId)
              ->where('status', 'active');
        })->with(['supplierCategories' => function ($q) use ($categoryId) {
            $q->where('category_id', $categoryId);
        }])->get();

        $formattedSuppliers = $suppliers->map(function ($supplier) use ($categoryId) {
            $assignment = $supplier->supplierCategories->first();
            return [
                'id' => $supplier->id,
                'name' => $supplier->name,
                'email' => $supplier->email,
                'performance_score' => $assignment ? $assignment->performance_score : 0,
                'avg_response_time' => $assignment ? $assignment->avg_response_time_hours : null,
                'quotation_win_rate' => $assignment ? $assignment->quotation_win_rate : 0,
                'minimum_order_value' => $assignment ? $assignment->minimum_order_value : null,
                'lead_time_days' => $assignment ? $assignment->lead_time_days : null,
            ];
        });

        return response()->json(['suppliers' => $formattedSuppliers]);
    }

    /**
     * Export supplier category data
     */
    public function export(Request $request)
    {
        $query = SupplierCategory::with(['supplier', 'category']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $assignments = $query->get();

        // Create CSV export
        $filename = 'supplier_categories_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $callback = function() use ($assignments) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'Supplier Name', 'Supplier Email', 'Category', 'Status', 
                'Min Order Value', 'Lead Time Days', 'Commission Rate',
                'Avg Response Time', 'Win Rate %', 'Total Quotations',
                'Won Quotations', 'Customer Rating', 'Assigned Date'
            ]);

            foreach ($assignments as $assignment) {
                fputcsv($file, [
                    $assignment->supplier->name,
                    $assignment->supplier->email,
                    $assignment->category->name,
                    $assignment->status,
                    $assignment->minimum_order_value,
                    $assignment->lead_time_days,
                    $assignment->commission_rate,
                    $assignment->avg_response_time_hours,
                    $assignment->quotation_win_rate,
                    $assignment->total_quotations,
                    $assignment->won_quotations,
                    $assignment->avg_customer_rating,
                    $assignment->created_at->format('Y-m-d'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
} 