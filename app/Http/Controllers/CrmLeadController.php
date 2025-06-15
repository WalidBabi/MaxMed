<?php

namespace App\Http\Controllers;

use App\Models\CrmLead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CrmLeadController extends Controller
{
    public function index(Request $request)
    {
        $query = CrmLead::with(['assignedUser', 'activities']);
        
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
        
        $leads = $query->orderBy('created_at', 'desc')->paginate(15);
        $users = User::all();
        
        return view('crm.leads.index', compact('leads', 'users'));
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
} 