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
        Schema::create('user_behaviors', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->nullable()->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->text('page_url');
            $table->text('referrer_url')->nullable();
            $table->text('user_agent')->nullable();
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
            // Index on page_url with limited length to avoid key length issues
            $table->index(['page_url'], 'user_behaviors_page_url_index', 'btree', 191);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_behaviors');
    }
}; 