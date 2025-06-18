<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('contact_lists', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('criteria')->nullable(); // For dynamic lists based on criteria
            $table->enum('type', ['static', 'dynamic'])->default('static');
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            
            $table->index('created_by');
        });
    }

    public function down()
    {
        Schema::dropIfExists('contact_lists');
    }
}; 