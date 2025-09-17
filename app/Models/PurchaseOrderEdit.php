<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderEdit extends Model
{
    protected $fillable = [
        'purchase_order_id',
        'edited_by',
        'field_name',
        'old_value',
        'new_value',
        'po_status_when_edited',
        'edit_reason'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the purchase order that was edited
     */
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    /**
     * Get the user who made the edit
     */
    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'edited_by');
    }

    /**
     * Get a formatted display of the change
     */
    public function getChangeDescriptionAttribute(): string
    {
        $fieldName = str_replace('_', ' ', $this->field_name);
        $fieldName = ucwords($fieldName);
        
        if (empty($this->old_value) && !empty($this->new_value)) {
            return "Added {$fieldName}: {$this->new_value}";
        } elseif (!empty($this->old_value) && empty($this->new_value)) {
            return "Removed {$fieldName}: {$this->old_value}";
        } else {
            return "Changed {$fieldName} from '{$this->old_value}' to '{$this->new_value}'";
        }
    }
}
