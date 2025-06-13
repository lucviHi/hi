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
        Schema::table('live_performance_days', function (Blueprint $table) {
            $table->bigInteger('live_impressions')->default(0)->after('gmv');
            $table->decimal('entry_rate', 6, 4)->default(0)->after('live_impressions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('live_performance_days', function (Blueprint $table) {
            $table->dropColumn('live_impressions');
            $table->dropColumn('entry_rate');
        });
    }
};
