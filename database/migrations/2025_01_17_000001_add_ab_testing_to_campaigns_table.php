<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('campaigns', function (Blueprint $table) {
            // A/B Testing fields
            $table->boolean('is_ab_test')->default(false)->after('type');
            $table->string('ab_test_type')->nullable()->after('is_ab_test'); // 'subject_line', 'send_time', 'content'
            $table->integer('ab_test_split_percentage')->default(50)->after('ab_test_type'); // Percentage for variant A
            $table->string('subject_variant_b')->nullable()->after('subject'); // Alternative subject line
            $table->timestamp('ab_test_winner_selected_at')->nullable()->after('ab_test_split_percentage');
            $table->string('ab_test_winner')->nullable()->after('ab_test_winner_selected_at'); // 'variant_a' or 'variant_b'
            $table->json('ab_test_results')->nullable()->after('ab_test_winner'); // Store detailed test results
            
            // Indexes for performance
            $table->index(['is_ab_test', 'status']);
            $table->index(['ab_test_type', 'status']);
        });
    }

    public function down()
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropIndex(['is_ab_test', 'status']);
            $table->dropIndex(['ab_test_type', 'status']);
            
            $table->dropColumn([
                'is_ab_test',
                'ab_test_type',
                'ab_test_split_percentage',
                'subject_variant_b',
                'ab_test_winner_selected_at',
                'ab_test_winner',
                'ab_test_results'
            ]);
        });
    }
}; 