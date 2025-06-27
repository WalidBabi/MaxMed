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
            if (!Schema::hasColumn('quote_items', 'size')) {
                $table->string('size', 100)->nullable()->after('specifications');
                echo "Added size column to quote_items table.\n";
            } else {
                echo "size column already exists in quote_items table, skipping.\n";
            }
        });

        // Add size column to invoice_items table
        Schema::table('invoice_items', function (Blueprint $table) {
            if (!Schema::hasColumn('invoice_items', 'size')) {
                $table->string('size', 100)->nullable()->after('description');
                echo "Added size column to invoice_items table.\n";
            } else {
                echo "size column already exists in invoice_items table, skipping.\n";
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove size column from quote_items table
        Schema::table('quote_items', function (Blueprint $table) {
            if (Schema::hasColumn('quote_items', 'size')) {
                $table->dropColumn('size');
                echo "Dropped size column from quote_items table.\n";
            } else {
                echo "size column does not exist in quote_items table, skipping drop.\n";
            }
        });

        // Remove size column from invoice_items table
        Schema::table('invoice_items', function (Blueprint $table) {
            if (Schema::hasColumn('invoice_items', 'size')) {
                $table->dropColumn('size');
                echo "Dropped size column from invoice_items table.\n";
            } else {
                echo "size column does not exist in invoice_items table, skipping drop.\n";
            }
        });
    }
};
