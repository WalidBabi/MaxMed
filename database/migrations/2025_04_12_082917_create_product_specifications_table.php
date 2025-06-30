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
        if (!Schema::hasTable('product_specifications')) {
            Schema::create('product_specifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->onDelete('cascade');
                $table->string('specification_key'); // e.g., 'test_count', 'detection_time', 'sensitivity'
                $table->text('specification_value'); // The actual value
                $table->string('unit')->nullable(); // e.g., 'tests', 'minutes', '%', 'ml'
                $table->string('category')->nullable(); // e.g., 'performance', 'physical', 'regulatory'
                $table->string('display_name'); // Human-readable name for frontend
                $table->text('description')->nullable(); // Additional details about the specification
                $table->integer('sort_order')->default(0); // For ordering display
                $table->boolean('is_filterable')->default(false); // Can be used as a filter
                $table->boolean('is_searchable')->default(false); // Can be searched
                $table->boolean('show_on_listing')->default(false); // Show in product listings
                $table->boolean('show_on_detail')->default(true); // Show on product detail page
                $table->timestamps();
                
                $table->index(['product_id', 'specification_key']);
                $table->index(['specification_key', 'is_filterable']);
                $table->index(['category', 'sort_order']);
            });
        } else {
            Schema::table('product_specifications', function (Blueprint $table) {
                // Check and add any missing columns
                $columns = Schema::getColumnListing('product_specifications');
                
                if (!in_array('product_id', $columns)) {
                    $table->foreignId('product_id')->constrained()->onDelete('cascade');
                }
                if (!in_array('specification_key', $columns)) {
                    $table->string('specification_key');
                }
                if (!in_array('specification_value', $columns)) {
                    $table->text('specification_value');
                }
                if (!in_array('unit', $columns)) {
                    $table->string('unit')->nullable();
                }
                if (!in_array('category', $columns)) {
                    $table->string('category')->nullable();
                }
                if (!in_array('display_name', $columns)) {
                    $table->string('display_name');
                }
                if (!in_array('description', $columns)) {
                    $table->text('description')->nullable();
                }
                if (!in_array('sort_order', $columns)) {
                    $table->integer('sort_order')->default(0);
                }
                if (!in_array('is_filterable', $columns)) {
                    $table->boolean('is_filterable')->default(false);
                }
                if (!in_array('is_searchable', $columns)) {
                    $table->boolean('is_searchable')->default(false);
                }
                if (!in_array('show_on_listing', $columns)) {
                    $table->boolean('show_on_listing')->default(false);
                }
                if (!in_array('show_on_detail', $columns)) {
                    $table->boolean('show_on_detail')->default(true);
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
