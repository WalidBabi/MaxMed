<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use App\Models\CrmLead;
use App\Models\QuotationRequest;
use App\Models\Product;
use App\Notifications\LeadCreatedNotification;
use App\Mail\LeadAssignmentMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactSubmissionController extends Controller
{
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

        return view('crm.contact-submissions.index', compact('submissions'));
    }

    /**
     * Show specific contact submission
     */
    public function show(ContactSubmission $submission)
    {
        $submission->load(['assignedTo', 'convertedToInquiry']);
        $products = Product::orderBy('name')->get();
        
        return view('crm.contact-submissions.show', compact('submission', 'products'));
    }

    /**
     * Convert contact submission to CRM lead
     */
    public function convertToLead(Request $request, ContactSubmission $submission)
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
            // Split name for CRM lead
            $nameParts = explode(' ', trim($submission->name), 2);
            $firstName = $nameParts[0] ?? 'Unknown';
            $lastName = $nameParts[1] ?? 'Contact';

            // Create CRM lead
            $lead = CrmLead::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $submission->email,
                'phone' => $submission->phone,
                'company_name' => $submission->company ?? 'Unknown Company',
                'source' => $validated['lead_source'],
                'status' => $validated['lead_status'],
                'priority' => $validated['priority'],
                'estimated_value' => $validated['estimated_value'],
                'notes' => "Converted from contact submission:\n" . 
                          "Subject: {$submission->subject}\n" . 
                          "Original Message: {$submission->message}\n" .
                          ($validated['notes'] ? "CRM Notes: {$validated['notes']}" : ""),
                'assigned_to' => auth()->id(),
                'last_contacted_at' => now(),
                'expected_close_date' => now()->addDays(30), // Default 30-day close expectation
            ]);

            // Automatically create a customer from this lead
            try {
                $customer = $lead->createCustomer();
                $lead->logActivity('note', 'Customer created', "Customer '{$customer->name}' (ID: {$customer->id}) automatically created from this lead");
                $successMessage = 'Contact submission converted to lead and customer successfully.';
            } catch (\Exception $e) {
                // Log the error but don't fail the lead creation
                Log::error("Failed to create customer from lead {$lead->id}: " . $e->getMessage());
                $lead->logActivity('note', 'Customer creation failed', "Failed to automatically create customer: " . $e->getMessage());
                $successMessage = 'Contact submission converted to lead successfully. (Customer creation failed)';
            }

            // Update contact submission
            $submission->update([
                'status' => 'converted_to_lead',
                'assigned_to' => auth()->id(),
            ]);

            // Send email notification to assigned user (which is the current user in this case)
            try {
                $assignedUser = auth()->user();
                if ($assignedUser) {
                    // Send email immediately using Mail facade (same as quotes/invoices)
                    Mail::to($assignedUser->email)->send(new LeadAssignmentMail($lead, $assignedUser, null, $assignedUser, true));
                    
                    // Update email history like quotes/invoices
                    $emailHistory = $lead->email_history ?? [];
                    $emailHistory[] = [
                        'sent_at' => now()->toISOString(),
                        'to' => $assignedUser->email,
                        'subject' => '👥 New Lead Assigned - ' . $lead->full_name,
                        'type' => 'assignment_from_contact',
                        'sent_by' => $assignedUser->name,
                        'original_submission_id' => $submission->id
                    ];
                    
                    $lead->update([
                        'email_history' => $emailHistory,
                        'last_email_sent_at' => now()
                    ]);
                    
                    // Also send database notification for dashboard
                    $assignedUser->notify(new LeadCreatedNotification($lead));
                    
                    \Log::info('Lead assignment email sent successfully (converted from contact)', [
                        'lead_id' => $lead->id,
                        'assigned_to' => $assignedUser->id,
                        'assigned_to_email' => $assignedUser->email,
                        'original_submission_id' => $submission->id
                    ]);
                    
                    $lead->logActivity('note', 'Assignment email sent', "Assignment email sent to {$assignedUser->name} ({$assignedUser->email})");
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send lead assignment email (converted from contact)', [
                    'lead_id' => $lead->id,
                    'assigned_to' => $lead->assigned_to,
                    'submission_id' => $submission->id,
                    'error' => $e->getMessage()
                ]);
                // Non-fatal: continue even if email fails
            }

            DB::commit();

            return redirect()->route('crm.leads.show', $lead)
                ->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error converting contact submission to lead: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to convert submission to lead.');
        }
    }

    /**
     * Convert contact submission to quotation request (for direct sales inquiries)
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
                'notes' => "Converted from contact submission (CRM):\n" . 
                          "Name: {$submission->name}\n" . 
                          "Email: {$submission->email}\n" . 
                          ($submission->phone ? "Phone: {$submission->phone}\n" : "") .
                          ($submission->company ? "Company: {$submission->company}\n" : "") .
                          "Original Message: {$submission->message}\n" .
                          ($validated['notes'] ? "CRM Notes: {$validated['notes']}" : ""),
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
            'status' => 'required|in:new,in_review,converted_to_lead,converted_to_inquiry,responded,closed',
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
     * Add CRM notes
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

    /**
     * Bulk actions for contact submissions
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:assign,update_status,mark_as_lead_potential',
            'submission_ids' => 'required|array',
            'submission_ids.*' => 'exists:contact_submissions,id',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'nullable|in:new,in_review,converted_to_lead,converted_to_inquiry,responded,closed',
        ]);

        $submissions = ContactSubmission::whereIn('id', $validated['submission_ids']);

        switch ($validated['action']) {
            case 'assign':
                if ($validated['assigned_to']) {
                    $submissions->update(['assigned_to' => $validated['assigned_to']]);
                    $count = count($validated['submission_ids']);
                    return redirect()->back()->with('success', "{$count} submissions assigned successfully.");
                }
                break;

            case 'update_status':
                if ($validated['status']) {
                    $submissions->update([
                        'status' => $validated['status'],
                        'assigned_to' => auth()->id(),
                    ]);
                    $count = count($validated['submission_ids']);
                    return redirect()->back()->with('success', "{$count} submissions updated successfully.");
                }
                break;

            case 'mark_as_lead_potential':
                $submissions->update([
                    'status' => 'in_review',
                    'assigned_to' => auth()->id(),
                    'admin_notes' => DB::raw("CONCAT(COALESCE(admin_notes, ''), '\n\n[" . now()->format('Y-m-d H:i') . " - " . auth()->user()->name . "]\nMarked as potential lead for follow-up')")
                ]);
                $count = count($validated['submission_ids']);
                return redirect()->back()->with('success', "{$count} submissions marked as potential leads.");
                break;
        }

        return redirect()->back()->with('error', 'Invalid bulk action.');
    }
} 