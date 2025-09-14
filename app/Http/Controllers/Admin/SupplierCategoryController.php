<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Category;
use App\Models\SupplierCategory;
use App\Notifications\SupplierCategoryApprovalNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class SupplierCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:suppliers.view')->only(['index', 'show']);
        $this->middleware('permission:suppliers.manage_categories')->only(['create', 'store', 'edit', 'update', 'destroy']);
        $this->middleware('permission:suppliers.approve')->only(['approve', 'reject']);
    }

    /**
     * Display a listing of supplier category assignments.
     */
    public function index()
    {
        $suppliers = User::whereHas('role', function($q) {
            $q->where('name', 'supplier');
        })->with(['activeAssignedCategories', 'role', 'supplierInformation'])->get();

        $categories = Category::all();

        return view('admin.supplier-categories.index', compact('suppliers', 'categories'));
    }

    /**
     * Show the form for editing supplier category assignments.
     */
    public function edit(User $supplier)
    {
        // Ensure the user is actually a supplier
        if (!$supplier->isSupplier()) {
            return redirect()->route('admin.supplier-categories.index')
                ->with('error', 'Selected user is not a supplier.');
        }

        $supplier->load('supplierInformation');
        // Filter to show only leaf categories (categories that have no subcategories of their own)
        $categories = Category::whereNotNull('parent_id')
            ->whereDoesntHave('subcategories')
            ->get();
        $assignedCategoryIds = $supplier->activeAssignedCategories->pluck('id')->toArray();

        return view('admin.supplier-categories.edit', compact('supplier', 'categories', 'assignedCategoryIds'));
    }

    /**
     * Update supplier category assignments.
     */
    public function update(Request $request, User $supplier)
    {
        // Ensure the user is actually a supplier
        if (!$supplier->isSupplier()) {
            return redirect()->route('admin.supplier-categories.index')
                ->with('error', 'Selected user is not a supplier.');
        }

        $request->validate([
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
        ]);

        DB::transaction(function () use ($request, $supplier) {
            // Deactivate all current assignments
            $supplier->supplierCategories()->update(['status' => 'inactive']);

            // Create new assignments
            if ($request->has('categories') && is_array($request->categories)) {
                foreach ($request->categories as $categoryId) {
                    SupplierCategory::updateOrCreate(
                        [
                            'supplier_id' => $supplier->id,
                            'category_id' => $categoryId,
                        ],
                        [
                            'status' => 'active',
                            'assigned_at' => now(),
                        ]
                    );
                }
            }
        });

        return redirect()->route('admin.supplier-categories.index')
            ->with('success', "Category assignments updated for {$supplier->name}.");
    }



    /**
     * Get category statistics for dashboard.
     */
    public function getStats()
    {
        $stats = [
            'total_suppliers' => User::whereHas('role', function($q) {
                $q->where('name', 'supplier');
            })->count(),
            'assigned_suppliers' => User::whereHas('role', function($q) {
                $q->where('name', 'supplier');
            })->whereHas('activeAssignedCategories')->count(),
            'total_categories' => Category::count(),
            'assigned_categories' => Category::whereHas('activeSupplierAssignments')->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Export supplier category assignments.
     */
    public function export()
    {
        $suppliers = User::whereHas('role', function($q) {
            $q->where('name', 'supplier');
        })->with('activeAssignedCategories')->get();

        $csvData = [];
        $csvData[] = ['Supplier Name', 'Email', 'Assigned Categories', 'Assignment Date'];

        foreach ($suppliers as $supplier) {
            $categories = $supplier->activeAssignedCategories->pluck('name')->join('; ');
            $latestAssignment = $supplier->supplierCategories()
                ->where('status', 'active')
                ->latest('assigned_at')
                ->first();
            
            $assignmentDate = $latestAssignment && $latestAssignment->assigned_at ? $latestAssignment->assigned_at->format('Y-m-d H:i:s') : 'N/A';

            $csvData[] = [
                $supplier->name,
                $supplier->email,
                $categories ?: 'No categories assigned',
                $assignmentDate,
            ];
        }

        $filename = 'supplier-categories-' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Approve a pending category request
     */
    public function approve(User $supplier, SupplierCategory $category)
    {
        if ($category->supplier_id !== $supplier->id) {
            return redirect()->back()->with('error', 'Invalid category assignment.');
        }

        $category->update([
            'status' => SupplierCategory::STATUS_ACTIVE,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Send approval notification to supplier
        try {
            $supplier->notify(new SupplierCategoryApprovalNotification(
                category: $category->category,
                supplierCategory: $category,
                approvedBy: auth()->user(),
                isApproved: true
            ));
        } catch (\Exception $e) {
            Log::error('Failed to send category approval notification to supplier: ' . $supplier->email, [
                'error' => $e->getMessage(),
                'supplier_id' => $supplier->id,
                'category_id' => $category->id
            ]);
        }

        return redirect()->back()->with('success', 'Category approved successfully. An email notification has been sent to the supplier.');
    }

    /**
     * Reject a pending category request
     */
    public function reject(User $supplier, SupplierCategory $category)
    {
        if ($category->supplier_id !== $supplier->id) {
            return redirect()->back()->with('error', 'Invalid category assignment.');
        }

        // Store category info before deletion for email
        $categoryInfo = $category->category;
        $supplierEmail = $supplier->email;
        $supplierName = $supplier->name;
        $approvedBy = auth()->user();

        $category->delete();

        // Send rejection notification to supplier
        try {
            $supplier->notify(new SupplierCategoryApprovalNotification(
                category: $categoryInfo,
                supplierCategory: null,
                approvedBy: $approvedBy,
                isApproved: false
            ));
        } catch (\Exception $e) {
            Log::error('Failed to send category rejection notification to supplier: ' . $supplierEmail, [
                'error' => $e->getMessage(),
                'supplier_id' => $supplier->id,
                'category_id' => $categoryInfo->id
            ]);
        }

        return redirect()->back()->with('success', 'Category request rejected. An email notification has been sent to the supplier.');
    }

    /**
     * Display supplier response times
     */
    public function responseTimes()
    {
        $suppliers = User::whereHas('role', function($q) {
            $q->where('name', 'supplier');
        })->with(['activeSupplierCategories.category', 'role', 'supplierInformation'])->get();

        // Calculate response times for each supplier
        foreach ($suppliers as $supplier) {
            foreach ($supplier->activeSupplierCategories as $assignment) {
                $this->calculateSupplierResponseTime($assignment);
            }
        }

        // Sort suppliers by average response time
        $suppliers = $suppliers->sortBy(function($supplier) {
            $avgResponseTime = $supplier->activeSupplierCategories->avg('avg_response_time_hours');
            return $avgResponseTime ?? 999; // Put suppliers with no data at the end
        });

        return view('admin.supplier-categories.response-times', compact('suppliers'));
    }

    /**
     * Calculate response time for a supplier category assignment
     */
    private function calculateSupplierResponseTime($assignment)
    {
        // Get quotation requests for this supplier and category
        $quotationRequests = \App\Models\QuotationRequest::where('supplier_id', $assignment->supplier_id)
            ->whereHas('product', function($query) use ($assignment) {
                $query->where('category_id', $assignment->category_id);
            })
            ->whereNotNull('forwarded_at')
            ->whereNotNull('supplier_responded_at')
            ->get();

        if ($quotationRequests->count() > 0) {
            $totalResponseTime = 0;
            $validResponses = 0;

            foreach ($quotationRequests as $request) {
                $responseTime = $request->forwarded_at->diffInHours($request->supplier_responded_at);
                if ($responseTime >= 0 && $responseTime <= 168) { // Max 1 week, reasonable range
                    $totalResponseTime += $responseTime;
                    $validResponses++;
                }
            }

            if ($validResponses > 0) {
                $avgResponseTime = $totalResponseTime / $validResponses;
                $assignment->avg_response_time_hours = round($avgResponseTime, 1);
                $assignment->save();
            }
        }
    }
} 