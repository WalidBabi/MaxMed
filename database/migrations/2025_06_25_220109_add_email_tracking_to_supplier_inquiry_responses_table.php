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
        Schema::table('supplier_inquiry_responses', function (Blueprint $table) {
            $table->timestamp('email_sent_at')->nullable()->after('notes');
            $table->boolean('email_sent_successfully')->default(false)->after('email_sent_at');
            $table->text('email_error')->nullable()->after('email_sent_successfully');
            $table->timestamp('email_opened_at')->nullable()->after('email_error');
            $table->integer('email_click_count')->default(0)->after('email_opened_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier_inquiry_responses', function (Blueprint $table) {
            $table->dropColumn([
                'email_sent_at',
                'email_sent_successfully', 
                'email_error',
                'email_opened_at',
                'email_click_count'
            ]);
        });
    }
};
