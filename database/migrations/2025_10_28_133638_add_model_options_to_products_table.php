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
            if (!Schema::hasColumn('products', 'has_model_options')) {
                $table->boolean('has_model_options')->default(false)->after('has_size_options');
            }
            if (!Schema::hasColumn('products', 'model_options')) {
                $table->json('model_options')->nullable()->after('has_model_options');
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
    }
};
