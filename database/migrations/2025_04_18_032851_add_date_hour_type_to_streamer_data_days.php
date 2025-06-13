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
        Schema::table('streamer_data_days', function (Blueprint $table) {
            $table->date('date')->nullable()->after('start_time');
            $table->time('hour')->nullable()->after('date');
            $table->enum('type', ['daily', 'hourly'])->default('daily')->after('hour');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('streamer_data_days', function (Blueprint $table) {
            $table->dropColumn(['date', 'hour', 'type']);
        });
    }
};
