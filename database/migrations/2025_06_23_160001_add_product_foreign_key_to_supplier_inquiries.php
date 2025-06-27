<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only proceed if the supplier_inquiries table exists and has product_id column
        if (Schema::hasTable('supplier_inquiries') && Schema::hasColumn('supplier_inquiries', 'product_id')) {
            // Check if the foreign key constraint already exists
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'supplier_inquiries' 
                AND COLUMN_NAME = 'product_id' 
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");
            
            if (empty($foreignKeys)) {
                try {
                    // Add foreign key constraint
                    DB::statement('
                        ALTER TABLE supplier_inquiries 
                        ADD CONSTRAINT supplier_inquiries_product_id_foreign 
                        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
                    ');
                    echo "Successfully added foreign key constraint for product_id in supplier_inquiries table.\n";
                } catch (\Exception $e) {
                    echo "Warning: Could not add foreign key constraint for product_id: " . $e->getMessage() . "\n";
                    echo "The table will function without the foreign key constraint.\n";
                    
                    // Log the error for debugging
                    \Log::warning('Failed to add foreign key constraint for supplier_inquiries.product_id', [
                        'error' => $e->getMessage(),
                        'table' => 'supplier_inquiries',
                        'column' => 'product_id',
                        'referenced_table' => 'products',
                        'referenced_column' => 'id'
                    ]);
                }
            } else {
                echo "Foreign key constraint for product_id already exists in supplier_inquiries table.\n";
            }
        } else {
            echo "Skipping foreign key addition - supplier_inquiries table or product_id column not found.\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only proceed if the supplier_inquiries table exists
        if (Schema::hasTable('supplier_inquiries')) {
            try {
                // Drop the foreign key constraint if it exists
                DB::statement('
                    ALTER TABLE supplier_inquiries 
                    DROP FOREIGN KEY supplier_inquiries_product_id_foreign
                ');
                echo "Successfully dropped foreign key constraint for product_id in supplier_inquiries table.\n";
            } catch (\Exception $e) {
                echo "Warning: Could not drop foreign key constraint for product_id: " . $e->getMessage() . "\n";
            }
        }
    }
}; 