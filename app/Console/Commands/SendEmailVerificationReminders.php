<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Notifications\EmailVerificationReminder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendEmailVerificationReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:send-verification-reminders {--reminder-after=5 : Send reminder after X days}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email verification reminders to users who haven\'t verified their emails';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $reminderAfterDays = $this->option('reminder-after');
        $now = Carbon::now();
        
        $this->info("Sending verification reminders to unverified users created at least {$reminderAfterDays} days ago");
        
        // Find users who:
        // 1. Haven't verified their email
        // 2. Haven't received a reminder yet
        // 3. Were created at least X days ago
        $unverifiedUsers = User::whereNull('email_verified_at')
            ->whereNull('verification_reminder_sent_at')
            ->get()
            ->filter(function ($user) use ($reminderAfterDays, $now) {
                // Calculate days since creation, handling future dates
                $daysSinceCreation = abs($now->diffInDays($user->created_at));
                return $daysSinceCreation >= $reminderAfterDays;
            });
            
        // Debug information
        $totalUnverified = User::whereNull('email_verified_at')->count();
        $totalUnverifiedOldEnough = User::whereNull('email_verified_at')
            ->get()
            ->filter(function ($user) use ($reminderAfterDays, $now) {
                return abs($now->diffInDays($user->created_at)) >= $reminderAfterDays;
            })
            ->count();
        $totalUnverifiedNotReminded = User::whereNull('email_verified_at')
            ->whereNull('verification_reminder_sent_at')
            ->count();
            
        $this->info("Debug info:");
        $this->info("- Total unverified users: {$totalUnverified}");
        $this->info("- Unverified users old enough (>= {$reminderAfterDays} days): {$totalUnverifiedOldEnough}");
        $this->info("- Unverified users not reminded yet: {$totalUnverifiedNotReminded}");
        $this->info("- Users matching all criteria: {$unverifiedUsers->count()}");
        
        if ($unverifiedUsers->isEmpty()) {
            $this->info('No users found who need verification reminders.');
            return 0;
        }
        
        $this->info("Found {$unverifiedUsers->count()} users to send reminders to:");
        
        $sentCount = 0;
        
        foreach ($unverifiedUsers as $user) {
            try {
                $daysRemaining = abs(7 - $reminderAfterDays); // Days remaining before deletion
                
                $this->line("  - Sending reminder to: {$user->name} ({$user->email}) - {$daysRemaining} days remaining");
                
                $user->notify(new EmailVerificationReminder($daysRemaining));
                
                // Mark that reminder was sent
                $user->update(['verification_reminder_sent_at' => $now]);
                
                $sentCount++;
                
            } catch (\Exception $e) {
                $this->error("  - Failed to send reminder to {$user->email}: " . $e->getMessage());
                Log::error("Failed to send verification reminder to user {$user->id}: " . $e->getMessage());
            }
        }
        
        $this->info("Successfully sent {$sentCount} email verification reminders.");
        Log::info("Email verification reminders sent: {$sentCount} reminders sent to users created {$reminderAfterDays} days ago.");
    }
}
