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
        Schema::create('affiliate_partner_services', function (Blueprint $table) {
            $table->string('affiliate_partner_id', 50);
            $table->string('service_id', 50);
            $table->primary(['affiliate_partner_id', 'service_id']);
            
            $table->foreign('affiliate_partner_id')
                  ->references('affiliate_partner_id')
                  ->on('affiliate_partners')
                  ->onDelete('cascade');
                  
            $table->foreign('service_id')
                  ->references('service_id')
                  ->on('services')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliate_partner_services');
    }
};
