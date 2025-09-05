<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use App\Notifications\ContactSubmissionNotification;

class ContactSubmission extends Model
{
    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'phone',
        'company',
        'status',
        'converted_to_inquiry_id',
        'assigned_to',
        'admin_notes',
        'responded_at',
        'lead_potential',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        // Send notification when a new contact submission is created
        static::created(function ($submission) {
            $submission->sendNewSubmissionNotification();
        });
    }

    /**
     * Send notification to admin/CRM users about new contact submission
     */
    public function sendNewSubmissionNotification()
    {
        try {
            Log::info('Attempting to send contact submission notification for submission: ' . $this->id);
            
            // Find all admin and CRM users in database
            $users = User::where(function($query) {
                $query->whereHas('role', function($q) {
                    $q->where('name', 'admin');
                })
                      ->orWhereHas('role', function($roleQuery) {
                          $roleQuery->whereIn('name', ['admin', 'crm']);
                      });
            })
            ->whereNotNull('email')
            ->whereDoesntHave('role', function($query) {
                $query->where('name', 'supplier');
            })
            ->get();

            Log::info('Found ' . $users->count() . ' admin/CRM users for notification');

            if ($users->count() > 0) {
                Notification::send($users, new ContactSubmissionNotification($this));
                Log::info('Contact submission notification sent to ' . $users->count() . ' user(s) for submission: ' . $this->id);
                
                // Log each user that received the notification
                foreach ($users as $user) {
                    Log::info('Notification sent to user: ' . $user->email . ' (ID: ' . $user->id . ')');
                }
            } else {
                Log::warning('No admin/CRM users found for contact submission notification');
                
                // Try to find ANY users for debugging
                $allUsers = User::all();
                Log::info('Total users in database: ' . $allUsers->count());
                foreach ($allUsers as $user) {
                    Log::info('User found: ' . $user->email . ' (is_admin: ' . ($user->isAdmin() ? 'true' : 'false') . ', role: ' . ($user->role ? $user->role->name : 'none') . ')');
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to send contact submission notification: ' . $e->getMessage());
            Log::error('Exception details: ' . $e->getTraceAsString());
        }
    }

    /**
     * Get the quotation request this was converted to
     */
    public function convertedToInquiry(): BelongsTo
    {
        return $this->belongsTo(QuotationRequest::class, 'converted_to_inquiry_id');
    }

    /**
     * Get the admin user assigned to handle this submission
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Check if this is a sales inquiry
     */
    public function isSalesInquiry(): bool
    {
        return strtolower($this->subject) === 'sales inquiry' || 
               str_contains(strtolower($this->subject), 'sales') ||
               str_contains(strtolower($this->message), 'quote') ||
               str_contains(strtolower($this->message), 'price');
    }

    /**
     * Check if this can be converted to inquiry
     */
    public function canConvertToInquiry(): bool
    {
        return $this->status === 'new' || $this->status === 'in_review';
    }

    /**
     * Check if this can be converted to lead
     */
    public function canConvertToLead(): bool
    {
        return in_array($this->status, ['new', 'in_review']);
    }

    /**
     * Get status badge class for UI
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'new' => 'bg-blue-100 text-blue-800',
            'in_review' => 'bg-yellow-100 text-yellow-800',
            'converted_to_lead' => 'bg-indigo-100 text-indigo-800',
            'converted_to_inquiry' => 'bg-green-100 text-green-800',
            'responded' => 'bg-purple-100 text-purple-800',
            'closed' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get formatted status
     */
    public function getFormattedStatusAttribute(): string
    {
        return match($this->status) {
            'new' => 'New',
            'in_review' => 'In Review',
            'converted_to_lead' => 'Converted to Lead',
            'converted_to_inquiry' => 'Converted to Inquiry',
            'responded' => 'Responded',
            'closed' => 'Closed',
            default => ucfirst($this->status)
        };
    }

    /**
     * Scope to get new submissions
     */
    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    /**
     * Scope to get sales inquiries
     */
    public function scopeSalesInquiries($query)
    {
        return $query->where('subject', 'sales inquiry')
                    ->orWhere('subject', 'like', '%sales%')
                    ->orWhere('message', 'like', '%quote%')
                    ->orWhere('message', 'like', '%price%');
    }

    /**
     * Scope to get submissions by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
} 