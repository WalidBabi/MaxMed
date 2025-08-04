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
        if (Schema::hasTable('invoice_items')) {
            Schema::table('invoice_items', function (Blueprint $table) {
                if (Schema::hasColumn('invoice_items', 'tax')) {
                    $table->dropColumn('tax');
                }
                if (Schema::hasColumn('invoice_items', 'total')) {
                    $table->dropColumn('total');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('invoice_items')) {
            Schema::table('invoice_items', function (Blueprint $table) {
                if (!Schema::hasColumn('invoice_items', 'tax')) {
                    $table->decimal('tax', 10, 2)->default(0)->after('subtotal');
                }
                if (!Schema::hasColumn('invoice_items', 'total')) {
                    $table->decimal('total', 10, 2)->default(0)->after('tax');
                }
            });
        }
    }
}; 