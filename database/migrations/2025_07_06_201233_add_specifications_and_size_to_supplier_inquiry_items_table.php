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
        Schema::table('supplier_inquiry_items', function (Blueprint $table) {
            $table->text('specifications')->nullable()->after('product_specifications');
            $table->string('size')->nullable()->after('specifications');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier_inquiry_items', function (Blueprint $table) {
            $table->dropColumn(['specifications', 'size']);
        });
    }
};
