<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('live_performance_days', function (Blueprint $table) {
            $table->unsignedTinyInteger('hour')->nullable()->change();
        });
    }
    
    public function down()
    {
        Schema::table('live_performance_days', function (Blueprint $table) {
            $table->time('hour')->nullable()->change();
        });
    }
    
};
