<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shipment_id');
            $table->string('code', 100)->nullable()->index();
            $table->unsignedBigInteger('assigned_by')->nullable();
            $table->dateTime('assigned_at')->nullable();
            $table->text('contents')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('packages');
    }
};
