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
        Schema::table('user_behaviors', function (Blueprint $table) {
            // Drop the problematic index first
            $table->dropIndex('user_behaviors_page_url_index');
            
            // Change page_url from TEXT to VARCHAR(2048)
            $table->string('page_url', 2048)->change();
            
            // Change referrer_url from TEXT to VARCHAR(2048)
            $table->string('referrer_url', 2048)->nullable()->change();
            
            // Recreate the index with proper length
            $table->index(['page_url'], 'user_behaviors_page_url_index', 'btree', 100);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_behaviors', function (Blueprint $table) {
            // Drop the index
            $table->dropIndex('user_behaviors_page_url_index');
            
            // Change back to TEXT columns
            $table->text('page_url')->change();
            $table->text('referrer_url')->nullable()->change();
        });
    }
};
