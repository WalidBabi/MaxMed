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
        // Add 'rejected' status to the enum
        DB::statement("ALTER TABLE supplier_inquiry_responses MODIFY COLUMN status ENUM('pending', 'viewed', 'quoted', 'accepted', 'not_available', 'rejected') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'rejected' status from the enum
        DB::statement("ALTER TABLE supplier_inquiry_responses MODIFY COLUMN status ENUM('pending', 'viewed', 'quoted', 'accepted', 'not_available') DEFAULT 'pending'");
    }
};
