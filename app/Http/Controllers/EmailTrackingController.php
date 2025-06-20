<?php

namespace App\Http\Controllers;

use App\Models\EmailLog;
use App\Models\Campaign;
use App\Models\MarketingContact;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class EmailTrackingController extends Controller
{
    /**
     * Track email opens via tracking pixel
     */
    public function trackOpen(Request $request, $trackingId)
    {
        try {
            // Decode the tracking ID
            $decoded = base64_decode($trackingId);
            
            if (!$decoded) {
                return $this->generateTrackingPixel();
            }
            
            $parts = explode('|', $decoded);
            
            if (count($parts) < 3) {
                return $this->generateTrackingPixel();
            }
            
            $campaignId = $parts[0];
            $contactId = $parts[1];
            $emailLogId = $parts[2];
            
            // Find the email log
            $emailLog = EmailLog::where('id', $emailLogId)
                              ->where('campaign_id', $campaignId)
                              ->where('marketing_contact_id', $contactId)
                              ->first();
            
            if ($emailLog && !$emailLog->opened_at) {
                // Mark as opened
                $emailLog->markAsOpened(
                    $request->ip(),
                    $request->userAgent()
                );
                
                // Update campaign statistics
                $campaign = Campaign::find($campaignId);
                if ($campaign) {
                    $campaign->updateStatistics();
                }
                
                Log::info('Email opened', [
                    'campaign_id' => $campaignId,
                    'contact_id' => $contactId,
                    'email_log_id' => $emailLogId,
                    'ip' => $request->ip()
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Email open tracking error: ' . $e->getMessage());
        }
        
        return $this->generateTrackingPixel();
    }
    
    /**
     * Track email clicks
     */
    public function trackClick(Request $request, $trackingId)
    {
        try {
            // Decode the tracking ID
            $decoded = base64_decode($trackingId);
            
            if (!$decoded) {
                Log::warning('Email click tracking: Invalid tracking ID', ['trackingId' => $trackingId]);
                return redirect('/');
            }
            
            $parts = explode('|', $decoded);
            
            if (count($parts) < 4) {
                Log::warning('Email click tracking: Malformed tracking ID', ['parts_count' => count($parts)]);
                return redirect('/');
            }
            
            $campaignId = $parts[0];
            $contactId = $parts[1];
            $emailLogId = $parts[2];
            $originalUrl = base64_decode($parts[3]);
            
            // Validate URL
            if (!$originalUrl || !filter_var($originalUrl, FILTER_VALIDATE_URL)) {
                Log::warning('Email click tracking: Invalid original URL', ['originalUrl' => $originalUrl]);
                return redirect('/');
            }
            
            // Find the email log
            $emailLog = EmailLog::where('id', $emailLogId)
                              ->where('campaign_id', $campaignId)
                              ->where('marketing_contact_id', $contactId)
                              ->first();
            
            if (!$emailLog) {
                Log::warning('Email click tracking: Email log not found', [
                    'campaign_id' => $campaignId,
                    'contact_id' => $contactId,
                    'email_log_id' => $emailLogId
                ]);
                return redirect($originalUrl);
            }
            
            // Mark as clicked (this also marks as opened if not already)
            $emailLog->markAsClicked(
                $request->ip(),
                $request->userAgent()
            );
            
            // Update campaign statistics
            $campaign = Campaign::find($campaignId);
            if ($campaign) {
                $campaign->updateStatistics();
                Log::info('Campaign statistics updated after click', [
                    'campaign_id' => $campaignId,
                    'clicked_count' => $campaign->clicked_count
                ]);
            }
            
            Log::info('Email clicked successfully', [
                'campaign_id' => $campaignId,
                'contact_id' => $contactId,
                'email_log_id' => $emailLogId,
                'url' => $originalUrl,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            // Redirect to the original URL
            return redirect($originalUrl);
            
        } catch (\Exception $e) {
            Log::error('Email click tracking error: ' . $e->getMessage(), [
                'trackingId' => $trackingId,
                'exception' => $e->getTraceAsString()
            ]);
            return redirect('/');
        }
    }
    
    /**
     * Handle unsubscribe with tracking
     */
    public function trackUnsubscribe(Request $request, $token)
    {
        try {
            // Decode the token
            $decoded = base64_decode($token);
            $parts = explode('|', $decoded);
            
            if (count($parts) < 2) {
                return view('marketing.unsubscribe', ['error' => 'Invalid unsubscribe link.']);
            }
            
            $contactId = $parts[0];
            $email = $parts[1];
            
            // Extract campaign ID if available (for tracking unsubscribe by campaign)
            $campaignId = count($parts) >= 3 ? $parts[2] : null;
            
            // Find the contact
            $contact = MarketingContact::where('id', $contactId)
                                     ->where('email', $email)
                                     ->first();
            
            if (!$contact) {
                return view('marketing.unsubscribe', ['error' => 'Contact not found.']);
            }
            
            if ($contact->status === 'unsubscribed') {
                return view('marketing.unsubscribe', ['message' => 'You are already unsubscribed from our emails.']);
            }
            
            // Unsubscribe the contact
            $contact->update([
                'status' => 'unsubscribed',
                'unsubscribed_at' => now()
            ]);
            
            // Update campaign statistics if this unsubscribe is campaign-specific
            if ($campaignId) {
                $campaign = Campaign::find($campaignId);
                if ($campaign) {
                    $campaign->increment('unsubscribed_count');
                    $campaign->updateStatistics();
                }
            }
            
            Log::info('Contact unsubscribed via tracking', [
                'contact_id' => $contact->id,
                'email' => $contact->email,
                'campaign_id' => $campaignId
            ]);
            
            return view('marketing.unsubscribe', [
                'success' => 'You have been successfully unsubscribed from our emails.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Unsubscribe tracking error: ' . $e->getMessage());
            return view('marketing.unsubscribe', ['error' => 'An error occurred while processing your request.']);
        }
    }
    
    /**
     * Generate a 1x1 transparent tracking pixel
     */
    private function generateTrackingPixel()
    {
        // 1x1 transparent GIF
        $pixel = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
        
        return response($pixel, 200, [
            'Content-Type' => 'image/gif',
            'Content-Length' => strlen($pixel),
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }
} 