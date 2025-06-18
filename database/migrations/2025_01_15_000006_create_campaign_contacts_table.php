<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('campaign_contacts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('campaign_id');
            $table->unsignedBigInteger('marketing_contact_id');
            $table->enum('status', ['pending', 'sent', 'delivered', 'bounced', 'failed'])->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->integer('open_count')->default(0);
            $table->integer('click_count')->default(0);
            $table->string('bounce_reason')->nullable();
            $table->json('personalization_data')->nullable();
            $table->timestamps();
            
            $table->foreign('campaign_id')->references('id')->on('campaigns')->onDelete('cascade');
            $table->foreign('marketing_contact_id')->references('id')->on('marketing_contacts')->onDelete('cascade');
            
            $table->unique(['campaign_id', 'marketing_contact_id'], 'campaign_contact_unique');
            $table->index(['campaign_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('campaign_contacts');
    }
}; 