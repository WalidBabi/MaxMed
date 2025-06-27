<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, ensure all current admin users have the admin role
        $adminRole = Role::where('name', 'admin')->first();
        
        if ($adminRole) {
            User::where('is_admin', true)->update(['role_id' => $adminRole->id]);
        }

        // Remove the is_admin column
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('email');
        });

        // Restore admin status based on role
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            User::where('role_id', $adminRole->id)->update(['is_admin' => true]);
        }
    }
}; 