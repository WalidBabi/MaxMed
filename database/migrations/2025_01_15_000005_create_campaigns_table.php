<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('campaigns')) {
            Schema::create('campaigns', function (Blueprint $table) {
                $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('subject');
            $table->longText('html_content');
            $table->longText('text_content')->nullable();
            $table->unsignedBigInteger('email_template_id')->nullable();
            $table->enum('type', ['one_time', 'recurring', 'drip'])->default('one_time');
            $table->enum('status', ['draft', 'scheduled', 'sending', 'sent', 'paused', 'cancelled'])->default('draft');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->json('recipients_criteria')->nullable(); // For dynamic recipient selection
            $table->integer('total_recipients')->default(0);
            $table->integer('sent_count')->default(0);
            $table->integer('delivered_count')->default(0);
            $table->integer('opened_count')->default(0);
            $table->integer('clicked_count')->default(0);
            $table->integer('bounced_count')->default(0);
            $table->integer('unsubscribed_count')->default(0);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            
            $table->index('email_template_id');
            $table->index('created_by');
            $table->index(['status', 'scheduled_at']);
            });
        }
    });
        } else {
            Schema::table('campaigns', function (Blueprint $table) {
                // Check and add any missing columns
                $columns = Schema::getColumnListing('campaigns');
                $schemaContent = '$table->id();
            $table->string(\'name\');
            $table->text(\'description\')->nullable();
            $table->string(\'subject\');
            $table->longText(\'html_content\');
            $table->longText(\'text_content\')->nullable();
            $table->unsignedBigInteger(\'email_template_id\')->nullable();
            $table->enum(\'type\', [\'one_time\', \'recurring\', \'drip\'])->default(\'one_time\');
            $table->enum(\'status\', [\'draft\', \'scheduled\', \'sending\', \'sent\', \'paused\', \'cancelled\'])->default(\'draft\');
            $table->timestamp(\'scheduled_at\')->nullable();
            $table->timestamp(\'sent_at\')->nullable();
            $table->json(\'recipients_criteria\')->nullable(); // For dynamic recipient selection
            $table->integer(\'total_recipients\')->default(0);
            $table->integer(\'sent_count\')->default(0);
            $table->integer(\'delivered_count\')->default(0);
            $table->integer(\'opened_count\')->default(0);
            $table->integer(\'clicked_count\')->default(0);
            $table->integer(\'bounced_count\')->default(0);
            $table->integer(\'unsubscribed_count\')->default(0);
            $table->unsignedBigInteger(\'created_by\');
            $table->timestamps();
            
            $table->index(\'email_template_id\');
            $table->index(\'created_by\');
            $table->index([\'status\', \'scheduled_at\']);';
                
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