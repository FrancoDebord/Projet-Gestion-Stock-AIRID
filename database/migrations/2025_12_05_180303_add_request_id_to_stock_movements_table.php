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
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->unsignedBigInteger('request_id')->nullable()->after('usage_form_path');
            $table->foreign('request_id', 'stock_movements_request_fk')->references('id')->on('stock_item_usage_requests');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropForeign('stock_movements_request_fk');
            $table->dropColumn('request_id');
        });
    }
};
