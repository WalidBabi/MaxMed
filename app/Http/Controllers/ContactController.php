<?php
 
 namespace App\Http\Controllers;
 
 use Illuminate\Http\Request;
 use Illuminate\Support\Facades\Mail;
 use Illuminate\Support\Facades\Http;
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
             'recipient' => 'required|email',
             'g-recaptcha-response' => ['required', function ($attribute, $value, $fail) {
                 $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                     'secret' => config('services.recaptcha.secret_key'),
                     'response' => $value,
                     'remoteip' => request()->ip(),
                 ]);
                 if (!$response->json('success')) {
                     $fail('The reCAPTCHA verification failed. Please try again.');
                 }
             }],
         ]);

         // Send email
         Mail::to($request->recipient)
             ->send(new ContactFormMail($validated));

         return redirect()->back()->with('success', 'Thank you for your message! We\'ll get back to you soon.');
     }
 }