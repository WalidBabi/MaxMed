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
        if (!Schema::hasTable('cache')) {
            Schema::create('cache', function (Blueprint $table) {
                $table->string('key')->primary();
                $table->mediumText('value');
                $table->integer('expiration');
            });
        } else {
            Schema::table('cache', function (Blueprint $table) {
                // Check and add any missing columns
                $columns = Schema::getColumnListing('cache');
                
                // Add missing columns if they don't exist
                if (!in_array('value', $columns)) {
                    $table->mediumText('value');
                }
                if (!in_array('expiration', $columns)) {
                    $table->integer('expiration');
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
