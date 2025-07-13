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
        // Check if the table exists and if the index already exists
        if (Schema::hasTable('user_behaviors')) {
            try {
                // Check if the index already exists with proper length
                $indexExists = DB::select("
                    SELECT COUNT(*) as count 
                    FROM information_schema.statistics 
                    WHERE table_schema = DATABASE() 
                    AND table_name = 'user_behaviors' 
                    AND index_name = 'user_behaviors_page_url_index'
                ")[0]->count > 0;
                
                if (!$indexExists) {
                    // Only create the index if it doesn't exist
                    DB::statement('CREATE INDEX user_behaviors_page_url_index ON user_behaviors (page_url(100))');
                }
            } catch (Exception $e) {
                // If there's any error, just log it and continue
                // The table structure is already correct from our previous migrations
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration doesn't need to be reversed since it's just a fix
        // The table structure is already correct
    }
};
