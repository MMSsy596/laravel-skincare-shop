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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('voucher_code')->nullable()->after('total');
            $table->decimal('voucher_discount', 10, 2)->default(0)->after('voucher_code');
            $table->decimal('shipping_fee', 10, 2)->default(0)->after('voucher_discount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['voucher_code', 'voucher_discount', 'shipping_fee']);
        });
    }
};
