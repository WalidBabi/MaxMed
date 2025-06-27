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
        if (!Schema::hasTable('feedback')) {
            Schema::create('feedback', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('order_id');
                $table->integer('rating');
                $table->text('comment')->nullable();
                $table->timestamps();
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
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