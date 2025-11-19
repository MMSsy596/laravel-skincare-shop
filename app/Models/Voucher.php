<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'type',
        'value',
        'min_order',
        'max_discount',
        'usage_limit',
        'used_count',
        'valid_from',
        'valid_until',
        'is_active',
        'is_public',
        'description',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'is_active' => 'boolean',
        'is_public' => 'boolean',
    ];

    /**
     * Kiểm tra voucher có hợp lệ không
     */
    public function isValid($orderTotal = 0)
    {
        // Kiểm tra active
        if (!$this->is_active) {
            return false;
        }

        // Kiểm tra thời gian
        $now = Carbon::now();
        if ($now->lt($this->valid_from) || $now->gt($this->valid_until)) {
            return false;
        }

        // Kiểm tra số lần sử dụng
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return false;
        }

        // Kiểm tra đơn hàng tối thiểu
        if ($orderTotal < $this->min_order) {
            return false;
        }

        return true;
    }

    /**
     * Tính số tiền giảm giá
     */
    public function calculateDiscount($orderTotal)
    {
        if (!$this->isValid($orderTotal)) {
            return 0;
        }

        if ($this->type === 'fixed') {
            // Giảm trực tiếp tiền
            return min($this->value, $orderTotal);
        } else {
            // Giảm theo phần trăm
            $discount = ($orderTotal * $this->value) / 100;
            if ($this->max_discount) {
                $discount = min($discount, $this->max_discount);
            }
            return $discount;
        }
    }

    /**
     * Tăng số lần sử dụng
     */
    public function incrementUsage()
    {
        $this->increment('used_count');
    }

    /**
     * Kiểm tra voucher có còn sử dụng được không
     */
    public function isAvailable()
    {
        return $this->is_active 
            && Carbon::now()->between($this->valid_from, $this->valid_until)
            && (!$this->usage_limit || $this->used_count < $this->usage_limit);
    }
}
