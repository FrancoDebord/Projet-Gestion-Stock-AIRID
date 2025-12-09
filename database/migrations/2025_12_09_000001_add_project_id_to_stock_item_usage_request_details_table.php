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
        Schema::table('stock_item_usage_request_details', function (Blueprint $table) {
            $table->unsignedBigInteger('project_id')->nullable()->after('usage_request_id');
            $table->foreign('project_id', 'usage_req_details_project_fk')->references('id')->on('projects');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_item_usage_request_details', function (Blueprint $table) {
            $table->dropForeign('usage_req_details_project_fk');
            $table->dropColumn('project_id');
        });
    }
};