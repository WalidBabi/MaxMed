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
        if (!Schema::hasTable('product_reservations')) {
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
        } else {
            Schema::table('product_reservations', function (Blueprint $table) {
                // Check and add any missing columns
                $columns = Schema::getColumnListing('product_reservations');
                if (!in_array('product_id', $columns)) {
                    $table->unsignedBigInteger('product_id')->index('product_reservations_product_id_foreign');
                }
                if (!in_array('user_id', $columns)) {
                    $table->unsignedBigInteger('user_id')->nullable()->index('product_reservations_user_id_foreign');
                }
                if (!in_array('quantity', $columns)) {
                    $table->integer('quantity');
                }
                if (!in_array('session_id', $columns)) {
                    $table->string('session_id');
                }
                if (!in_array('expires_at', $columns)) {
                    $table->timestamp('expires_at')->useCurrentOnUpdate()->useCurrent();
                }
                if (!in_array('status', $columns)) {
                    $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
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
