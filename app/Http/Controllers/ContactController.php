<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\ContactFormMail;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use GuzzleHttp\Client;

class ContactController extends Controller
{
    public function submit(Request $request)
    {
        // Standard reCAPTCHA verification
        $recaptchaSecretKey = env('RECAPTCHA_SECRET_KEY');
        $recaptchaResponse = $request->input('g-recaptcha-response');
        
        $recaptchaValid = false;
        if ($recaptchaResponse) {
            $client = new Client();
            $response = $client->post('https://www.google.com/recaptcha/api/siteverify', [
                'form_params' => [
                    'secret' => $recaptchaSecretKey,
                    'response' => $recaptchaResponse,
                    'remoteip' => $request->ip()
                ]
            ]);
            
            $body = json_decode($response->getBody(), true);
            $recaptchaValid = $body['success'] ?? false;
        }
        
        if (!$recaptchaValid) {
            return back()->with('error', 'Please complete the reCAPTCHA verification.');
        }
        
        // Rate limit: 3 submissions per hour per IP
        $rateLimiter = RateLimiter::for('contact-submissions:' . $request->ip(), function () {
            return Limit::perHour(3);
        });

        if ($rateLimiter->tooManyAttempts('contact-form', 3)) {
            return back()->with('error', 'Too many submissions. Please try again later.');
        }

        $rateLimiter->increment('contact-form');
        
        // If honeypot field is filled, it's likely a bot
        if ($request->filled('website')) {
            return redirect()->back();
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:100|regex:/^[a-zA-Z\s]+$/',
            'email' => 'required|email:rfc,dns',
            'subject' => 'required|string|in:sales,support,service,other',
            'message' => 'required|string|min:10|max:1000',
        ]);
        
        // Additional spam checks
        $spamKeywords = ['casino', 'viagra', 'lottery', 'discount'];
        $messageContent = strtolower($request->message);
        
        foreach ($spamKeywords as $keyword) {
            if (str_contains($messageContent, $keyword)) {
                return back()->with('error', 'Your message was flagged as potential spam.');
            }
        }
        
        // Send email
        try {
            // Send directly instead of queuing for troubleshooting
            Mail::to($request->recipient)
                ->send(new ContactFormMail($validated));
            
            return redirect()->back()->with('success', 'Thank you for your message! We\'ll get back to you soon.');
        } catch (\Exception $e) {
            // Log the error
            Log::error('Mail sending failed: ' . $e->getMessage());
            
            // Return with error message
            return back()->with('error', 'Unable to send email: ' . $e->getMessage());
        }
    }
} 