<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SystemFeedback;
use App\Models\User;
use App\Models\Role;
use App\Mail\AdminSystemFeedbackSubmitted;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class TestSystemFeedbackEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:system-feedback-email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the system feedback email notification';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Create a test admin role if it doesn't exist
        $adminRole = Role::firstOrCreate(['name' => 'admin'], [
            'name' => 'admin',
            'description' => 'Administrator'
        ]);

        // Create a test admin user if it doesn't exist
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Test Admin',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'role_id' => $adminRole->id,
                'email_verified_at' => now(),
            ]
        );

        // Create a test system feedback
        $feedback = SystemFeedback::create([
            'user_id' => $adminUser->id,
            'type' => 'bug_report',
            'title' => 'Test Feedback',
            'description' => 'This is a test feedback to verify email notifications',
            'priority' => 'high',
            'status' => 'pending'
        ]);

        try {
            // Send the email
            Mail::to($adminUser->email)->send(new AdminSystemFeedbackSubmitted($feedback));
            $this->info('Test email sent successfully to ' . $adminUser->email);
            Log::info('Test system feedback email sent to: ' . $adminUser->email);
        } catch (\Exception $e) {
            $this->error('Failed to send test email: ' . $e->getMessage());
            Log::error('Failed to send test system feedback email: ' . $e->getMessage());
        }
    }
} 