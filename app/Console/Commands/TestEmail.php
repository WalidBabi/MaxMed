<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\SupplierInvitation;

class TestEmail extends Command
{
    protected $signature = 'email:test {email=test@example.com}';
    protected $description = 'Test email sending functionality';

    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info("Testing email send to: {$email}");
        
        try {
            Mail::to($email)->send(new SupplierInvitation(
                $email,
                'Test User',
                'test-token-' . uniqid(),
                'Test Company',
                'Admin User',
                'This is a test invitation message.'
            ));
            
            $this->info('✅ Email sent successfully!');
            $this->info('Check your log file at: storage/logs/laravel.log');
            
        } catch (\Exception $e) {
            $this->error('❌ Email failed to send: ' . $e->getMessage());
            $this->error('Stack trace:');
            $this->error($e->getTraceAsString());
        }
    }
} 