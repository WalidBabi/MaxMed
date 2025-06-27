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
        if (!Schema::hasTable('supplier_category_types')) {
            Schema::create('supplier_category_types', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();
            });
        } else {
            Schema::table('supplier_category_types', function (Blueprint $table) {
                // Add columns if they don't exist
                if (!Schema::hasColumn('supplier_category_types', 'name')) {
                    $table->string('name');
                }
                if (!Schema::hasColumn('supplier_category_types', 'slug')) {
                    $table->string('slug')->unique();
                }
                if (!Schema::hasColumn('supplier_category_types', 'description')) {
                    $table->text('description')->nullable();
                }
                if (!Schema::hasColumn('supplier_category_types', 'is_active')) {
                    $table->boolean('is_active')->default(true);
                }
                if (!Schema::hasColumn('supplier_category_types', 'deleted_at')) {
                    $table->softDeletes();
                }
            });
        }

        // Create pivot table for supplier categories if it doesn't exist
        if (!Schema::hasTable('supplier_category_type_user')) {
            Schema::create('supplier_category_type_user', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('supplier_category_type_id')->constrained()->cascadeOnDelete();
                $table->timestamps();

                // Use a shorter name for the unique index
                $table->unique(['user_id', 'supplier_category_type_id'], 'sct_user_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the pivot table first to maintain referential integrity
        Schema::dropIfExists('supplier_category_type_user');
        
        // Don't drop the main table in production to preserve data
        if (app()->environment('local', 'testing')) {
            Schema::dropIfExists('supplier_category_types');
        }
    }
}; 