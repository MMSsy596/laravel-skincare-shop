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
            $table->decimal('latitude', 10, 8)->nullable()->after('shipping_address');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->decimal('distance', 8, 2)->nullable()->after('longitude')->comment('Khoảng cách tính bằng km');
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
            $table->dropColumn(['latitude', 'longitude', 'distance']);
        });
    }
};
