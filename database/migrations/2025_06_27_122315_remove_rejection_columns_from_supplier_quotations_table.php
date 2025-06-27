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
            // First drop the foreign key constraint if it exists
            $table->dropForeign(['rejected_by']);
            // Then drop the columns
            $table->dropColumn(['rejection_reason', 'rejected_at', 'rejected_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier_quotations', function (Blueprint $table) {
            $table->text('rejection_reason')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->unsignedBigInteger('rejected_by')->nullable();
            
            $table->foreign('rejected_by')->references('id')->on('users')->onDelete('set null');
        });
    }
};
