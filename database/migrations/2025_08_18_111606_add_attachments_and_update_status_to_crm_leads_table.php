<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crm_leads', function (Blueprint $table) {
            // Add attachments field for storing multiple file paths as JSON
            $table->json('attachments')->nullable()->after('notes');
        });
        
        // Update status enum values to match the new pipeline statuses
        \DB::statement("ALTER TABLE crm_leads MODIFY COLUMN status ENUM('new_inquiry', 'quote_requested', 'follow_up_1', 'follow_up_2', 'follow_up_3', 'quote_sent', 'negotiating_price', 'payment_pending', 'order_confirmed', 'deal_lost') DEFAULT 'new_inquiry'");
        
        // Update existing records with old status values to new ones
        \DB::statement("UPDATE crm_leads SET status = 'new_inquiry' WHERE status = 'new'");
        \DB::statement("UPDATE crm_leads SET status = 'quote_requested' WHERE status = 'contacted'");
        \DB::statement("UPDATE crm_leads SET status = 'quote_sent' WHERE status = 'qualified'");
        \DB::statement("UPDATE crm_leads SET status = 'negotiating_price' WHERE status = 'proposal'");
        \DB::statement("UPDATE crm_leads SET status = 'negotiating_price' WHERE status = 'negotiation'");
        \DB::statement("UPDATE crm_leads SET status = 'order_confirmed' WHERE status = 'won'");
        \DB::statement("UPDATE crm_leads SET status = 'deal_lost' WHERE status = 'lost'");
    }

    public function down(): void
    {
        Schema::table('crm_leads', function (Blueprint $table) {
            $table->dropColumn('attachments');
        });
        
        // Revert status enum values back to original
        \DB::statement("ALTER TABLE crm_leads MODIFY COLUMN status ENUM('new', 'contacted', 'qualified', 'proposal', 'negotiation', 'won', 'lost') DEFAULT 'new'");
        
        // Revert existing records back to old status values
        \DB::statement("UPDATE crm_leads SET status = 'new' WHERE status = 'new_inquiry'");
        \DB::statement("UPDATE crm_leads SET status = 'contacted' WHERE status = 'quote_requested'");
        \DB::statement("UPDATE crm_leads SET status = 'qualified' WHERE status = 'quote_sent'");
        \DB::statement("UPDATE crm_leads SET status = 'proposal' WHERE status = 'negotiating_price'");
        \DB::statement("UPDATE crm_leads SET status = 'won' WHERE status = 'order_confirmed'");
        \DB::statement("UPDATE crm_leads SET status = 'lost' WHERE status = 'deal_lost'");
    }
};