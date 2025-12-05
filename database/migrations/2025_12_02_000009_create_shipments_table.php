<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('shipment_number', 100)->nullable()->index();
            $table->dateTime('received_at')->nullable();
            $table->unsignedBigInteger('received_by')->nullable();
            $table->integer('colis_count')->default(0);
            $table->string('sender', 150)->nullable();
            $table->unsignedBigInteger('to_location_id')->nullable();
            $table->text('admin_notes')->nullable();
            $table->dateTime('finalized_at')->nullable();
            $table->unsignedBigInteger('finalized_by')->nullable();
            $table->boolean('ack_sent')->default(false);
            $table->unsignedBigInteger('project_id')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('shipments');
    }
};
