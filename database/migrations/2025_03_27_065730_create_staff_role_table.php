<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('staff_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staffs')->onDelete('cascade');
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade'); // Liên kết với bảng rooms
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staffs_roles');
    }
};
