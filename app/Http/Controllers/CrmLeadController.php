<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CrmLead;
use App\Models\User;
use App\Models\CrmDeal;
use App\Notifications\LeadCreatedNotification;
use App\Notifications\LeadReassignedNotification;
use App\Mail\LeadAssignmentMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;

class CrmLeadController extends Controller
{
    /**
     * Get valid status options with labels for validation
     */
    private function getValidStatuses()
    {
        return [
            'new_inquiry' => 'New Inquiry',
            'quote_requested' => 'Quote Requested',
            'getting_price' => 'Getting Price',
            'price_submitted' => 'Price Submitted',
            'quote_sent' => 'Quote Sent',
            'follow_up_1' => 'Follow-up 1',
            'follow_up_2' => 'Follow-up 2',
            'follow_up_3' => 'Follow-up 3',
            'negotiating_price' => 'Negotiating Price',
            'payment_pending' => 'Payment Pending',
            'order_confirmed' => 'Order Confirmed',
            'deal_lost' => 'Deal Lost',
            'on_hold' => 'On Hold',
            'cancelled' => 'Cancelled',
            'pending_approval' => 'Pending Approval',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'archived' => 'Archived'
        ];
    }

    /**
     * Get status validation rule with custom error message
     */
    private function getStatusValidationRule()
    {
        $validStatuses = $this->getValidStatuses();
        
        return [
            'required',
            function ($attribute, $value, $fail) use ($validStatuses) {
                if (!array_key_exists($value, $validStatuses)) {
                    $statusOptions = array_map(function($key, $label) {
                        return "• {$label}";
                    }, array_keys($validStatuses), $validStatuses);
                    
                    $fail("❌ **Invalid Status**: The status '{$value}' is not available in the system. Please choose from one of these valid statuses:\n\n" . implode("\n", $statusOptions) . "\n\n💡 **Tip**: Make sure you're using the correct status from the dropdown menu. If you believe this is an error, please contact your administrator.");
                }
            }
        ];
    }
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:crm.leads.view')->only(['index', 'show']);
        $this->middleware('permission:crm.leads.edit')->only(['create', 'store', 'edit', 'update']);
        $this->middleware('permission:crm.leads.delete')->only(['destroy']);
        
