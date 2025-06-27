<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the enum to include the 'accepted' status
        DB::statement("ALTER TABLE supplier_inquiry_responses MODIFY COLUMN status ENUM('pending', 'viewed', 'interested', 'not_interested', 'quoted', 'accepted') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum values
        DB::statement("ALTER TABLE supplier_inquiry_responses MODIFY COLUMN status ENUM('pending', 'viewed', 'interested', 'not_interested', 'quoted') DEFAULT 'pending'");
    }
};
