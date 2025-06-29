<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\EmailTemplate;
use App\Models\MarketingContact;
use App\Models\ContactList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CampaignController extends Controller
{
    public function index(Request $request)
    {
        $query = Campaign::with(['creator', 'emailTemplate']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('subject', 'LIKE', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Sort options
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $campaigns = $query->paginate(25)->withQueryString();

        return view('crm.marketing.campaigns.index', compact('campaigns'));
    }

    public function create()
    {
        $emailTemplates = EmailTemplate::active()->get();
        $contactLists = ContactList::active()->get();
        
        return view('crm.marketing.campaigns.create', compact('emailTemplates', 'contactLists'));
    }

    public function store(Request $request)
    {
        // Debug: Log the incoming request
        \Log::info('Campaign store request received', [
            'user_id' => auth()->id(),
            'data' => $request->all()
        ]);

        // Handle scheduling - combine date and time if provided
        $scheduledAt = null;
        if ($request->send_option === 'schedule' && $request->scheduled_date && $request->scheduled_time) {
            $scheduledAt = $request->scheduled_date . ' ' . $request->scheduled_time;
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subject' => 'nullable|string|max:255',
            'subject_variant_b' => 'nullable|string|max:255',
            'text_content' => 'nullable|string|min:10',
            'email_template_id' => 'nullable|exists:email_templates,id',
            'type' => 'required|in:one_time,recurring,drip',
            'is_ab_test' => 'boolean',
            'ab_test_type' => 'required_if:is_ab_test,1|in:subject_line,cta,template,send_time',
            'ab_test_split_percentage' => 'required_if:is_ab_test,1|integer|min:10|max:90',
            
            // CTA variant fields - only required when A/B testing CTA
            'cta_text_variant_a' => 'nullable|string|max:255',
            'cta_url_variant_a' => 'nullable|url',
            'cta_color_variant_a' => 'nullable|in:indigo,green,orange,red,purple',
            'cta_text_variant_b' => 'nullable|string|max:255',
            'cta_url_variant_b' => 'nullable|url',
            'cta_color_variant_b' => 'nullable|in:indigo,green,orange,red,purple',
            
            // Template variant fields - only required when A/B testing templates
            'email_template_variant_a_id' => 'nullable|exists:email_templates,id',
            'text_content_variant_a' => 'nullable|string',
            'email_template_variant_b_id' => 'nullable|exists:email_templates,id',
            'text_content_variant_b' => 'nullable|string',
            
            // Send time variant fields - only required when A/B testing send times
            'scheduled_date_variant_a' => 'nullable|date|after:today',
            'scheduled_time_variant_a' => 'nullable',
            'scheduled_date_variant_b' => 'nullable|date|after:today',
            'scheduled_time_variant_b' => 'nullable',
            
            'send_option' => 'required|in:draft,now,schedule',
            'scheduled_date' => 'required_if:send_option,schedule|nullable|date|after:today',
            'scheduled_time' => 'required_if:send_option,schedule|nullable',
            'recipient_type' => 'required|in:all,lists,custom',
            'contact_lists' => 'required_if:recipient_type,lists|array',
            'contact_lists.*' => 'exists:contact_lists,id',
            'recipient_criteria' => 'required_if:recipient_type,custom|array',
        ]);

        // Apply conditional validation rules based on A/B test type
        if ($request->boolean('is_ab_test')) {
            $abTestType = $request->ab_test_type;
            
            // Add specific validation rules based on A/B test type
            $conditionalRules = [];
            
            switch ($abTestType) {
                case 'subject_line':
                    $conditionalRules['subject_variant_b'] = 'required|string|max:255';
                    break;
                    
                case 'cta':
                    $conditionalRules['cta_text_variant_a'] = 'required|string|max:255';
                    $conditionalRules['cta_url_variant_a'] = 'required|url';
                    $conditionalRules['cta_text_variant_b'] = 'required|string|max:255';
                    $conditionalRules['cta_url_variant_b'] = 'required|url';
                    break;
                    
                case 'template':
                    $conditionalRules['email_template_variant_a_id'] = 'required|exists:email_templates,id';
                    $conditionalRules['email_template_variant_b_id'] = 'required|exists:email_templates,id';
                    break;
                    
                case 'send_time':
                    $conditionalRules['scheduled_date_variant_a'] = 'required|date|after:today';
                    $conditionalRules['scheduled_time_variant_a'] = 'required';
                    $conditionalRules['scheduled_date_variant_b'] = 'required|date|after:today';
                    $conditionalRules['scheduled_time_variant_b'] = 'required';
                    break;
            }
            
            // Apply conditional rules
            if (!empty($conditionalRules)) {
                $validator->addRules($conditionalRules);
            }
        } else {
            // Standard campaign validation - require content or template
            if (!$request->email_template_id) {
                $validator->addRules(['text_content' => 'required|string|min:10']);
            }
        }

        if ($validator->fails()) {
            \Log::info('Campaign validation failed', [
                'errors' => $validator->errors()->toArray(),
                'input' => $request->all()
            ]);
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        \Log::info('Campaign validation passed, proceeding with creation');

        // Generate HTML content from text content if no template is selected
        $htmlContent = null;
        if (!$request->email_template_id && $request->text_content) {
            $htmlContent = $this->generateHtmlFromText($request->text_content);
        }

        // Determine campaign status based on send option
        $status = 'draft';
        if ($request->send_option === 'schedule') {
            $status = 'scheduled';
        } elseif ($request->send_option === 'now') {
            $status = 'draft'; // Will be sent immediately after creation
        }

        try {
            // Prepare variant data based on A/B test type
            $variantData = [];
            if ($request->boolean('is_ab_test')) {
                $variantData = $this->buildVariantData($request);
            }
            
            $campaign = Campaign::create([
                'name' => $request->name,
                'description' => $request->description,
                'subject' => $request->subject,
                'subject_variant_b' => $request->subject_variant_b,
                'text_content' => $request->text_content,
                'html_content' => $htmlContent,
                'email_template_id' => $request->email_template_id,
                'type' => $request->type,
                'is_ab_test' => $request->boolean('is_ab_test'),
                'ab_test_type' => $request->is_ab_test ? $request->ab_test_type : null,
                'ab_test_split_percentage' => $request->is_ab_test ? $request->ab_test_split_percentage : null,
                'ab_test_variant_data' => !empty($variantData) ? json_encode($variantData) : null,
                'status' => $status,
                'scheduled_at' => $scheduledAt,
                'recipients_criteria' => $this->buildRecipientsCriteria($request),
                'created_by' => auth()->id(),
            ]);
            
            \Log::info('Campaign created successfully', ['campaign_id' => $campaign->id]);

            // Add recipients
            $this->attachRecipients($campaign, $request);
            
            \Log::info('Recipients attached successfully', ['campaign_id' => $campaign->id]);

        } catch (\Exception $e) {
            \Log::error('Campaign creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->all()
            ]);
            
            return redirect()->back()
                           ->with('error', 'Failed to create campaign: ' . $e->getMessage())
                           ->withInput();
        }

        // Handle immediate sending
        if ($request->send_option === 'now') {
            try {
                $campaign->markAsSending();
                \App\Jobs\SendCampaignJob::dispatch($campaign);
                $message = 'Campaign is being sent. You will be notified when complete.';
            } catch (\Exception $e) {
                \Log::error('Campaign send error: ' . $e->getMessage());
                $campaign->update(['status' => 'draft']);
                $message = 'Campaign created successfully, but failed to start sending. You can send it manually.';
            }
        } else {
            $message = $campaign->isScheduled() ? 'Campaign scheduled successfully.' : 'Campaign created successfully.';
        }
        
        return redirect()->route('crm.marketing.campaigns.show', $campaign)
                        ->with('success', $message);
    }

    public function show(Campaign $campaign)
    {
        $campaign->load(['creator', 'emailTemplate', 'contacts', 'emailLogs']);
        
        // Update statistics
        $campaign->updateStatistics();
        
        // Update A/B test results if this is an A/B test campaign
        if ($campaign->isAbTest()) {
            $campaign->updateAbTestResults();
        }
        
        // Get recent email logs
        $recentLogs = $campaign->emailLogs()
                             ->with('contact')
                             ->latest()
                             ->limit(10)
                             ->get();

        // Get performance data for charts
        $performanceData = $this->getCampaignPerformanceData($campaign);

        return view('crm.marketing.campaigns.show', compact(
            'campaign', 
            'recentLogs', 
            'performanceData'
        ));
    }

    public function edit(Campaign $campaign)
    {
        if (!$campaign->isDraft()) {
            return redirect()->route('crm.marketing.campaigns.show', $campaign)
                           ->with('error', 'Only draft campaigns can be edited.');
        }

        $emailTemplates = EmailTemplate::active()->get();
        $contactLists = ContactList::active()->get();
        $campaign->load('contacts');

        return view('crm.marketing.campaigns.edit', compact(
            'campaign', 
            'emailTemplates', 
            'contactLists'
        ));
    }

    public function update(Request $request, Campaign $campaign)
    {
        if (!$campaign->isDraft()) {
            return redirect()->route('crm.marketing.campaigns.show', $campaign)
                           ->with('error', 'Only draft campaigns can be edited.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'subject' => 'nullable|string|max:255',
            'scheduled_at' => 'nullable|date|after:now',
            'recipient_type' => 'required|in:all,lists,custom',
            'contact_lists' => 'required_if:recipient_type,lists|array',
            'contact_lists.*' => 'exists:contact_lists,id',
            'recipient_criteria' => 'required_if:recipient_type,custom|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        $campaign->update([
            'name' => $request->name,
            'subject' => $request->subject,
            'status' => $request->filled('scheduled_at') ? 'scheduled' : 'draft',
            'scheduled_at' => $request->scheduled_at,
            'recipients_criteria' => $this->buildRecipientsCriteria($request),
        ]);

        // Update recipients
        $campaign->contacts()->detach();
        $this->attachRecipients($campaign, $request);

        return redirect()->route('crm.marketing.campaigns.show', $campaign)
                        ->with('success', 'Campaign updated successfully.');
    }

    public function destroy(Campaign $campaign)
    {
        if ($campaign->isSent()) {
            return redirect()->back()
                           ->with('error', 'Cannot delete a sent campaign.');
        }

        $campaign->delete();

        return redirect()->route('crm.marketing.campaigns.index')
                        ->with('success', 'Campaign deleted successfully.');
    }

    public function duplicate(Campaign $campaign)
    {
        $newCampaign = Campaign::create([
            'name' => $campaign->name . ' (Copy)',
            'description' => $campaign->description,
            'subject' => $campaign->subject,
            'text_content' => $campaign->text_content,
            'html_content' => $campaign->html_content,
            'email_template_id' => $campaign->email_template_id,
            'type' => $campaign->type,
            'status' => 'draft',
            'recipients_criteria' => $campaign->recipients_criteria,
            'created_by' => auth()->id(),
        ]);

        // Copy recipients
        $recipientIds = $campaign->contacts()->pluck('marketing_contacts.id')->toArray();
        $newCampaign->contacts()->attach($recipientIds);
        $newCampaign->update(['total_recipients' => count($recipientIds)]);

        return redirect()->route('crm.marketing.campaigns.edit', $newCampaign)
                        ->with('success', 'Campaign duplicated successfully.');
    }

    public function schedule(Request $request, Campaign $campaign)
    {
        if (!$campaign->isDraft()) {
            return redirect()->back()
                           ->with('error', 'Only draft campaigns can be scheduled.');
        }

        $validator = Validator::make($request->all(), [
            'scheduled_at' => 'required|date|after:now',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator);
        }

        $campaign->schedule($request->scheduled_at);

        return redirect()->back()
                        ->with('success', 'Campaign scheduled successfully.');
    }

    public function pause(Campaign $campaign)
    {
        if (!$campaign->isSending() && !$campaign->isScheduled()) {
            return redirect()->back()
                           ->with('error', 'Only sending or scheduled campaigns can be paused.');
        }

        $campaign->pause();

        return redirect()->back()
                        ->with('success', 'Campaign paused successfully.');
    }

    public function resume(Campaign $campaign)
    {
        if (!$campaign->isPaused()) {
            return redirect()->back()
                           ->with('error', 'Only paused campaigns can be resumed.');
        }

        $campaign->resume();

        return redirect()->back()
                        ->with('success', 'Campaign resumed successfully.');
    }

    public function cancel(Campaign $campaign)
    {
        if ($campaign->isSent()) {
            return redirect()->back()
                           ->with('error', 'Cannot cancel a sent campaign.');
        }

        $campaign->cancel();

        return redirect()->back()
                        ->with('success', 'Campaign cancelled successfully.');
    }

    public function send(Campaign $campaign)
    {
        if (!$campaign->isDraft() && !$campaign->isScheduled()) {
            return redirect()->back()
                           ->with('error', 'Only draft or scheduled campaigns can be sent.');
        }

        if ($campaign->total_recipients == 0) {
            return redirect()->back()
                           ->with('error', 'Campaign has no recipients. Please add contacts before sending.');
        }

        try {
            // Reset all contact statuses to 'pending' so they can be sent again
            $campaign->contacts()->updateExistingPivot(
                $campaign->contacts()->pluck('marketing_contacts.id')->toArray(),
                [
                    'status' => 'pending',
                    'sent_at' => null,
                    'delivered_at' => null,
                    'opened_at' => null,
                    'clicked_at' => null,
                    'bounce_reason' => null
                ]
            );

            // For A/B testing campaigns, ensure variant assignments exist
            if ($campaign->isAbTest()) {
                $this->ensureAbTestVariantAssignments($campaign);
            }

            // Mark campaign as sending
            $campaign->markAsSending();

            // Dispatch the campaign sending job
            \App\Jobs\SendCampaignJob::dispatch($campaign);

            return redirect()->back()
                           ->with('success', 'Campaign is being sent. You will be notified when complete.');
        } catch (\Exception $e) {
            \Log::error('Campaign send error: ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Failed to start sending campaign. Please try again.');
        }
    }

    /**
     * Ensure A/B test variant assignments exist for all contacts
     */
    private function ensureAbTestVariantAssignments(Campaign $campaign)
    {
        if (!$campaign->isAbTest()) {
            return;
        }

        // Check if any contacts are missing variant assignments
        $contactsWithoutVariants = $campaign->contacts()
            ->whereNull('campaign_contacts.ab_test_variant')
            ->get();

        if ($contactsWithoutVariants->isEmpty()) {
            return; // All contacts already have variants assigned
        }

        \Log::info('Assigning missing A/B test variants for campaign', [
            'campaign_id' => $campaign->id,
            'contacts_without_variants' => $contactsWithoutVariants->count()
        ]);

        // Assign variants to contacts without assignments
        $splitPercentage = $campaign->ab_test_split_percentage ?? 50;
        $totalContacts = $contactsWithoutVariants->count();
        $variantASampleSize = (int) ceil(($splitPercentage / 100) * $totalContacts);

        $contactsWithoutVariants->each(function ($contact, $index) use ($variantASampleSize, $campaign) {
            $variant = $index < $variantASampleSize ? 'variant_a' : 'variant_b';
            
            $campaign->contacts()->updateExistingPivot($contact->id, [
                'ab_test_variant' => $variant
            ]);
        });
    }

    public function selectWinner(Request $request, Campaign $campaign)
    {
        // Validate that this is an A/B test campaign
        if (!$campaign->isAbTest()) {
            return redirect()->back()
                           ->with('error', 'This action is only available for A/B test campaigns.');
        }

        // Validate that the campaign has been sent
        if (!$campaign->isSent()) {
            return redirect()->back()
                           ->with('error', 'Cannot select winner for a campaign that has not been sent yet.');
        }

        // Validate that a winner hasn't already been selected
        if ($campaign->hasWinner()) {
            return redirect()->back()
                           ->with('error', 'A winner has already been selected for this campaign.');
        }

        // Validate the winner selection
        $request->validate([
            'winner' => 'required|in:variant_a,variant_b'
        ]);

        try {
            // Update A/B test results before selecting winner
            $campaign->updateAbTestResults();
            
            // Select the winner
            $campaign->selectWinner($request->winner);

            $winnerLabel = $request->winner === 'variant_a' ? 'Variant A' : 'Variant B';
            
            return redirect()->back()
                           ->with('success', "Winner selected successfully! {$winnerLabel} has been chosen as the winning variant.");
                           
        } catch (\Exception $e) {
            \Log::error('Failed to select campaign winner: ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Failed to select winner. Please try again.');
        }
    }

    public function preview(Campaign $campaign)
    {
        $sampleContact = MarketingContact::active()->first();
        
        if (!$sampleContact) {
            $sampleContact = new MarketingContact([
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@example.com',
                'company' => 'Sample Company',
                'job_title' => 'Marketing Manager',
                'industry' => 'Technology',
                'country' => 'United States',
                'city' => 'New York',
            ]);
        }

        $data = [
            'contact' => $sampleContact,
            'company_name' => config('app.name'),
            'current_date' => now()->format('Y-m-d'),
            'current_year' => now()->year,
            'unsubscribe_url' => route('marketing.unsubscribe', ['token' => 'sample-token']),
        ];

        // Render email content based on whether template is used or not
        if ($campaign->emailTemplate) {
            // Use the email template
            $htmlContent = $campaign->emailTemplate->renderHtmlContent($data);
            $subject = $campaign->emailTemplate->renderSubject($data);
        } else {
            // Use campaign content directly
            $subject = $this->replaceVariables($campaign->subject ?: 'No Subject', $data);
            
            if ($campaign->html_content) {
                // Use existing HTML content
                $htmlContent = $this->replaceVariables($campaign->html_content, $data);
            } elseif ($campaign->text_content) {
                // Generate HTML from text content
                $processedText = $this->replaceVariables($campaign->text_content, $data);
                $bannerImage = $campaign->emailTemplate?->banner_image;
                $htmlContent = $this->generateHtmlFromText($processedText, $bannerImage);
            } else {
                $htmlContent = '<p>No content available for preview.</p>';
            }
        }

        return view('crm.marketing.campaigns.preview', compact(
            'campaign', 
            'htmlContent', 
            'subject',
            'sampleContact'
        ));
    }

    /**
     * Replace variables in content with actual data
     */
    private function replaceVariables(string $content, array $data): string
    {
        // Replace variables in the format {{variable_name}}
        foreach ($data as $key => $value) {
            if (is_string($value) || is_numeric($value)) {
                $content = str_replace('{{' . $key . '}}', $value, $content);
            }
        }

        // Handle contact-specific variables
        if (isset($data['contact']) && $data['contact'] instanceof \App\Models\MarketingContact) {
            $contact = $data['contact'];
            
            $contactData = [
                'first_name' => $contact->first_name,
                'last_name' => $contact->last_name,
                'full_name' => $contact->full_name,
                'email' => $contact->email,
                'phone' => $contact->phone,
                'company' => $contact->company,
                'job_title' => $contact->job_title,
                'industry' => $contact->industry,
                'country' => $contact->country,
                'city' => $contact->city,
            ];

            foreach ($contactData as $key => $value) {
                $content = str_replace('{{' . $key . '}}', $value ?: '', $content);
            }

            // Handle custom fields
            if ($contact->custom_fields) {
                foreach ($contact->custom_fields as $key => $value) {
                    $content = str_replace('{{custom_' . $key . '}}', $value, $content);
                }
            }
        }

        // Remove any remaining unreplaced variables
        $content = preg_replace('/\{\{([^}]+)\}\}/', '', $content);

        return $content;
    }

    private function buildRecipientsCriteria(Request $request): array
    {
        $criteria = [
            'type' => $request->recipient_type,
        ];

        switch ($request->recipient_type) {
            case 'lists':
                $criteria['contact_lists'] = $request->contact_lists ?: [];
                break;
            case 'custom':
                $criteria['criteria'] = $request->recipient_criteria ?: [];
                break;
        }

        return $criteria;
    }

    private function attachRecipients(Campaign $campaign, Request $request): void
    {
        $contacts = collect();

        switch ($request->recipient_type) {
            case 'all':
                $contacts = MarketingContact::active()->get();
                break;
                
            case 'lists':
                if ($request->filled('contact_lists')) {
                    // Get the contact lists
                    $contactLists = ContactList::whereIn('id', $request->contact_lists)->get();
                    $allContactIds = collect();
                    
                    foreach ($contactLists as $contactList) {
                        if ($contactList->isDynamic()) {
                            // For dynamic lists, get contacts based on criteria
                            $dynamicContactIds = $contactList->getDynamicContacts()
                                                            ->where('status', 'active')
                                                            ->pluck('id');
                            $allContactIds = $allContactIds->merge($dynamicContactIds);
                        } else {
                            // For static lists, get contacts from the pivot table
                            $staticContactIds = $contactList->activeContacts()->pluck('marketing_contacts.id');
                            $allContactIds = $allContactIds->merge($staticContactIds);
                        }
                    }
                    
                    // Remove duplicates and get the actual contact models
                    $uniqueContactIds = $allContactIds->unique()->values();
                    if ($uniqueContactIds->isNotEmpty()) {
                        $contacts = MarketingContact::active()->whereIn('id', $uniqueContactIds)->get();
                    }
                }
                break;
                
            case 'custom':
                if ($request->filled('recipient_criteria')) {
                    $query = MarketingContact::active();
                    
                    foreach ($request->recipient_criteria as $criterion) {
                        $field = $criterion['field'] ?? null;
                        $operator = $criterion['operator'] ?? '=';
                        $value = $criterion['value'] ?? null;

                        if ($field && $value !== null) {
                            switch ($operator) {
                                case 'equals':
                                    $query->where($field, '=', $value);
                                    break;
                                case 'not_equals':
                                    $query->where($field, '!=', $value);
                                    break;
                                case 'contains':
                                    $query->where($field, 'LIKE', '%' . $value . '%');
                                    break;
                                case 'in':
                                    if (is_array($value)) {
                                        $query->whereIn($field, $value);
                                    }
                                    break;
                            }
                        }
                    }
                    
                    $contacts = $query->get();
                }
                break;
        }

        if ($contacts->isNotEmpty()) {
            $contactIds = $contacts->pluck('id')->toArray();
            $campaign->contacts()->attach($contactIds);
            $campaign->update(['total_recipients' => count($contactIds)]);
        }
    }

    private function getCampaignPerformanceData(Campaign $campaign): array
    {
        return [
            'overview' => [
                'total_recipients' => $campaign->total_recipients,
                'sent_count' => $campaign->sent_count,
                'delivered_count' => $campaign->delivered_count,
                'opened_count' => $campaign->opened_count,
                'clicked_count' => $campaign->clicked_count,
                'bounced_count' => $campaign->bounced_count,
                'unsubscribed_count' => $campaign->unsubscribed_count,
                'delivery_rate' => $campaign->delivery_rate,
                'open_rate' => $campaign->open_rate,
                'click_rate' => $campaign->click_rate,
                'bounce_rate' => $campaign->bounce_rate,
                'unsubscribe_rate' => $campaign->unsubscribe_rate,
            ],
            'timeline' => $this->getCampaignTimeline($campaign),
        ];
    }

    private function getCampaignTimeline(Campaign $campaign): array
    {
        $timeline = [];
        
        if ($campaign->created_at) {
            $timeline[] = [
                'date' => $campaign->created_at,
                'event' => 'Campaign Created',
                'description' => "Campaign '{$campaign->name}' was created",
            ];
        }
        
        if ($campaign->scheduled_at) {
            $timeline[] = [
                'date' => $campaign->scheduled_at,
                'event' => 'Campaign Scheduled',
                'description' => "Campaign scheduled to send",
            ];
        }
        
        if ($campaign->sent_at) {
            $timeline[] = [
                'date' => $campaign->sent_at,
                'event' => 'Campaign Sent',
                'description' => "Campaign was sent to {$campaign->total_recipients} recipients",
            ];
        }

        return collect($timeline)->sortBy('date')->values()->toArray();
    }

    public function export(Request $request)
    {
        $query = Campaign::with(['creator']);

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('subject', 'LIKE', "%{$search}%");
            });
        }

        $campaigns = $query->get();

        $filename = 'email_campaigns_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($campaigns) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'Campaign Name', 'Subject', 'Type', 'Status', 'Total Recipients', 
                'Sent Count', 'Delivered Count', 'Opened Count', 'Clicked Count', 
                'Bounced Count', 'Unsubscribed Count', 'Open Rate (%)', 'Click Rate (%)', 
                'Bounce Rate (%)', 'Unsubscribe Rate (%)', 'Scheduled At', 'Sent At', 
                'Created By', 'Created At'
            ]);

            foreach ($campaigns as $campaign) {
                fputcsv($file, [
                    $campaign->name,
                    $campaign->subject,
                    ucfirst(str_replace('_', ' ', $campaign->type)),
                    ucfirst($campaign->status),
                    $campaign->total_recipients ?? 0,
                    $campaign->sent_count ?? 0,
                    $campaign->delivered_count ?? 0,
                    $campaign->opened_count ?? 0,
                    $campaign->clicked_count ?? 0,
                    $campaign->bounced_count ?? 0,
                    $campaign->unsubscribed_count ?? 0,
                    number_format($campaign->open_rate ?? 0, 2),
                    number_format($campaign->click_rate ?? 0, 2),
                    number_format($campaign->bounce_rate ?? 0, 2),
                    number_format($campaign->unsubscribe_rate ?? 0, 2),
                    $campaign->scheduled_at?->format('Y-m-d H:i:s'),
                    $campaign->sent_at?->format('Y-m-d H:i:s'),
                    $campaign->creator?->name ?? 'Unknown',
                    $campaign->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function getStatistics(Campaign $campaign)
    {
        // Update statistics first
        $campaign->updateStatistics();
        
        // Return fresh statistics as JSON
        return response()->json([
            'success' => true,
            'statistics' => [
                'total_recipients' => $campaign->total_recipients,
                'sent_count' => $campaign->sent_count,
                'delivered_count' => $campaign->delivered_count,
                'opened_count' => $campaign->opened_count,
                'clicked_count' => $campaign->clicked_count,
                'bounced_count' => $campaign->bounced_count,
                'unsubscribed_count' => $campaign->unsubscribed_count,
                'delivery_rate' => $campaign->delivery_rate,
                'open_rate' => $campaign->open_rate,
                'click_rate' => $campaign->click_rate,
                'bounce_rate' => $campaign->bounce_rate,
                'unsubscribe_rate' => $campaign->unsubscribe_rate,
            ],
            'updated_at' => now()->toISOString()
        ]);
    }

    /**
     * Generate professional HTML from text content
     */
    private function generateHtmlFromText(string $textContent, string $bannerImage = null): string
    {
        // Create a professional email template
        $htmlContent = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Preview</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        p {
            margin: 0 0 16px 0;
        }
        .header {
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .signature {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            font-size: 14px;
            color: #2c3e50;
        }
        .signature-name {
            font-weight: bold;
            color: #1a365d;
        }
        .signature-title {
            color: #2d3748;
            font-style: italic;
            margin-bottom: 10px;
        }
        .company-info {
            color: #4a5568;
            line-height: 1.5;
        }
        .contact-info a {
            color: #3182ce;
            text-decoration: none;
        }
        .contact-info a:hover {
            text-decoration: underline;
        }
        .banner-section {
            text-align: center;
            margin: 20px 0;
        }
        .footer {
            border-top: 1px solid #e9ecef;
            padding-top: 20px;
            margin-top: 30px;
            font-size: 12px;
            color: #6c757d;
            text-align: center;
        }
        .unsubscribe-link {
            color: #6c757d;
            text-decoration: none;
        }
        .unsubscribe-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1 style="margin: 0; color: #2c3e50; font-size: 24px;">' . config('app.name', 'MaxMed') . '</h1>
        </div>
        
        <!-- Banner Section - At the beginning -->
        ' . ($bannerImage ? '<div class="banner-section" style="text-align: center; margin: 0 0 30px 0;"><img src="' . asset('storage/' . $bannerImage) . '" alt="Company Banner" style="max-width: 100%; height: auto; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);"></div>' : '') . '
        
        <div class="content">';
        
        // Convert line breaks to paragraphs
        $paragraphs = explode("\n\n", $textContent);
        foreach ($paragraphs as $paragraph) {
            $paragraph = trim($paragraph);
            if (!empty($paragraph)) {
                // Auto-convert emails to trackable mailto links
                $paragraph = preg_replace(
                    '/([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/',
                    '<a href="mailto:$1">$1</a>',
                    $paragraph
                );
                
                // Auto-convert websites to trackable links (but not emails that might look like domains)
                $paragraph = preg_replace(
                    '/(?<!\w)(?:https?:\/\/)?(?:www\.)?([a-zA-Z0-9.-]+\.[a-zA-Z]{2,}(?:\/[^\s]*)?)\b/i',
                    '<a href="https://$1">$1</a>',
                    $paragraph
                );
                
                // Auto-convert phone numbers to trackable tel links
                $paragraph = preg_replace(
                    '/(\+?[\d\s\-\(\)]{10,})/',
                    '<a href="tel:$1">$1</a>',
                    $paragraph
                );
                
                // Convert single line breaks to <br> tags
                $paragraph = nl2br($paragraph);
                $htmlContent .= '<p>' . $paragraph . '</p>';
            }
        }
        
        $htmlContent .= '
        </div>
        
        <!-- Signature Section -->
        <div class="signature">
            <div class="signature-name">Walid Babi</div>
            <div class="signature-title">Sales Specialist</div>
            <br>
            <div class="company-info">
                <strong>MaxMed Scientific and Laboratory Equipment Trading CO.LLC</strong><br>
                Dubai, P.O Box 448945 | Tel: <a href="tel:+971554602500">+971 55 4602500</a><br>
                United Arab Emirates<br>
                <div class="contact-info">
                    <a href="mailto:wbabi@maxmedme.com">wbabi@maxmedme.com</a> | 
                    <a href="https://www.maxmedme.com" target="_blank">www.maxmedme.com</a>
                </div>
            </div>
        </div>
        
        <div class="footer">
            <p><a href="{{unsubscribe_url}}" class="unsubscribe-link">Unsubscribe from this list</a></p>
        </div>
    </div>
</body>
</html>';
        
        return $htmlContent;
    }

    /**
     * Build variant data for A/B testing based on test type
     */
    private function buildVariantData(Request $request): array
    {
        $data = [];
        
        switch ($request->ab_test_type) {
            case 'subject_line':
                // Subject line data is already handled by the subject and subject_variant_b fields
                break;
                
            case 'cta':
                $data = [
                    'variant_a' => [
                        'cta_text' => $request->cta_text_variant_a,
                        'cta_url' => $request->cta_url_variant_a,
                        'cta_color' => $request->cta_color_variant_a,
                    ],
                    'variant_b' => [
                        'cta_text' => $request->cta_text_variant_b,
                        'cta_url' => $request->cta_url_variant_b,
                        'cta_color' => $request->cta_color_variant_b,
                    ]
                ];
                break;
                
            case 'template':
                $data = [
                    'variant_a' => [
                        'email_template_id' => $request->email_template_variant_a_id,
                        'text_content' => $request->text_content_variant_a,
                    ],
                    'variant_b' => [
                        'email_template_id' => $request->email_template_variant_b_id,
                        'text_content' => $request->text_content_variant_b,
                    ]
                ];
                break;
                
            case 'send_time':
                $scheduledAtA = null;
                if ($request->scheduled_date_variant_a && $request->scheduled_time_variant_a) {
                    $scheduledAtA = $request->scheduled_date_variant_a . ' ' . $request->scheduled_time_variant_a;
                }
                
                $scheduledAtB = null;
                if ($request->scheduled_date_variant_b && $request->scheduled_time_variant_b) {
                    $scheduledAtB = $request->scheduled_date_variant_b . ' ' . $request->scheduled_time_variant_b;
                }
                
                $data = [
                    'variant_a' => [
                        'scheduled_at' => $scheduledAtA,
                    ],
                    'variant_b' => [
                        'scheduled_at' => $scheduledAtB,
                    ]
                ];
                break;
        }
        
        return $data;
    }
} 