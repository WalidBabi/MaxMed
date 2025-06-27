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
        Schema::table('invoice_items', function (Blueprint $table) {
            if (!Schema::hasColumn('invoice_items', 'sort_order')) {
                if (Schema::hasColumn('invoice_items', 'total')) {
                    $table->integer('sort_order')->default(0)->after('total');
                } else {
                    $table->integer('sort_order')->default(0);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            if (Schema::hasColumn('invoice_items', 'sort_order')) {
                $table->dropColumn('sort_order');
            }
        });
    }
};
