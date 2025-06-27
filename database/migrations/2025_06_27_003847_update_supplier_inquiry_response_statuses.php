<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First modify the enum to include the new status value
        DB::statement("ALTER TABLE supplier_inquiry_responses MODIFY COLUMN status ENUM('pending', 'viewed', 'interested', 'not_interested', 'quoted', 'accepted', 'not_available') DEFAULT 'pending'");

        // Update not_interested to not_available in supplier_inquiry_responses
        DB::statement("UPDATE supplier_inquiry_responses SET status = 'not_available' WHERE status = 'not_interested'");

        // Update interested to viewed in supplier_inquiry_responses
        DB::statement("UPDATE supplier_inquiry_responses SET status = 'viewed' WHERE status = 'interested'");

        // Update not_interested to not_available in quotation_requests
        DB::statement("UPDATE quotation_requests SET supplier_response = 'not_available' WHERE supplier_response = 'not_interested'");

        // Finally, remove the old statuses from the enum
        DB::statement("ALTER TABLE supplier_inquiry_responses MODIFY COLUMN status ENUM('pending', 'viewed', 'quoted', 'accepted', 'not_available') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First add back the old statuses to the enum
        DB::statement("ALTER TABLE supplier_inquiry_responses MODIFY COLUMN status ENUM('pending', 'viewed', 'quoted', 'accepted', 'not_available', 'not_interested', 'interested') DEFAULT 'pending'");

        // Convert back to old statuses in supplier_inquiry_responses
        DB::statement("UPDATE supplier_inquiry_responses SET status = 'not_interested' WHERE status = 'not_available'");

        // We can't reliably determine which 'viewed' statuses were previously 'interested'
        // so we'll leave them as 'viewed' in the rollback

        // Revert legacy quotation requests
        DB::statement("UPDATE quotation_requests SET supplier_response = 'not_interested' WHERE supplier_response = 'not_available'");

        // Finally, restore the original enum
        DB::statement("ALTER TABLE supplier_inquiry_responses MODIFY COLUMN status ENUM('pending', 'viewed', 'interested', 'not_interested', 'quoted', 'accepted') DEFAULT 'pending'");
    }
};
