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
        Schema::table('customers', function (Blueprint $table) {
            $table->string('billing_google_maps_link')->nullable()->after('billing_country');
            $table->string('shipping_google_maps_link')->nullable()->after('shipping_country');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['billing_google_maps_link', 'shipping_google_maps_link']);
        });
    }
};
