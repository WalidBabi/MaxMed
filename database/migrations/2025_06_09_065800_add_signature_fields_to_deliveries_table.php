<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            if (!Schema::hasColumn('deliveries', 'customer_signature')) {
                $table->text('customer_signature')->nullable()->after('delivered_at');
            }
            if (!Schema::hasColumn('deliveries', 'signature_ip_address')) {
                $table->string('signature_ip_address', 45)->nullable()->after('customer_signature');
            }
            if (!Schema::hasColumn('deliveries', 'signed_at')) {
                $table->timestamp('signed_at')->nullable()->after('signature_ip_address');
            }
            if (!Schema::hasColumn('deliveries', 'delivery_conditions')) {
                $table->json('delivery_conditions')->nullable()->after('signed_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropColumn([
                'customer_signature',
                'signature_ip_address',
                'signed_at',
                'delivery_conditions'
            ]);
        });
    }
};
