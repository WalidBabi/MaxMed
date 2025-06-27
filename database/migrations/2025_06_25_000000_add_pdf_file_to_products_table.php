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
            if (!Schema::hasColumn('products', 'pdf_file')) {
                $table->string('pdf_file')->nullable()->after('image_url');
                echo "Added pdf_file column to products table.\n";
            } else {
                echo "pdf_file column already exists in products table, skipping.\n";
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'pdf_file')) {
                $table->dropColumn('pdf_file');
                echo "Dropped pdf_file column from products table.\n";
            } else {
                echo "pdf_file column does not exist in products table, skipping drop.\n";
            }
        });
    }
}; 