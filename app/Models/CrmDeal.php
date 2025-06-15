<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmDeal extends Model
{
    use HasFactory;

    protected $fillable = [
        'deal_name',
        'lead_id',
        'deal_value',
        'stage',
        'probability',
        'expected_close_date',
        'actual_close_date',
        'description',
        'products_interested',
        'assigned_to',
        'loss_reason',
    ];

    protected $casts = [
        'deal_value' => 'decimal:2',
        'expected_close_date' => 'date',
        'actual_close_date' => 'date',
        'products_interested' => 'array',
    ];

    // Relationships
    public function lead()
    {
        return $this->belongsTo(CrmLead::class, 'lead_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Accessors
    public function getStageColorAttribute()
    {
        return match($this->stage) {
            'prospecting' => 'blue',
            'qualification' => 'cyan',
            'needs_analysis' => 'purple',
            'proposal' => 'orange',
            'negotiation' => 'yellow',
            'closed_won' => 'green',
            'closed_lost' => 'red',
            default => 'gray'
        };
    }

    public function getWeightedValueAttribute()
    {
        return $this->deal_value * ($this->probability / 100);
    }

    public function getDaysToCloseAttribute()
    {
        if ($this->actual_close_date) {
            return null;
        }
        return now()->diffInDays($this->expected_close_date, false);
    }

    // Methods
    public function isOverdue()
    {
        return !$this->actual_close_date && 
               $this->expected_close_date < now()->toDateString() &&
               !in_array($this->stage, ['closed_won', 'closed_lost']);
    }

    public function markAsWon($closeDate = null)
    {
        $this->update([
            'stage' => 'closed_won',
            'probability' => 100,
            'actual_close_date' => $closeDate ?? now()->toDateString(),
        ]);

        // Update lead status
        $this->lead->update(['status' => 'won']);
    }

    public function markAsLost($reason = null, $closeDate = null)
    {
        $this->update([
            'stage' => 'closed_lost',
            'probability' => 0,
            'actual_close_date' => $closeDate ?? now()->toDateString(),
            'loss_reason' => $reason,
        ]);

        // Update lead status
        $this->lead->update(['status' => 'lost']);
    }

    // Scopes
    public function scopeOpen($query)
    {
        return $query->whereNotIn('stage', ['closed_won', 'closed_lost']);
    }

    public function scopeWon($query)
    {
        return $query->where('stage', 'closed_won');
    }

    public function scopeLost($query)
    {
        return $query->where('stage', 'closed_lost');
    }

    public function scopeOverdue($query)
    {
        return $query->open()
                    ->where('expected_close_date', '<', now()->toDateString());
    }

    public function scopeByStage($query, $stage)
    {
        return $query->where('stage', $stage);
    }
} 