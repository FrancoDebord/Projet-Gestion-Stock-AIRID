<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stock_incoming_records', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date_reception');
            $table->unsignedBigInteger('stock_arrival_admin_id');
            $table->text('description_globale')->nullable();
            $table->unsignedBigInteger('receiver');
            $table->unsignedBigInteger('stock_location_destination_id');
            $table->unsignedBigInteger('project_id')->nullable();
            $table->string('sender', 150)->nullable();
            $table->string('certificat_analyse', 255)->nullable();
            $table->string('msds', 255)->nullable();
            $table->string('borderau_livraison', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_incoming_records');
    }
};
