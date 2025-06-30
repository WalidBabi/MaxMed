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
            // Check if message column doesn't exist and add it
            if (!Schema::hasColumn('supplier_invitations', 'message')) {
                $table->text('message')->nullable()->after('contact_name');
            }
            
            // Also check for other potentially missing columns from the original migration
            if (!Schema::hasColumn('supplier_invitations', 'phone')) {
                $table->string('phone')->nullable()->after('contact_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier_invitations', function (Blueprint $table) {
            if (Schema::hasColumn('supplier_invitations', 'message')) {
                $table->dropColumn('message');
            }
            if (Schema::hasColumn('supplier_invitations', 'phone')) {
                $table->dropColumn('phone');
            }
        });
    }
}; 