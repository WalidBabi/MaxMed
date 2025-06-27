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
        if (!Schema::hasTable('deliveries')) {
            Schema::create('deliveries', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id');
                $table->string('tracking_number')->unique();
                $table->enum('status', ['pending', 'in_transit', 'delivered', 'failed'])->default('pending');
                $table->string('carrier')->nullable();
                $table->dateTime('estimated_delivery_date')->nullable();
                $table->dateTime('actual_delivery_date')->nullable();
                $table->text('delivery_notes')->nullable();
                $table->string('recipient_name')->nullable();
                $table->string('recipient_phone')->nullable();
                $table->timestamps();
                
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
