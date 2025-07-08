<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Only proceed if the table exists
        if (!Schema::hasTable('campaign_contacts')) {
            return;
        }

        Schema::table('campaign_contacts', function (Blueprint $table) {
            // Get existing columns
            $columns = Schema::getColumnListing('campaign_contacts');
            
            // Add missing columns only if they don't exist
            if (!in_array('ab_test_variant', $columns)) {
                $table->string('ab_test_variant')->nullable()->after('personalization_data');
            }
            
            if (!in_array('status', $columns)) {
                $table->enum('status', ['pending', 'sent', 'delivered', 'bounced', 'failed'])->default('pending');
            }
            
            if (!in_array('sent_at', $columns)) {
                $table->timestamp('sent_at')->nullable();
            }
            
            if (!in_array('delivered_at', $columns)) {
                $table->timestamp('delivered_at')->nullable();
            }
            
            if (!in_array('opened_at', $columns)) {
                $table->timestamp('opened_at')->nullable();
            }
            
            if (!in_array('clicked_at', $columns)) {
                $table->timestamp('clicked_at')->nullable();
            }
            
            if (!in_array('open_count', $columns)) {
                $table->integer('open_count')->default(0);
            }
            
            if (!in_array('click_count', $columns)) {
                $table->integer('click_count')->default(0);
            }
            
            if (!in_array('bounce_reason', $columns)) {
                $table->string('bounce_reason')->nullable();
            }
            
            if (!in_array('personalization_data', $columns)) {
                $table->json('personalization_data')->nullable();
            }
        });
        
        // Add indexes separately and check if they already exist
        $this->addIndexIfNotExists('campaign_contacts', ['campaign_id', 'status'], 'campaign_contacts_campaign_id_status_index');
        $this->addIndexIfNotExists('campaign_contacts', ['campaign_id', 'ab_test_variant'], 'campaign_contacts_campaign_id_ab_test_variant_index');
    }

    public function down(): void
    {
        Schema::table('campaign_contacts', function (Blueprint $table) {
            // Don't drop columns in production to preserve data
            // Only drop indexes if they exist
            $this->dropIndexIfExists('campaign_contacts', 'campaign_contacts_campaign_id_status_index');
            $this->dropIndexIfExists('campaign_contacts', 'campaign_contacts_campaign_id_ab_test_variant_index');
        });
    }
    
    private function addIndexIfNotExists(string $table, array $columns, string $indexName): void
    {
        try {
            $indexes = \DB::select("SHOW INDEX FROM `{$table}`");
            $existingIndexes = collect($indexes)->pluck('Key_name')->toArray();
            
            if (!in_array($indexName, $existingIndexes)) {
                Schema::table($table, function (Blueprint $table) use ($columns, $indexName) {
                    $table->index($columns, $indexName);
                });
            }
        } catch (\Exception $e) {
            \Log::info("Could not add index {$indexName} to {$table}: " . $e->getMessage());
        }
    }
    
    private function dropIndexIfExists(string $table, string $indexName): void
    {
        try {
            $indexes = \DB::select("SHOW INDEX FROM `{$table}`");
            $existingIndexes = collect($indexes)->pluck('Key_name')->toArray();
            
            if (in_array($indexName, $existingIndexes)) {
                Schema::table($table, function (Blueprint $table) use ($indexName) {
                    $table->dropIndex($indexName);
                });
            }
        } catch (\Exception $e) {
            \Log::info("Could not drop index {$indexName} from {$table}: " . $e->getMessage());
        }
    }
}; 