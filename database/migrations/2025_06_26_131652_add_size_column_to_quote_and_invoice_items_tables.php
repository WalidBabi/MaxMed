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
        // Add size column to quote_items table
        Schema::table('quote_items', function (Blueprint $table) {
            $table->string('size', 100)->nullable()->after('specifications');
        });

        // Add size column to invoice_items table
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->string('size', 100)->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove size column from quote_items table
        Schema::table('quote_items', function (Blueprint $table) {
            $table->dropColumn('size');
        });

        // Remove size column from invoice_items table
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn('size');
        });
    }
};
