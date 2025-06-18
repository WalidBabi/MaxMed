<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('contact_list_contacts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contact_list_id');
            $table->unsignedBigInteger('marketing_contact_id');
            $table->timestamp('added_at')->useCurrent();
            
            $table->foreign('contact_list_id')->references('id')->on('contact_lists')->onDelete('cascade');
            $table->foreign('marketing_contact_id')->references('id')->on('marketing_contacts')->onDelete('cascade');
            
            $table->unique(['contact_list_id', 'marketing_contact_id'], 'contact_list_contact_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('contact_list_contacts');
    }
}; 