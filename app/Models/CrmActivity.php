<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'user_id',
        'type',
        'subject',
        'description',
        'activity_date',
        'status',
        'due_date',
        'metadata',
    ];

    protected $casts = [
        'activity_date' => 'datetime',
        'due_date' => 'datetime',
        'metadata' => 'array',
    ];

    // Relationships
    public function lead()
    {
        return $this->belongsTo(CrmLead::class, 'lead_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessors
    public function getTypeColorAttribute()
    {
        return match($this->type) {
            'call' => 'blue',
            'email' => 'green',
            'meeting' => 'purple',
            'note' => 'gray',
            'quote_sent' => 'orange',
            'demo' => 'indigo',
            'follow_up' => 'yellow',
            'task' => 'red',
            default => 'gray'
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'completed' => 'green',
            'scheduled' => 'blue',
            'overdue' => 'red',
            default => 'gray'
        };
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'scheduled')
                    ->where('due_date', '<', now());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', 'scheduled')
                    ->where('due_date', '>=', now())
                    ->orderBy('due_date', 'asc');
    }
} 