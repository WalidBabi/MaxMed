<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EmailTemplateController extends Controller
{
    public function index(Request $request)
    {
        $query = EmailTemplate::with(['creator']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('subject', 'LIKE', "%{$search}%");
            });
        }

        // Filter by type (category)
        if ($request->filled('type')) {
            $query->where('category', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'draft') {
                $query->where('is_active', false);
            }
        }

        // Sort options
        $sortField = $request->get('sort', 'updated_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $templates = $query->paginate(25)->withQueryString();

        // Add campaigns count to each template
        foreach ($templates as $template) {
            $template->campaigns_count = $template->campaigns()->count();
        }

        return view('crm.marketing.email-templates.index', compact('templates'));
    }

    public function create()
    {
        $categories = [
            'newsletter' => 'Newsletter',
            'promotional' => 'Promotional',
            'welcome' => 'Welcome',
            'transactional' => 'Transactional',
            'announcement' => 'Announcement',
        ];

        return view('crm.marketing.email-templates.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'subject' => 'nullable|string|max:255',
            'text_content' => 'required|string',
            'category' => 'required|in:newsletter,promotional,welcome,transactional,announcement',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        $bannerImagePath = null;
        if ($request->hasFile('banner_image')) {
            $bannerImagePath = $request->file('banner_image')->store('email-banners', 'public');
        }

        $template = EmailTemplate::create([
            'name' => $request->name,
            'subject' => $request->subject,
            'html_content' => '', // HTML will be auto-generated from text
            'text_content' => $request->text_content,
            'category' => $request->category,
            'banner_image' => $bannerImagePath,
            'is_active' => $request->boolean('is_active', false),
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('crm.marketing.email-templates.show', $template)
                        ->with('success', 'Email template created successfully.');
    }

    public function show(EmailTemplate $emailTemplate)
    {
        $emailTemplate->load(['creator', 'campaigns']);
        
        // Get usage statistics
        $usageStats = [
            'total_campaigns' => $emailTemplate->campaigns()->count(),
            'sent_campaigns' => $emailTemplate->campaigns()->where('status', 'sent')->count(),
            'last_used' => $emailTemplate->campaigns()->latest()->first()?->created_at,
        ];

        return view('crm.marketing.email-templates.show', compact('emailTemplate', 'usageStats'));
    }

    public function edit(EmailTemplate $emailTemplate)
    {
        $categories = [
            'newsletter' => 'Newsletter',
            'promotional' => 'Promotional',
            'welcome' => 'Welcome',
            'transactional' => 'Transactional',
            'announcement' => 'Announcement',
        ];

        return view('crm.marketing.email-templates.edit', compact('emailTemplate', 'categories'));
    }

    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'subject' => 'nullable|string|max:255',
            'text_content' => 'required|string',
            'category' => 'required|in:newsletter,promotional,welcome,transactional,announcement',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        $updateData = [
            'name' => $request->name,
            'subject' => $request->subject,
            'html_content' => '', // HTML will be auto-generated from text
            'text_content' => $request->text_content,
            'category' => $request->category,
            'is_active' => $request->boolean('is_active', $emailTemplate->is_active),
        ];

        // Handle banner image upload
        if ($request->hasFile('banner_image')) {
            // Delete old banner image if exists
            if ($emailTemplate->banner_image && Storage::disk('public')->exists($emailTemplate->banner_image)) {
                Storage::disk('public')->delete($emailTemplate->banner_image);
            }
            
            $updateData['banner_image'] = $request->file('banner_image')->store('email-banners', 'public');
        }

        $emailTemplate->update($updateData);

        return redirect()->route('crm.marketing.email-templates.show', $emailTemplate)
                        ->with('success', 'Email template updated successfully.');
    }

    public function destroy(EmailTemplate $emailTemplate)
    {
        // Check if template is being used in any campaigns
        $campaignsCount = $emailTemplate->campaigns()->count();
        
        if ($campaignsCount > 0) {
            return redirect()->back()
                           ->with('error', "Cannot delete template that is being used in {$campaignsCount} campaign(s).");
        }

        $emailTemplate->delete();

        return redirect()->route('crm.marketing.email-templates.index')
                        ->with('success', 'Email template deleted successfully.');
    }

    public function clone(Request $request, EmailTemplate $emailTemplate)
    {
        $newName = $request->input('name', $emailTemplate->name . ' (Copy)');
        
        // Ensure unique name
        $counter = 1;
        $baseName = $newName;
        while (EmailTemplate::where('name', $newName)->exists()) {
            $newName = $baseName . ' (' . $counter . ')';
            $counter++;
        }

        $clonedTemplate = $emailTemplate->clone($newName);

        return redirect()->route('crm.marketing.email-templates.edit', $clonedTemplate)
                        ->with('success', 'Email template cloned successfully.');
    }

    public function preview(Request $request, EmailTemplate $emailTemplate)
    {
        // Sample data for preview
        $sampleData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'full_name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'phone' => '+1 (555) 123-4567',
            'company' => 'MaxMed Solutions',
            'job_title' => 'Laboratory Manager',
            'industry' => 'Healthcare',
            'country' => 'United States',
            'city' => 'New York',
            'unsubscribe_url' => '#unsubscribe-link',
            'company_name' => config('app.name', 'MaxMed'),
            'company_address' => '123 Business Street, City, State 12345',
            'current_date' => now()->format('F j, Y'),
            'current_year' => now()->year,
        ];

        $previewData = [
            'subject' => $emailTemplate->renderSubject($sampleData),
            'html_content' => $emailTemplate->renderHtmlContent($sampleData),
            'text_content' => $emailTemplate->renderTextContent($sampleData),
        ];

        if ($request->wantsJson()) {
            return response()->json($previewData);
        }

        return view('crm.marketing.email-templates.preview', compact('emailTemplate', 'previewData'));
    }

    public function toggleStatus(EmailTemplate $emailTemplate)
    {
        $emailTemplate->update([
            'is_active' => !$emailTemplate->is_active
        ]);

        $status = $emailTemplate->is_active ? 'activated' : 'deactivated';
        
        return redirect()->back()
                        ->with('success', "Email template {$status} successfully.");
    }

    public function export(EmailTemplate $emailTemplate)
    {
        $data = [
            'name' => $emailTemplate->name,
            'subject' => $emailTemplate->subject,
            'html_content' => $emailTemplate->html_content,
            'text_content' => $emailTemplate->text_content,
            'category' => $emailTemplate->category,
            'variables' => $emailTemplate->variables,
            'exported_at' => now()->toISOString(),
        ];

        $filename = Str::slug($emailTemplate->name) . '-template.json';
        
        return response()->json($data)
                         ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'template_file' => 'required|file|mimes:json',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator);
        }

        try {
            $file = $request->file('template_file');
            $content = file_get_contents($file->getPathname());
            $data = json_decode($content, true);

            if (!$data || !isset($data['name'], $data['subject'], $data['html_content'])) {
                throw new \Exception('Invalid template file format.');
            }

            // Ensure unique name
            $name = $data['name'];
            $counter = 1;
            $baseName = $name;
            while (EmailTemplate::where('name', $name)->exists()) {
                $name = $baseName . ' (Imported ' . $counter . ')';
                $counter++;
            }

            $template = EmailTemplate::create([
                'name' => $name,
                'subject' => $data['subject'],
                'html_content' => $data['html_content'] ?: '',
                'text_content' => $data['text_content'] ?? '',
                'category' => $data['category'] ?? 'newsletter',
                'variables' => $data['variables'] ?? [],
                'is_active' => false, // Start as inactive
                'created_by' => auth()->id(),
            ]);

            return redirect()->route('crm.marketing.email-templates.show', $template)
                           ->with('success', 'Email template imported successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Failed to import template: ' . $e->getMessage());
        }
    }
} 