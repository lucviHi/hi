<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('live_days', function (Blueprint $table) {
            $table->date('live_date')->primary(); // Ngày là khóa chính
            $table->decimal('gmv_target', 15, 2)->nullable(); // GMV mục tiêu
            $table->enum('day_type', ['sale', 'key', 'normal'])->default('normal'); // Loại ngày
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('live_days');
    }
};

