<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('email_logs')) {
            Schema::create('email_logs', function (Blueprint $table) {
                $table->id();
            $table->unsignedBigInteger('campaign_id')->nullable();
            $table->unsignedBigInteger('marketing_contact_id');
            $table->string('email');
            $table->string('subject');
            $table->enum('type', ['campaign', 'transactional', 'test'])->default('campaign');
            $table->enum('status', ['pending', 'sent', 'delivered', 'bounced', 'failed', 'spam'])->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->string('message_id')->nullable();
            $table->string('bounce_reason')->nullable();
            $table->text('error_message')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->foreign('campaign_id')->references('id')->on('campaigns')->onDelete('set null');
            $table->foreign('marketing_contact_id')->references('id')->on('marketing_contacts')->onDelete('cascade');
            
            $table->index(['email', 'status']);
            $table->index(['campaign_id', 'status']);
            $table->index('sent_at');
            });
        }
    });
        } else {
            Schema::table('email_logs', function (Blueprint $table) {
                // Check and add any missing columns
                $columns = Schema::getColumnListing('email_logs');
                $schemaContent = '$table->id();
            $table->unsignedBigInteger(\'campaign_id\')->nullable();
            $table->unsignedBigInteger(\'marketing_contact_id\');
            $table->string(\'email\');
            $table->string(\'subject\');
            $table->enum(\'type\', [\'campaign\', \'transactional\', \'test\'])->default(\'campaign\');
            $table->enum(\'status\', [\'pending\', \'sent\', \'delivered\', \'bounced\', \'failed\', \'spam\'])->default(\'pending\');
            $table->timestamp(\'sent_at\')->nullable();
            $table->timestamp(\'delivered_at\')->nullable();
            $table->timestamp(\'opened_at\')->nullable();
            $table->timestamp(\'clicked_at\')->nullable();
            $table->string(\'message_id\')->nullable();
            $table->string(\'bounce_reason\')->nullable();
            $table->text(\'error_message\')->nullable();
            $table->string(\'ip_address\')->nullable();
            $table->string(\'user_agent\')->nullable();
            $table->json(\'metadata\')->nullable();
            $table->timestamps();
            
            $table->foreign(\'campaign_id\')->references(\'id\')->on(\'campaigns\')->onDelete(\'set null\');
            $table->foreign(\'marketing_contact_id\')->references(\'id\')->on(\'marketing_contacts\')->onDelete(\'cascade\');
            
            $table->index([\'email\', \'status\']);
            $table->index([\'campaign_id\', \'status\']);
            $table->index(\'sent_at\');';
                
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