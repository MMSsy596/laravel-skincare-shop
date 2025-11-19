<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('type', ['fixed', 'percentage']); // fixed: giảm trực tiếp tiền, percentage: giảm theo %
            $table->decimal('value', 10, 2); // Giá trị giảm (tiền hoặc %)
            $table->decimal('min_order', 10, 2)->default(0); // Đơn hàng tối thiểu
            $table->decimal('max_discount', 10, 2)->nullable(); // Giảm tối đa (cho percentage)
            $table->integer('usage_limit')->nullable(); // Giới hạn số lần sử dụng
            $table->integer('used_count')->default(0); // Số lần đã sử dụng
            $table->dateTime('valid_from'); // Ngày bắt đầu
            $table->dateTime('valid_to'); // Ngày kết thúc
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vouchers');
    }
};
