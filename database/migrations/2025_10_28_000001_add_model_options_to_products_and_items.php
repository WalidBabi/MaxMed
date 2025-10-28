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
        // Add model options to products
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'has_model_options')) {
                $table->boolean('has_model_options')->default(false)->after('has_size_options');
            }
            if (!Schema::hasColumn('products', 'model_options')) {
                $table->json('model_options')->nullable()->after('size_options');
            }
        });

        // Add model column to quote_items
        Schema::table('quote_items', function (Blueprint $table) {
            if (!Schema::hasColumn('quote_items', 'model')) {
                $table->string('model', 191)->nullable()->after('size');
            }
        });

        // Add model column to invoice_items
        Schema::table('invoice_items', function (Blueprint $table) {
            if (!Schema::hasColumn('invoice_items', 'model')) {
                $table->string('model', 191)->nullable()->after('size');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'model_options')) {
                $table->dropColumn('model_options');
            }
            if (Schema::hasColumn('products', 'has_model_options')) {
                $table->dropColumn('has_model_options');
            }
        });

        Schema::table('quote_items', function (Blueprint $table) {
            if (Schema::hasColumn('quote_items', 'model')) {
                $table->dropColumn('model');
            }
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            if (Schema::hasColumn('invoice_items', 'model')) {
                $table->dropColumn('model');
            }
        });
    }
};


