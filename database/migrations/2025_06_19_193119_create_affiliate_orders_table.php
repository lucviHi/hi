<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAffiliateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('affiliate_orders', function (Blueprint $table) {
            $table->id();

            // Liên kết với bảng projects
            $table->foreignId('project_id')->constrained()->onDelete('cascade');

            // Thông tin đơn hàng
            $table->string('order_id')->index();                  // ID đơn hàng
            $table->string('product_id')->nullable();             // ID sản phẩm
            $table->string('product_name')->nullable();           // Tên sản phẩm
            $table->string('sku')->nullable();                    // SKU
            $table->string('sku_id')->nullable();                 // ID SKU
            $table->string('seller_sku')->nullable();             // SKU người bán

            $table->decimal('price', 12, 2)->nullable();          // Giá sản phẩm
            $table->decimal('payment_amount', 12, 2)->nullable(); // Số tiền thanh toán
            $table->integer('quantity')->default(1);             // Số lượng

            $table->string('order_status')->nullable();           // Trạng thái đơn
            $table->string('content_type')->nullable();           // Loại nội dung
            $table->decimal('commission_rate', 5, 2)->nullable(); // Tỷ lệ hoa hồng (%)

            $table->timestamp('order_created_at')->nullable();    // Thời gian tạo đơn

            $table->timestamps();
            $table->softDeletes();                                // Xóa mềm
        });
    }

    public function down()
    {
        Schema::dropIfExists('affiliate_orders');
    }
}
