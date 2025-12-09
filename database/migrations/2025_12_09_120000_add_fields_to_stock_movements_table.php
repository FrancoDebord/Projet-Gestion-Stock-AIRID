<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->string('batch_number', 100)->nullable()->after('usage_form_path');
            $table->unsignedBigInteger('stock_incoming_detail_id')->nullable()->after('batch_number');
            $table->dateTime('date_mouvement')->nullable()->after('stock_incoming_detail_id');
            $table->unsignedBigInteger('stock_item_usage_request_id')->nullable()->after('date_mouvement');

            $table->foreign('stock_incoming_detail_id', 'stock_movements_incoming_detail_fk')
                ->references('id')->on('stock_incoming_record_details');

            $table->foreign('stock_item_usage_request_id', 'stock_movements_usage_request_fk')
                ->references('id')->on('stock_item_usage_requests');
        });
    }

    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropForeign('stock_movements_incoming_detail_fk');
            $table->dropForeign('stock_movements_usage_request_fk');
            $table->dropColumn(['batch_number', 'stock_incoming_detail_id', 'date_mouvement', 'stock_item_usage_request_id']);
        });
    }
};

