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
        Schema::table('supplier_information', function (Blueprint $table) {
            if (!Schema::hasColumn('supplier_information', 'brand_id')) {
                $table->unsignedBigInteger('brand_id')->nullable()->after('company_name');
                $table->foreign('brand_id')->references('id')->on('brands')->onDelete('set null');
                $table->index('brand_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier_information', function (Blueprint $table) {
            if (Schema::hasColumn('supplier_information', 'brand_id')) {
                $table->dropForeign(['brand_id']);
                $table->dropIndex(['brand_id']);
                $table->dropColumn('brand_id');
            }
        });
    }
};
