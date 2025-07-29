<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('payment_method', ['cash', 'bank_transfer', 'qr_code'])->default('cash')->after('phone');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending')->after('payment_method');
            $table->string('transaction_id')->nullable()->after('payment_status');
            $table->text('payment_notes')->nullable()->after('transaction_id');
            $table->timestamp('paid_at')->nullable()->after('payment_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'payment_method',
                'payment_status', 
                'transaction_id',
                'payment_notes',
                'paid_at'
            ]);
        });
    }
};
