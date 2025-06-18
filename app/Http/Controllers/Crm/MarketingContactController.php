<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\MarketingContact;
use App\Models\ContactList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MarketingContactController extends Controller
{
    public function index(Request $request)
    {
        $query = MarketingContact::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('company', 'LIKE', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by industry
        if ($request->filled('industry')) {
            $query->where('industry', $request->industry);
        }

        // Filter by contact list
        if ($request->filled('list_id')) {
            $query->whereHas('contactLists', function ($q) use ($request) {
                $q->where('contact_lists.id', $request->list_id);
            });
        }

        // Sort options
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $contacts = $query->paginate(25)->withQueryString();

        // Get filter options
        $industries = MarketingContact::distinct()->pluck('industry')->filter();
        $contactLists = ContactList::active()->get();

        return view('crm.marketing.contacts.index', compact(
            'contacts', 
            'industries', 
            'contactLists'
        ));
    }

    public function create()
    {
        $contactLists = ContactList::active()->get();
        return view('crm.marketing.contacts.create', compact('contactLists'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:marketing_contacts,email',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'industry' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'contact_lists' => 'nullable|array',
            'contact_lists.*' => 'exists:contact_lists,id',
            'custom_fields' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        $contact = MarketingContact::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'company' => $request->company,
            'job_title' => $request->job_title,
            'industry' => $request->industry,
            'country' => $request->country,
            'city' => $request->city,
            'source' => $request->source ?? 'manual',
            'notes' => $request->notes,
            'custom_fields' => $request->custom_fields,
            'status' => 'active',
            'subscribed_at' => now(),
        ]);

        // Attach to contact lists
        if ($request->filled('contact_lists')) {
            $contact->contactLists()->attach($request->contact_lists, [
                'added_at' => now()
            ]);
        }

        return redirect()->route('crm.marketing.contacts.index')
                        ->with('success', 'Contact created successfully.');
    }

    public function show(MarketingContact $contact)
    {
        $contact->load(['contactLists', 'campaigns', 'emailLogs']);
        
        // Get campaign statistics for this contact
        $campaignStats = $contact->campaigns()
                                ->selectRaw('
                                    COUNT(*) as total_campaigns,
                                    SUM(CASE WHEN campaign_contacts.opened_at IS NOT NULL THEN 1 ELSE 0 END) as opened_campaigns,
                                    SUM(campaign_contacts.open_count) as total_opens,
                                    SUM(campaign_contacts.click_count) as total_clicks
                                ')
                                ->first();

        return view('crm.marketing.contacts.show', compact('contact', 'campaignStats'));
    }

    public function edit(MarketingContact $contact)
    {
        $contactLists = ContactList::active()->get();
        $contact->load('contactLists');
        
        return view('crm.marketing.contacts.edit', compact('contact', 'contactLists'));
    }

    public function update(Request $request, MarketingContact $contact)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:marketing_contacts,email,' . $contact->id,
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'industry' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'contact_lists' => 'nullable|array',
            'contact_lists.*' => 'exists:contact_lists,id',
            'custom_fields' => 'nullable|array',
            'status' => 'required|in:active,unsubscribed,bounced,complained',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        $contact->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'company' => $request->company,
            'job_title' => $request->job_title,
            'industry' => $request->industry,
            'country' => $request->country,
            'city' => $request->city,
            'source' => $request->source,
            'notes' => $request->notes,
            'custom_fields' => $request->custom_fields,
            'status' => $request->status,
        ]);

        // Sync contact lists
        if ($request->has('contact_lists')) {
            $contact->contactLists()->sync($request->contact_lists ?: []);
        }

        return redirect()->route('crm.marketing.contacts.show', $contact)
                        ->with('success', 'Contact updated successfully.');
    }

    public function destroy(MarketingContact $contact)
    {
        $contact->delete();

        return redirect()->route('crm.marketing.contacts.index')
                        ->with('success', 'Contact deleted successfully.');
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv,txt,xlsx',
            'contact_list_id' => 'nullable|exists:contact_lists,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator);
        }

        $file = $request->file('file');
        $contacts = $this->parseContactFile($file);
        
        $imported = 0;
        $errors = [];

        foreach ($contacts as $index => $contactData) {
            try {
                $contact = MarketingContact::create([
                    'first_name' => $contactData['first_name'] ?? '',
                    'last_name' => $contactData['last_name'] ?? '',
                    'email' => $contactData['email'],
                    'phone' => $contactData['phone'] ?? null,
                    'company' => $contactData['company'] ?? null,
                    'job_title' => $contactData['job_title'] ?? null,
                    'industry' => $contactData['industry'] ?? null,
                    'country' => $contactData['country'] ?? null,
                    'city' => $contactData['city'] ?? null,
                    'source' => 'import',
                    'status' => 'active',
                    'subscribed_at' => now(),
                ]);

                if ($request->filled('contact_list_id')) {
                    $contact->contactLists()->attach($request->contact_list_id, [
                        'added_at' => now()
                    ]);
                }

                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Row " . ($index + 1) . ": " . $e->getMessage();
            }
        }

        $message = "Successfully imported {$imported} contacts.";
        if (!empty($errors)) {
            $message .= " " . count($errors) . " errors occurred.";
        }

        return redirect()->route('crm.marketing.contacts.index')
                        ->with('success', $message)
                        ->with('import_errors', $errors);
    }

    public function export(Request $request)
    {
        $query = MarketingContact::query();

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('industry')) {
            $query->where('industry', $request->industry);
        }

        if ($request->filled('list_id')) {
            $query->whereHas('contactLists', function ($q) use ($request) {
                $q->where('contact_lists.id', $request->list_id);
            });
        }

        $contacts = $query->get();

        $filename = 'marketing_contacts_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($contacts) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'First Name', 'Last Name', 'Email', 'Phone', 'Company', 
                'Job Title', 'Industry', 'Country', 'City', 'Status', 
                'Source', 'Subscribed At', 'Notes'
            ]);

            foreach ($contacts as $contact) {
                fputcsv($file, [
                    $contact->first_name,
                    $contact->last_name,
                    $contact->email,
                    $contact->phone,
                    $contact->company,
                    $contact->job_title,
                    $contact->industry,
                    $contact->country,
                    $contact->city,
                    $contact->status,
                    $contact->source,
                    $contact->subscribed_at?->format('Y-m-d H:i:s'),
                    $contact->notes,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function unsubscribe(MarketingContact $contact)
    {
        $contact->unsubscribe();

        return redirect()->back()
                        ->with('success', 'Contact unsubscribed successfully.');
    }

    public function resubscribe(MarketingContact $contact)
    {
        $contact->resubscribe();

        return redirect()->back()
                        ->with('success', 'Contact resubscribed successfully.');
    }

    private function parseContactFile($file)
    {
        $contacts = [];
        $path = $file->getPathname();
        
        if (($handle = fopen($path, 'r')) !== false) {
            $headers = fgetcsv($handle);
            $headers = array_map('strtolower', $headers);
            $headers = array_map(function($header) {
                return str_replace(' ', '_', trim($header));
            }, $headers);

            while (($data = fgetcsv($handle)) !== false) {
                $contactData = array_combine($headers, $data);
                
                // Validate required email field
                if (!empty($contactData['email']) && filter_var($contactData['email'], FILTER_VALIDATE_EMAIL)) {
                    $contacts[] = $contactData;
                }
            }
            fclose($handle);
        }

        return $contacts;
    }
} 