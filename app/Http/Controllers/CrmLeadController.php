<?php

namespace App\Http\Controllers;

use App\Models\CrmLead;
use App\Models\User;
use App\Models\CrmDeal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CrmLeadController extends Controller
{
    public function index(Request $request)
    {
        $query = CrmLead::with(['assignedUser', 'activities']);
        
        // Check if user wants pipeline view (default) or table view
        $viewType = $request->get('view', 'pipeline');
        
        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }
        
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%");
            });
        }
        
        if ($viewType === 'pipeline') {
            // Group leads by status for pipeline view
            $pipelineData = $this->getPipelineData($query);
            $users = User::all();
            
            return view('crm.leads.index', compact('pipelineData', 'users', 'viewType'));
        } else {
            // Traditional table view
            $leads = $query->orderBy('created_at', 'desc')->paginate(15);
            $users = User::all();
            
            return view('crm.leads.index', compact('leads', 'users', 'viewType'));
        }
    }
    
    /**
     * Get leads organized by pipeline stages
     */
    private function getPipelineData($baseQuery)
    {
        $stages = [
            'new' => ['title' => 'New Leads', 'color' => 'blue'],
            'contacted' => ['title' => 'Contacted', 'color' => 'yellow'],
            'qualified' => ['title' => 'Qualified', 'color' => 'purple'],
            'proposal' => ['title' => 'Proposal', 'color' => 'orange'],
            'negotiation' => ['title' => 'Negotiation', 'color' => 'indigo'],
            'won' => ['title' => 'Won', 'color' => 'green'],
            'lost' => ['title' => 'Lost', 'color' => 'red']
        ];
        
        $pipelineData = [];
        
        foreach ($stages as $status => $config) {
            $stageQuery = clone $baseQuery;
            $leads = $stageQuery->where('status', $status)->orderBy('created_at', 'desc')->get();
            
            $pipelineData[$status] = [
                'title' => $config['title'],
                'color' => $config['color'],
                'leads' => $leads,
                'count' => $leads->count(),
                'total_value' => $leads->sum('estimated_value'),
                'high_priority_count' => $leads->where('priority', 'high')->count(),
                'overdue_count' => $leads->filter(function($lead) { return $lead->isOverdue(); })->count()
            ];
        }
        
        return $pipelineData;
    }
    
    public function create()
    {
        $users = User::all();
        return view('crm.leads.create', compact('users'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:crm_leads',
            'phone' => 'nullable|string|max:20',
            'company_name' => 'required|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'company_address' => 'nullable|string',
            'source' => 'required|in:website,linkedin,email,phone,referral,trade_show,google_ads,other',
            'priority' => 'required|in:low,medium,high',
            'estimated_value' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'expected_close_date' => 'nullable|date',
            'assigned_to' => 'required|exists:users,id',
        ]);
        
        $lead = CrmLead::create($validated);
        
        // Log initial activity
        $lead->logActivity('note', 'Lead created', "Lead created from {$lead->source}");
        
        return redirect()->route('crm.leads.show', $lead)
                        ->with('success', 'Lead created successfully!');
    }
    
    public function show(CrmLead $lead)
    {
        $lead->load(['assignedUser', 'activities.user', 'deals.assignedUser']);
        return view('crm.leads.show', compact('lead'));
    }
    
    public function edit(CrmLead $lead)
    {
        $users = User::all();
        return view('crm.leads.edit', compact('lead', 'users'));
    }
    
    public function update(Request $request, CrmLead $lead)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:crm_leads,email,' . $lead->id,
            'phone' => 'nullable|string|max:20',
            'company_name' => 'required|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'company_address' => 'nullable|string',
            'status' => 'required|in:new,contacted,qualified,proposal,negotiation,won,lost',
            'source' => 'required|in:website,linkedin,email,phone,referral,trade_show,google_ads,other',
            'priority' => 'required|in:low,medium,high',
            'estimated_value' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'expected_close_date' => 'nullable|date',
            'assigned_to' => 'required|exists:users,id',
        ]);
        
        $oldStatus = $lead->status;
        $lead->update($validated);
        
        // Log status change if it changed
        if ($oldStatus !== $validated['status']) {
            $lead->logActivity('note', 'Status changed', "Status changed from {$oldStatus} to {$validated['status']}");
        }
        
        return redirect()->route('crm.leads.show', $lead)
                        ->with('success', 'Lead updated successfully!');
    }
    
    public function destroy(CrmLead $lead)
    {
        $lead->delete();
        
        return redirect()->route('crm.leads.index')
                        ->with('success', 'Lead deleted successfully!');
    }
    
    public function addActivity(Request $request, CrmLead $lead)
    {
        $validated = $request->validate([
            'type' => 'required|in:call,email,meeting,note,quote_sent,demo,follow_up,task',
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'activity_date' => 'nullable|date',
            'status' => 'required|in:completed,scheduled',
            'due_date' => 'nullable|date|required_if:status,scheduled',
        ]);
        
        $lead->activities()->create([
            'user_id' => Auth::id() ?? 1,
            'type' => $validated['type'],
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'activity_date' => $validated['activity_date'] ?? now(),
            'status' => $validated['status'],
            'due_date' => $validated['due_date'],
        ]);
        
        // Update last contacted date
        if ($validated['status'] === 'completed') {
            $lead->updateLastContacted();
        }
        
        return redirect()->route('crm.leads.show', $lead)
                        ->with('success', 'Activity added successfully!');
    }
    
    public function convert(CrmLead $lead)
    {
        // Convert lead to customer if won
        if ($lead->status === 'won') {
            // Integration with your existing customers table
            // This would depend on your current customer model structure
        }
        
        return redirect()->route('crm.leads.show', $lead);
    }
    
    /**
     * Update lead status via AJAX
     */
    public function updateStatus(Request $request, CrmLead $lead)
    {
        $validated = $request->validate([
            'status' => 'required|in:new,contacted,qualified,proposal,negotiation,won,lost'
        ]);
        
        $oldStatus = $lead->status;
        $lead->update(['status' => $validated['status']]);
        
        // Log status change
        $lead->logActivity('note', 'Status changed', "Status changed from {$oldStatus} to {$validated['status']}");
        
        return response()->json([
            'success' => true,
            'message' => 'Lead status updated successfully',
            'lead' => $lead->load(['assignedUser', 'activities'])
        ]);
    }
    
    /**
     * Convert qualified lead to deal
     */
    public function convertToDeal(Request $request, CrmLead $lead)
    {
        $validated = $request->validate([
            'deal_name' => 'required|string|max:255',
            'deal_value' => 'required|numeric|min:0',
            'expected_close_date' => 'required|date|after:today',
            'description' => 'nullable|string',
            'products_interested' => 'nullable|array',
            'products_interested.*' => 'exists:products,id',
        ]);

        if (!in_array($lead->status, ['qualified', 'proposal'])) {
            return redirect()->back()->with('error', 'Lead must be qualified before converting to deal.');
        }

        DB::beginTransaction();
        try {
            // Create the deal
            $deal = CrmDeal::create([
                'deal_name' => $validated['deal_name'],
                'lead_id' => $lead->id,
                'deal_value' => $validated['deal_value'],
                'stage' => 'qualification',
                'probability' => 25, // Initial probability for qualification stage
                'expected_close_date' => $validated['expected_close_date'],
                'description' => $validated['description'],
                'products_interested' => $validated['products_interested'] ?? [],
                'assigned_to' => $lead->assigned_to,
            ]);

            // Update lead status to proposal
            $lead->update([
                'status' => 'proposal',
                'estimated_value' => $validated['deal_value'],
                'expected_close_date' => $validated['expected_close_date'],
            ]);

            // Log activity
            $lead->logActivity('note', 'Deal created', "Deal '{$deal->deal_name}' created with value AED " . number_format($deal->deal_value));

            DB::commit();

            return redirect()->route('crm.leads.show', $lead)
                ->with('success', 'Deal created successfully! Lead moved to proposal stage.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error converting lead to deal: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to create deal.');
        }
    }

    /**
     * Create quotation request from lead for supplier pricing
     */
    public function createQuotationRequest(Request $request, CrmLead $lead)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'size' => 'nullable|string',
            'requirements' => 'nullable|string',
            'internal_notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Create quotation request linked to lead
            $quotationRequest = QuotationRequest::create([
                'product_id' => $validated['product_id'],
                'user_id' => 0, // CRM generated, not from customer
                'lead_id' => $lead->id,
                'quantity' => $validated['quantity'],
                'size' => $validated['size'],
                'requirements' => $validated['requirements'],
                'notes' => "CRM Generated from Lead: {$lead->full_name} ({$lead->company_name})",
                'internal_notes' => $validated['internal_notes'],
                'status' => 'pending',
            ]);

            // Update lead activity
            $lead->logActivity('note', 'Quotation request created', "Created quotation request for " . $quotationRequest->product->name);

            DB::commit();

            return redirect()->route('admin.inquiries.show', $quotationRequest)
                ->with('success', 'Quotation request created. Forward to suppliers to get pricing.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error creating quotation request from lead: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to create quotation request.');
        }
    }
} 