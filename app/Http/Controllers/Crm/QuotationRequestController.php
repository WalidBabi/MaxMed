<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\QuotationRequest;
use App\Models\CrmLead;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuotationRequestController extends Controller
{
    /**
     * Constructor - Apply middleware for permissions
     */
    public function __construct()
    {
        $this->middleware('auth');
        // Simple role-based access control for CRM
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            
            if (!$user) {
                return redirect()->route('login');
            }
            
            // Allow super admins, business admins, and CRM-related roles
            if ($user->hasRole('super_admin') || 
                $user->hasRole('business_admin') || 
                $user->hasAnyRole(['operations_manager', 'sales_manager', 'customer_service_manager', 'purchasing_crm_assistant', 'purchasing-specialist']) ||
                $user->hasPermission('crm.access')) {
                return $next($request);
            }
            
            abort(403, 'Access denied. You need CRM permissions to access this resource.');
        });
    }

    /**
     * Display quotation requests for CRM
     */
    public function index(Request $request)
    {
        $query = QuotationRequest::with(['product', 'user'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by delivery timeline
        if ($request->filled('delivery_timeline')) {
            $query->where('delivery_timeline', $request->delivery_timeline);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('product', function ($productQuery) use ($search) {
                    $productQuery->where('name', 'like', "%{$search}%")
                              ->orWhere('sku', 'like', "%{$search}%");
                })->orWhereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                })->orWhere('requirements', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        // Filter by lead potential (if quotation came from contact submission)
        if ($request->filled('lead_potential')) {
            $query->whereHas('relatedContactSubmission', function ($q) use ($request) {
                $q->where('lead_potential', $request->lead_potential);
            });
        }

        // Filter high value requests (large quantities)
        if ($request->filled('high_value') && $request->high_value == '1') {
            $query->where('quantity', '>=', 10);
        }

        // Filter urgent requests
        if ($request->filled('urgent') && $request->urgent == '1') {
            $query->where('delivery_timeline', 'urgent');
        }

        $quotationRequests = $query->paginate(15);

        // Get statistics for dashboard cards
        $stats = [
            'total' => QuotationRequest::count(),
            'pending' => QuotationRequest::where('status', 'pending')->count(),
            'urgent' => QuotationRequest::where('delivery_timeline', 'urgent')->count(),
            'high_value' => QuotationRequest::where('quantity', '>=', 10)->count(),
        ];

        return view('crm.quotation-requests.index', compact('quotationRequests', 'stats'));
    }

    /**
     * Show specific quotation request
     */
    public function show(QuotationRequest $quotationRequest)
    {
        $quotationRequest->load(['product', 'user', 'relatedContactSubmission']);
        
        return view('crm.quotation-requests.show', compact('quotationRequest'));
    }

    /**
     * Convert quotation request to CRM lead
     */
    public function convertToLead(Request $request, QuotationRequest $quotationRequest)
    {
        $validated = $request->validate([
            'lead_source' => 'required|in:website,linkedin,email,phone,referral,trade_show,google_ads,other',
            'lead_status' => 'required|in:new,contacted,qualified,proposal,negotiation,won,lost',
            'priority' => 'required|in:low,medium,high',
            'estimated_value' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Determine customer information
            $customerName = $quotationRequest->user ? $quotationRequest->user->name : 'Guest Customer';
            $customerEmail = $quotationRequest->user ? $quotationRequest->user->email : 'guest@quotation.request';
            
            // Check if related contact submission exists for better info
            $contactSubmission = $quotationRequest->relatedContactSubmission;
            if ($contactSubmission) {
                $customerName = $contactSubmission->name;
                $customerEmail = $contactSubmission->email;
            }

            // Split name for CRM lead
            $nameParts = explode(' ', trim($customerName), 2);
            $firstName = $nameParts[0] ?? 'Guest';
            $lastName = $nameParts[1] ?? 'Customer';

            // Create CRM lead
            $lead = CrmLead::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $customerEmail,
                'phone' => $contactSubmission->phone ?? null,
                'company_name' => $contactSubmission->company ?? 'Unknown Company',
                'source' => $validated['lead_source'],
                'status' => $validated['lead_status'],
                'priority' => $validated['priority'],
                'estimated_value' => $validated['estimated_value'],
                'notes' => "Converted from quotation request:\n" . 
                          "Product: {$quotationRequest->product->name}\n" . 
                          "Quantity: {$quotationRequest->quantity}\n" . 
                          ($quotationRequest->size ? "Size: {$quotationRequest->size}\n" : "") .
                          ($quotationRequest->delivery_timeline ? "Timeline: " . ucfirst($quotationRequest->delivery_timeline) . "\n" : "") .
                          ($quotationRequest->requirements ? "Requirements: {$quotationRequest->requirements}\n" : "") .
                          ($quotationRequest->notes ? "Notes: {$quotationRequest->notes}\n" : "") .
                          ($validated['notes'] ? "CRM Notes: {$validated['notes']}" : ""),
                'assigned_to' => auth()->id(),
                'last_contacted_at' => now(),
                'expected_close_date' => now()->addDays(30), // Default 30-day close expectation
            ]);

            // Update quotation request status
            $quotationRequest->update([
                'status' => 'converted_to_lead',
                'internal_notes' => ($quotationRequest->internal_notes ?? '') . 
                                  "\n\n[" . now()->format('Y-m-d H:i') . " - " . auth()->user()->name . "]\n" .
                                  "Converted to CRM lead (ID: {$lead->id})"
            ]);

            // Update related contact submission if exists
            if ($contactSubmission) {
                $contactSubmission->update([
                    'status' => 'converted_to_lead',
                    'assigned_to' => auth()->id(),
                ]);
            }

            DB::commit();

            return redirect()->route('crm.leads.show', $lead)
                ->with('success', 'Quotation request converted to lead successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error converting quotation request to lead: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to convert quotation request to lead.');
        }
    }

    /**
     * Update quotation request status
     */
    public function updateStatus(Request $request, QuotationRequest $quotationRequest)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,forwarded,supplier_responded,quote_created,completed,cancelled,converted_to_lead',
            'internal_notes' => 'nullable|string',
        ]);

        $quotationRequest->update([
            'status' => $validated['status'],
            'internal_notes' => ($quotationRequest->internal_notes ?? '') . 
                              "\n\n[" . now()->format('Y-m-d H:i') . " - " . auth()->user()->name . "]\n" .
                              ($validated['internal_notes'] ?? "Status updated to: {$validated['status']}"),
        ]);

        return redirect()->back()->with('success', 'Quotation request status updated successfully.');
    }

    /**
     * Add CRM notes to quotation request
     */
    public function addNotes(Request $request, QuotationRequest $quotationRequest)
    {
        $validated = $request->validate([
            'internal_notes' => 'required|string',
        ]);

        $existingNotes = $quotationRequest->internal_notes ?? '';
        $newNotes = $existingNotes . "\n\n[" . now()->format('Y-m-d H:i') . " - " . auth()->user()->name . "]\n" . $validated['internal_notes'];

        $quotationRequest->update([
            'internal_notes' => $newNotes,
        ]);

        return redirect()->back()->with('success', 'Notes added successfully.');
    }

    /**
     * Bulk actions for quotation requests
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:update_status,mark_urgent,mark_high_value',
            'quotation_request_ids' => 'required|array',
            'quotation_request_ids.*' => 'exists:quotation_requests,id',
            'status' => 'nullable|in:pending,forwarded,supplier_responded,quote_created,completed,cancelled',
        ]);

        $quotationRequests = QuotationRequest::whereIn('id', $validated['quotation_request_ids']);

        switch ($validated['action']) {
            case 'update_status':
                if ($validated['status']) {
                    $quotationRequests->update([
                        'status' => $validated['status'],
                        'internal_notes' => DB::raw("CONCAT(COALESCE(internal_notes, ''), '\n\n[" . now()->format('Y-m-d H:i') . " - " . auth()->user()->name . "]\nBulk status update to: {$validated['status']}')")
                    ]);
                    $count = count($validated['quotation_request_ids']);
                    return redirect()->back()->with('success', "{$count} quotation requests updated successfully.");
                }
                break;

            case 'mark_urgent':
                $quotationRequests->update([
                    'internal_notes' => DB::raw("CONCAT(COALESCE(internal_notes, ''), '\n\n[" . now()->format('Y-m-d H:i') . " - " . auth()->user()->name . "]\nMarked as urgent for follow-up')")
                ]);
                $count = count($validated['quotation_request_ids']);
                return redirect()->back()->with('success', "{$count} quotation requests marked as urgent.");
                break;

            case 'mark_high_value':
                $quotationRequests->update([
                    'internal_notes' => DB::raw("CONCAT(COALESCE(internal_notes, ''), '\n\n[" . now()->format('Y-m-d H:i') . " - " . auth()->user()->name . "]\nMarked as high-value opportunity')")
                ]);
                $count = count($validated['quotation_request_ids']);
                return redirect()->back()->with('success', "{$count} quotation requests marked as high-value.");
                break;
        }

        return redirect()->back()->with('error', 'Invalid bulk action.');
    }
}
