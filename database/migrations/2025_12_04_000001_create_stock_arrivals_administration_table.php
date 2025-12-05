<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stock_arrivals_administration', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date_arrival');
            $table->string('sender', 150)->nullable();
            $table->text('description_globale')->nullable();
            $table->unsignedBigInteger('stock_location_destination');
            $table->unsignedBigInteger('administration_staff');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_arrivals_administration');
    }
};
