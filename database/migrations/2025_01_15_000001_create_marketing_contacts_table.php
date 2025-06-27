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
        if (!Schema::hasTable('marketing_contacts')) {
            Schema::create('marketing_contacts', function (Blueprint $table) {
                $table->id();
                $table->string('email')->unique();
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->string('company')->nullable();
                $table->string('job_title')->nullable();
                $table->string('phone')->nullable();
                $table->string('industry')->nullable();
                $table->string('country')->nullable();
                $table->text('interests')->nullable();
                $table->boolean('subscribed')->default(true);
                $table->timestamp('last_contacted_at')->nullable();
                $table->integer('email_opens')->default(0);
                $table->integer('email_clicks')->default(0);
                $table->timestamps();
                $table->softDeletes();
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