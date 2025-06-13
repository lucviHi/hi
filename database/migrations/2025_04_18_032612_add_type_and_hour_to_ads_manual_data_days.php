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
        Schema::table('ads_manual_data_days', function (Blueprint $table) {
            $table->enum('type', ['daily', 'hourly'])->default('daily')->after('date');
            $table->time('hour')->nullable()->after('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ads_manual_data_days', function (Blueprint $table) {
            $table->dropColumn(['type', 'hour']);
        });
    }
};
