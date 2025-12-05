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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_item_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('type', ['in', 'out', 'adjustment'])->default('in');
            $table->integer('quantity');
            $table->text('reason')->nullable();
            $table->text('notes')->nullable();
            $table->string('reference')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
