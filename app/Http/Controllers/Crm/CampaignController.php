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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subject' => 'nullable|string|max:255',
            'html_content' => 'nullable|string',
            'text_content' => 'nullable|string',
            'email_template_id' => 'nullable|exists:email_templates,id',
            'type' => 'required|in:one_time,recurring,drip',
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

        $campaign = Campaign::create([
            'name' => $request->name,
            'description' => $request->description,
            'subject' => $request->subject,
            'html_content' => $request->html_content,
            'text_content' => $request->text_content,
            'email_template_id' => $request->email_template_id,
            'type' => $request->type,
            'status' => $request->filled('scheduled_at') ? 'scheduled' : 'draft',
            'scheduled_at' => $request->scheduled_at,
            'recipients_criteria' => $this->buildRecipientsCriteria($request),
            'created_by' => auth()->id(),
        ]);

        // Add recipients
        $this->attachRecipients($campaign, $request);

        $message = $campaign->isScheduled() ? 'Campaign scheduled successfully.' : 'Campaign created successfully.';
        
        return redirect()->route('crm.marketing.campaigns.show', $campaign)
                        ->with('success', $message);
    }

    public function show(Campaign $campaign)
    {
        $campaign->load(['creator', 'emailTemplate', 'contacts', 'emailLogs']);
        
        // Update statistics
        $campaign->updateStatistics();
        
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
            'html_content' => $campaign->html_content,
            'text_content' => $campaign->text_content,
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

        if ($campaign->emailTemplate) {
            $htmlContent = $campaign->emailTemplate->renderHtmlContent($data);
            $subject = $campaign->emailTemplate->renderSubject($data);
        } else {
            $template = new EmailTemplate([
                'subject' => $campaign->subject,
                'html_content' => $campaign->html_content,
            ]);
            $htmlContent = $template->renderHtmlContent($data);
            $subject = $template->renderSubject($data);
        }

        return view('crm.marketing.campaigns.preview', compact(
            'campaign', 
            'htmlContent', 
            'subject',
            'sampleContact'
        ));
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
} 