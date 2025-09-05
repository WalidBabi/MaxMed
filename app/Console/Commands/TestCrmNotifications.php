<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ContactSubmission;
use App\Models\QuotationRequest;
use App\Models\Product;
use App\Models\User;

class TestCrmNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:crm-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test CRM notification system by creating sample contact submissions and quotation requests';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing CRM notification system...');

        // Test 1: Create a contact submission
        $this->info('Creating test contact submission...');
        $contactSubmission = ContactSubmission::create([
            'name' => 'Test Customer',
            'email' => 'test@example.com',
            'subject' => 'Sales Inquiry',
            'message' => 'I am interested in your laboratory equipment. Please send me more information.',
            'phone' => '+1234567890',
            'company' => 'Test Lab Inc.',
            'status' => 'new',
        ]);
        $this->info("Contact submission created with ID: {$contactSubmission->id}");

        // Test 2: Create a quotation request
        $product = Product::first();
        if ($product) {
            $this->info('Creating test quotation request...');
            $quotationRequest = QuotationRequest::create([
                'product_id' => $product->id,
                'user_id' => 0, // Guest user
                'quantity' => 5,
                'size' => 'Standard',
                'requirements' => 'Need for research laboratory',
                'notes' => 'Test quotation request',
                'delivery_timeline' => 'standard',
                'status' => 'pending',
            ]);
            $this->info("Quotation request created with ID: {$quotationRequest->id}");
        } else {
            $this->warn('No products found. Skipping quotation request test.');
        }

        // Check if notifications were sent
        $adminUsers = User::where(function($query) {
            $query->whereHas('role', function($q) {
                $q->where('name', 'admin');
            })
                  ->orWhereHas('role', function($roleQuery) {
                      $roleQuery->whereIn('name', ['admin', 'crm']);
                  });
        })->get();

        $this->info("Found {$adminUsers->count()} admin/CRM users to receive notifications:");
        foreach ($adminUsers as $user) {
            $unreadCount = $user->unreadNotifications()->count();
            $this->info("- {$user->name} ({$user->email}): {$unreadCount} unread notifications");
        }

        $this->info('CRM notification test completed!');
    }
} 