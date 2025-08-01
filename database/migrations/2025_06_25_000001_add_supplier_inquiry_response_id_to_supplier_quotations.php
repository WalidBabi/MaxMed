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
        Schema::table('supplier_quotations', function (Blueprint $table) {
            // Add supplier_inquiry_response_id column if it doesn't exist
            if (!Schema::hasColumn('supplier_quotations', 'supplier_inquiry_response_id')) {
                $table->unsignedBigInteger('supplier_inquiry_response_id')->nullable()->after('supplier_id');
                      
                // Add index for better performance
                $table->index('supplier_inquiry_response_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier_quotations', function (Blueprint $table) {
            // Drop foreign key constraint and column if they exist
            if (Schema::hasColumn('supplier_quotations', 'supplier_inquiry_response_id')) {
                $table->dropForeign(['supplier_inquiry_response_id']);
                $table->dropIndex(['supplier_inquiry_response_id']);
                $table->dropColumn('supplier_inquiry_response_id');
            }
        });
    }
}; 