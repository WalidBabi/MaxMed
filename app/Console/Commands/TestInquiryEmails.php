<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SupplierInquiry;
use App\Models\User;
use App\Notifications\NewInquiryNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TestInquiryEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:inquiry-emails {inquiry_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test inquiry email sending and check email tracking status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $inquiryId = $this->argument('inquiry_id');
        
        if ($inquiryId) {
            $inquiry = SupplierInquiry::find($inquiryId);
            if (!$inquiry) {
                $this->error("Inquiry with ID {$inquiryId} not found.");
                return 1;
            }
            $this->testSpecificInquiry($inquiry);
        } else {
            $this->testRecentInquiries();
        }
        
        return 0;
    }
    
    private function testSpecificInquiry(SupplierInquiry $inquiry)
    {
        $this->info("Testing inquiry: {$inquiry->reference_number}");
        $this->info("Status: {$inquiry->status}");
        $this->info("Broadcast at: " . ($inquiry->broadcast_at ?? 'Not broadcast yet'));
        
        $responses = $inquiry->supplierResponses;
        $this->info("Total supplier responses: " . $responses->count());
        
        $this->table(
            ['Supplier', 'Email', 'Status', 'Email Sent', 'Email Success', 'Viewed At', 'Click Count'],
            $responses->map(function ($response) {
                return [
                    $response->supplier->name ?? 'Unknown',
                    $response->supplier->email ?? 'Unknown',
                    $response->status,
                    $response->email_sent_at ? $response->email_sent_at->format('Y-m-d H:i:s') : 'Not sent',
                    $response->email_sent_successfully ? 'Yes' : 'No',
                    $response->viewed_at ? $response->viewed_at->format('Y-m-d H:i:s') : 'Not viewed',
                    $response->email_click_count ?? 0
                ];
            })->toArray()
        );
        
        // Test email sending to one supplier
        if ($responses->count() > 0) {
            $testResponse = $responses->first();
            $supplier = $testResponse->supplier;
            
            if ($this->confirm("Do you want to test sending email to {$supplier->name} ({$supplier->email})?")) {
                $this->info("Sending test email...");
                try {
                    $supplier->notify(new NewInquiryNotification($inquiry));
                    $this->info("✅ Email sent successfully!");
                    
                    // Update tracking
                    $testResponse->update([
                        'email_sent_at' => now(),
                        'email_sent_successfully' => true
                    ]);
                } catch (\Exception $e) {
                    $this->error("❌ Email failed: " . $e->getMessage());
                    
                    // Update tracking
                    $testResponse->update([
                        'email_sent_at' => now(),
                        'email_sent_successfully' => false,
                        'email_error' => $e->getMessage()
                    ]);
                }
            }
        }
    }
    
    private function testRecentInquiries()
    {
        $inquiries = SupplierInquiry::with('supplierResponses')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        if ($inquiries->isEmpty()) {
            $this->warn("No inquiries found.");
            return;
        }
        
        $this->info("Recent inquiries:");
        
        $data = [];
        foreach ($inquiries as $inquiry) {
            $responses = $inquiry->supplierResponses;
            $emailsSent = $responses->where('email_sent_successfully', true)->count();
            $emailsFailed = $responses->where('email_sent_successfully', false)->count();
            $emailClicks = $responses->whereNotNull('viewed_at')->count();
            
            $data[] = [
                $inquiry->id,
                $inquiry->reference_number,
                $inquiry->status,
                $responses->count() . ' suppliers',
                $emailsSent . ' sent',
                $emailsFailed . ' failed',
                $emailClicks . ' clicked'
            ];
        }
        
        $this->table(
            ['ID', 'Reference', 'Status', 'Suppliers', 'Emails Sent', 'Failed', 'Clicked'],
            $data
        );
        
        if ($this->confirm("Do you want to test a specific inquiry?")) {
            $inquiryId = $this->ask("Enter inquiry ID:");
            $inquiry = SupplierInquiry::find($inquiryId);
            if ($inquiry) {
                $this->testSpecificInquiry($inquiry);
            } else {
                $this->error("Inquiry not found.");
            }
        }
    }
}
