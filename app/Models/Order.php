<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total',
        'status',
        'shipping_address',
        'phone',
        'payment_method',
        'payment_status',
        'transaction_id',
        'payment_notes',
        'paid_at',
        'canceller_id'
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    // Payment Methods
    const PAYMENT_METHODS = [
        'cash' => 'Tiền mặt',
        'bank_transfer' => 'Chuyển khoản ngân hàng',
        'qr_code' => 'Quét mã QR',
    ];

    // Payment Status
    const PAYMENT_STATUSES = [
        'pending' => 'Chờ thanh toán',
        'paid' => 'Đã thanh toán',
        'failed' => 'Thanh toán thất bại',
        'refunded' => 'Đã hoàn tiền',
    ];

    // Order Status
    const ORDER_STATUSES = [
        'pending' => 'Chờ xử lý',
        'processing' => 'Đang xử lý',
        'shipped' => 'Đã giao hàng',
        'delivered' => 'Đã nhận hàng',
        'cancelled' => 'Đã hủy',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function canceller()
    {
        return $this->belongsTo(User::class, 'canceller_id');
    }

    // Payment Methods
    public function getPaymentMethodNameAttribute()
    {
        return self::PAYMENT_METHODS[$this->payment_method] ?? 'Không xác định';
    }

    public function getPaymentStatusNameAttribute()
    {
        return self::PAYMENT_STATUSES[$this->payment_status] ?? 'Không xác định';
    }

    public function getOrderStatusNameAttribute()
    {
        return self::ORDER_STATUSES[$this->status] ?? 'Không xác định';
    }

    public function isPaid()
    {
        return $this->payment_status === 'paid';
    }

    public function isPendingPayment()
    {
        return $this->payment_status === 'pending';
    }

    public function markAsPaid($transactionId = null, $notes = null)
    {
        $this->update([
            'payment_status' => 'paid',
            'transaction_id' => $transactionId,
            'payment_notes' => $notes,
            'paid_at' => now(),
        ]);
    }

    public function markAsFailed($notes = null)
    {
        $this->update([
            'payment_status' => 'failed',
            'payment_notes' => $notes,
        ]);
    }

    public function getFormattedTotalAttribute()
    {
        return number_format($this->total) . ' VNĐ';
    }

    public function getPaymentMethodIconAttribute()
    {
        switch ($this->payment_method) {
            case 'cash':
                return 'fas fa-money-bill-wave';
            case 'bank_transfer':
                return 'fas fa-university';
            case 'qr_code':
                return 'fas fa-qrcode';
            default:
                return 'fas fa-credit-card';
        }
    }
}
