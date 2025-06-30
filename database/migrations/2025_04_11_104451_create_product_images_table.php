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
        if (!Schema::hasTable('product_images')) {
            Schema::create('product_images', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_id');
                $table->string('image_path');
                $table->string('image_url');
                $table->integer('sort_order')->default(0);
                $table->boolean('is_primary')->default(false);
                $table->timestamps();

                $table->foreign('product_id')
                      ->references('id')
                      ->on('products')
                      ->onDelete('cascade');
            });
        } else {
            Schema::table('product_images', function (Blueprint $table) {
                // Check and add any missing columns
                $columns = Schema::getColumnListing('product_images');
                
                if (!in_array('product_id', $columns)) {
                    $table->unsignedBigInteger('product_id');
                }
                if (!in_array('image_path', $columns)) {
                    $table->string('image_path');
                }
                if (!in_array('image_url', $columns)) {
                    $table->string('image_url');
                }
                if (!in_array('sort_order', $columns)) {
                    $table->integer('sort_order')->default(0);
                }
                if (!in_array('is_primary', $columns)) {
                    $table->boolean('is_primary')->default(false);
                }
                
                // Add foreign key if it doesn't exist
                try {
                    $table->foreign('product_id')
                          ->references('id')
                          ->on('products')
                          ->onDelete('cascade');
                } catch (\Exception $e) {
                    // Foreign key already exists
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
