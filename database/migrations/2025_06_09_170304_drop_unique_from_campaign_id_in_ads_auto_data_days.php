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
        Schema::table('ads_auto_data_days', function (Blueprint $table) {
            $table->dropUnique('ads_auto_data_days_campaign_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ads_auto_data_days', function (Blueprint $table) {
            //
        });
    }
};
