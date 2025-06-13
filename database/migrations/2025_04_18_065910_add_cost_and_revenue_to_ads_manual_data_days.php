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
            $table->decimal('cost_vnd', 12, 2)->default(0)->after('cost_usd');
            $table->decimal('manual_revenue', 15, 2)->default(0)->after('roas_payment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ads_manual_data_days', function (Blueprint $table) {
            $table->dropColumn(['cost_vnd', 'manual_revenue']);
        });
    }
};
