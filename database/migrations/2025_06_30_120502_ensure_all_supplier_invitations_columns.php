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
        Schema::table('supplier_invitations', function (Blueprint $table) {
            // Check and add all potentially missing columns from the original migration
            
            if (!Schema::hasColumn('supplier_invitations', 'token')) {
                $table->string('token', 64)->unique()->after('message');
            }
            
            if (!Schema::hasColumn('supplier_invitations', 'status')) {
                $table->enum('status', ['pending', 'accepted', 'rejected', 'expired'])->default('pending')->after('token');
            }
            
            if (!Schema::hasColumn('supplier_invitations', 'expires_at')) {
                $table->timestamp('expires_at')->after('status');
            }
            
            if (!Schema::hasColumn('supplier_invitations', 'accepted_at')) {
                $table->timestamp('accepted_at')->nullable()->after('expires_at');
            }
            
            if (!Schema::hasColumn('supplier_invitations', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable()->after('accepted_at');
            }
            
            if (!Schema::hasColumn('supplier_invitations', 'invited_by')) {
                $table->unsignedBigInteger('invited_by')->nullable()->after('rejected_at');
            }
            
            if (!Schema::hasColumn('supplier_invitations', 'supplier_id')) {
                $table->unsignedBigInteger('supplier_id')->nullable()->after('invited_by');
            }
            
            if (!Schema::hasColumn('supplier_invitations', 'deleted_at')) {
                $table->timestamp('deleted_at')->nullable()->after('updated_at');
            }
        });

        // Add foreign keys if they don't exist
        try {
            Schema::table('supplier_invitations', function (Blueprint $table) {
                $table->foreign('invited_by')->references('id')->on('users')->onDelete('set null');
                $table->foreign('supplier_id')->references('id')->on('users')->onDelete('set null');
            });
        } catch (\Exception $e) {
            // Foreign keys might already exist
        }

        // Add indexes if they don't exist
        try {
            Schema::table('supplier_invitations', function (Blueprint $table) {
                $table->index(['token', 'status']);
                $table->index('expires_at');
            });
        } catch (\Exception $e) {
            // Indexes might already exist
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier_invitations', function (Blueprint $table) {
            // Only drop columns if they exist
            $columnsToCheck = ['token', 'status', 'expires_at', 'accepted_at', 'rejected_at', 'invited_by', 'supplier_id', 'deleted_at'];
            
            foreach ($columnsToCheck as $column) {
                if (Schema::hasColumn('supplier_invitations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}; 