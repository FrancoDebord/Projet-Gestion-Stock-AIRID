<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('shipment_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shipment_id');
            $table->string('path', 255);
            $table->string('original_name', 255)->nullable();
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('shipment_documents');
    }
};
