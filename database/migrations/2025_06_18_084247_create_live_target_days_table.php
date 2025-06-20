<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('live_target_days', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('room_id');
            $table->date('date');
            $table->decimal('gmv_target', 15, 2)->nullable();
            $table->decimal('cost_limit', 15, 2)->nullable();
            $table->integer('team_count')->nullable();
            $table->enum('day_type', ['normal', 'sale', 'key'])->default('normal');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['room_id', 'date']);
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('live_target_days');
    }
};
