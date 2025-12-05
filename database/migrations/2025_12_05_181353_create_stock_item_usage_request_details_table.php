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
        Schema::create('stock_item_usage_request_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('usage_request_id');
            $table->unsignedBigInteger('stock_item_id');
            $table->integer('requested_quantity');
            $table->text('usage_description')->nullable(); // description de l'usage
            $table->text('request_reason'); // raison de la demande
            $table->boolean('facility_manager_approval')->default(false); // autorisation_facility_manager
            $table->boolean('data_manager_approval')->default(false); // visa_data_manager
            $table->text('observations')->nullable(); // observation
            $table->integer('approved_quantity')->nullable(); // quantité approuvée
            $table->timestamps();

            $table->foreign('usage_request_id')->references('id')->on('stock_item_usage_requests');
            $table->foreign('stock_item_id')->references('id')->on('stock_items');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_item_usage_request_details');
    }
};
