<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('campaign_contacts', function (Blueprint $table) {
            $table->string('ab_test_variant')->nullable()->after('personalization_data'); // 'variant_a' or 'variant_b'
            
            // Index for performance when querying A/B test results
            $table->index(['campaign_id', 'ab_test_variant']);
        });
    }

    public function down()
    {
        Schema::table('campaign_contacts', function (Blueprint $table) {
            $table->dropIndex(['campaign_id', 'ab_test_variant']);
            $table->dropColumn('ab_test_variant');
        });
    }
}; 