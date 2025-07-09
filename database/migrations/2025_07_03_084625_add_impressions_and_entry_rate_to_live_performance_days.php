<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImpressionsAndEntryRateToLivePerformanceDays extends Migration
{
    public function up()
    {
        Schema::table('live_performance_days', function (Blueprint $table) {
            $table->unsignedBigInteger('live_impressions')->nullable()->after('gmv');
            $table->decimal('entry_rate', 6, 4)->nullable()->after('live_impressions');
        });
    }

    public function down()
    {
        Schema::table('live_performance_days', function (Blueprint $table) {
            $table->dropColumn(['live_impressions', 'entry_rate']);
        });
    }
}
