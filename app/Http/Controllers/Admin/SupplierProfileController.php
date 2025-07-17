<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SupplierInformation;
use App\Models\SupplierCategory;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class SupplierProfileController extends Controller
{
    /**
     * Display a listing of supplier profiles
     */
    public function index(Request $request)
    {
        $query = User::whereHas('role', function($q) {
            $q->where('name', 'supplier');
        })->with(['supplierInformation', 'activeSupplierCategories.category', 'role']);

        // Filter by status
        if ($request->filled('status')) {
            $query->whereHas('supplierInformation', function($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        // Filter by onboarding completion
        if ($request->filled('onboarding')) {
            if ($request->onboarding === 'completed') {
                $query->whereHas('supplierInformation', function($q) {
                    $q->where('onboarding_completed', true);
                });
            } else {
                $query->whereHas('supplierInformation', function($q) {
                    $q->where('onboarding_completed', false);
                });
            }
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('supplierInformation', function($subQ) use ($search) {
                      $subQ->where('company_name', 'like', "%{$search}%")
                           ->orWhere('business_registration_number', 'like', "%{$search}%")
                           ->orWhere('trade_license_number', 'like', "%{$search}%");
                  });
            });
        }

        // Sort suppliers
        $sort = $request->get('sort', 'created_desc');
        switch ($sort) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'company_asc':
                $query->join('supplier_information', 'users.id', '=', 'supplier_information.user_id')
                      ->orderBy('supplier_information.company_name', 'asc')
                      ->select('users.*');
                break;
            case 'company_desc':
                $query->join('supplier_information', 'users.id', '=', 'supplier_information.user_id')
                      ->orderBy('supplier_information.company_name', 'desc')
                      ->select('users.*');
                break;
            case 'created_asc':
                $query->orderBy('created_at', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $suppliers = $query->paginate(15)->appends($request->query());

        // Get statistics
        $stats = [
            'total' => User::whereHas('role', function($q) {
                $q->where('name', 'supplier');
            })->count(),
            'completed_onboarding' => User::whereHas('role', function($q) {
                $q->where('name', 'supplier');
            })->whereHas('supplierInformation', function($q) {
                $q->where('onboarding_completed', true);
            })->count(),
            'pending_approval' => SupplierInformation::where('status', SupplierInformation::STATUS_PENDING_APPROVAL)->count(),
            'active' => SupplierInformation::where('status', SupplierInformation::STATUS_ACTIVE)->count(),
        ];

        return view('admin.supplier-profiles.index', compact('suppliers', 'stats'));
    }

    /**
     * Display comprehensive supplier profile
     */
    public function show(User $supplier)
    {
        // Ensure the user is actually a supplier
        if (!$supplier->isSupplier()) {
            return redirect()->route('admin.supplier-profiles.index')
                ->with('error', 'Selected user is not a supplier.');
        }

        // Load all related data
        $supplier->load([
            'supplierInformation.brand',
            'activeSupplierCategories.category',
            'supplierCategories.category',
            'products.category',
            'products.brand',
            'products.inventory'
        ]);

        // Get performance metrics
        $performanceMetrics = $this->getSupplierPerformanceMetrics($supplier);

        // Get recent activity
        $recentActivity = $this->getSupplierRecentActivity($supplier);

        return view('admin.supplier-profiles.show', compact('supplier', 'performanceMetrics', 'recentActivity'));
    }

    /**
     * Download supplier document
     */
    public function downloadDocument(User $supplier, $documentType)
    {
        if (!$supplier->isSupplier() || !$supplier->supplierInformation) {
            abort(404);
        }

        $documents = $supplier->supplierInformation->documents ?? [];
        
        if (!isset($documents[$documentType])) {
            abort(404, 'Document not found');
        }

        $documentPath = $documents[$documentType];
        
        // Handle certification files (array)
        if ($documentType === 'certification_files' && is_array($documentPath)) {
            abort(400, 'Please specify which certification file to download');
        }

        // Check if file exists in supplier_documents disk
        if (!Storage::disk('supplier_documents')->exists($documentPath)) {
            abort(404, 'File not found on server');
        }

        $fileName = basename($documentPath);
        $fileContent = Storage::disk('supplier_documents')->get($documentPath);
        $mimeType = Storage::disk('supplier_documents')->mimeType($documentPath);

        return Response::make($fileContent, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    /**
     * Download certification file (for multiple certification files)
     */
    public function downloadCertification(User $supplier, $index)
    {
        if (!$supplier->isSupplier() || !$supplier->supplierInformation) {
            abort(404);
        }

        $documents = $supplier->supplierInformation->documents ?? [];
        
        if (!isset($documents['certification_files']) || !is_array($documents['certification_files'])) {
            abort(404, 'Certification files not found');
        }

        $certificationFiles = $documents['certification_files'];
        
        if (!isset($certificationFiles[$index])) {
            abort(404, 'Certification file not found');
        }

        $documentPath = $certificationFiles[$index];

        // Check if file exists in supplier_documents disk
        if (!Storage::disk('supplier_documents')->exists($documentPath)) {
            abort(404, 'File not found on server');
        }

        $fileName = basename($documentPath);
        $fileContent = Storage::disk('supplier_documents')->get($documentPath);
        $mimeType = Storage::disk('supplier_documents')->mimeType($documentPath);

        return Response::make($fileContent, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    /**
     * Update supplier status (approve/suspend/activate)
     */
    public function updateStatus(Request $request, User $supplier)
    {
        if (!$supplier->isSupplier() || !$supplier->supplierInformation) {
            return redirect()->back()->with('error', 'Supplier information not found.');
        }

        $request->validate([
            'status' => 'required|in:' . implode(',', [
                SupplierInformation::STATUS_ACTIVE,
                SupplierInformation::STATUS_PENDING_APPROVAL,
                SupplierInformation::STATUS_SUSPENDED,
                SupplierInformation::STATUS_INACTIVE
            ]),
            'notes' => 'nullable|string|max:1000'
        ]);

        $oldStatus = $supplier->supplierInformation->status;
        $newStatus = $request->status;

        $supplier->supplierInformation->update([
            'status' => $newStatus,
            'approved_at' => $newStatus === SupplierInformation::STATUS_ACTIVE ? now() : null,
            'approved_by' => $newStatus === SupplierInformation::STATUS_ACTIVE ? auth()->id() : null,
        ]);

        $statusMessages = [
            SupplierInformation::STATUS_ACTIVE => 'approved and activated',
            SupplierInformation::STATUS_SUSPENDED => 'suspended',
            SupplierInformation::STATUS_INACTIVE => 'deactivated',
            SupplierInformation::STATUS_PENDING_APPROVAL => 'set to pending approval',
        ];

        $message = "Supplier has been " . ($statusMessages[$newStatus] ?? 'updated');

        return redirect()->back()->with('success', $message);
    }

    /**
     * Get supplier performance metrics
     */
    private function getSupplierPerformanceMetrics(User $supplier)
    {
        $metrics = [
            'total_products' => $supplier->products()->count(),
            'active_products' => $supplier->products()->where('is_active', true)->count(),
            'total_categories' => $supplier->activeSupplierCategories()->count(),
            'total_quotations' => 0,
            'won_quotations' => 0,
            'win_rate' => 0,
            'avg_response_time' => 0,
            'avg_rating' => 0,
            'last_activity' => null,
        ];

        // Calculate quotation metrics from supplier categories
        $categoryMetrics = $supplier->activeSupplierCategories()->get();
        if ($categoryMetrics->isNotEmpty()) {
            $metrics['total_quotations'] = $categoryMetrics->sum('total_quotations');
            $metrics['won_quotations'] = $categoryMetrics->sum('won_quotations');
            $metrics['win_rate'] = $metrics['total_quotations'] > 0 
                ? round(($metrics['won_quotations'] / $metrics['total_quotations']) * 100, 1)
                : 0;
            $metrics['avg_response_time'] = round($categoryMetrics->avg('avg_response_time_hours'), 1);
            $metrics['avg_rating'] = round($categoryMetrics->avg('avg_customer_rating'), 1);
        }

        // Get last activity
        $lastQuotation = $categoryMetrics->max('last_quotation_at');
        $lastProductUpdate = $supplier->products()->latest('updated_at')->first()?->updated_at;
        
        $metrics['last_activity'] = max($lastQuotation, $lastProductUpdate, $supplier->updated_at);

        return $metrics;
    }

    /**
     * Get supplier recent activity
     */
    private function getSupplierRecentActivity(User $supplier)
    {
        $activities = collect();

        // Recent products
        $recentProducts = $supplier->products()
            ->latest()
            ->take(5)
            ->get()
            ->map(function($product) {
                return [
                    'type' => 'product',
                    'action' => 'created',
                    'description' => "Added product: {$product->name}",
                    'timestamp' => $product->created_at,
                    'data' => $product
                ];
            });

        $activities = $activities->merge($recentProducts);

        // Recent category assignments
        $recentCategories = $supplier->supplierCategories()
            ->latest('created_at')
            ->take(3)
            ->with('category')
            ->get()
            ->map(function($assignment) {
                return [
                    'type' => 'category',
                    'action' => 'assigned',
                    'description' => "Assigned to category: {$assignment->category->name}",
                    'timestamp' => $assignment->created_at,
                    'data' => $assignment
                ];
            });

        $activities = $activities->merge($recentCategories);

        // Sort by timestamp and limit
        return $activities->sortByDesc('timestamp')->take(10)->values();
    }
} 