        // Allow purchasing specialists to view pipeline but restrict detailed access
        $this->middleware(function ($request, $next) {
            if (Auth::check() && !Auth::user()->isAdmin() && $this->hasPurchasingPermissions(Auth::user())) {
                // Allow index (pipeline view) and show (with filtered data) but block other actions
                if (in_array($request->route()->getActionMethod(), ['create', 'store', 'edit', 'update', 'destroy'])) {
                    abort(403, 'Access Denied: Users with purchasing permissions can only view the sales pipeline overview and basic lead information. Detailed lead editing is restricted.');
                }
            }
            return $next($request);
        })->only(['create', 'store', 'edit', 'update', 'destroy']);
    }
    
    /**
     * Check if user has any purchasing permissions
     */
    private function hasPurchasingPermissions($user)
    {
        $purchasingPermissions = [
            'purchase_orders.view',
            'purchase_orders.create',
            'purchase_orders.edit',
            'purchase_orders.delete',
            'purchase_orders.approve',
            'purchase_orders.send',
            'purchase_orders.manage_status',
            'purchase_orders.view_financials'
        ];
        
        foreach ($purchasingPermissions as $permission) {
            if ($user->hasPermission($permission)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Filter lead data for purchasing users (hide personal information)
     */
    private function filterLeadForPurchasingUser($lead)
    {
        // Create a filtered version of the lead for purchasing users
        $filteredLead = new class($lead) extends \stdClass {
            private $originalLead;
            
            public function __construct($originalLead) {
                $this->originalLead = $originalLead;
                
                // Set all the properties that purchasing users can see
                $this->id = $originalLead->id;
                $this->company_name = $originalLead->company_name;
                $this->status = $originalLead->status;
                $this->priority = $originalLead->priority;
                $this->estimated_value = $originalLead->estimated_value;
                $this->created_at = $originalLead->created_at;
                $this->updated_at = $originalLead->updated_at;
                $this->assignedUser = $originalLead->assignedUser ? (object) [
                    'id' => $originalLead->assignedUser->id,
                    'name' => $originalLead->assignedUser->name
                ] : null;
                $this->notes = $originalLead->notes; // Allow viewing notes for purchasing context
                $this->source = $originalLead->source;
                $this->expected_close_date = $originalLead->expected_close_date;
                $this->last_contacted_at = $originalLead->last_contacted_at;
                $this->attachments = $originalLead->attachments;
                
                // Hide personal information
                $this->first_name = '***';
                $this->last_name = '***';
                $this->full_name = '***';
                $this->email = '***@***.***';
                $this->phone = '***';
                $this->mobile = '***';
                $this->job_title = '***';
                $this->company_address = '***';
                
                // Add computed properties
                $this->created_ago = $originalLead->created_ago;
                $this->status_color = $originalLead->status_color;
                $this->priority_color = $originalLead->priority_color;
                
                // Filter activities to remove personal information
                $this->activities = $originalLead->activities->map(function($activity) {
                    return (object) [
                        'id' => $activity->id,
                        'type' => $activity->type,
                        'subject' => $activity->subject,
                        'description' => $activity->description,
                        'activity_date' => $activity->activity_date,
                        'status' => $activity->status,
                        'due_date' => $activity->due_date,
                        'created_at' => $activity->created_at,
                        'updated_at' => $activity->updated_at,
                        'user' => $activity->user ? (object) ['name' => $activity->user->name] : null,
                    ];
                });
                
                // Filter deals to remove personal information
                $this->deals = $originalLead->deals->map(function($deal) {
                    return (object) [
                        'id' => $deal->id,
                        'deal_name' => $deal->deal_name,
                        'deal_value' => $deal->deal_value,
                        'stage' => $deal->stage,
                        'probability' => $deal->probability,
                        'expected_close_date' => $deal->expected_close_date,
                        'description' => $deal->description,
                        'created_at' => $deal->created_at,
                        'updated_at' => $deal->updated_at,
                        'assignedUser' => $deal->assignedUser ? (object) [
                            'id' => $deal->assignedUser->id,
                            'name' => $deal->assignedUser->name
                        ] : null,
                    ];
                });
            }
            
            public function isOverdue() {
                return $this->originalLead->isOverdue();
            }
            
            public function daysSinceLastContact() {
                return $this->originalLead->daysSinceLastContact();
            }
            
            public function hasAttachments() {
                return $this->originalLead->hasAttachments();
            }
            
            public function getAttachmentCount() {
                return $this->originalLead->getAttachmentCount();
            }
            
            public function getAttachmentsByType() {
                return $this->originalLead->getAttachmentsByType();
            }
            
            // Add price submissions methods
            public function hasPriceSubmissions() {
                return $this->originalLead->hasPriceSubmissions();
            }
            
            public function priceSubmissions() {
                return $this->originalLead->priceSubmissions();
            }
            
            public function latestPriceSubmission() {
                return $this->originalLead->latestPriceSubmission();
            }
            
            // Delegate other methods to original lead
            public function __call($method, $args) {
                if (method_exists($this->originalLead, $method)) {
                    return call_user_func_array([$this->originalLead, $method], $args);
                }
                throw new \BadMethodCallException("Method {$method} not found");
            }
        };
        
        return $filteredLead;
    }

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

        // Check if user is purchasing specialist
        // Superadmins should always see customer info regardless of purchasing permissions
        $user = Auth::user();
        $isAdmin = $user->isAdmin();
        $isSuperAdmin = $user->hasRole('super_admin') || $user->hasRole('superadmin') || $user->hasRole('admin') || $user->hasRole('super-administrator');
        $hasPurchasing = $this->hasPurchasingPermissions($user);
        
        // Log for debugging in production
        \Log::info('CRM Lead Index Access Check', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'role_name' => $user->role ? $user->role->name : 'no_role',
            'isAdmin' => $isAdmin,
            'isSuperAdmin' => $isSuperAdmin,
            'hasPurchasing' => $hasPurchasing
        ]);
        
        $isPurchasingUser = Auth::check() && !$isAdmin && !$isSuperAdmin && $hasPurchasing;

        // Build query with filters
        $query = CrmLead::with(['assignedUser', 'activities', 'priceSubmissions'])
            ->when($search, function($q) use ($search, $isPurchasingUser) {
                if ($isPurchasingUser) {
                    // For purchasing users, only allow searching by company name
                    $q->where('company_name', 'like', "%{$search}%");
                } else {
                    $q->where(function($subQ) use ($search) {
                        $subQ->where('first_name', 'like', "%{$search}%")
                             ->orWhere('last_name', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%")
                             ->orWhere('company_name', 'like', "%{$search}%");
                    });
                }
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
        $pipelineData = $this->getPipelineData($query, $isPurchasingUser);

        // Get additional data for filters
        $users = User::select('id', 'name')->orderBy('name')->get();
        $priorities = ['low', 'medium', 'high'];
        $sources = ['website', 'referral', 'social_media', 'email_campaign', 'phone_call', 'trade_show', 'other'];

        // Check if this is an AJAX request for kanban only
        if ($request->get('kanban_only') === '1' && $request->ajax()) {
            return view('crm.leads.partials.pipeline-view', compact('pipelineData', 'isPurchasingUser'));
        }

        return view('crm.leads.index', compact('pipelineData', 'users', 'priorities', 'sources', 'isPurchasingUser'));
    }
    
    /**
     * Get leads organized by pipeline stages
     */
    private function getPipelineData($baseQuery, $isPurchasingUser = false)
    {
        $stages = [
            'new_inquiry' => ['title' => '📩 New Inquiry', 'color' => 'blue'],
            'quote_requested' => ['title' => '💰 Quote Requested', 'color' => 'slate'],
            'getting_price' => ['title' => '🔍 Getting Price', 'color' => 'indigo'],
            'price_submitted' => ['title' => '📋 Price Submitted', 'color' => 'teal'],
            'follow_up_1' => ['title' => '⏰ Follow-up 1', 'color' => 'sky'],
            'follow_up_2' => ['title' => '🔔 Follow-up 2', 'color' => 'blue'],
            'follow_up_3' => ['title' => '🚨 Follow-up 3', 'color' => 'indigo'],
            'negotiating_price' => ['title' => '🤝 Price Negotiation', 'color' => 'orange'],
            'payment_pending' => ['title' => '💳 Payment Pending', 'color' => 'emerald'],
            'order_confirmed' => ['title' => '✅ Order Confirmed', 'color' => 'green'],
            'deal_lost' => ['title' => '❌ Deal Lost', 'color' => 'gray']
        ];
        
        $pipelineData = [];
        
        foreach ($stages as $status => $config) {
            $stageQuery = clone $baseQuery;
            $leads = $stageQuery->where('status', $status)->orderBy('created_at', 'desc')->get();
            
            // Filter lead information for purchasing users
            if ($isPurchasingUser) {
                $leads = $leads->map(function($lead) {
                    // Create a class that extends stdClass to allow method calls
                    $filteredLead = new class($lead) extends \stdClass {
                        private $originalLead;
                        
                        public function __construct($originalLead) {
                            $this->originalLead = $originalLead;
                            
                            // Set all the properties
                            $this->id = $originalLead->id;
                            $this->company_name = $originalLead->company_name;
                            $this->status = $originalLead->status;
                            $this->priority = $originalLead->priority;
                            $this->estimated_value = $originalLead->estimated_value;
                            $this->created_at = $originalLead->created_at;
                            $this->updated_at = $originalLead->updated_at;
                            $this->assignedUser = $originalLead->assignedUser ? (object) [
                                'id' => $originalLead->assignedUser->id,
                                'name' => $originalLead->assignedUser->name
                            ] : null;
                            $this->notes = $originalLead->notes;
                            $this->source = $originalLead->source;
                            $this->expected_close_date = $originalLead->expected_close_date;
                            $this->last_contacted_at = $originalLead->last_contacted_at;
                            
                            // Hide personal information but provide full_name for view compatibility
                            $this->first_name = '***';
                            $this->last_name = '***';
                            $this->full_name = '***';
                            $this->email = '***@***.***';
                            $this->phone = '***';
                            $this->mobile = '***';
                            $this->job_title = '***';
                            $this->company_address = '***';
                            
                            // Add computed properties
                            $this->created_ago = $originalLead->created_ago;
                        }
                        
                        public function isOverdue() {
                            return $this->originalLead->isOverdue();
                        }
                        
                        public function daysSinceLastContact() {
                            return $this->originalLead->daysSinceLastContact();
                        }
                        
                        public function hasPriceSubmissions() {
                            return $this->originalLead->hasPriceSubmissions();
                        }
                    };
                    
                    return $filteredLead;
                });
            }
            
            $pipelineData[$status] = [
                'title' => $config['title'],
                'color' => $config['color'],
                'leads' => $leads,
                'count' => $leads->count(),
                'total_value' => $leads->sum('estimated_value'),
                'high_priority_count' => $leads->where('priority', 'high')->count(),
                'overdue_count' => $leads->filter(function($lead) { 
                    return isset($lead->expected_close_date) && $lead->expected_close_date < now(); 
                })->count()
            ];
        }
        
        return $pipelineData;
    }
    
    public function create()
    {
        // Get users who have CRM permissions and can be assigned leads
        $users = User::whereHas('role.permissions', function($query) {
            $query->whereIn('name', [
                'crm.leads.view',
                'crm.leads.create',
                'crm.leads.edit', 
                'crm.access'
            ]);
        })->get();
        
        return view('crm.leads.create', compact('users'));
    }
    
    public function store(Request $request)
    {
        \Log::info('CRM Lead creation started', ['request_data' => $request->except(['_token', 'attachments'])]);
        
        try {
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email',
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
                'attachments.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,gif,webp,xls,xlsx,csv|max:10240', // Max 10MB per file
            ]);
        
        // Remove attachments from validated data for model creation
        $attachmentFiles = $request->file('attachments', []);
        unset($validated['attachments']);
        
        $lead = CrmLead::create($validated);
        
        // Handle file uploads
        if (!empty($attachmentFiles)) {
            $attachments = [];
            foreach ($attachmentFiles as $file) {
                if ($file && $file->isValid()) {
                    $originalName = $file->getClientOriginalName();
                    $fileName = time() . '_' . uniqid() . '_' . $originalName;
                    $filePath = $file->storeAs('lead_attachments', $fileName, 'public');
                    
                    $attachments[] = [
                        'path' => $filePath,
                        'original_name' => $originalName,
                        'size' => $file->getSize(),
                        'uploaded_at' => now()->toISOString(),
                    ];
                }
            }
            
            if (!empty($attachments)) {
                $lead->attachments = $attachments;
                $lead->save();
                
                $lead->logActivity('note', 'Attachments uploaded', count($attachments) . ' file(s) uploaded during lead creation');
            }
        }
        
        // Log initial activity
        $lead->logActivity('note', 'Lead created', "Lead created from {$lead->source}");

        // Also create/update customer record from this lead, but do not block lead creation
        try {
            $customer = $lead->createCustomer();
            $lead->logActivity('note', 'Customer created/linked', "Customer '{$customer->name}' (ID: {$customer->id}) linked to this lead");
        } catch (\Exception $e) {
            \Log::error("Customer creation from lead {$lead->id} failed: " . $e->getMessage());
            // Non-fatal: keep going even if customer creation fails
        }

        // Send database notification to assigned user
        try {
            $assignedUser = User::find($lead->assigned_to);
            if ($assignedUser) {
                // Send database notification for dashboard
                $assignedUser->notify(new LeadCreatedNotification($lead));
                
                \Log::info('Lead assignment database notification sent', [
                    'lead_id' => $lead->id,
                    'assigned_to' => $assignedUser->id,
                    'assigned_to_email' => $assignedUser->email
                ]);
                
                $lead->logActivity('note', 'Lead assigned', "Lead assigned to {$assignedUser->name}. Use 'Send Email' button to notify them.");
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send lead assignment notification', [
                'lead_id' => $lead->id,
                'assigned_to' => $lead->assigned_to,
                'error' => $e->getMessage()
            ]);
            // Non-fatal: continue even if notification fails
        }
            
            \Log::info('CRM Lead created successfully', ['lead_id' => $lead->id, 'lead_name' => $lead->full_name]);
            
            return redirect()->route('crm.leads.show', $lead)
                            ->with('success', 'Lead created successfully!');
                            
        } catch (\Exception $e) {
            \Log::error('CRM Lead creation failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            
            return redirect()->back()
                           ->withInput()
                           ->withErrors(['error' => 'Failed to create lead: ' . $e->getMessage()]);
        }
    }
    
    public function show(CrmLead $lead)
    {
        $lead->load(['assignedUser', 'activities.user', 'deals.assignedUser', 'priceSubmissions.user']);
        
        // Check if user is purchasing specialist
        // Superadmins should always see customer info regardless of purchasing permissions
        $user = Auth::user();
        $isAdmin = $user->isAdmin();
        $isSuperAdmin = $user->hasRole('super_admin') || $user->hasRole('superadmin') || $user->hasRole('admin') || $user->hasRole('super-administrator');
        $hasPurchasing = $this->hasPurchasingPermissions($user);
        
        // Log for debugging in production
        \Log::info('CRM Lead Show Access Check', [
            'lead_id' => $lead->id,
            'user_id' => $user->id,
            'user_email' => $user->email,
            'role_name' => $user->role ? $user->role->name : 'no_role',
            'isAdmin' => $isAdmin,
            'isSuperAdmin' => $isSuperAdmin,
            'hasPurchasing' => $hasPurchasing
        ]);
        
        $isPurchasingUser = Auth::check() && !$isAdmin && !$isSuperAdmin && $hasPurchasing;
        
        // Filter lead data for purchasing users
        if ($isPurchasingUser) {
            $lead = $this->filterLeadForPurchasingUser($lead);
        }
        
        // When requested via AJAX, return the modal-friendly partial
        if (request()->ajax() || request()->wantsJson()) {
            return view('crm.leads.partials.show-content', compact('lead', 'isPurchasingUser'));
        }
        
        // For non-AJAX requests, show the full lead details page
        return view('crm.leads.show', compact('lead', 'isPurchasingUser'));
    }
    
    public function edit(CrmLead $lead)
    {
        // Check if user has permission to edit leads
        if (!Auth::user()->isAdmin() && !Auth::user()->hasPermission('crm.leads.edit')) {
            abort(403, 'You do not have permission to edit leads.');
        }
        
        // Check if user is assigned to this lead or is admin
        if ($lead->assigned_to !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'You can only edit leads assigned to you.');
        }
        
        // Get users who have CRM permissions and can be assigned leads
        $users = User::whereHas('role.permissions', function($query) {
            $query->whereIn('name', [
                'crm.leads.view',
                'crm.leads.create',
                'crm.leads.edit', 
                'crm.access'
            ]);
        })->get();
        
        return view('crm.leads.edit', compact('lead', 'users'));
    }
    
    public function update(Request $request, CrmLead $lead)
    {
        // Check if user has permission to edit leads
        if (!Auth::user()->isAdmin() && !Auth::user()->hasPermission('crm.leads.edit')) {
            abort(403, 'Access Denied: You do not have permission to edit leads. Please contact your administrator to request CRM lead editing permissions.');
        }
        
        // Check if user is assigned to this lead or is admin
        if ($lead->assigned_to !== Auth::id() && !Auth::user()->isAdmin()) {
            $assignedUser = $lead->assignedUser ? $lead->assignedUser->name : 'Unassigned';
            abort(403, "Access Denied: This lead '{$lead->full_name}' is assigned to '{$assignedUser}'. You can only edit leads assigned to you. Please ask your manager to assign this lead to you, or work with leads that are already assigned to you.");
        }
        
        \Log::info('CRM Lead update started', [
            'lead_id' => $lead->id,
            'request_data' => $request->except(['_token', '_method', 'attachments']),
            'remove_attachments' => $request->input('remove_attachments', [])
        ]);
        
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            // Allow duplicate emails between leads
            'email' => 'required|email',
            'mobile' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'company_name' => 'required|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'company_address' => 'nullable|string',
            'status' => $this->getStatusValidationRule(),
            'source' => 'required|in:website,linkedin,email,phone,whatsapp,on_site_visit,referral,trade_show,google_ads,other',
            'priority' => 'required|in:low,medium,high',
            'estimated_value' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'expected_close_date' => 'nullable|date',
            'assigned_to' => 'required|exists:users,id',
            'attachments.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,gif,webp,xls,xlsx,csv|max:10240', // Max 10MB per file
            'remove_attachments' => 'nullable|array',
            'remove_attachments.*' => 'numeric',
        ]);
        
        $oldStatus = $lead->status;
        $oldAssignedTo = $lead->assigned_to;
        
        // Handle attachment removal
        $removeAttachments = $request->input('remove_attachments', []);
        $existingAttachments = $lead->attachments ?: [];
        
        \Log::info('Processing attachment removal', [
            'lead_id' => $lead->id,
            'remove_attachments' => $removeAttachments,
            'existing_attachments_count' => count($existingAttachments)
        ]);
        
        if (!empty($removeAttachments)) {
            $filesToDelete = [];
            foreach ($removeAttachments as $index) {
                if (isset($existingAttachments[$index])) {
                    $filesToDelete[] = $existingAttachments[$index]['path'];
                    unset($existingAttachments[$index]);
                }
            }
            
            // Delete files from storage
            foreach ($filesToDelete as $filePath) {
                \Storage::disk('public')->delete($filePath);
            }
            
            // Reindex array to maintain proper indexing
            $existingAttachments = array_values($existingAttachments);
            
            if (!empty($removeAttachments)) {
                \Log::info('Attachments removed successfully', [
                    'lead_id' => $lead->id,
                    'removed_files' => $filesToDelete,
                    'remaining_attachments' => count($existingAttachments)
                ]);
                $lead->logActivity('note', 'Attachments removed', count($removeAttachments) . ' file(s) removed');
            }
        }
        
        // Handle new file uploads
        $attachmentFiles = $request->file('attachments', []);
        unset($validated['attachments'], $validated['remove_attachments']);
        
        $lead->update($validated);
        
        if (!empty($attachmentFiles)) {
            $newAttachments = [];
            
            foreach ($attachmentFiles as $file) {
                if ($file && $file->isValid()) {
                    $originalName = $file->getClientOriginalName();
                    $fileName = time() . '_' . uniqid() . '_' . $originalName;
                    $filePath = $file->storeAs('lead_attachments', $fileName, 'public');
                    
                    $newAttachments[] = [
                        'path' => $filePath,
                        'original_name' => $originalName,
                        'size' => $file->getSize(),
                        'uploaded_at' => now()->toISOString(),
                    ];
                }
            }
            
            if (!empty($newAttachments)) {
                $existingAttachments = array_merge($existingAttachments, $newAttachments);
                $lead->logActivity('note', 'Attachments added', count($newAttachments) . ' new file(s) uploaded');
            }
        }
        
        // Update attachments
        $lead->attachments = $existingAttachments;
        $lead->save();
        
        // Log status change if it changed
        if ($oldStatus !== $validated['status']) {
            $lead->logActivity('note', 'Status changed', "Status changed from {$oldStatus} to {$validated['status']}");
        }

        // Handle assignment change notification (database notification only)
        if ($oldAssignedTo != $validated['assigned_to']) {
            try {
                $previousAssignee = $oldAssignedTo ? User::find($oldAssignedTo) : null;
                $newAssignee = User::find($validated['assigned_to']);
                $reassignedBy = Auth::user();

                if ($newAssignee) {
                    // Send database notification for dashboard only
                    $newAssignee->notify(new LeadReassignedNotification($lead, $previousAssignee, $newAssignee, $reassignedBy));
                    
                    \Log::info('Lead reassignment database notification sent', [
                        'lead_id' => $lead->id,
                        'previous_assignee' => $previousAssignee?->name ?? 'Unassigned',
                        'new_assignee' => $newAssignee->name,
                        'reassigned_by' => $reassignedBy->name
                    ]);

                    // Log the assignment change
                    $previousName = $previousAssignee?->name ?? 'Unassigned';
                    $lead->logActivity('note', 'Lead reassigned', "Lead reassigned from {$previousName} to {$newAssignee->name} by {$reassignedBy->name}");
                    $lead->logActivity('note', 'Assignment changed', "Use 'Send Email' button to notify {$newAssignee->name} about this assignment");
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send lead reassignment notification', [
                    'lead_id' => $lead->id,
                    'old_assigned_to' => $oldAssignedTo,
                    'new_assigned_to' => $validated['assigned_to'],
                    'error' => $e->getMessage()
                ]);
                // Non-fatal: continue even if notification fails
            }
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
        if ($lead->status === 'order_confirmed') {
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
        // Get the requested status
        $requestedStatus = $request->input('status');
        
        // Define purchasing-related statuses
        $purchasingStatuses = ['getting_price', 'price_submitted', 'quote_sent', 'negotiating_price', 'payment_pending', 'order_confirmed'];
        $isPurchasingStatus = in_array($requestedStatus, $purchasingStatuses);
        
        // Check if user has permission to edit leads
        if (!Auth::user()->isAdmin() && !Auth::user()->hasPermission('crm.leads.edit')) {
            $message = "🚫 **Permission Required**: You don't have permission to update lead statuses. To update lead statuses, you need the 'CRM Lead Editing' permission. Please contact your administrator to request this permission.";
            
            // Log request details for debugging
            \Log::info('Permission check failed', [
                'user_id' => Auth::id(),
                'expects_json' => $request->expectsJson(),
                'is_ajax' => $request->ajax(),
                'headers' => $request->headers->all(),
                'content_type' => $request->header('Content-Type'),
                'accept' => $request->header('Accept')
            ]);
            
            // Always return JSON for PATCH requests (which are used for status updates)
            if ($request->expectsJson() || $request->ajax() || $request->header('Accept') === 'application/json' || str_contains($request->header('Accept', ''), 'application/json') || $request->isMethod('PATCH')) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'error_type' => 'permission_denied',
                    'permission_required' => 'crm.leads.edit',
                    'permission_name' => 'CRM Lead Editing',
                    'dismissible' => true,
                    'notification_type' => 'error'
                ], 403);
            }
            abort(403, $message);
        }
        
        // Check if user has purchasing permissions for purchasing-related statuses
        if ($isPurchasingStatus && !Auth::user()->isAdmin() && !Auth::user()->hasPermission('purchase_orders.view')) {
            $statusLabel = $this->getValidStatuses()[$requestedStatus] ?? $requestedStatus;
            $message = "🔒 **Purchasing Permission Required**: You don't have permission to change lead status to '{$statusLabel}'. This status requires 'Purchase Orders View' permission. Please contact your administrator to request purchasing permissions or ask a purchasing team member to update this status.";
            
            // Always return JSON for PATCH requests (which are used for status updates)
            if ($request->expectsJson() || $request->ajax() || $request->header('Accept') === 'application/json' || str_contains($request->header('Accept', ''), 'application/json') || $request->isMethod('PATCH')) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'error_type' => 'purchasing_permission_denied',
                    'permission_required' => 'purchase_orders.view',
                    'permission_name' => 'Purchase Orders View',
                    'requested_status' => $requestedStatus,
                    'status_label' => $statusLabel,
                    'dismissible' => true,
                    'notification_type' => 'warning'
                ], 403);
            }
            abort(403, $message);
        }
        
        // Check if user is assigned to this lead or is admin
        if ($lead->assigned_to !== Auth::id() && !Auth::user()->isAdmin()) {
            $assignedUser = $lead->assignedUser ? $lead->assignedUser->name : 'Unassigned';
            
            // User-friendly error message
            $message = "👤 **Access Restricted**: You can only update leads assigned to you. This lead '{$lead->full_name}' is currently assigned to '{$assignedUser}'. Please ask your manager to assign this lead to you, or work with leads that are already assigned to you.";
            
            // Always return JSON for PATCH requests (which are used for status updates)
            if ($request->expectsJson() || $request->ajax() || $request->header('Accept') === 'application/json' || str_contains($request->header('Accept', ''), 'application/json') || $request->isMethod('PATCH')) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'error_type' => 'access_denied',
                    'assigned_to' => $assignedUser,
                    'lead_name' => $lead->full_name,
                    'requested_status' => $requestedStatus,
                    'is_purchasing_status' => $isPurchasingStatus,
                    'dismissible' => true,
                    'notification_type' => 'info'
                ], 403);
            }
            abort(403, $message);
        }
        
        \Log::info('Quick status update called', [
            'lead_id' => $lead->id,
            'current_status' => $lead->status,
            'request_data' => $request->all(),
            'url' => $request->url(),
            'method' => $request->method()
        ]);
        
        $validated = $request->validate([
            'status' => $this->getStatusValidationRule()
        ]);
        
        $oldStatus = $lead->status;
        $lead->update(['status' => $validated['status']]);
        
        // Log status change
        $lead->logActivity('note', 'Status changed', "Status changed from {$oldStatus} to {$validated['status']}");
        
        \Log::info('Quick status update completed', [
            'lead_id' => $lead->id,
            'old_status' => $oldStatus,
            'new_status' => $validated['status'],
            'success' => true
        ]);
        
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
     * View lead requirements for purchasing (without personal information)
     */
    public function viewRequirements(CrmLead $lead)
    {
        // Check if user has permission to view lead requirements
        if (!Auth::user()->hasPermission('crm.leads.view_requirements')) {
            abort(403, 'You do not have permission to view lead requirements.');
        }
        
        // For purchasing users, only show requirements and basic info (no personal details)
        // Superadmins should always see customer info regardless of purchasing permissions
        $user = Auth::user();
        $isAdmin = $user->isAdmin();
        $isSuperAdmin = $user->hasRole('super_admin') || $user->hasRole('superadmin') || $user->hasRole('admin') || $user->hasRole('super-administrator');
        $hasPurchasing = $this->hasPurchasingPermissions($user);
        
        // Log for debugging in production
        \Log::info('CRM Lead Requirements Access Check', [
            'lead_id' => $lead->id,
            'user_id' => $user->id,
            'user_email' => $user->email,
            'role_name' => $user->role ? $user->role->name : 'no_role',
            'isAdmin' => $isAdmin,
            'isSuperAdmin' => $isSuperAdmin,
            'hasPurchasing' => $hasPurchasing
        ]);
        
        $isPurchasingUser = Auth::check() && !$isAdmin && !$isSuperAdmin && $hasPurchasing;
        
        $response = [
            'success' => true,
            'lead_id' => $lead->id,
            'requirements' => $lead->notes,
            'estimated_value' => $lead->estimated_value,
            'status' => $lead->status,
            'created_at' => $lead->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $lead->updated_at->format('Y-m-d H:i:s')
        ];
        
        // Add company name for purchasing users (they can see this in the pipeline)
        if ($isPurchasingUser) {
            $response['company_name'] = $lead->company_name;
        }
        
        return response()->json($response);
    }

    /**
     * Bulk status update for multiple leads
     */
    public function bulkStatusUpdate(Request $request)
    {
        // Get the requested status
        $requestedStatus = $request->input('status');
        
        // Define purchasing-related statuses
        $purchasingStatuses = ['getting_price', 'price_submitted', 'quote_sent', 'negotiating_price', 'payment_pending', 'order_confirmed'];
        $isPurchasingStatus = in_array($requestedStatus, $purchasingStatuses);
        
        // Check if user has permission to edit leads
        if (!Auth::user()->isAdmin() && !Auth::user()->hasPermission('crm.leads.edit')) {
            $message = "🚫 **Permission Required**: You don't have permission to update lead statuses. To update lead statuses, you need the 'CRM Lead Editing' permission. Please contact your administrator to request this permission.";
                
            // Always return JSON for PATCH requests (which are used for status updates)
            if ($request->expectsJson() || $request->ajax() || $request->header('Accept') === 'application/json' || str_contains($request->header('Accept', ''), 'application/json') || $request->isMethod('PATCH')) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'error_type' => 'permission_denied',
                    'permission_required' => 'crm.leads.edit',
                    'permission_name' => 'CRM Lead Editing',
                    'dismissible' => true,
                    'notification_type' => 'error'
                ], 403);
            }
            abort(403, $message);
        }
        
        // Check if user has purchasing permissions for purchasing-related statuses
        if ($isPurchasingStatus && !Auth::user()->isAdmin() && !Auth::user()->hasPermission('purchase_orders.view')) {
            $statusLabel = $this->getValidStatuses()[$requestedStatus] ?? $requestedStatus;
            $message = "🔒 **Purchasing Permission Required**: You don't have permission to change lead status to '{$statusLabel}'. This status requires 'Purchase Orders View' permission. Please contact your administrator to request purchasing permissions or ask a purchasing team member to update this status.";
            
            // Always return JSON for PATCH requests (which are used for status updates)
            if ($request->expectsJson() || $request->ajax() || $request->header('Accept') === 'application/json' || str_contains($request->header('Accept', ''), 'application/json') || $request->isMethod('PATCH')) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'error_type' => 'purchasing_permission_denied',
                    'permission_required' => 'purchase_orders.view',
                    'permission_name' => 'Purchase Orders View',
                    'requested_status' => $requestedStatus,
                    'status_label' => $statusLabel,
                    'dismissible' => true,
                    'notification_type' => 'warning'
                ], 403);
            }
            abort(403, $message);
        }
        
        $validated = $request->validate([
            'lead_ids' => 'required|array|min:1',
            'lead_ids.*' => 'exists:crm_leads,id',
            'status' => $this->getStatusValidationRule()
        ]);
        
        // Check if user is assigned to all leads or is admin
        if (!Auth::user()->isAdmin()) {
            $leads = CrmLead::whereIn('id', $validated['lead_ids'])->get();
            $unauthorizedLeads = $leads->where('assigned_to', '!=', Auth::id());
            
            if ($unauthorizedLeads->count() > 0) {
                $unauthorizedLeadNames = $unauthorizedLeads->pluck('full_name')->implode(', ');
                
                // Different messages based on the status being changed
                if ($isPurchasingStatus) {
                    $message = "Access Denied: You can only change purchasing-related statuses for leads assigned to you. The following leads are not assigned to you: {$unauthorizedLeadNames}. Please ask your manager to assign these leads to you, or work with leads that are already assigned to you.";
                } else {
                    $message = "Access Denied: You can only change the status of leads assigned to you. The following leads are not assigned to you: {$unauthorizedLeadNames}. Please ask your manager to assign these leads to you, or work with leads that are already assigned to you.";
                }
                
                // Always return JSON for PATCH requests (which are used for status updates)
            if ($request->expectsJson() || $request->ajax() || $request->header('Accept') === 'application/json' || str_contains($request->header('Accept', ''), 'application/json') || $request->isMethod('PATCH')) {
                    return response()->json([
                        'success' => false,
                        'message' => $message,
                        'error_type' => 'access_denied',
                        'unauthorized_leads' => $unauthorizedLeadNames,
                        'requested_status' => $requestedStatus,
                        'is_purchasing_status' => $isPurchasingStatus
                    ], 403);
                }
                abort(403, $message);
            }
        }

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
                $previousAssignee = $lead->assignedUser;
                $oldAssignee = $previousAssignee?->name ?? 'Unassigned';
                
                $lead->update(['assigned_to' => $validated['assigned_to']]);
                
                // Send database notification to the new assignee
                try {
                    $reassignedBy = Auth::user();
                    
                    // Send database notification for dashboard
                    $assignedUser->notify(new LeadReassignedNotification($lead, $previousAssignee, $assignedUser, $reassignedBy));
                    
                    \Log::info('Bulk reassignment database notification sent', [
                        'lead_id' => $lead->id,
                        'previous_assignee' => $oldAssignee,
                        'new_assignee' => $assignedUser->name,
                        'reassigned_by' => $reassignedBy->name
                    ]);
                    
                    // Log the assignment change
                    $lead->logActivity('note', 'Lead reassigned (bulk)', 
                        "Lead reassigned from {$oldAssignee} to {$assignedUser->name} via bulk action by {$reassignedBy->name}");
                    $lead->logActivity('note', 'Bulk assignment completed', "Use 'Send Email' button to notify {$assignedUser->name} about this assignment");
                } catch (\Exception $e) {
                    \Log::error('Failed to send bulk reassignment notification', [
                        'lead_id' => $lead->id,
                        'new_assignee' => $assignedUser->name,
                        'error' => $e->getMessage()
                    ]);
                    
                    // Still log the assignment change even if notification fails
                    $lead->logActivity('note', 'Lead reassigned (bulk)', 
                        "Lead reassigned from {$oldAssignee} to {$assignedUser->name} via bulk action");
                }
                
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
 * Send lead assignment email
 */
