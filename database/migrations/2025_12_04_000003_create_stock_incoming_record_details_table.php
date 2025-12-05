<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stock_incoming_record_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_item_id');
            $table->unsignedBigInteger('stock_incoming_record_id');
            $table->string('code_lot', 100);
            $table->string('batch_number', 100)->nullable();
            $table->integer('quantite_lot');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_incoming_record_details');
    }
};
