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
        if (!Schema::hasTable('transactions')) {
            Schema::create('transactions', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('order_id')->index('transactions_order_id_foreign');
                $table->unsignedBigInteger('user_id')->index('transactions_user_id_foreign');
                $table->decimal('amount', 10);
                $table->string('payment_method');
                $table->string('status');
                $table->string('transaction_id')->unique();
                $table->timestamps();
            });
        } else {
            Schema::table('transactions', function (Blueprint $table) {
                // Check and add any missing columns
                $columns = Schema::getColumnListing('transactions');
                if (!in_array('order_id', $columns)) {
                    $table->unsignedBigInteger('order_id')->index('transactions_order_id_foreign');
                }
                if (!in_array('user_id', $columns)) {
                    $table->unsignedBigInteger('user_id')->index('transactions_user_id_foreign');
                }
                if (!in_array('amount', $columns)) {
                    $table->decimal('amount', 10);
                }
                if (!in_array('payment_method', $columns)) {
                    $table->string('payment_method');
                }
                if (!in_array('status', $columns)) {
                    $table->string('status');
                }
                if (!in_array('transaction_id', $columns)) {
                    $table->string('transaction_id')->unique();
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
