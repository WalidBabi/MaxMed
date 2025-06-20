<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class TestMailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:mail {--method=smtp : Mail method to test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test mail configuration and send test email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing Mail Configuration...');
        
        // Display current mail configuration
        $this->info('📧 Current Mail Settings:');
        $this->table(['Setting', 'Value'], [
            ['Driver', config('mail.default')],
            ['Host', config('mail.mailers.smtp.host')],
            ['Port', config('mail.mailers.smtp.port')],
            ['Username', config('mail.mailers.smtp.username')],
            ['Encryption', config('mail.mailers.smtp.encryption')],
            ['From Address', config('mail.from.address')],
            ['Admin Email', config('mail.admin_email')],
        ]);

        $adminEmail = config('mail.admin_email') ?: 'wbabi@localhost.com';
        
        $this->info("🎯 Testing email to: {$adminEmail}");

        try {
            // Test basic mail sending
            Mail::raw('🧪 This is a test email from Laravel to verify hMail Server configuration.', function ($message) use ($adminEmail) {
                $message->to($adminEmail)
                        ->subject('🔧 Laravel Mail Test - ' . now()->format('Y-m-d H:i:s'));
            });

            $this->info('✅ Test email sent successfully!');
            $this->info('📬 Check your hMail Server logs and inbox.');
            
            // Test login notification specifically
            $this->info('🔐 Testing login notification...');
            
            $testUser = User::first();
            if ($testUser) {
                $admin = new User();
                $admin->email = $adminEmail;
                $admin->name = 'Test Admin';
                $admin->id = 0;

                // Import the notification class
                $authNotification = new \App\Notifications\AuthNotification($testUser, 'login', 'Test Method');
                
                // Send notification
                \Illuminate\Support\Facades\Notification::send($admin, $authNotification);
                
                $this->info('✅ Login notification test sent!');
            } else {
                $this->warn('⚠️ No users found to test login notification');
            }

        } catch (\Exception $e) {
            $this->error('❌ Failed to send test email:');
            $this->error($e->getMessage());
            
            $this->info('🔍 Troubleshooting steps:');
            $this->info('1. Check if hMail Server is running');
            $this->info('2. Verify IP ranges in hMail Administrator');
            $this->info('3. Check domain and account configuration');
            $this->info('4. Try disabling SMTP authentication in hMail');
            
            return 1;
        }

        return 0;
    }
}
