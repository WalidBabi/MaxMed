<?php

namespace App\Traits;

use Carbon\Carbon;

trait DubaiDateFormat
{
    /**
     * Convert a date to Dubai timezone and format it
     */
    public function formatForDubai($attribute, $format = 'M d, Y H:i')
    {
        if (!$this->$attribute) {
            return null;
        }
        
        return $this->$attribute->setTimezone('Asia/Dubai')->format($format);
    }
    
    /**
     * Get created_at in Dubai timezone
     */
    public function getCreatedAtDubaiAttribute()
    {
        return $this->created_at ? $this->created_at->setTimezone('Asia/Dubai') : null;
    }
    
    /**
     * Get updated_at in Dubai timezone
     */
    public function getUpdatedAtDubaiAttribute()
    {
        return $this->updated_at ? $this->updated_at->setTimezone('Asia/Dubai') : null;
    }
    
    /**
     * Get deleted_at in Dubai timezone
     */
    public function getDeletedAtDubaiAttribute()
    {
        return $this->deleted_at ? $this->deleted_at->setTimezone('Asia/Dubai') : null;
    }
    
    /**
     * Format created_at for display in Dubai timezone
     */
    public function createdAtDubai($format = 'M d, Y H:i')
    {
        return $this->formatForDubai('created_at', $format);
    }
    
    /**
     * Format updated_at for display in Dubai timezone
     */
    public function updatedAtDubai($format = 'M d, Y H:i')
    {
        return $this->formatForDubai('updated_at', $format);
    }
    
    /**
     * Get human readable time difference in Dubai timezone
     */
    public function createdAtDubaiForHumans()
    {
        if (!$this->created_at) {
            return null;
        }
        
        return $this->created_at->setTimezone('Asia/Dubai')->diffForHumans();
    }
    
    /**
     * Get human readable time difference in Dubai timezone for updated_at
     */
    public function updatedAtDubaiForHumans()
    {
        if (!$this->updated_at) {
            return null;
        }
        
        return $this->updated_at->setTimezone('Asia/Dubai')->diffForHumans();
    }
} 