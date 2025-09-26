<?php

// Using fully qualified class names inline to satisfy static analysis

return new class extends \Illuminate\Database\Migrations\Migration
{
    public function up(): void
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('role_user')) {
            \Illuminate\Support\Facades\Schema::create('role_user', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('role_id')->constrained()->onDelete('cascade');
                $table->timestamps();
                $table->unique(['user_id', 'role_id']);
            });
        }

        // Backfill from users.role_id if present (only for valid role_ids)
        if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'role_id')) {
            $pairs = \Illuminate\Support\Facades\DB::table('users')
                ->whereNotNull('role_id')
                ->join('roles', 'users.role_id', '=', 'roles.id') // ensure role exists
                ->select('users.id as user_id', 'users.role_id')
                ->get();

            foreach ($pairs as $pair) {
                $exists = \Illuminate\Support\Facades\DB::table('role_user')
                    ->where('user_id', $pair->user_id)
                    ->where('role_id', $pair->role_id)
                    ->exists();
                if (!$exists) {
                    \Illuminate\Support\Facades\DB::table('role_user')->insert([
                        'user_id' => $pair->user_id,
                        'role_id' => $pair->role_id,
                        'created_at' => \Carbon\Carbon::now(),
                        'updated_at' => \Carbon\Carbon::now(),
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        // Do not drop data if table exists; safe rollback only drops table in non-production
        if (\Illuminate\Support\Facades\Schema::hasTable('role_user')) {
            \Illuminate\Support\Facades\Schema::dropIfExists('role_user');
        }
    }
};


