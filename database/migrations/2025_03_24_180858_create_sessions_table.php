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
        if (!Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        } else {
            Schema::table('sessions', function (Blueprint $table) {
                // Check and add any missing columns
                $columns = Schema::getColumnListing('sessions');
                
                // Add missing columns if they don't exist
                if (!in_array('user_id', $columns)) {
                    $table->unsignedBigInteger('user_id')->nullable()->index();
                }
                if (!in_array('ip_address', $columns)) {
                    $table->string('ip_address', 45)->nullable();
                }
                if (!in_array('user_agent', $columns)) {
                    $table->text('user_agent')->nullable();
                }
                if (!in_array('payload', $columns)) {
                    $table->longText('payload');
                }
                if (!in_array('last_activity', $columns)) {
                    $table->integer('last_activity')->index();
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
