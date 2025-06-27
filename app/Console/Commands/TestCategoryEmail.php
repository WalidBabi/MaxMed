<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Category;
use App\Models\SupplierCategory;
use App\Notifications\SupplierCategoryApprovalNotification;

class TestCategoryEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:category-email {email} {--type=approval}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test category approval/rejection email sending';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $type = $this->option('type'); // approval or rejection

        // Find or create a test user
        $user = User::where('email', $email)->first();
        if (!$user) {
            $this->error("User with email {$email} not found.");
            return 1;
        }

        // Find a category
        $category = Category::first();
        if (!$category) {
            $this->error("No categories found in the database.");
            return 1;
        }

        // Create a test admin user
        $admin = User::whereHas('role', function($query) {
            $query->where('name', 'admin');
        })->first();
        
        if (!$admin) {
            $admin = new User([
                'name' => 'Test Admin',
                'email' => 'admin@test.com',
                'id' => 999
            ]);
        }

        $this->info("Testing {$type} email for user: {$user->name} ({$user->email})");
        $this->info("Category: {$category->name}");
        $this->info("Admin: {$admin->name}");

        try {
            // Create notification
            $notification = new SupplierCategoryApprovalNotification(
                category: $category,
                supplierCategory: $type === 'approval' ? new SupplierCategory([
                    'approved_at' => now(),
                    'status' => 'active'
                ]) : null,
                approvedBy: $admin,
                isApproved: $type === 'approval'
            );

            // Send notification
            $user->notify($notification);

            $this->info("✅ {$type} email sent successfully!");
            $this->info("📬 Check the email inbox for: {$email}");
            
        } catch (\Exception $e) {
            $this->error("❌ Failed to send {$type} email:");
            $this->error($e->getMessage());
            return 1;
        }

        return 0;
    }
}
