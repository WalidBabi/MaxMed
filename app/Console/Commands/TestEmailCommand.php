<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\Quote;
use App\Models\Customer;
use App\Mail\QuoteEmail;

class TestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email {--email=sales@maxmedme.com}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email sending functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->option('email');
        
        $this->info("Testing email configuration...");
        
        // Test 1: Check mail configuration
        $this->info("Mail driver: " . config('mail.default'));
        $this->info("Mail host: " . config('mail.mailers.' . config('mail.default') . '.host', 'Not configured'));
        
        // Test 2: Test basic email sending
        try {
            $this->info("Sending test email to: $email");
            
            Mail::raw('This is a test email from MaxMed Quote System.', function ($message) use ($email) {
                $message->to($email)
                        ->subject('Test Email - MaxMed Quote System');
            });
            
            $this->info("✅ Basic email sent successfully!");
            
        } catch (\Exception $e) {
            $this->error("❌ Basic email failed: " . $e->getMessage());
            return 1;
        }
        
        // Test 3: Test Quote Email specifically
        try {
            $this->info("Testing Quote Email functionality...");
            
            // Get a sample quote
            $quote = Quote::with('items')->first();
            if (!$quote) {
                $this->error("❌ No quotes found in database to test with");
                return 1;
            }
            
            // Create a sample customer
            $customer = Customer::first();
            if (!$customer) {
                $customer = new Customer([
                    'name' => 'Test Customer',
                    'email' => $email
                ]);
            }
            
            Mail::to($email)->send(new QuoteEmail($quote, $customer));
            
            $this->info("✅ Quote email sent successfully!");
            
        } catch (\Exception $e) {
            $this->error("❌ Quote email failed: " . $e->getMessage());
            Log::error('Quote email test failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
        
        $this->info("🎉 All email tests passed!");
        return 0;
    }
}
