<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('ads_manual_data_days', function (Blueprint $table) {
            $table->unsignedBigInteger('room_id')->after('id'); // Thêm cột room_id
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade'); // Liên kết với bảng rooms
        });
    }

    public function down()
    {
        Schema::table('ads_manual_data_days', function (Blueprint $table) {
            $table->dropForeign(['room_id']);
            $table->dropColumn('room_id');
        });
    }
};
