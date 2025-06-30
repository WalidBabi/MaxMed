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
            // Check if contact_name column doesn't exist and add it
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
            if (Schema::hasColumn('supplier_invitations', 'contact_name')) {
                $table->dropColumn('contact_name');
            }
        });
    }
}; 