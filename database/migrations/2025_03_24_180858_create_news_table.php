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
        if (!Schema::hasTable('news')) {
            Schema::create('news', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('title');
                $table->text('content');
                $table->string('image_url')->nullable();
                $table->boolean('published')->default(true);
                $table->timestamps();
            });
        } else {
            Schema::table('news', function (Blueprint $table) {
                // Check and add any missing columns
                $columns = Schema::getColumnListing('news');
                
                if (!in_array('title', $columns)) {
                    $table->string('title');
                }
                if (!in_array('content', $columns)) {
                    $table->text('content');
                }
                if (!in_array('image_url', $columns)) {
                    $table->string('image_url')->nullable();
                }
                if (!in_array('published', $columns)) {
                    $table->boolean('published')->default(true);
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
