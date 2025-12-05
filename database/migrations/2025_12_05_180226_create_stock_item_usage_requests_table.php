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
        Schema::create('stock_item_usage_requests', function (Blueprint $table) {
            $table->id();
            $table->dateTime('request_date');
            $table->unsignedBigInteger('requester_id'); // Celui qui demande
            $table->unsignedBigInteger('project_id')->nullable();
            $table->string('code_machine', 100)->nullable(); // numéro de machine
            $table->string('room_number', 50)->nullable(); // numéro du bureau
            $table->enum('status', ['pending', 'approved_facility_manager', 'approved_data_manager', 'rejected', 'completed'])->default('pending');
            $table->text('general_notes')->nullable();
            $table->unsignedBigInteger('facility_manager_id')->nullable(); // Qui valide
            $table->dateTime('facility_manager_approval_date')->nullable();
            $table->text('facility_manager_notes')->nullable();
            $table->unsignedBigInteger('data_manager_id')->nullable(); // Visa data manager
            $table->dateTime('data_manager_approval_date')->nullable();
            $table->text('data_manager_notes')->nullable();
            $table->timestamps();

            $table->foreign('requester_id', 'usage_req_requester_fk')->references('id')->on('users');
            $table->foreign('project_id', 'usage_req_project_fk')->references('id')->on('projects');
            $table->foreign('facility_manager_id', 'usage_req_facility_mgr_fk')->references('id')->on('users');
            $table->foreign('data_manager_id', 'usage_req_data_mgr_fk')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_item_usage_requests');
    }
};
