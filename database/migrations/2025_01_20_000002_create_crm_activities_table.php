<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('crm_activities')) {
            Schema::create('crm_activities', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('type'); // call, email, meeting, task, note
                $table->string('subject');
                $table->text('description')->nullable();
                $table->timestamp('activity_date');
                $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
                $table->json('metadata')->nullable();
                $table->timestamps();
                
                $table->index(['user_id', 'activity_date']);
                $table->index(['user_id', 'status']);
                $table->index(['type', 'activity_date']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_activities');
    }
}; 