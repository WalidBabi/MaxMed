<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use App\Models\QuotationRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ContactSubmissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:crm.access')->only(['index', 'show']);
        $this->middleware('permission:crm.leads.view')->only(['index', 'show']);
        $this->middleware('permission:crm.leads.edit')->only(['edit', 'update', 'assign', 'convert']);
        $this->middleware('permission:crm.leads.delete')->only(['destroy']);
    }

    /**
     * Display contact submissions
     */
    public function index(Request $request)
    {
        $query = ContactSubmission::with(['assignedTo', 'convertedToInquiry'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%");
            });
        }

        // Filter sales inquiries
        if ($request->filled('sales_only') && $request->sales_only == '1') {
            $query->salesInquiries();
        }

        $submissions = $query->paginate(15);

        return view('admin.contact-submissions.index', compact('submissions'));
    }

    /**
     * Show specific contact submission
     */
    public function show(ContactSubmission $submission)
    {
        $submission->load(['assignedTo', 'convertedToInquiry']);
        $products = Product::orderBy('name')->get();
        
        return view('admin.contact-submissions.show', compact('submission', 'products'));
    }

    /**
     * Convert contact submission to quotation request
     */
    public function convertToInquiry(Request $request, ContactSubmission $submission)
    {
        if (!$submission->canConvertToInquiry()) {
            return redirect()->back()->with('error', 'This submission cannot be converted to an inquiry.');
        }

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'size' => 'nullable|string',
            'requirements' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Create quotation request
            $quotationRequest = QuotationRequest::create([
                'product_id' => $validated['product_id'],
                'user_id' => 0, // Guest user from contact form
                'quantity' => $validated['quantity'],
                'size' => $validated['size'],
                'requirements' => $validated['requirements'],
                'notes' => "Converted from contact submission:\n" . 
                          "Name: {$submission->name}\n" . 
                          "Email: {$submission->email}\n" . 
                          ($submission->phone ? "Phone: {$submission->phone}\n" : "") .
                          ($submission->company ? "Company: {$submission->company}\n" : "") .
                          "Original Message: {$submission->message}\n" .
                          ($validated['notes'] ? "Additional Notes: {$validated['notes']}" : ""),
                'status' => 'pending',
            ]);

            // Update contact submission
            $submission->update([
                'status' => 'converted_to_inquiry',
                'converted_to_inquiry_id' => $quotationRequest->id,
                'assigned_to' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->route('admin.inquiries.show', $quotationRequest)
                ->with('success', 'Contact submission converted to quotation request successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error converting contact submission to inquiry: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to convert submission to inquiry.');
        }
    }

    /**
     * Update contact submission status
     */
    public function updateStatus(Request $request, ContactSubmission $submission)
    {
        $validated = $request->validate([
            'status' => 'required|in:new,in_review,responded,closed',
            'admin_notes' => 'nullable|string',
        ]);

        $submission->update([
            'status' => $validated['status'],
            'admin_notes' => $validated['admin_notes'],
            'assigned_to' => auth()->id(),
            'responded_at' => in_array($validated['status'], ['responded', 'closed']) ? now() : null,
        ]);

        return redirect()->back()->with('success', 'Contact submission status updated successfully.');
    }

    /**
     * Add admin notes
     */
    public function addNotes(Request $request, ContactSubmission $submission)
    {
        $validated = $request->validate([
            'admin_notes' => 'required|string',
        ]);

        $existingNotes = $submission->admin_notes ?? '';
        $newNotes = $existingNotes . "\n\n[" . now()->format('Y-m-d H:i') . " - " . auth()->user()->name . "]\n" . $validated['admin_notes'];

        $submission->update([
            'admin_notes' => $newNotes,
            'assigned_to' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Notes added successfully.');
    }
} 