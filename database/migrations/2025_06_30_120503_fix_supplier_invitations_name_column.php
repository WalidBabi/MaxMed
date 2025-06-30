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
            // Check if there's a 'name' column that shouldn't be there
            if (Schema::hasColumn('supplier_invitations', 'name')) {
                // If the 'name' column exists, either drop it or make it nullable
                // Since we have 'contact_name', the 'name' column is redundant
                $table->dropColumn('name');
            }
            
            // Ensure contact_name is properly set up
            if (!Schema::hasColumn('supplier_invitations', 'contact_name')) {
                $table->string('contact_name')->after('email');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier_invitations', function (Blueprint $table) {
            // In case we need to rollback, add the name column back
            if (!Schema::hasColumn('supplier_invitations', 'name')) {
                $table->string('name')->nullable()->after('email');
            }
        });
    }
}; 