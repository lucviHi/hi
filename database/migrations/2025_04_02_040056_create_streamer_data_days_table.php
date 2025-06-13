<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('streamer_data_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->onDelete('cascade'); // Liên kết với room
            $table->string('live_name');
            $table->timestamp('start_time');
            $table->integer('duration'); // Thời lượng (giây)
            $table->bigInteger('total_revenue');
            $table->bigInteger('gmv');
            $table->integer('items_sold');
            $table->integer('customers');
            $table->integer('avg_price');
            $table->integer('paid_orders');
            $table->integer('gmv_per_1k_impressions');
            $table->integer('gmv_per_1k_views');
            $table->integer('views');
            $table->integer('viewers');
            $table->integer('max_viewers');
            $table->integer('new_followers');
            $table->integer('avg_watch_time');
            $table->integer('likes');
            $table->integer('comments');
            $table->integer('shares');
            $table->integer('product_displays');
            $table->integer('product_clicks');
            $table->decimal('ctr', 5, 4);
            $table->decimal('ctor', 5, 4);
            $table->timestamps();
            $table->softDeletes(); // Xóa mềm
        });
    }

    public function down() {
        Schema::dropIfExists('streamer_data_days');
    }
};