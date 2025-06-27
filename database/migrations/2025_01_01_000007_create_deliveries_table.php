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
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->index();
            $table->string('tracking_number')->unique()->nullable();
            $table->string('status')->default('pending'); // pending, processing, in_transit, delivered, cancelled
            $table->string('carrier')->nullable();
            $table->text('shipping_address');
            $table->text('billing_address')->nullable();
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->decimal('total_weight', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();

            // Add foreign key constraint separately
            $table->foreign('order_id')
                  ->references('id')
                  ->on('orders')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