public function sendEmail(Request $request, CrmLead $lead)
{
    $request->validate([
        'assigned_user_email' => 'required|email',
        'cc_emails' => 'nullable|string',
        'message' => 'nullable|string'
    ]);

    try {
        // Parse CC emails
        $ccEmails = [];
        if ($request->filled('cc_emails')) {
            $ccEmails = array_filter(
                array_map('trim', explode(',', $request->cc_emails)),
                function($email) {
                    return filter_var($email, FILTER_VALIDATE_EMAIL);
                }
            );
        }

        // Find the assigned user
        $assignedUser = User::where('email', $request->assigned_user_email)->first();
        if (!$assignedUser) {
            return response()->json([
                'success' => false,
                'message' => 'Assigned user not found with the provided email address.'
            ], 404);
        }

        // Determine if this is a new assignment or reassignment
        $isNewLead = $lead->created_at->diffInMinutes(now()) < 5; // Consider "new" if created within 5 minutes
        $reassignedBy = Auth::user();

        // Send email immediately using Mail facade (same as quotes/invoices)
        $mailInstance = Mail::to($request->assigned_user_email);
        
        if (!empty($ccEmails)) {
            $mailInstance->cc($ccEmails);
        }
        
        $mailInstance->send(new LeadAssignmentMail(
            $lead, 
            $assignedUser, 
            null, // previousAssignee - we don't track this in manual sends
            $reassignedBy, 
            $isNewLead
        ));

        // Update email history like quotes/invoices
        $emailHistory = $lead->email_history ?? [];
        $emailHistory[] = [
            'sent_at' => now()->toISOString(),
            'to' => $request->assigned_user_email,
            'cc' => $ccEmails,
            'subject' => ($isNewLead ? '👥 New Lead Assigned' : '🔄 Lead Reassigned') . ' - ' . $lead->full_name,
            'type' => 'manual_assignment',
            'sent_by' => $reassignedBy->name,
            'custom_message' => $request->message
        ];

        $lead->update([
            'email_history' => $emailHistory,
            'last_email_sent_at' => now()
        ]);

        // Log the activity
        $lead->logActivity('note', 'Assignment email sent manually', 
            "Assignment email sent manually to {$assignedUser->name} ({$request->assigned_user_email}) by {$reassignedBy->name}");

        \Log::info('Lead assignment email sent manually', [
            'lead_id' => $lead->id,
            'to_email' => $request->assigned_user_email,
            'cc_emails' => $ccEmails,
            'sent_by' => $reassignedBy->name
        ]);

        // Return JSON response for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Lead assignment email sent successfully!',
                'email_sent_to' => $request->assigned_user_email,
                'cc_emails' => $ccEmails
            ]);
        }

        return redirect()->back()->with('success', 'Lead assignment email sent successfully to ' . $request->assigned_user_email . '!');

    } catch (\Exception $e) {
        \Log::error('Failed to send lead assignment email', [
            'lead_id' => $lead->id,
            'to_email' => $request->assigned_user_email,
            'error' => $e->getMessage()
        ]);
        
        // Return JSON response for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email: ' . $e->getMessage()
            ]);
        }
        
        return redirect()->back()->with('error', 'Failed to send email: ' . $e->getMessage());
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
            'email' => 'required|email',
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

            // Also create/update customer record from this lead (non-blocking)
            try {
                $customer = $lead->createCustomer();
                $lead->logActivity('note', 'Customer created/linked', "Customer '{$customer->name}' (ID: {$customer->id}) linked to this lead");
            } catch (\Exception $e) {
                \Log::error("Customer creation from lead {$lead->id} failed: " . $e->getMessage());
            }

            // Send database notification to assigned user (which is the current user in quick add)
            try {
                $assignedUser = auth()->user();
                if ($assignedUser) {
                    // Send database notification for dashboard
                    $assignedUser->notify(new LeadCreatedNotification($lead));
                    
                    \Log::info('Lead assignment database notification sent (quick add)', [
                        'lead_id' => $lead->id,
                        'assigned_to' => $assignedUser->id,
                        'assigned_to_email' => $assignedUser->email
                    ]);
                    
                    $lead->logActivity('note', 'Lead assigned (quick add)', "Lead assigned to {$assignedUser->name}. Use 'Send Email' button to notify them.");
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send lead assignment notification (quick add)', [
                    'lead_id' => $lead->id,
                    'assigned_to' => $lead->assigned_to,
                    'error' => $e->getMessage()
                ]);
                // Non-fatal: continue even if notification fails
            }

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