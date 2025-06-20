<?php

namespace App\Http\Controllers;

use App\Models\MarketingContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MarketingController extends Controller
{
    public function unsubscribe($token)
    {
        try {
            // Decode the token
            $decoded = base64_decode($token);
            $parts = explode('|', $decoded);
            
            if (count($parts) !== 3) {
                return view('marketing.unsubscribe', ['error' => 'Invalid unsubscribe link.']);
            }
            
            $contactId = $parts[0];
            $email = $parts[1];
            
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
            
            Log::info('Contact unsubscribed', [
                'contact_id' => $contact->id,
                'email' => $contact->email
            ]);
            
            return view('marketing.unsubscribe', ['success' => 'You have been successfully unsubscribed from our emails.']);
            
        } catch (\Exception $e) {
            Log::error('Unsubscribe error: ' . $e->getMessage());
            return view('marketing.unsubscribe', ['error' => 'An error occurred while processing your request.']);
        }
    }
} 