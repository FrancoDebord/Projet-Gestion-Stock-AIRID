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
        Schema::create('stock_items', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('brand', 100)->nullable();
            $table->string('category', 100)->nullable();
            $table->text('description')->nullable();
            $table->integer('initial_quantity')->default(0);
            $table->integer('min_quantity')->default(5);
            $table->string('unit', 50)->default('piece');
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->unsignedBigInteger('stock_location_id');
            $table->enum('type_usage_product', ['finished', 'consumed'])->default('consumed');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_items');
    }
};
