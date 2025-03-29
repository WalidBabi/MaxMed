<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactFormMail;

class ContactController extends Controller
{
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'recipient' => 'required|email'
        ]);
        
        // Send email
        Mail::to($request->recipient)
            ->send(new ContactFormMail($validated));
            
        return redirect()->back()->with('success', 'Thank you for your message! We\'ll get back to you soon.');
    }
} 