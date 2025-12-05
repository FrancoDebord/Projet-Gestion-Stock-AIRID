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
        Schema::table('stock_arrivals_administration', function (Blueprint $table) {
            $table->string('bordereau_delivery')->nullable()->after('description_globale');
            $table->string('certificate_analysis')->nullable()->after('bordereau_delivery');
            $table->string('msds')->nullable()->after('certificate_analysis');
            $table->string('other_document')->nullable()->after('msds');
            $table->unsignedBigInteger('staff_transmis_stock')->nullable()->after('administration_staff');

            $table->index('staff_transmis_stock');
            $table->foreign('staff_transmis_stock')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_arrivals_administration', function (Blueprint $table) {
            $table->dropForeign(['staff_transmis_stock']);
            $table->dropIndex(['staff_transmis_stock']);
            $table->dropColumn(['bordereau_delivery','certificate_analysis','msds','other_document','staff_transmis_stock']);
        });
    }
};
