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
        Schema::table('supplier_quotations', function (Blueprint $table) {
            // Check if column exists before adding to avoid duplicates
            if (!Schema::hasColumn('supplier_quotations', 'supplier_inquiry_id')) {
                try {
                    $table->foreignId('supplier_inquiry_id')->nullable()->after('supplier_id')
                          ->constrained('supplier_inquiries')->onDelete('cascade');
                    echo "Added supplier_inquiry_id column to supplier_quotations table.\n";
                } catch (\Exception $e) {
                    echo "Warning: Could not add supplier_inquiry_id column: " . $e->getMessage() . "\n";
                }
            } else {
                echo "supplier_inquiry_id column already exists in supplier_quotations table, skipping.\n";
            }
            
            if (!Schema::hasColumn('supplier_quotations', 'order_id')) {
                try {
                    $table->foreignId('order_id')->nullable()->after('supplier_id')
                          ->constrained('orders')->onDelete('cascade');
                    echo "Added order_id column to supplier_quotations table.\n";
                } catch (\Exception $e) {
                    echo "Warning: Could not add order_id column: " . $e->getMessage() . "\n";
                }
            } else {
                echo "order_id column already exists in supplier_quotations table, skipping.\n";
            }
            
            if (!Schema::hasColumn('supplier_quotations', 'product_id')) {
                try {
                    $table->foreignId('product_id')->nullable()->after('supplier_id')
                          ->constrained('products')->onDelete('set null');
                    echo "Added product_id column to supplier_quotations table.\n";
                } catch (\Exception $e) {
                    echo "Warning: Could not add product_id column: " . $e->getMessage() . "\n";
                }
            } else {
                echo "product_id column already exists in supplier_quotations table, skipping.\n";
            }
            
            if (!Schema::hasColumn('supplier_quotations', 'total_amount')) {
                try {
                    // Check which shipping column exists and add total_amount after it
                    if (Schema::hasColumn('supplier_quotations', 'shipping_cost')) {
                        $table->decimal('total_amount', 10, 2)->nullable()->after('shipping_cost');
                    } elseif (Schema::hasColumn('supplier_quotations', 'shipping')) {
                        $table->decimal('total_amount', 10, 2)->nullable()->after('shipping');
                    } else {
                        // If neither shipping column exists, add at the end
                        $table->decimal('total_amount', 10, 2)->nullable();
                    }
                    echo "Added total_amount column to supplier_quotations table.\n";
                } catch (\Exception $e) {
                    echo "Warning: Could not add total_amount column: " . $e->getMessage() . "\n";
                }
            } else {
                echo "total_amount column already exists in supplier_quotations table, skipping.\n";
            }
            
            if (!Schema::hasColumn('supplier_quotations', 'size')) {
                try {
                    $table->string('size')->nullable()->after('total_amount');
                    echo "Added size column to supplier_quotations table.\n";
                } catch (\Exception $e) {
                    echo "Warning: Could not add size column: " . $e->getMessage() . "\n";
                }
            } else {
                echo "size column already exists in supplier_quotations table, skipping.\n";
            }
            
            if (!Schema::hasColumn('supplier_quotations', 'approved_at')) {
                try {
                    $table->timestamp('approved_at')->nullable()->after('status');
                    echo "Added approved_at column to supplier_quotations table.\n";
                } catch (\Exception $e) {
                    echo "Warning: Could not add approved_at column: " . $e->getMessage() . "\n";
                }
            } else {
                echo "approved_at column already exists in supplier_quotations table, skipping.\n";
            }
            
            if (!Schema::hasColumn('supplier_quotations', 'rejected_at')) {
                try {
                    $table->timestamp('rejected_at')->nullable()->after('approved_at');
                    echo "Added rejected_at column to supplier_quotations table.\n";
                } catch (\Exception $e) {
                    echo "Warning: Could not add rejected_at column: " . $e->getMessage() . "\n";
                }
            } else {
                echo "rejected_at column already exists in supplier_quotations table, skipping.\n";
            }
            
            if (!Schema::hasColumn('supplier_quotations', 'rejection_reason')) {
                try {
                    $table->text('rejection_reason')->nullable()->after('rejected_at');
                    echo "Added rejection_reason column to supplier_quotations table.\n";
                } catch (\Exception $e) {
                    echo "Warning: Could not add rejection_reason column: " . $e->getMessage() . "\n";
                }
            } else {
                echo "rejection_reason column already exists in supplier_quotations table, skipping.\n";
            }
        });

        // Make quotation_request_id nullable to support both systems
        try {
            if (Schema::hasColumn('supplier_quotations', 'quotation_request_id')) {
                DB::statement('ALTER TABLE supplier_quotations MODIFY quotation_request_id BIGINT UNSIGNED NULL');
                echo "Made quotation_request_id column nullable in supplier_quotations table.\n";
            } else {
                echo "quotation_request_id column not found in supplier_quotations table, skipping modification.\n";
            }
        } catch (\Exception $e) {
            echo "Warning: Could not modify quotation_request_id column: " . $e->getMessage() . "\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier_quotations', function (Blueprint $table) {
            // Remove added columns if they exist
            if (Schema::hasColumn('supplier_quotations', 'supplier_inquiry_id')) {
                $table->dropForeign(['supplier_inquiry_id']);
                $table->dropColumn('supplier_inquiry_id');
            }
            
            if (Schema::hasColumn('supplier_quotations', 'order_id')) {
                $table->dropForeign(['order_id']);
                $table->dropColumn('order_id');
            }
            
            if (Schema::hasColumn('supplier_quotations', 'product_id')) {
                $table->dropForeign(['product_id']);
                $table->dropColumn('product_id');
            }
            
            if (Schema::hasColumn('supplier_quotations', 'total_amount')) {
                $table->dropColumn('total_amount');
            }
            
            if (Schema::hasColumn('supplier_quotations', 'size')) {
                $table->dropColumn('size');
            }
            
            if (Schema::hasColumn('supplier_quotations', 'approved_at')) {
                $table->dropColumn('approved_at');
            }
            
            if (Schema::hasColumn('supplier_quotations', 'rejected_at')) {
                $table->dropColumn('rejected_at');
            }
            
            if (Schema::hasColumn('supplier_quotations', 'rejection_reason')) {
                $table->dropColumn('rejection_reason');
            }
        });
    }
};
