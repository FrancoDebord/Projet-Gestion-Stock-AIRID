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
            $table->unsignedBigInteger('stock_item_usage_request_id');
            $table->unsignedBigInteger('stock_item_id');
            $table->integer('requested_quantity');
            $table->integer('approved_quantity')->nullable();
            $table->text('usage_description')->nullable(); // description de l'usage
            $table->text('request_reason')->nullable(); // raison de la demande
            $table->text('facility_manager_notes')->nullable();
            $table->text('data_manager_notes')->nullable();
            $table->text('observation')->nullable();
            $table->timestamps();

            $table->foreign('stock_item_usage_request_id', 'usage_req_details_req_id_fk')->references('id')->on('stock_item_usage_requests');
            $table->foreign('stock_item_id', 'usage_req_details_item_id_fk')->references('id')->on('stock_items');
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
