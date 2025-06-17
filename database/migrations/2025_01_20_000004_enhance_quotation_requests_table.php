<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Simple raw SQL approach to add foreign keys
        try {
            // Add supplier_id foreign key (references users.id since suppliers are users with supplier role)
            DB::statement('ALTER TABLE quotation_requests ADD CONSTRAINT fk_quotation_requests_supplier_id FOREIGN KEY (supplier_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE');
            echo "✅ Added supplier_id foreign key\n";
        } catch (\Exception $e) {
            echo "⚠️ supplier_id foreign key already exists or error: " . $e->getMessage() . "\n";
        }

        try {
            // Add generated_quote_id foreign key
            DB::statement('ALTER TABLE quotation_requests ADD CONSTRAINT fk_quotation_requests_generated_quote_id FOREIGN KEY (generated_quote_id) REFERENCES quotes(id) ON DELETE SET NULL ON UPDATE CASCADE');
            echo "✅ Added generated_quote_id foreign key\n";
        } catch (\Exception $e) {
            echo "⚠️ generated_quote_id foreign key already exists or error: " . $e->getMessage() . "\n";
        }

        try {
            // Add performance indexes
            DB::statement('CREATE INDEX idx_quotation_requests_status_created_at ON quotation_requests (status, created_at)');
            echo "✅ Added status_created_at index\n";
        } catch (\Exception $e) {
            echo "⚠️ Status index already exists or error: " . $e->getMessage() . "\n";
        }

        try {
            DB::statement('CREATE INDEX idx_quotation_requests_supplier_status ON quotation_requests (supplier_id, status)');
            echo "✅ Added supplier_status index\n";
        } catch (\Exception $e) {
            echo "⚠️ Supplier status index already exists or error: " . $e->getMessage() . "\n";
        }
    }

    protected function info($message)
    {
        echo $message . "\n";
    }

    public function down(): void
    {
        Schema::table('quotation_requests', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropForeign(['generated_quote_id']);
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex(['supplier_id', 'status']);
            
            $table->dropColumn([
                'status',
                'supplier_id',
                'forwarded_at',
                'supplier_responded_at',
                'internal_notes',
                'supplier_response',
                'supplier_notes',
                'generated_quote_id'
            ]);
        });
    }
}; 