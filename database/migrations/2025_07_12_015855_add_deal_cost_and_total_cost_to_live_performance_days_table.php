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
            $table->decimal('deal_cost', 15, 2)->nullable()->after('ads_total_cost');
            $table->decimal('total_cost', 15, 2)->nullable()->after('deal_cost');
        });
    }

    public function down(): void
    {
        Schema::table('live_performance_days', function (Blueprint $table) {
            $table->dropColumn(['deal_cost', 'total_cost']);
        });
    }
};
