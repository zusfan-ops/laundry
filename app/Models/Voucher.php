<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Voucher extends Model
{
    protected $fillable = [
        'code', 'type', 'value', 'min_order', 'max_discount', 'quota',
        'per_user_limit', 'starts_at', 'ends_at', 'is_active',
    ];

    protected $casts = [
        'value' => 'integer',
        'min_order' => 'integer',
        'max_discount' => 'integer',
        'quota' => 'integer',
        'per_user_limit' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function usages(): HasMany
    {
        return $this->hasMany(VoucherUsage::class);
    }

    public function isCurrentlyValid(): bool
    {
        if (! $this->is_active) {
            return false;
        }
        $now = now();
        if ($this->starts_at && $now->lt($this->starts_at)) {
            return false;
        }
        if ($this->ends_at && $now->gt($this->ends_at)) {
            return false;
        }
        if ($this->quota !== null && $this->usages()->count() >= $this->quota) {
            return false;
        }

        return true;
    }

    /** Compute discount for a given subtotal and shipping fee. Returns rupiah integer. */
    public function discountFor(int $subtotal, int $shippingFee): int
    {
        $discount = match ($this->type) {
            'percent' => (int) floor($subtotal * $this->value / 100),
            'fixed' => $this->value,
            'free_shipping' => $shippingFee,
            default => 0,
        };

        if ($this->max_discount !== null) {
            $discount = min($discount, $this->max_discount);
        }

        return min($discount, $subtotal + $shippingFee);
    }
}
