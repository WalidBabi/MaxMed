<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CrmLead extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'company_name',
        'job_title',
        'company_address',
        'status',
        'source',
        'priority',
        'estimated_value',
        'notes',
        'expected_close_date',
        'last_contacted_at',
        'assigned_to',
    ];

    protected $casts = [
        'estimated_value' => 'decimal:2',
        'expected_close_date' => 'date',
        'last_contacted_at' => 'datetime',
    ];

    // Relationships
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function activities()
    {
        return $this->hasMany(CrmActivity::class, 'lead_id')->orderBy('activity_date', 'desc');
    }

    public function deals()
    {
        return $this->hasMany(CrmDeal::class, 'lead_id');
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'new' => 'blue',
            'contacted' => 'yellow',
            'qualified' => 'purple',
            'proposal' => 'orange',
            'negotiation' => 'indigo',
            'won' => 'green',
            'lost' => 'red',
            default => 'gray'
        };
    }

    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'red',
            default => 'gray'
        };
    }

    // Methods
    public function daysSinceLastContact()
    {
        if (!$this->last_contacted_at) {
            return $this->created_at->diffInDays(now());
        }
        return $this->last_contacted_at->diffInDays(now());
    }

    public function isOverdue()
    {
        return $this->daysSinceLastContact() > 7; // Consider overdue if no contact in 7 days
    }

    public function logActivity($type, $subject, $description = null, $activityDate = null)
    {
        return $this->activities()->create([
            'user_id' => auth()->id(),
            'type' => $type,
            'subject' => $subject,
            'description' => $description,
            'activity_date' => $activityDate ?? now(),
        ]);
    }

    public function updateLastContacted()
    {
        $this->update(['last_contacted_at' => now()]);
    }

    // Scopes
    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeOverdue($query)
    {
        return $query->where(function($q) {
            $q->where('last_contacted_at', '<', now()->subDays(7))
              ->orWhereNull('last_contacted_at');
        })->where('status', '!=', 'won')->where('status', '!=', 'lost');
    }

    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high');
    }
} 