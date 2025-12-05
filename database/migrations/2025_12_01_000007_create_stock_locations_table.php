<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stock_locations', function (Blueprint $table) {
            $table->id();
            $table->string('stock_name', 100)->unique();
            $table->dateTime('creation_date');
            $table->unsignedBigInteger('creator');
            $table->unsignedBigInteger('principal_manager')->nullable();
            $table->text('description')->nullable();
            $table->string('code_stock', 50)->unique();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_locations');
    }
};
