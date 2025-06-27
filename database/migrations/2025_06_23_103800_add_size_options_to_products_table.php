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
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'has_size_options')) {
                $table->boolean('has_size_options')->default(false)->after('price_aed');
                echo "Added has_size_options column to products table.\n";
            } else {
                echo "has_size_options column already exists in products table, skipping.\n";
            }
            if (!Schema::hasColumn('products', 'size_options')) {
                $table->json('size_options')->nullable()->after('has_size_options');
                echo "Added size_options column to products table.\n";
            } else {
                echo "size_options column already exists in products table, skipping.\n";
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'has_size_options')) {
                $table->dropColumn('has_size_options');
                echo "Dropped has_size_options column from products table.\n";
            }
            if (Schema::hasColumn('products', 'size_options')) {
                $table->dropColumn('size_options');
                echo "Dropped size_options column from products table.\n";
            }
        });
    }
}; 