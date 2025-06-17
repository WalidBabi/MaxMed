<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the existing foreign key constraint if it exists
        try {
            DB::statement('ALTER TABLE contact_submissions DROP FOREIGN KEY contact_submissions_converted_to_inquiry_id_foreign');
            echo "Foreign key constraint dropped successfully\n";
        } catch (Exception $e) {
            echo "Foreign key constraint might not exist or already dropped\n";
        }
    }

    public function down(): void
    {
        // Re-add the foreign key constraint
        Schema::table('contact_submissions', function (Blueprint $table) {
            $table->foreign('converted_to_inquiry_id')->references('id')->on('quotation_requests')->onDelete('set null');
        });
    }
}; 