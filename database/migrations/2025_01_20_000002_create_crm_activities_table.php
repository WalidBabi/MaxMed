<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('crm_activities')) {
            Schema::create('crm_activities', function (Blueprint $table) {
                $table->id();
            $table->foreignId('lead_id')->constrained('crm_leads')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('type', ['call', 'email', 'meeting', 'note', 'quote_sent', 'demo', 'follow_up', 'task']);
            $table->string('subject');
            $table->text('description')->nullable();
            $table->timestamp('activity_date');
            $table->enum('status', ['completed', 'scheduled', 'overdue'])->default('completed');
            $table->timestamp('due_date')->nullable();
            $table->json('metadata')->nullable(); // For storing additional data like email attachments, call duration, etc.
            $table->timestamps();
            
            $table->index(['lead_id', 'activity_date']);
            $table->index(['user_id', 'status']);
            $table->index(['type', 'activity_date']);
            });
        }
    });
        } else {
            Schema::table('crm_activities', function (Blueprint $table) {
                // Check and add any missing columns
                $columns = Schema::getColumnListing('crm_activities');
                $schemaContent = '$table->id();
            $table->foreignId(\'lead_id\')->constrained(\'crm_leads\')->onDelete(\'cascade\');
            $table->foreignId(\'user_id\')->constrained(\'users\')->onDelete(\'cascade\');
            $table->enum(\'type\', [\'call\', \'email\', \'meeting\', \'note\', \'quote_sent\', \'demo\', \'follow_up\', \'task\']);
            $table->string(\'subject\');
            $table->text(\'description\')->nullable();
            $table->timestamp(\'activity_date\');
            $table->enum(\'status\', [\'completed\', \'scheduled\', \'overdue\'])->default(\'completed\');
            $table->timestamp(\'due_date\')->nullable();
            $table->json(\'metadata\')->nullable(); // For storing additional data like email attachments, call duration, etc.
            $table->timestamps();
            
            $table->index([\'lead_id\', \'activity_date\']);
            $table->index([\'user_id\', \'status\']);
            $table->index([\'type\', \'activity_date\']);';
                
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