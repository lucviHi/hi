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
        Schema::create('live_performance_days', function (Blueprint $table) {
            $table->id();

            // Kênh
            $table->foreignId('room_id')->constrained()->onDelete('cascade');

            // Thời gian
            $table->date('date');
            $table->time('hour')->nullable(); // null nếu daily
            $table->enum('type', ['daily', 'hourly'])->default('daily');

            // Tên phiên live
            $table->string('live_session_name')->nullable();

            // Chi phí quảng cáo
            $table->decimal('ads_manual_cost', 12, 2)->default(0);
            $table->decimal('ads_auto_cost', 12, 2)->default(0);
            $table->decimal('ads_total_cost', 12, 2)->default(0);

            // Doanh thu từ quảng cáo
            $table->decimal('manual_revenue', 15, 2)->default(0);
            $table->decimal('auto_revenue', 15, 2)->default(0);    // đã nhân 0.8
            $table->decimal('gross_revenue', 15, 2)->default(0);   // từ auto gốc

            // Doanh thu streamer
            $table->decimal('gmv', 15, 2)->default(0);

            // Hiệu suất
            $table->decimal('roas_manual', 8, 2)->nullable(); // từ bảng manual
            $table->decimal('roi', 8, 2)->nullable();         // từ bảng auto
            $table->decimal('roas_total', 8, 2)->nullable();  // tự tính

            // Bán hàng & tương tác
            $table->integer('items_sold')->default(0);
            $table->integer('views')->default(0);
            $table->integer('comments')->default(0);
            $table->integer('shares')->default(0);
            $table->decimal('ctr', 5, 2)->default(0);
            $table->decimal('ctor', 5, 2)->default(0);

            // Hoa hồng
            $table->decimal('commission', 12, 2)->nullable();

            // Ghi chú từ vận hành
            $table->text('note')->nullable();

            $table->timestamps();

            $table->unique(['room_id', 'date', 'hour', 'type'], 'room_date_hour_type_unique');
            $table->softDeletes(); // Xóa mềm
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_performance_days');
    }
};
