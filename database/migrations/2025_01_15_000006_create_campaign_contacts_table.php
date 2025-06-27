<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('campaign_contacts')) {
            Schema::create('campaign_contacts', function (Blueprint $table) {
                $table->id();
            $table->unsignedBigInteger('campaign_id');
            $table->unsignedBigInteger('marketing_contact_id');
            $table->enum('status', ['pending', 'sent', 'delivered', 'bounced', 'failed'])->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->integer('open_count')->default(0);
            $table->integer('click_count')->default(0);
            $table->string('bounce_reason')->nullable();
            $table->json('personalization_data')->nullable();
            $table->timestamps();
            
            $table->foreign('campaign_id')->references('id')->on('campaigns')->onDelete('cascade');
            $table->foreign('marketing_contact_id')->references('id')->on('marketing_contacts')->onDelete('cascade');
            
            $table->unique(['campaign_id', 'marketing_contact_id'], 'campaign_contact_unique');
            $table->index(['campaign_id', 'status']);
            });
        } else {
            Schema::table('campaign_contacts', function (Blueprint $table) {
                // Check and add any missing columns
                $columns = Schema::getColumnListing('campaign_contacts');
                $schemaContent = '$table->id();
            $table->unsignedBigInteger(\'campaign_id\');
            $table->unsignedBigInteger(\'marketing_contact_id\');
            $table->enum(\'status\', [\'pending\', \'sent\', \'delivered\', \'bounced\', \'failed\'])->default(\'pending\');
            $table->timestamp(\'sent_at\')->nullable();
            $table->timestamp(\'delivered_at\')->nullable();
            $table->timestamp(\'opened_at\')->nullable();
            $table->timestamp(\'clicked_at\')->nullable();
            $table->integer(\'open_count\')->default(0);
            $table->integer(\'click_count\')->default(0);
            $table->string(\'bounce_reason\')->nullable();
            $table->json(\'personalization_data\')->nullable();
            $table->timestamps();
            
            $table->foreign(\'campaign_id\')->references(\'id\')->on(\'campaigns\')->onDelete(\'cascade\');
            $table->foreign(\'marketing_contact_id\')->references(\'id\')->on(\'marketing_contacts\')->onDelete(\'cascade\');
            
            $table->unique([\'campaign_id\', \'marketing_contact_id\'], \'campaign_contact_unique\');
            $table->index([\'campaign_id\', \'status\']);';
                
                // Parse the schema content to find column definitions
                preg_match_all('/$table->([^;]+);/', $schemaContent, $columnMatches);
                foreach ($columnMatches[1] as $columnDef) {
                    if (preg_match('/^(\w+)\(['"]([^'"]+)['"]\)/', $columnDef, $colMatch)) {
                        $columnName = $colMatch[2];
                        if (!in_array($columnName, $columns)) {
                            $table->{$colMatch[1]}($columnName);
                        }
                    }
                }
            });
        }
    });
    }

    public function down(): void
    {
        // Don't drop the table in production to preserve data
        // Only drop columns that were added in this migration if any
    }
}; 