<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('supplier_invitations')) {
            Schema::create('supplier_invitations', function (Blueprint $table) {
                $table->id();
            
            // Invitation details
            $table->string('email')->index();
            $table->string('name');
            $table->string('company_name')->nullable();
            $table->string('token', 60)->unique();
            
            // Who invited and when
            $table->unsignedBigInteger('invited_by');
            $table->text('custom_message')->nullable();
            
            // Status and tracking
            $table->enum('status', ['pending', 'accepted', 'expired', 'cancelled'])->default('pending');
            $table->timestamp('expires_at');
            $table->timestamp('accepted_at')->nullable();
            
            // Link to created user (when accepted)
            $table->unsignedBigInteger('user_id')->nullable();
            
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('invited_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            
            // Indexes
            $table->index(['email', 'status']);
            $table->index(['token', 'status']);
            $table->index('expires_at');
            });
        }
    });
        } else {
            Schema::table('supplier_invitations', function (Blueprint $table) {
                // Check and add any missing columns
                $columns = Schema::getColumnListing('supplier_invitations');
                $schemaContent = '$table->id();
            
            // Invitation details
            $table->string(\'email\')->index();
            $table->string(\'name\');
            $table->string(\'company_name\')->nullable();
            $table->string(\'token\', 60)->unique();
            
            // Who invited and when
            $table->unsignedBigInteger(\'invited_by\');
            $table->text(\'custom_message\')->nullable();
            
            // Status and tracking
            $table->enum(\'status\', [\'pending\', \'accepted\', \'expired\', \'cancelled\'])->default(\'pending\');
            $table->timestamp(\'expires_at\');
            $table->timestamp(\'accepted_at\')->nullable();
            
            // Link to created user (when accepted)
            $table->unsignedBigInteger(\'user_id\')->nullable();
            
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign(\'invited_by\')->references(\'id\')->on(\'users\')->onDelete(\'cascade\');
            $table->foreign(\'user_id\')->references(\'id\')->on(\'users\')->onDelete(\'set null\');
            
            // Indexes
            $table->index([\'email\', \'status\']);
            $table->index([\'token\', \'status\']);
            $table->index(\'expires_at\');';
                
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Don't drop the table in production to preserve data
        // Only drop columns that were added in this migration if any
    }
}; 