<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add all missing statuses for superadmin flexibility
        \DB::statement("ALTER TABLE crm_leads MODIFY COLUMN status ENUM(
            'new_inquiry',
            'quote_requested', 
            'getting_price',
            'price_submitted',
            'quote_sent',
            'follow_up_1',
            'follow_up_2',
            'follow_up_3',
            'negotiating_price',
            'payment_pending',
            'order_confirmed',
            'deal_lost',
            'on_hold',
            'cancelled',
            'pending_approval',
            'approved',
            'rejected',
            'in_progress',
            'completed',
            'archived'
        ) DEFAULT 'new_inquiry'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to basic statuses
        \DB::statement("ALTER TABLE crm_leads MODIFY COLUMN status ENUM(
            'new_inquiry',
            'quote_requested',
            'getting_price',
            'quote_sent',
            'follow_up_1',
            'follow_up_2',
            'follow_up_3',
            'negotiating_price',
            'payment_pending',
            'order_confirmed',
            'deal_lost'
        ) DEFAULT 'new_inquiry'");
    }
};