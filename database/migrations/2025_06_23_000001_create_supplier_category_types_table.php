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
        // Drop existing tables if they exist
        Schema::dropIfExists('supplier_category_type_user');
        Schema::dropIfExists('supplier_category_types');

        Schema::create('supplier_category_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Create pivot table for supplier categories
        Schema::create('supplier_category_type_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_category_type_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            // Use a shorter name for the unique index
            $table->unique(['user_id', 'supplier_category_type_id'], 'sct_user_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_category_type_user');
        Schema::dropIfExists('supplier_category_types');
    }
}; 