<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('subject');
            $table->longText('html_content')->nullable();
            $table->longText('text_content')->nullable();
            $table->json('variables')->nullable(); // Available template variables
            $table->string('category')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            
            $table->index('created_by');
            $table->index(['category', 'is_active']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('email_templates');
    }
}; 