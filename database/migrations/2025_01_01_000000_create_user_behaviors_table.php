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
        Schema::create('user_behaviors', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->nullable()->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('page_url', 2048); // Changed from text to string with max length
            $table->string('referrer_url', 2048)->nullable(); // Changed from text to string
            $table->text('user_agent')->nullable(); // Keep as text since it can be very long
            $table->string('ip_address', 45)->nullable();
            $table->string('event_type');
            $table->json('event_data')->nullable();
            $table->timestamp('timestamp');
            $table->integer('duration')->nullable(); // Time on page in seconds
            $table->integer('scroll_depth')->nullable(); // Percentage of page scrolled
            $table->json('mouse_position')->nullable(); // x, y coordinates
            $table->json('click_target')->nullable(); // Element selector, text, etc.
            $table->json('interaction_path')->nullable(); // Sequence of interactions
            $table->json('device_info')->nullable(); // Screen size, device type, etc.
            $table->json('location_data')->nullable(); // Country, city, etc.
            $table->timestamps();

            // Indexes for better query performance
            $table->index(['event_type', 'timestamp']);
            $table->index(['user_id', 'timestamp']);
            $table->index(['session_id', 'timestamp']);
        });

        // Create index on page_url with limited length using raw SQL
        DB::statement('CREATE INDEX user_behaviors_page_url_index ON user_behaviors (page_url(100))');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_behaviors');
    }
}; 