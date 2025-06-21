<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('campaigns', function (Blueprint $table) {
            // CTA A/B Testing fields
            $table->text('cta_text_variant_b')->nullable()->after('subject_variant_b');
            $table->string('cta_url_variant_b')->nullable()->after('cta_text_variant_b');
            $table->string('cta_color_variant_b')->nullable()->after('cta_url_variant_b');
            
            // Template A/B Testing fields
            $table->unsignedBigInteger('email_template_variant_b_id')->nullable()->after('email_template_id');
            $table->longText('html_content_variant_b')->nullable()->after('html_content');
            $table->longText('text_content_variant_b')->nullable()->after('text_content');
            
            // Send Time A/B Testing
            $table->timestamp('scheduled_at_variant_b')->nullable()->after('scheduled_at');
            
            // Indexes for performance
            $table->index('email_template_variant_b_id');
        });
    }

    public function down()
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropIndex(['email_template_variant_b_id']);
            
            $table->dropColumn([
                'cta_text_variant_b',
                'cta_url_variant_b', 
                'cta_color_variant_b',
                'email_template_variant_b_id',
                'html_content_variant_b',
                'text_content_variant_b',
                'scheduled_at_variant_b'
            ]);
        });
    }
}; 