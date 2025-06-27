<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('crm_deals')) {
            Schema::create('crm_deals', function (Blueprint $table) {
                $table->id();
            $table->string('deal_name');
            $table->foreignId('lead_id')->constrained('crm_leads')->onDelete('cascade');
            $table->decimal('deal_value', 15, 2);
            $table->enum('stage', ['prospecting', 'qualification', 'needs_analysis', 'proposal', 'negotiation', 'closed_won', 'closed_lost'])->default('prospecting');
            $table->integer('probability')->default(10); // Percentage 0-100
            $table->date('expected_close_date');
            $table->date('actual_close_date')->nullable();
            $table->text('description')->nullable();
            $table->json('products_interested')->nullable(); // Store product IDs they're interested in
            $table->foreignId('assigned_to')->constrained('users')->onDelete('cascade');
            $table->text('loss_reason')->nullable(); // For when deal is lost
            $table->timestamps();
            
            $table->index(['stage', 'expected_close_date']);
            $table->index(['assigned_to', 'stage']);
            });
        }
    });
        } else {
            Schema::table('crm_deals', function (Blueprint $table) {
                // Check and add any missing columns
                $columns = Schema::getColumnListing('crm_deals');
                $schemaContent = '$table->id();
            $table->string(\'deal_name\');
            $table->foreignId(\'lead_id\')->constrained(\'crm_leads\')->onDelete(\'cascade\');
            $table->decimal(\'deal_value\', 15, 2);
            $table->enum(\'stage\', [\'prospecting\', \'qualification\', \'needs_analysis\', \'proposal\', \'negotiation\', \'closed_won\', \'closed_lost\'])->default(\'prospecting\');
            $table->integer(\'probability\')->default(10); // Percentage 0-100
            $table->date(\'expected_close_date\');
            $table->date(\'actual_close_date\')->nullable();
            $table->text(\'description\')->nullable();
            $table->json(\'products_interested\')->nullable(); // Store product IDs they\'re interested in
            $table->foreignId(\'assigned_to\')->constrained(\'users\')->onDelete(\'cascade\');
            $table->text(\'loss_reason\')->nullable(); // For when deal is lost
            $table->timestamps();
            
            $table->index([\'stage\', \'expected_close_date\']);
            $table->index([\'assigned_to\', \'stage\']);';
                
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