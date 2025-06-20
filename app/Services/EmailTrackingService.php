<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\MarketingContact;
use App\Models\EmailLog;

class EmailTrackingService
{
    /**
     * Generate tracking pixel URL for email opens
     */
    public function generateTrackingPixelUrl(Campaign $campaign, MarketingContact $contact, EmailLog $emailLog): string
    {
        $trackingId = base64_encode($campaign->id . '|' . $contact->id . '|' . $emailLog->id);
        return route('email.track.open', ['trackingId' => $trackingId]);
    }
    
    /**
     * Generate trackable click URL
     */
    public function generateClickTrackingUrl(Campaign $campaign, MarketingContact $contact, EmailLog $emailLog, string $originalUrl): string
    {
        $trackingId = base64_encode(
            $campaign->id . '|' . 
            $contact->id . '|' . 
            $emailLog->id . '|' . 
            base64_encode($originalUrl)
        );
        
        return route('email.track.click', ['trackingId' => $trackingId]);
    }
    
    /**
     * Generate unsubscribe URL with campaign tracking
     */
    public function generateUnsubscribeUrl(MarketingContact $contact, Campaign $campaign = null): string
    {
        $token = $contact->id . '|' . $contact->email;
        
        if ($campaign) {
            $token .= '|' . $campaign->id;
        }
        
        return route('email.track.unsubscribe', ['token' => base64_encode($token)]);
    }
    
    /**
     * Add tracking pixel to HTML content
     */
    public function addTrackingPixelToHtml(string $htmlContent, string $trackingPixelUrl): string
    {
        $trackingPixel = '<img src="' . $trackingPixelUrl . '" width="1" height="1" alt="" style="display:none;" />';
        return $htmlContent . $trackingPixel;
    }
    
    /**
     * Make links in HTML content trackable
     */
    public function makeLinksTrackable(string $htmlContent, Campaign $campaign, MarketingContact $contact, EmailLog $emailLog): string
    {
        // Pattern to match href attributes
        $pattern = '/href=["\'](https?:\/\/[^"\']+)["\']/i';
        
        return preg_replace_callback($pattern, function($matches) use ($campaign, $contact, $emailLog) {
            $originalUrl = $matches[1];
            
            // Don't track unsubscribe links
            if (strpos($originalUrl, 'unsubscribe') !== false) {
                return $matches[0];
            }
            
            // Generate tracking URL
            $trackingUrl = $this->generateClickTrackingUrl($campaign, $contact, $emailLog, $originalUrl);
            
            return 'href="' . $trackingUrl . '"';
        }, $htmlContent);
    }
    
    /**
     * Process HTML content with full tracking
     */
    public function processHtmlContentForTracking(
        string $htmlContent, 
        Campaign $campaign, 
        MarketingContact $contact, 
        EmailLog $emailLog
    ): string {
        // Add click tracking to links
        $processedHtml = $this->makeLinksTrackable($htmlContent, $campaign, $contact, $emailLog);
        
        // Add tracking pixel
        $trackingPixelUrl = $this->generateTrackingPixelUrl($campaign, $contact, $emailLog);
        $processedHtml = $this->addTrackingPixelToHtml($processedHtml, $trackingPixelUrl);
        
        return $processedHtml;
    }
} 