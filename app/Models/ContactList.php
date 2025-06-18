<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ContactList extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'criteria',
        'type',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'criteria' => 'array',
        'is_active' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function contacts(): BelongsToMany
    {
        return $this->belongsToMany(MarketingContact::class, 'contact_list_contacts')
                    ->withTimestamps()
                    ->withPivot('added_at');
    }

    public function activeContacts(): BelongsToMany
    {
        return $this->contacts()->where('marketing_contacts.status', 'active');
    }

    public function isStatic(): bool
    {
        return $this->type === 'static';
    }

    public function isDynamic(): bool
    {
        return $this->type === 'dynamic';
    }

    public function getContactsCount(): int
    {
        if ($this->isDynamic()) {
            return $this->getDynamicContacts()->count();
        }
        
        return $this->contacts()->count();
    }

    public function getActiveContactsCount(): int
    {
        if ($this->isDynamic()) {
            return $this->getDynamicContacts()->where('status', 'active')->count();
        }
        
        return $this->activeContacts()->count();
    }

    public function getDynamicContacts()
    {
        if (!$this->isDynamic() || !$this->criteria) {
            return collect();
        }

        $query = MarketingContact::query();

        foreach ($this->criteria as $criterion) {
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
                    case 'starts_with':
                        $query->where($field, 'LIKE', $value . '%');
                        break;
                    case 'ends_with':
                        $query->where($field, 'LIKE', '%' . $value);
                        break;
                    case 'in':
                        if (is_array($value)) {
                            $query->whereIn($field, $value);
                        }
                        break;
                    case 'not_in':
                        if (is_array($value)) {
                            $query->whereNotIn($field, $value);
                        }
                        break;
                }
            }
        }

        return $query;
    }

    public function refreshDynamicContacts(): void
    {
        if (!$this->isDynamic()) {
            return;
        }

        // Clear existing contacts for dynamic lists
        $this->contacts()->detach();

        // Add contacts based on criteria
        $contacts = $this->getDynamicContacts()->get();
        
        $contactIds = $contacts->pluck('id')->toArray();
        $this->contacts()->attach($contactIds, ['added_at' => now()]);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeStatic($query)
    {
        return $query->where('type', 'static');
    }

    public function scopeDynamic($query)
    {
        return $query->where('type', 'dynamic');
    }
} 