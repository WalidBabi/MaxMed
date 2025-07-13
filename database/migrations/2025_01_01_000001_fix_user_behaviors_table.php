<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if table exists and has the problematic index
        if (Schema::hasTable('user_behaviors')) {
            // Try to drop the problematic index if it exists
            try {
                DB::statement('DROP INDEX user_behaviors_page_url_index ON user_behaviors');
            } catch (Exception $e) {
                // Index doesn't exist, continue
            }
            
            // Create the index with proper length
            DB::statement('CREATE INDEX user_behaviors_page_url_index ON user_behaviors (page_url(100))');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('user_behaviors')) {
            try {
                DB::statement('DROP INDEX user_behaviors_page_url_index ON user_behaviors');
            } catch (Exception $e) {
                // Index doesn't exist, continue
            }
        }
    }
}; 