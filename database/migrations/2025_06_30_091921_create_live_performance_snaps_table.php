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
        Schema::create('live_performance_snaps', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('room_id');
            $table->date('date');
            $table->unsignedTinyInteger('hour')->nullable(); // nullable nếu type = 'daily'
            $table->enum('type', ['hourly', 'daily'])->default('hourly');

            // Host
            $table->unsignedBigInteger('main_host_id')->nullable();
            $table->unsignedBigInteger('support_host_id')->nullable();
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
            $table->foreign('main_host_id')->references('id')->on('staffs')->onDelete('set null');
            $table->foreign('support_host_id')->references('id')->on('staffs')->onDelete('set null');

            // Metrics
            $table->decimal('gmv', 15, 2)->default(0);
            $table->decimal('ads_total_cost', 15, 2)->default(0);
            $table->decimal('views', 15, 2)->default(0);
            $table->decimal('live_impressions', 15, 2)->default(0);
            $table->integer('items_sold')->default(0);
            $table->integer('product_clicks')->default(0);

            // Newly added
            $table->integer('comments')->default(0);
            $table->integer('shares')->default(0);

            // KPIs
            $table->decimal('ctr', 5, 2)->nullable();
            $table->decimal('ctor', 5, 2)->nullable();
            $table->decimal('entry_rate', 5, 2)->nullable();

            $table->timestamps();
            $table->unique(['room_id', 'date', 'hour', 'type']);
            $table->softDeletes(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_performance_snaps');
    }
};
