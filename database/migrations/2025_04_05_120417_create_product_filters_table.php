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
        if (!Schema::hasTable('product_filters')) {
            Schema::create('product_filters', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
            });
        } else {
            Schema::table('product_filters', function (Blueprint $table) {
                // Check and add any missing columns
                $columns = Schema::getColumnListing('product_filters');
                if (!in_array('id', $columns)) {
                    $table->id();
                }
                if (!in_array('created_at', $columns) || !in_array('updated_at', $columns)) {
                    $table->timestamps();
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
