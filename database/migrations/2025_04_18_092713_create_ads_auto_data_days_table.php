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
        Schema::create('ads_auto_data_days', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('room_id'); // Kênh liên quan
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
            $table->date('date'); // Ngày báo cáo
            $table->time('hour')->nullable(); // Giờ báo cáo (có thể null)
            $table->enum('type', ['hourly', 'daily']); 
            $table->bigInteger('campaign_id')->unique();
            $table->string('campaign_name');
            $table->bigInteger('cost')->default(0); // Chi phí
            $table->bigInteger('net_cost')->default(0); // Chi phí ròng
            $table->integer('sku_orders')->default(0); // Đơn hàng (SKU)
            $table->bigInteger('cost_per_order')->default(0); // Chi phí mỗi đơn hàng
            $table->bigInteger('gross_revenue')->default(0); // Doanh thu gộp
            $table->decimal('roi', 8, 2)->default(0); // ROI
            $table->bigInteger('live_views')->default(0); // Số lượt xem phiên LIVE
            $table->bigInteger('cost_per_view')->default(0); // Chi phí mỗi lượt xem
            $table->bigInteger('ten_sec_views')->default(0); // Số lượt xem trong 10 giây
            $table->bigInteger('cost_per_ten_sec_view')->default(0); // Chi phí mỗi lượt xem 10 giây
            $table->bigInteger('followers')->default(0); // Số lượt theo dõi
            $table->integer('store_orders')->default(0); // Đơn hàng tại cửa hàng hiện tại
            $table->bigInteger('cost_per_store_order')->default(0); // Chi phí mỗi đơn hàng (Cửa hàng)
            $table->bigInteger('gross_revenue_store')->default(0); // Doanh thu gộp (Cửa hàng)
            $table->decimal('roi_store', 8, 2)->default(0); // ROI (Cửa hàng)
            $table->string('currency', 10)->default('VND'); // Đơn vị tiền tệ
            $table->timestamps();
            $table->softDeletes(); // Xóa mềm
           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ads_auto_data_days');
    }
};
