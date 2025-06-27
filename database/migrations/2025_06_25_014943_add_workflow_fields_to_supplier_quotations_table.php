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
        Schema::table('supplier_quotations', function (Blueprint $table) {
            // Add workflow tracking fields only if they don't exist
            if (!Schema::hasColumn('supplier_quotations', 'admin_notes')) {
                $table->text('admin_notes')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('supplier_quotations', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('admin_notes');
            }
            if (!Schema::hasColumn('supplier_quotations', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable()->after('approved_at');
            }
            if (!Schema::hasColumn('supplier_quotations', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('rejected_at');
            }
            if (!Schema::hasColumn('supplier_quotations', 'rejected_by')) {
                $table->unsignedBigInteger('rejected_by')->nullable()->after('approved_by');
            }
        });

        // Add foreign key constraints if columns were added
        Schema::table('supplier_quotations', function (Blueprint $table) {
            if (Schema::hasColumn('supplier_quotations', 'approved_by')) {
                try {
                    $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
                } catch (\Exception $e) {
                    // Foreign key might already exist
                }
            }
            if (Schema::hasColumn('supplier_quotations', 'rejected_by')) {
                try {
                    $table->foreign('rejected_by')->references('id')->on('users')->onDelete('set null');
                } catch (\Exception $e) {
                    // Foreign key might already exist
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier_quotations', function (Blueprint $table) {
            if (Schema::hasColumn('supplier_quotations', 'approved_by')) {
                $table->dropForeign(['approved_by']);
            }
            if (Schema::hasColumn('supplier_quotations', 'rejected_by')) {
                $table->dropForeign(['rejected_by']);
            }
            
            $columnsToCheck = ['admin_notes', 'approved_at', 'rejected_at', 'approved_by', 'rejected_by'];
            $columnsToRemove = [];
            
            foreach ($columnsToCheck as $column) {
                if (Schema::hasColumn('supplier_quotations', $column)) {
                    $columnsToRemove[] = $column;
                }
            }
            
            if (!empty($columnsToRemove)) {
                $table->dropColumn($columnsToRemove);
            }
        });
    }
};
