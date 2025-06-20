<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLivePerformanceSnapshotsTable extends Migration
{
    public function up()
    {
        Schema::create('live_performance_snapshots', function (Blueprint $table) {
            $table->id();

            // Kênh
            $table->foreignId('room_id')->constrained()->onDelete('cascade');

            // Thời gian
            $table->date('date');
            $table->time('hour')->nullable(); // null nếu daily
            $table->enum('type', ['daily', 'hourly'])->default('daily');

            // Thông tin phiên
            $table->string('live_session_name')->nullable();

            // Chi phí quảng cáo
            $table->decimal('ads_manual_cost', 12, 2)->default(0);
            $table->decimal('ads_auto_cost', 12, 2)->default(0);
            $table->decimal('ads_total_cost', 12, 2)->default(0);

            // Doanh thu & hiệu suất
            $table->decimal('gmv', 15, 2)->default(0);
            $table->decimal('roi', 8, 2)->nullable(); // gmv / ads_total_cost

            // Tương tác & bán hàng
            $table->integer('impressions')->default(0);
            $table->integer('views')->default(0);
            $table->integer('product_clicks')->default(0);
            $table->integer('items_sold')->default(0);
            $table->integer('comments')->default(0);
            $table->integer('shares')->default(0);

            // Chỉ số hiệu suất
            $table->decimal('ctr', 5, 2)->default(0);
            $table->decimal('ctor', 5, 2)->default(0);

            // Thời gian tạo/sửa
            $table->timestamps();
            $table->softDeletes();

            // Khóa duy nhất
            $table->unique(['room_id', 'date', 'hour', 'type'], 'room_date_hour_type_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('live_performance_snapshots');
    }
}
