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
        if (!Schema::hasTable('order_items')) {
            Schema::create('order_items', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('order_id')->index('order_items_order_id_foreign');
                $table->unsignedBigInteger('product_id')->index('order_items_product_id_foreign');
                $table->integer('quantity');
                $table->decimal('price', 10);
                $table->timestamps();
            });
        } else {
            Schema::table('order_items', function (Blueprint $table) {
                // Check and add any missing columns
                $columns = Schema::getColumnListing('order_items');
                if (!in_array('order_id', $columns)) {
                    $table->unsignedBigInteger('order_id')->index('order_items_order_id_foreign');
                }
                if (!in_array('product_id', $columns)) {
                    $table->unsignedBigInteger('product_id')->index('order_items_product_id_foreign');
                }
                if (!in_array('quantity', $columns)) {
                    $table->integer('quantity');
                }
                if (!in_array('price', $columns)) {
                    $table->decimal('price', 10);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Don't drop the table in production to preserve data
        // Only drop columns that were added in this migration if any
    }
};
