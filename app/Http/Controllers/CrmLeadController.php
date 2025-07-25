<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CrmLead;
use App\Models\User;
use App\Models\CrmDeal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class CrmLeadController extends Controller
{
    /**
     * Display a listing of leads with enhanced pipeline view
     */
    public function index(Request $request)
    {
        // Get filter parameters
        $search = $request->get('search');
        $assignedTo = $request->get('assigned_to');
        $priority = $request->get('priority');
        $source = $request->get('source');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        // Build query with filters
        $query = CrmLead::with(['assignedUser', 'activities'])
            ->when($search, function($q) use ($search) {
                $q->where(function($subQ) use ($search) {
                    $subQ->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('company_name', 'like', "%{$search}%");
                });
            })
            ->when($assignedTo, function($q) use ($assignedTo) {
                $q->where('assigned_to', $assignedTo);
            })
            ->when($priority, function($q) use ($priority) {
                $q->where('priority', $priority);
            })
            ->when($source, function($q) use ($source) {
                $q->where('source', $source);
            })
            ->when($dateFrom, function($q) use ($dateFrom) {
                $q->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($dateTo, function($q) use ($dateTo) {
                $q->whereDate('created_at', '<=', $dateTo);
            });

        $leads = $query->orderBy('created_at', 'desc')->get();

        // Group leads by status for pipeline view
        $pipelineData = $this->getPipelineData($query);

        // Get additional data for filters
        $users = User::select('id', 'name')->orderBy('name')->get();
        $priorities = ['low', 'medium', 'high'];
        $sources = ['website', 'referral', 'social_media', 'email_campaign', 'phone_call', 'trade_show', 'other'];

        // Check if this is an AJAX request for kanban only
        if ($request->get('kanban_only') === '1' && $request->ajax()) {
            return view('crm.leads.partials.pipeline-view', compact('pipelineData'));
        }

        return view('crm.leads.index', compact('pipelineData', 'users', 'priorities', 'sources'));
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
            'mobile' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'company_name' => 'required|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'company_address' => 'nullable|string',
            'source' => 'required|in:website,linkedin,email,phone,whatsapp,on_site_visit,referral,trade_show,google_ads,other',
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
            'mobile' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'company_name' => 'required|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'company_address' => 'nullable|string',
            'status' => 'required|in:new,contacted,qualified,proposal,negotiation,won,lost',
            'source' => 'required|in:website,linkedin,email,phone,whatsapp,on_site_visit,referral,trade_show,google_ads,other',
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
     * Update lead status
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
        
        // Check if this is an AJAX request or form submission
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Lead status updated successfully',
                'lead' => $lead->load(['assignedUser', 'activities'])
            ]);
        } else {
            // For form submissions, redirect back to leads page with success message
            return redirect()->route('crm.leads.index')
                           ->with('success', "Lead status updated to {$validated['status']} successfully!");
        }
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
                'probability' => 25, // Default probability for qualification stage
                'expected_close_date' => $validated['expected_close_date'],
                'description' => $validated['description'],
                'products_interested' => $validated['products_interested'] ?? [],
                'assigned_to' => $lead->assigned_to,
            ]);

            // Update lead status to proposal if not already
            if ($lead->status !== 'proposal') {
                $lead->update(['status' => 'proposal']);
                $lead->logActivity('note', 'Converted to Deal', "Lead converted to deal: {$deal->deal_name}");
            }

            DB::commit();

            return redirect()->route('crm.deals.show', $deal)
                           ->with('success', 'Lead successfully converted to deal!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Lead to deal conversion failed', [
                'lead_id' => $lead->id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Failed to convert lead to deal. Please try again.');
        }
    }

    /**
     * Bulk status update for multiple leads
     */
    public function bulkStatusUpdate(Request $request)
    {
        $validated = $request->validate([
            'lead_ids' => 'required|array|min:1',
            'lead_ids.*' => 'exists:crm_leads,id',
            'status' => 'required|in:new,contacted,qualified,proposal,negotiation,won,lost'
        ]);

        try {
            DB::beginTransaction();

            $updatedCount = 0;
            $errors = [];

            foreach ($validated['lead_ids'] as $leadId) {
                try {
                    $lead = CrmLead::findOrFail($leadId);
                    $oldStatus = $lead->status;
                    
                    $lead->update(['status' => $validated['status']]);
                    
                    // Log the status change
                    $lead->logActivity('note', 'Status changed (bulk)', 
                        "Status changed from {$oldStatus} to {$validated['status']} via bulk action");
                    
                    $updatedCount++;
                } catch (\Exception $e) {
                    $errors[] = "Failed to update lead ID {$leadId}: " . $e->getMessage();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Successfully updated {$updatedCount} lead(s)",
                'updated_count' => $updatedCount,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Bulk status update failed', [
                'lead_ids' => $validated['lead_ids'],
                'status' => $validated['status'],
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Bulk update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk assignment of leads to users
     */
    public function bulkAssign(Request $request)
    {
        $validated = $request->validate([
            'lead_ids' => 'required|array|min:1',
            'lead_ids.*' => 'exists:crm_leads,id',
            'assigned_to' => 'required|exists:users,id'
        ]);

        try {
            DB::beginTransaction();

            $updatedCount = 0;
            $assignedUser = User::findOrFail($validated['assigned_to']);

            foreach ($validated['lead_ids'] as $leadId) {
                $lead = CrmLead::findOrFail($leadId);
                $oldAssignee = $lead->assignedUser->name ?? 'Unassigned';
                
                $lead->update(['assigned_to' => $validated['assigned_to']]);
                
                // Log the assignment change
                $lead->logActivity('note', 'Lead reassigned (bulk)', 
                    "Lead reassigned from {$oldAssignee} to {$assignedUser->name} via bulk action");
                
                $updatedCount++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Successfully assigned {$updatedCount} lead(s) to {$assignedUser->name}",
                'updated_count' => $updatedCount
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Bulk assignment failed', [
                'lead_ids' => $validated['lead_ids'],
                'assigned_to' => $validated['assigned_to'],
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Bulk assignment failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get pipeline statistics for dashboard widgets
     */
    public function getPipelineStats(Request $request)
    {
        try {
            $baseQuery = CrmLead::with(['assignedUser']);
            
            // Apply filters if provided
            if ($request->filled('assigned_to')) {
                $baseQuery->where('assigned_to', $request->assigned_to);
            }
            
            if ($request->filled('date_from')) {
                $baseQuery->where('created_at', '>=', $request->date_from);
            }
            
            if ($request->filled('date_to')) {
                $baseQuery->where('created_at', '<=', $request->date_to);
            }

            $stats = [
                'total_leads' => $baseQuery->count(),
                'active_leads' => $baseQuery->whereNotIn('status', ['won', 'lost'])->count(),
                'won_leads' => $baseQuery->where('status', 'won')->count(),
                'lost_leads' => $baseQuery->where('status', 'lost')->count(),
                'conversion_rate' => 0,
                'total_value' => $baseQuery->sum('estimated_value'),
                'average_value' => $baseQuery->avg('estimated_value'),
                'overdue_leads' => $baseQuery->overdue()->count(),
                'high_priority_leads' => $baseQuery->where('priority', 'high')->count(),
            ];

            // Calculate conversion rate
            $totalCompleted = $stats['won_leads'] + $stats['lost_leads'];
            if ($totalCompleted > 0) {
                $stats['conversion_rate'] = round(($stats['won_leads'] / $totalCompleted) * 100, 1);
            }

            // Get stage-wise breakdown
            $stageBreakdown = $baseQuery->selectRaw('status, COUNT(*) as count, SUM(estimated_value) as total_value')
                ->groupBy('status')
                ->get()
                ->keyBy('status');

            $stats['stage_breakdown'] = $stageBreakdown;

            // Get recent activity summary
            $recentActivity = CrmActivity::with(['lead', 'user'])
                ->whereHas('lead', function($q) use ($baseQuery) {
                    $q->whereIn('id', $baseQuery->pluck('id'));
                })
                ->latest('activity_date')
                ->take(5)
                ->get();

            $stats['recent_activity'] = $recentActivity;

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Pipeline stats retrieval failed', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve pipeline statistics'
            ], 500);
        }
    }

    /**
     * Quick add lead (simplified form for rapid entry)
     */
    public function quickAdd(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:crm_leads',
            'phone' => 'nullable|string|max:20',
            'company' => 'required|string|max:255',
            'source' => 'required|in:website,linkedin,email,phone,whatsapp,on_site_visit,referral,trade_show,google_ads,other',
            'priority' => 'required|in:low,medium,high',
            'estimated_value' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        try {
            // Split name into first and last name
            $nameParts = explode(' ', trim($validated['name']), 2);
            $firstName = $nameParts[0] ?? 'Unknown';
            $lastName = $nameParts[1] ?? 'Contact';

            $lead = CrmLead::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'company_name' => $validated['company'],
                'source' => $validated['source'],
                'priority' => $validated['priority'],
                'estimated_value' => $validated['estimated_value'],
                'notes' => $validated['notes'],
                'status' => 'new',
                'assigned_to' => auth()->id(),
                'last_contacted_at' => null,
                'expected_close_date' => now()->addDays(30), // Default 30-day expectation
            ]);

            // Log initial activity
            $lead->logActivity('note', 'Lead created (quick add)', 
                "Lead created via quick add from {$lead->source}");

            return response()->json([
                'success' => true,
                'message' => 'Lead created successfully!',
                'lead' => $lead->load(['assignedUser', 'activities'])
            ]);

        } catch (\Exception $e) {
            Log::error('Quick add lead failed', [
                'data' => $validated,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create lead: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export leads to CSV
     */
    public function export(Request $request)
    {
        try {
            $query = CrmLead::with(['assignedUser', 'activities']);
            
            // Apply filters similar to index method
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

            $leads = $query->orderBy('created_at', 'desc')->get();

            $filename = 'crm_leads_' . now()->format('Y-m-d_H-i-s') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            $callback = function() use ($leads) {
                $file = fopen('php://output', 'w');
                
                // Add CSV headers
                fputcsv($file, [
                    'ID', 'First Name', 'Last Name', 'Email', 'Phone', 'Mobile',
                    'Company', 'Job Title', 'Status', 'Source', 'Priority',
                    'Estimated Value', 'Assigned To', 'Last Contacted',
                    'Created At', 'Updated At', 'Activities Count'
                ]);

                // Add data rows
                foreach ($leads as $lead) {
                    fputcsv($file, [
                        $lead->id,
                        $lead->first_name,
                        $lead->last_name,
                        $lead->email,
                        $lead->phone,
                        $lead->mobile,
                        $lead->company_name,
                        $lead->job_title,
                        $lead->status,
                        $lead->source,
                        $lead->priority,
                        $lead->estimated_value,
                        $lead->assignedUser->name ?? 'Unassigned',
                        $lead->last_contacted_at ? $lead->last_contacted_at->format('Y-m-d H:i:s') : '',
                        $lead->created_at->format('Y-m-d H:i:s'),
                        $lead->updated_at->format('Y-m-d H:i:s'),
                        $lead->activities->count()
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            Log::error('Lead export failed', [
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }
} 