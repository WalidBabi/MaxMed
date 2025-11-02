<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('push_subscriptions', function (Blueprint $table) {
            $table->boolean('is_enabled')->default(true)->after('user_agent');
            $table->timestamp('last_received_at')->nullable()->after('is_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('push_subscriptions', function (Blueprint $table) {
            $table->dropColumn(['is_enabled', 'last_received_at']);
        });
    }
};



