<?php
 
 namespace App\Http\Controllers;
 
 use Illuminate\Http\Request;
 use Illuminate\Support\Facades\Mail;
 use Illuminate\Support\Facades\Http;
 use Illuminate\Support\Facades\Log;
 use App\Mail\ContactFormMail;
 use App\Models\ContactSubmission;
 
 class ContactController extends Controller
 {
     public function submit(Request $request)
     {
         $validated = $request->validate([
             'name' => 'required|string|max:255',
             'email' => 'required|email|max:255',
             'subject' => 'required|string|max:255',
             'message' => 'required|string',
             'phone' => 'nullable|string|max:20',
             'company' => 'nullable|string|max:255',
             'recipient' => 'required|email',
             'g-recaptcha-response' => [
                 // Only require reCAPTCHA in production environment
                 app()->environment('production') ? 'required' : 'nullable',
                 function ($attribute, $value, $fail) {
                     // Skip reCAPTCHA validation in development environment
                     if (!app()->environment('production')) {
                         Log::info('Skipping reCAPTCHA validation in development environment');
                         return;
                     }
                     
                     if (empty($value)) {
                         $fail('The reCAPTCHA verification is required.');
                         return;
                     }
                     
                     $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                         'secret' => config('services.recaptcha.secret_key'),
                         'response' => $value,
                         'remoteip' => request()->ip(),
                     ]);
                     
                     Log::info('reCAPTCHA response', ['success' => $response->json('success')]);
                     
                     if (!$response->json('success')) {
                         $fail('The reCAPTCHA verification failed. Please try again.');
                     }
                 }
             ],
         ]);

         try {
             // Store contact submission in database
             Log::info('Creating contact submission', ['email' => $validated['email']]);
             
             $contactSubmission = ContactSubmission::create([
                 'name' => $validated['name'],
                 'email' => $validated['email'],
                 'subject' => $validated['subject'],
                 'message' => $validated['message'],
                 'phone' => $validated['phone'] ?? null,
                 'company' => $validated['company'] ?? null,
                 'status' => 'new',
             ]);

             Log::info('Contact submission created successfully', ['id' => $contactSubmission->id]);

             // Send email (with error handling)
             try {
                 Mail::to($request->recipient)
                     ->send(new ContactFormMail($validated));
                 Log::info('Contact form email sent successfully');
             } catch (\Exception $emailError) {
                 Log::error('Failed to send contact form email: ' . $emailError->getMessage());
                 // Continue - don't fail the form submission just because email failed
             }

             Log::info('Contact form submitted and stored', [
                 'submission_id' => $contactSubmission->id,
                 'subject' => $contactSubmission->subject,
                 'is_sales_inquiry' => $contactSubmission->isSalesInquiry()
             ]);

             return redirect()->back()->with('success', 'Thank you for your message! We\'ll get back to you soon.');
         } catch (\Exception $e) {
             Log::error('Failed to process contact form submission: ' . $e->getMessage());
             Log::error('Contact form error details: ', [
                 'file' => $e->getFile(),
                 'line' => $e->getLine(),
                 'trace' => $e->getTraceAsString()
             ]);
             return redirect()->back()->with('error', 'There was an error processing your submission. Please try again.');
         }
     }
 }