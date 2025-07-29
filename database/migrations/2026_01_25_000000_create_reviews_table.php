<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('rating'); // 1–5 sao
            $table->text('comment')->nullable();
            $table->timestamps();
    
            $table->unique(['user_id', 'product_id']); // Mỗi user chỉ đánh giá 1 lần cho mỗi sản phẩm
        });
    
        // Tách foreign key ra ngoài để đảm bảo bảng users và products đã được tạo
        Schema::table('reviews', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('reviews');
    }
}; 