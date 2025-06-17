<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('ads_manual_data_days', function (Blueprint $table) {
            $table->integer('hour')->nullable()->change();
        });

        Schema::table('ads_auto_data_days', function (Blueprint $table) {
            $table->integer('hour')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('ads_manual_data_days', function (Blueprint $table) {
            $table->time('hour')->nullable()->change(); // nếu cần rollback
        });

        Schema::table('ads_auto_data_days', function (Blueprint $table) {
            $table->time('hour')->nullable()->change(); // nếu cần rollback
        });
    }
};
