<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds discount fields to order_items table for tracking item-level discounts.
     * This migration is safe to run in production as it:
     * 1. Checks for column existence before adding
     * 2. Uses default values to maintain data consistency
     * 3. Has a proper rollback method
     */
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Add discount_percentage if it doesn't exist
            if (!Schema::hasColumn('order_items', 'discount_percentage')) {
                $table->decimal('discount_percentage', 5, 2)
                    ->default(0)
                    ->comment('Percentage discount applied to this item');
            }

            // Add discount_amount if it doesn't exist
            if (!Schema::hasColumn('order_items', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)
                    ->default(0)
                    ->comment('Fixed amount discount applied to this item');
            }
        });
    }

    /**
     * Reverse the migrations.
     * 
     * WARNING: Running this in production will PERMANENTLY DELETE all discount data!
     * Make sure to backup data before rolling back if needed.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Only drop if columns exist
            if (Schema::hasColumn('order_items', 'discount_percentage')) {
                $table->dropColumn('discount_percentage');
            }
            if (Schema::hasColumn('order_items', 'discount_amount')) {
                $table->dropColumn('discount_amount');
            }
        });
    }
}; 