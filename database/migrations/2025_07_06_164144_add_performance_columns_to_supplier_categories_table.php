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
        Schema::table('supplier_categories', function (Blueprint $table) {
            // Performance tracking columns
            if (!Schema::hasColumn('supplier_categories', 'avg_response_time_hours')) {
                $table->decimal('avg_response_time_hours', 8, 2)->default(24.00)->after('commission_rate');
            }
            if (!Schema::hasColumn('supplier_categories', 'quotation_win_rate')) {
                $table->decimal('quotation_win_rate', 5, 2)->default(0.00)->after('avg_response_time_hours');
            }
            if (!Schema::hasColumn('supplier_categories', 'total_quotations')) {
                $table->integer('total_quotations')->default(0)->after('quotation_win_rate');
            }
            if (!Schema::hasColumn('supplier_categories', 'won_quotations')) {
                $table->integer('won_quotations')->default(0)->after('total_quotations');
            }
            if (!Schema::hasColumn('supplier_categories', 'avg_customer_rating')) {
                $table->decimal('avg_customer_rating', 3, 2)->default(5.00)->after('won_quotations');
            }
            if (!Schema::hasColumn('supplier_categories', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('last_quotation_at');
            }
            if (!Schema::hasColumn('supplier_categories', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('approved_at');
                $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier_categories', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'avg_response_time_hours',
                'quotation_win_rate',
                'total_quotations',
                'won_quotations',
                'avg_customer_rating',
                'approved_at',
                'approved_by'
            ]);
        });
    }
};
