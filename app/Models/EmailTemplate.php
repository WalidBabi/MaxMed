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
        return $this->replaceVariables($this->html_content, $data);
    }

    public function renderTextContent(array $data = []): string
    {
        if (!$this->text_content) {
            return strip_tags($this->renderHtmlContent($data));
        }
        
        return $this->replaceVariables($this->text_content, $data);
    }

    protected function replaceVariables(string $content, array $data): string
    {
        // Replace variables in the format {{variable_name}}
        foreach ($data as $key => $value) {
            $content = str_replace('{{' . $key . '}}', $value, $content);
        }

        // Handle contact-specific variables
        if (isset($data['contact']) && $data['contact'] instanceof MarketingContact) {
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
            'html_content' => $this->html_content,
            'text_content' => $this->text_content,
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
} 