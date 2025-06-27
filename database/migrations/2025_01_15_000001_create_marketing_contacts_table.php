<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('marketing_contacts')) {
            Schema::create('marketing_contacts', function (Blueprint $table) {
                $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->string('job_title')->nullable();
            $table->string('industry')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->json('custom_fields')->nullable();
            $table->enum('status', ['active', 'unsubscribed', 'bounced', 'complained'])->default('active');
            $table->timestamp('subscribed_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->string('source')->nullable(); // manual, import, website, etc.
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['email', 'status']);
            $table->index('status');
            });
        } else {
            Schema::table('marketing_contacts', function (Blueprint $table) {
                // Check and add any missing columns
                $columns = Schema::getColumnListing('marketing_contacts');
                $schemaContent = '$table->id();
            $table->string(\'first_name\');
            $table->string(\'last_name\');
            $table->string(\'email\')->unique();
            $table->string(\'phone\')->nullable();
            $table->string(\'company\')->nullable();
            $table->string(\'job_title\')->nullable();
            $table->string(\'industry\')->nullable();
            $table->string(\'country\')->nullable();
            $table->string(\'city\')->nullable();
            $table->json(\'custom_fields\')->nullable();
            $table->enum(\'status\', [\'active\', \'unsubscribed\', \'bounced\', \'complained\'])->default(\'active\');
            $table->timestamp(\'subscribed_at\')->nullable();
            $table->timestamp(\'unsubscribed_at\')->nullable();
            $table->string(\'source\')->nullable(); // manual, import, website, etc.
            $table->text(\'notes\')->nullable();
            $table->timestamps();
            
            $table->index([\'email\', \'status\']);
            $table->index(\'status\');';
                
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