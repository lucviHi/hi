<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('ads_manual_data_days', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->decimal('cost_usd', 10, 2)->default(0);
            $table->decimal('cost_local', 10, 2)->default(0);
            $table->decimal('cpc_usd', 10, 2)->default(0);
            $table->decimal('cpa_usd', 10, 2)->default(0);
            $table->integer('total_purchases')->default(0);
            $table->decimal('cost_per_payment', 10, 2)->default(0);
            $table->integer('impressions')->default(0);
            $table->decimal('ctr', 5, 2)->default(0);
            $table->decimal('cpm', 10, 2)->default(0);
            $table->decimal('cpc', 10, 2)->default(0);
            $table->integer('clicks')->default(0);
            $table->integer('conversions')->default(0);
            $table->decimal('cvr', 5, 2)->default(0);
            $table->decimal('cpa', 10, 2)->default(0);
            $table->decimal('roas_purchase', 5, 2)->default(0);
            $table->decimal('roas_payment', 5, 2)->default(0);
            $table->decimal('roas_on_site', 5, 2)->default(0);
            $table->integer('shopping_purchases')->default(0);
            $table->integer('purchase_count')->default(0);
            $table->decimal('cost_per_purchase', 10, 2)->default(0);
            $table->decimal('cost_per_shopping_purchase', 10, 2)->default(0);
            $table->integer('total_payments')->default(0);
            $table->decimal('cost_per_payment_repeat', 10, 2)->default(0);
            $table->integer('video_views')->default(0);
            $table->integer('video_views_2s')->default(0);
            $table->integer('video_views_6s')->default(0);
            $table->timestamps();
        });
    }
    

    public function down() {
        Schema::dropIfExists('ads_manual_data_days');
    }
};
