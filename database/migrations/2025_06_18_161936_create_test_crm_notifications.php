<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get the first user (assuming it's a CRM user)
        $userId = DB::table('users')->first()->id ?? 1;
        
        // Create sample CRM notifications
        $notifications = [
            [
                'id' => Str::uuid(),
                'type' => 'App\\Notifications\\LeadCreatedNotification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => $userId,
                'data' => json_encode([
                    'type' => 'lead',
                    'lead_id' => 1,
                    'lead_name' => 'John Smith',
                    'lead_email' => 'john.smith@example.com',
                    'lead_source' => 'Website Contact Form',
                    'title' => 'New lead created',
                    'message' => 'A new lead "John Smith" has been created from Website Contact Form'
                ]),
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'type' => 'App\\Notifications\\ContactSubmissionNotification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => $userId,
                'data' => json_encode([
                    'type' => 'contact_submission',
                    'submission_id' => 1,
                    'name' => 'Sarah Johnson',
                    'email' => 'sarah.johnson@example.com',
                    'subject' => 'Product Inquiry',
                    'title' => 'New contact submission',
                    'message' => 'New contact submission from Sarah Johnson about "Product Inquiry"'
                ]),
                'read_at' => null,
                'created_at' => now()->subMinutes(10),
                'updated_at' => now()->subMinutes(10),
            ],
            [
                'id' => Str::uuid(),
                'type' => 'App\\Notifications\\QuotationRequestNotification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => $userId,
                'data' => json_encode([
                    'type' => 'quotation_request',
                    'quotation_request_id' => 1,
                    'customer_name' => 'Mike Davis',
                    'customer_email' => 'mike.davis@example.com',
                    'product_name' => 'Laboratory Equipment Set',
                    'quantity' => 5,
                    'title' => 'New quotation request',
                    'message' => 'New quotation request from Mike Davis for Laboratory Equipment Set'
                ]),
                'read_at' => null,
                'created_at' => now()->subMinutes(25),
                'updated_at' => now()->subMinutes(25),
            ],
        ];
        
        // Insert sample notifications
        DB::table('notifications')->insert($notifications);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove test notifications
        DB::table('notifications')
            ->whereIn('type', [
                'App\\Notifications\\LeadCreatedNotification',
                'App\\Notifications\\ContactSubmissionNotification',
                'App\\Notifications\\QuotationRequestNotification'
            ])
            ->delete();
    }
};
