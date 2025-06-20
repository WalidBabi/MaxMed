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
        $campaignStats = \DB::table('campaign_contacts')
                            ->where('marketing_contact_id', $contact->id)
                            ->selectRaw('
                                COUNT(*) as total_campaigns,
                                SUM(CASE WHEN opened_at IS NOT NULL THEN 1 ELSE 0 END) as opened_campaigns,
                                SUM(open_count) as total_opens,
                                SUM(click_count) as total_clicks
                            ')
                            ->first();

        // Add debug headers to ensure proper content type
        $response = response()->view('crm.marketing.contacts.show', compact('contact', 'campaignStats'));
        $response->header('Content-Type', 'text/html; charset=utf-8');
        $response->header('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response->header('Pragma', 'no-cache');
        $response->header('Expires', '0');
        
        return $response;
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
            'status' => 'required|in:active,inactive,unsubscribed,bounced,complained',
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

        // Redirect to index first, then JavaScript will redirect to show page
        // This avoids the browser content-type interpretation issue
        return redirect()->route('crm.marketing.contacts.index')
                        ->with('success', 'Contact updated successfully.')
                        ->with('redirect_to_contact', $contact->id);
    }

    public function destroy(MarketingContact $contact)
    {
        $contact->delete();

        return redirect()->route('crm.marketing.contacts.index')
                        ->with('success', 'Contact deleted successfully.');
    }

    public function import(Request $request)
    {
        $step = $request->get('step', 'preview');
        
        // Different validation rules based on step
        if ($step === 'preview') {
            $validator = Validator::make($request->all(), [
                'file' => 'required|file|mimes:csv,txt,xlsx',
                'contact_list_id' => 'nullable|exists:contact_lists,id',
                'step' => 'nullable|string|in:preview,confirm',
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'contact_list_id' => 'nullable|exists:contact_lists,id',
                'step' => 'required|string|in:preview,confirm',
                'column_mappings' => 'required|array',
                'column_mappings.email' => 'required|integer|min:0', // Email mapping is required
            ]);
        }

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator);
        }

        $file = $request->file('file');
        
        if ($step === 'preview') {
            return $this->showImportPreview($file, $request->get('contact_list_id'));
        }
        
        if ($step === 'confirm') {
            return $this->processImportWithMappings($file, $request->get('column_mappings', []), $request->get('contact_list_id'));
        }
        
        // Fallback to old behavior if no step specified
        try {
            $contacts = $this->parseContactFile($file);
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Failed to parse CSV file: ' . $e->getMessage())
                           ->withInput();
        }
        
        if (empty($contacts)) {
            return redirect()->back()
                           ->with('error', 'No valid contacts found in the CSV file. Please check your file format and ensure at least one row has a valid email address.')
                           ->withInput();
        }
        
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

    private function showImportPreview($file, $contactListId = null)
    {
        try {
            $previewData = $this->parseFileForPreview($file);
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Failed to parse CSV file: ' . $e->getMessage())
                           ->withInput();
        }

        if (empty($previewData['headers'])) {
            return redirect()->back()
                           ->with('error', 'No headers found in the CSV file.')
                           ->withInput();
        }

        // Store file temporarily in session for the next step
        $tempFileName = 'import_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $tempPath = storage_path('app/temp/' . $tempFileName);
        
        // Ensure temp directory exists
        if (!file_exists(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0755, true);
        }
        
        $file->move(dirname($tempPath), basename($tempPath));
        session(['import_temp_file' => $tempFileName]);

        $detectedMappings = $this->detectColumnMappings($previewData['headers']);
        $contactLists = ContactList::active()->get();

        return view('crm.marketing.contacts.import-preview', compact(
            'previewData', 
            'detectedMappings', 
            'contactListId',
            'contactLists'
        ));
    }

    private function processImportWithMappings($file, $columnMappings, $contactListId = null)
    {
        // Get temp file from session if file is null
        if (!$file && session('import_temp_file')) {
            $tempFileName = session('import_temp_file');
            $tempPath = storage_path('app/temp/' . $tempFileName);
            
            if (file_exists($tempPath)) {
                $file = new \Illuminate\Http\UploadedFile(
                    $tempPath,
                    $tempFileName,
                    mime_content_type($tempPath),
                    null,
                    true // test mode - don't validate
                );
            } else {
                return redirect()->back()
                               ->with('error', 'Temporary file not found. Please upload the file again.')
                               ->withInput();
            }
        }
        
        try {
            $contacts = $this->parseContactFileWithMappings($file, $columnMappings);
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Failed to parse CSV file: ' . $e->getMessage())
                           ->withInput();
        }
        
        if (empty($contacts)) {
            return redirect()->back()
                           ->with('error', 'No valid contacts found in the CSV file. Please check your mappings and ensure at least one row has a valid email address.')
                           ->withInput();
        }
        
        $imported = 0;
        $errors = [];

        foreach ($contacts as $index => $contactData) {
            try {
                // Validate email is present and valid
                if (empty($contactData['email']) || !filter_var($contactData['email'], FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Row " . ($index + 2) . ": Invalid or missing email address";
                    continue;
                }

                // Check for duplicate email
                $existingContact = MarketingContact::where('email', $contactData['email'])->first();
                if ($existingContact) {
                    $errors[] = "Row " . ($index + 2) . ": Contact with email '{$contactData['email']}' already exists";
                    continue;
                }

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
                    'notes' => $contactData['notes'] ?? null,
                    'source' => 'import',
                    'status' => 'active',
                    'subscribed_at' => now(),
                ]);

                if ($contactListId) {
                    $contact->contactLists()->attach($contactListId, [
                        'added_at' => now()
                    ]);
                }

                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
            }
        }

        $message = "Successfully imported {$imported} contacts.";
        if (!empty($errors)) {
            $message .= " " . count($errors) . " errors occurred.";
        }

        // Clean up temporary file
        if (session('import_temp_file')) {
            $tempPath = storage_path('app/temp/' . session('import_temp_file'));
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }
            session()->forget('import_temp_file');
        }

        return redirect()->route('crm.marketing.contacts.index')
                        ->with('success', $message)
                        ->with('import_errors', $errors);
    }

    private function parseFileForPreview($file)
    {
        $headers = [];
        $sampleRows = [];
        $path = $file->getPathname();
        
        if (($handle = fopen($path, 'r')) !== false) {
            // Read headers
            $headers = fgetcsv($handle);
            
            if ($headers === false || empty($headers)) {
                fclose($handle);
                throw new \Exception('Invalid CSV file: No headers found');
            }
            
            // Clean headers but keep original mapping
            $originalHeaders = $headers;
            $headers = array_map('trim', $headers);
            
            // Read first 5 rows as sample
            $rowCount = 0;
            while (($data = fgetcsv($handle)) !== false && $rowCount < 5) {
                if (!empty(array_filter($data, function($value) {
                    return trim($value) !== '';
                }))) {
                    // Ensure data array matches header count
                    $dataCount = count($data);
                    $headerCount = count($headers);
                    
                    if ($dataCount < $headerCount) {
                        $data = array_pad($data, $headerCount, '');
                    } elseif ($dataCount > $headerCount) {
                        $data = array_slice($data, 0, $headerCount);
                    }
                    
                    $sampleRows[] = array_combine($headers, $data);
                    $rowCount++;
                }
            }
            fclose($handle);
        }

        return [
            'headers' => $headers,
            'originalHeaders' => $originalHeaders,
            'sampleRows' => $sampleRows,
            'totalPreviewRows' => $rowCount
        ];
    }

    private function detectColumnMappings($headers)
    {
        $dbColumns = [
            'first_name' => ['first_name', 'firstname', 'first name', 'fname', 'given_name', 'given name'],
            'last_name' => ['last_name', 'lastname', 'last name', 'lname', 'surname', 'family_name', 'family name'],
            'email' => ['email', 'email_address', 'email address', 'e-mail', 'e_mail', 'mail'],
            'phone' => ['phone', 'phone_number', 'phone number', 'telephone', 'tel', 'mobile', 'cell'],
            'company' => ['company', 'organization', 'organisation', 'business', 'firm', 'corp'],
            'job_title' => ['job_title', 'job title', 'title', 'position', 'role', 'designation'],
            'industry' => ['industry', 'sector', 'field', 'domain', 'business_type', 'business type'],
            'country' => ['country', 'nation', 'location'],
            'city' => ['city', 'town', 'locality', 'place'],
            'notes' => ['notes', 'comments', 'remarks', 'description', 'memo'],
        ];

        $mappings = [];
        $usedHeaders = [];

        foreach ($dbColumns as $dbColumn => $patterns) {
            $bestMatch = null;
            $bestScore = 0;

            foreach ($headers as $index => $header) {
                if (in_array($index, $usedHeaders)) {
                    continue;
                }

                $headerLower = strtolower(trim($header));
                $headerNormalized = str_replace([' ', '_', '-'], ['_', '_', '_'], $headerLower);

                foreach ($patterns as $pattern) {
                    $patternLower = strtolower($pattern);
                    $patternNormalized = str_replace([' ', '_', '-'], ['_', '_', '_'], $patternLower);

                    // Exact match
                    if ($headerNormalized === $patternNormalized) {
                        $bestMatch = $index;
                        $bestScore = 100;
                        break 2;
                    }

                    // Contains match
                    if (strpos($headerNormalized, $patternNormalized) !== false || strpos($patternNormalized, $headerNormalized) !== false) {
                        $score = 80;
                        if ($score > $bestScore) {
                            $bestMatch = $index;
                            $bestScore = $score;
                        }
                    }

                    // Similarity match
                    $similarity = 0;
                    similar_text($headerNormalized, $patternNormalized, $similarity);
                    if ($similarity > 70 && $similarity > $bestScore) {
                        $bestMatch = $index;
                        $bestScore = $similarity;
                    }
                }
            }

            if ($bestMatch !== null && $bestScore > 60) {
                $mappings[$dbColumn] = $bestMatch;
                $usedHeaders[] = $bestMatch;
            }
        }

        return $mappings;
    }

    private function parseContactFileWithMappings($file, $columnMappings)
    {
        $contacts = [];
        $path = $file->getPathname();
        
        if (($handle = fopen($path, 'r')) !== false) {
            $headers = fgetcsv($handle);
            
            if ($headers === false || empty($headers)) {
                fclose($handle);
                throw new \Exception('Invalid CSV file: No headers found');
            }
            
            $rowNumber = 1; // Header is row 1, so data starts at row 2

            while (($data = fgetcsv($handle)) !== false) {
                $rowNumber++;
                
                // Skip empty rows
                if (empty(array_filter($data, function($value) {
                    return trim($value) !== '';
                }))) {
                    continue;
                }
                
                // Ensure data array matches header count
                $dataCount = count($data);
                $headerCount = count($headers);
                
                if ($dataCount < $headerCount) {
                    $data = array_pad($data, $headerCount, '');
                } elseif ($dataCount > $headerCount) {
                    $data = array_slice($data, 0, $headerCount);
                }
                
                try {
                    $contactData = [];
                    
                    // Apply column mappings
                    foreach ($columnMappings as $dbColumn => $csvColumnIndex) {
                        if (isset($data[$csvColumnIndex])) {
                            $contactData[$dbColumn] = trim($data[$csvColumnIndex]);
                        }
                    }
                    
                    // Only add if we have at least an email
                    if (!empty($contactData['email'])) {
                        $contacts[] = $contactData;
                    }
                } catch (\Exception $e) {
                    \Log::warning("Failed to parse CSV row {$rowNumber}: " . $e->getMessage());
                }
            }
            fclose($handle);
        }

        return $contacts;
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

    public function publicUnsubscribe($token)
    {
        // For now, we'll create a simple unsubscribe page
        // In a real implementation, you would decode the token to find the contact
        // and handle the unsubscribe process
        
        return view('marketing.unsubscribe', [
            'token' => $token,
            'message' => 'You have been successfully unsubscribed from our mailing list.'
        ]);
    }

    private function parseContactFile($file)
    {
        $contacts = [];
        $path = $file->getPathname();
        
        if (($handle = fopen($path, 'r')) !== false) {
            $headers = fgetcsv($handle);
            
            if ($headers === false || empty($headers)) {
                fclose($handle);
                throw new \Exception('Invalid CSV file: No headers found');
            }
            
            // Clean headers
            $headers = array_map('strtolower', $headers);
            $headers = array_map(function($header) {
                return str_replace(' ', '_', trim($header));
            }, $headers);
            
            $headerCount = count($headers);
            $rowNumber = 1; // Header is row 1, so data starts at row 2

            while (($data = fgetcsv($handle)) !== false) {
                $rowNumber++;
                
                // Skip empty rows
                if (empty(array_filter($data, function($value) {
                    return trim($value) !== '';
                }))) {
                    continue;
                }
                
                $dataCount = count($data);
                
                // Handle mismatched column counts
                if ($dataCount !== $headerCount) {
                    // If fewer columns in data, pad with empty strings
                    if ($dataCount < $headerCount) {
                        $data = array_pad($data, $headerCount, '');
                    } else {
                        // If more columns in data, truncate to match headers
                        $data = array_slice($data, 0, $headerCount);
                    }
                }
                
                try {
                    $contactData = array_combine($headers, $data);
                    
                    // Validate required email field
                    if (!empty($contactData['email']) && filter_var($contactData['email'], FILTER_VALIDATE_EMAIL)) {
                        $contacts[] = $contactData;
                    }
                } catch (\Exception $e) {
                    // Log parsing error but continue processing
                    \Log::warning("Failed to parse CSV row {$rowNumber}: " . $e->getMessage());
                }
            }
            fclose($handle);
        }

        return $contacts;
    }
} 