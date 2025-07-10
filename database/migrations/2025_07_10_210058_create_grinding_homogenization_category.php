<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add Grinding & Homogenization Instruments category
        DB::table('categories')->insert([
            'name' => 'Grinding & Homogenization Instruments',
            'slug' => 'grinding-homogenization-instruments',
            'parent_id' => 66, // Lab Equipment parent category
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the category
        DB::table('categories')->where('name', 'Grinding & Homogenization Instruments')->delete();
    }
};
