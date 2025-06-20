<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'subject',
        'html_content',
        'text_content',
        'variables',
        'category',
        'banner_image',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
    ];

    protected $appends = ['type', 'status'];

    // Accessor methods for compatibility with views
    public function getTypeAttribute()
    {
        return $this->category;
    }

    public function getStatusAttribute()
    {
        if ($this->is_active) {
            return 'active';
        }
        return 'draft';
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    public function renderSubject(array $data = []): string
    {
        return $this->replaceVariables($this->subject, $data);
    }

    public function renderHtmlContent(array $data = []): string
    {
        // If html_content exists, use it, otherwise generate from text_content
        if (!empty($this->html_content)) {
            return $this->replaceVariables($this->html_content, $data);
        }

        // Auto-generate HTML from text content
        $textContent = $this->replaceVariables($this->text_content, $data);
        
        // Convert text to HTML with proper formatting
        $htmlContent = $this->convertTextToHtml($textContent);
        
        return $htmlContent;
    }

    public function renderTextContent(array $data = []): string
    {
        // Always use text content since we're now focusing on plain text emails
        return $this->replaceVariables($this->text_content, $data);
    }

    public function renderContent(array $data = []): string
    {
        // Primary content is now text content
        return $this->renderTextContent($data);
    }

    protected function replaceVariables(string $content, array $data): string
    {
        // Replace variables in the format {{variable_name}}
        foreach ($data as $key => $value) {
            $content = str_replace('{{' . $key . '}}', $value, $content);
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

        // Add default values for remaining variables
        $content = preg_replace('/\{\{([^}]+)\}\}/', '', $content);

        return $content;
    }

    public function getAvailableVariables(): array
    {
        $defaultVariables = [
            'first_name' => 'Contact\'s first name',
            'last_name' => 'Contact\'s last name',
            'full_name' => 'Contact\'s full name',
            'email' => 'Contact\'s email address',
            'phone' => 'Contact\'s phone number',
            'company' => 'Contact\'s company',
            'job_title' => 'Contact\'s job title',
            'industry' => 'Contact\'s industry',
            'country' => 'Contact\'s country',
            'city' => 'Contact\'s city',
            'unsubscribe_url' => 'Unsubscribe link',
            'company_name' => 'Your company name',
            'company_address' => 'Your company address',
            'current_date' => 'Current date',
            'current_year' => 'Current year',
        ];

        return array_merge($defaultVariables, $this->variables ?: []);
    }

    public function clone(string $newName): static
    {
        return static::create([
            'name' => $newName,
            'subject' => $this->subject,
            'html_content' => $this->html_content ?: '',
            'text_content' => $this->text_content ?: '',
            'variables' => $this->variables,
            'category' => $this->category,
            'is_active' => false, // New template starts as inactive
            'created_by' => auth()->id(),
        ]);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    protected function convertTextToHtml(string $textContent): string
    {
        // Start with basic HTML structure
        $html = '<html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head><body style="font-family: Arial, sans-serif; font-size: 16px; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">';
        
        // Process the text content
        $content = htmlspecialchars($textContent, ENT_QUOTES, 'UTF-8');
        
        // Convert line breaks to <br> tags
        $content = nl2br($content);
        
        // Convert email addresses to clickable links
        $content = preg_replace('/([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/', '<a href="mailto:$1" style="color: #007cba; text-decoration: none;">$1</a>', $content);
        
        // Convert URLs to clickable links
        $content = preg_replace('/(https?:\/\/[^\s]+)/', '<a href="$1" style="color: #007cba; text-decoration: none;" target="_blank">$1</a>', $content);
        $content = preg_replace('/(www\.[^\s]+)/', '<a href="http://$1" style="color: #007cba; text-decoration: none;" target="_blank">$1</a>', $content);
        
        // Convert phone numbers to clickable links
        $content = preg_replace('/(\+?[\d\s\-\(\)]{10,})/', '<a href="tel:$1" style="color: #007cba; text-decoration: none;">$1</a>', $content);
        
        // Add the content to HTML
        $html .= '<div style="padding: 20px 0;">' . $content . '</div>';
        
        // Add banner image at the end if exists
        if ($this->banner_image) {
            $bannerUrl = asset('storage/' . $this->banner_image);
            $html .= '<div style="text-align: center; margin-top: 30px; margin-bottom: 20px;"><img src="' . $bannerUrl . '" alt="' . config('app.name') . '" style="max-width: 100%; height: auto;"></div>';
        }
        
        // Add footer
        $html .= '<div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #eee; font-size: 14px; color: #666; text-align: center;">';
        $html .= '<p>This email was sent by ' . config('app.name') . '</p>';
        $html .= '<p><a href="{{unsubscribe_url}}" style="color: #007cba; text-decoration: none;">Unsubscribe</a></p>';
        $html .= '</div>';
        
        $html .= '</body></html>';
        
        return $html;
    }
} 