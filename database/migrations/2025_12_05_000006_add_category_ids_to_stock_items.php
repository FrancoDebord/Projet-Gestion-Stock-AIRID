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
        Schema::table('stock_items', function (Blueprint $table) {
            $table->unsignedBigInteger('product_category_id')->nullable()->after('category');
            $table->unsignedBigInteger('sub_category_id')->nullable()->after('product_category_id');

            $table->index('product_category_id');
            $table->index('sub_category_id');

            $table->foreign('product_category_id')->references('id')->on('product_categories')->onDelete('set null');
            $table->foreign('sub_category_id')->references('id')->on('sub_categories')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_items', function (Blueprint $table) {
            $table->dropForeign(['product_category_id']);
            $table->dropForeign(['sub_category_id']);
            $table->dropIndex(['product_category_id']);
            $table->dropIndex(['sub_category_id']);
            $table->dropColumn(['product_category_id', 'sub_category_id']);
        });
    }
};
