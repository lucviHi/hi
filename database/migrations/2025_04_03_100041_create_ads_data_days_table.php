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
        Schema::create('ads_data_days', function (Blueprint $table) {
            $table->id();
            $table->date('date'); // Ngày của dữ liệu
            $table->foreignId('room_id')->constrained()->onDelete('cascade'); // Liên kết với room

            // Dữ liệu từ bảng Ads GMV Max
            $table->decimal('gmv_max_cost', 15, 2)->default(0); // Chi phí quảng cáo tự động
            $table->decimal('gmv_max_gross_revenue', 15, 2)->default(0); // Doanh thu từ quảng cáo tự động
            $table->decimal('gmv_max_store_revenue', 15, 2)->default(0); // Doanh thu từ quảng cáo tự động
            $table->decimal('gmv_max_real_revenue', 15, 2)->default(0); // Doanh thu thực từ quảng cáo tự động

            // Dữ liệu từ bảng Ads GMV Manual
            $table->decimal('manual_cost', 15, 2)->default(0); // Chi phí quảng cáo thủ công
            $table->decimal('manual_roas', 10, 2)->default(0); // ROAS quảng cáo thủ công
            $table->decimal('manual_revenue', 15, 2)->default(0); // Doanh thu từ quảng cáo thủ công

            // Tổng hợp dữ liệu quảng cáo
            $table->decimal('total_ads_cost', 15, 2)->default(0); // Tổng chi phí quảng cáo
            $table->decimal('total_ads_revenue', 15, 2)->default(0); // Tổng doanh thu quảng cáo
            $table->decimal('total_roas', 10, 2)->default(0); // ROAS tổng hợp

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ads_data_days');
    }
};
