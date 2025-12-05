<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->unsignedBigInteger('project_id')->nullable()->after('notes');
            $table->string('purpose')->nullable()->after('project_id');
            $table->string('usage_form_path')->nullable()->after('purpose');
        });
    }

    public function down()
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropColumn(['project_id', 'purpose', 'usage_form_path']);
        });
    }
};
