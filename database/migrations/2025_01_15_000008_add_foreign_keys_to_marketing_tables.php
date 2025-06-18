<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Add foreign key constraints after all tables are created
        // Using try-catch to handle any constraint issues gracefully
        
        try {
            Schema::table('contact_lists', function (Blueprint $table) {
                $table->foreign('created_by', 'contact_lists_created_by_fk')->references('id')->on('users')->onDelete('cascade');
            });
        } catch (Exception $e) {
            // Skip if constraint already exists or fails
            echo "Skipping contact_lists foreign key: " . $e->getMessage() . "\n";
        }
        
        try {
            Schema::table('email_templates', function (Blueprint $table) {
                $table->foreign('created_by', 'email_templates_created_by_fk')->references('id')->on('users')->onDelete('cascade');
            });
        } catch (Exception $e) {
            // Skip if constraint already exists or fails
            echo "Skipping email_templates foreign key: " . $e->getMessage() . "\n";
        }
        
        try {
            Schema::table('campaigns', function (Blueprint $table) {
                $table->foreign('email_template_id', 'campaigns_email_template_id_fk')->references('id')->on('email_templates')->onDelete('set null');
                $table->foreign('created_by', 'campaigns_created_by_fk')->references('id')->on('users')->onDelete('cascade');
            });
        } catch (Exception $e) {
            // Skip if constraint already exists or fails
            echo "Skipping campaigns foreign keys: " . $e->getMessage() . "\n";
        }
    }

    public function down()
    {
        try {
            Schema::table('campaigns', function (Blueprint $table) {
                $table->dropForeign(['email_template_id']);
                $table->dropForeign(['created_by']);
            });
        } catch (Exception $e) {
            // Skip if constraint doesn't exist
        }
        
        try {
            Schema::table('email_templates', function (Blueprint $table) {
                $table->dropForeign(['created_by']);
            });
        } catch (Exception $e) {
            // Skip if constraint doesn't exist
        }
        
        try {
            Schema::table('contact_lists', function (Blueprint $table) {
                $table->dropForeign(['created_by']);
            });
        } catch (Exception $e) {
            // Skip if constraint doesn't exist
        }
    }
}; 