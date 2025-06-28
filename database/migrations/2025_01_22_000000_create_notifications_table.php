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
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('type');
                $table->morphs('notifiable');
                $table->text('data');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('notifications', function (Blueprint $table) {
                // Check and add any missing columns
                $columns = Schema::getColumnListing('notifications');
                
                // Add missing columns if they don't exist
                if (!in_array('type', $columns)) {
                    $table->string('type');
                }
                if (!in_array('notifiable_type', $columns)) {
                    $table->string('notifiable_type');
                }
                if (!in_array('notifiable_id', $columns)) {
                    $table->unsignedBigInteger('notifiable_id');
                }
                if (!in_array('data', $columns)) {
                    $table->text('data');
                }
                if (!in_array('read_at', $columns)) {
                    $table->timestamp('read_at')->nullable();
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