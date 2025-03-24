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
        Schema::create('product_reservations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_id')->index('product_reservations_product_id_foreign');
            $table->unsignedBigInteger('user_id')->nullable()->index('product_reservations_user_id_foreign');
            $table->integer('quantity');
            $table->string('session_id');
            $table->timestamp('expires_at')->useCurrentOnUpdate()->useCurrent();
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_reservations');
    }
};
