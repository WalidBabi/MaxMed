<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadPriceSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'crm_lead_id',
        'user_id',
        'price',
        'currency',
        'notes',
        'attachments',
        'status',
        'submitted_at',
    ];

    protected $casts = [
        'attachments' => 'array',
        'submitted_at' => 'datetime',
        'price' => 'decimal:2',
    ];

    /**
     * Get the lead that owns this price submission
     */
    public function lead(): BelongsTo
    {
        return $this->belongsTo(CrmLead::class, 'crm_lead_id');
    }

    /**
     * Get the user who submitted this price
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get attachments grouped by type
     */
    public function getAttachmentsByType()
    {
        if (!$this->attachments) {
            return [];
        }

        $grouped = [];
        foreach ($this->attachments as $attachment) {
            $extension = strtolower(pathinfo($attachment['original_name'], PATHINFO_EXTENSION));
            
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $grouped['images'][] = $attachment;
            } elseif (in_array($extension, ['pdf'])) {
                $grouped['pdfs'][] = $attachment;
            } elseif (in_array($extension, ['doc', 'docx', 'xls', 'xlsx'])) {
                $grouped['documents'][] = $attachment;
            } else {
                $grouped['others'][] = $attachment;
            }
        }
        
        return $grouped;
    }

    /**
     * Add an attachment to this price submission
     */
    public function addAttachment($filePath, $originalName, $size = null, $mimeType = null)
    {
        $attachments = $this->attachments ?: [];
        $attachments[] = [
            'path' => $filePath,
            'original_name' => $originalName,
            'size' => $size,
            'mime_type' => $mimeType,
            'uploaded_at' => now()->toISOString(),
        ];
        
        $this->attachments = $attachments;
        return $this;
    }

    /**
     * Get formatted price with currency
     */
    public function getFormattedPriceAttribute()
    {
        return $this->currency . ' ' . number_format($this->price, 2);
    }
}
