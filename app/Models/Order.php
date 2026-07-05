<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code', 'client_uuid', 'user_id', 'outlet_id', 'address_id', 'status',
        'estimated_subtotal', 'final_subtotal', 'shipping_fee', 'discount_amount',
        'estimated_total', 'final_total', 'estimated_weight', 'actual_weight',
        'pickup_slot_id', 'delivery_slot_id', 'pickup_date', 'delivery_date',
        'payment_mode', 'payment_status', 'voucher_id', 'notes', 'rating', 'review', 'created_by',
    ];

    protected $casts = [
        'estimated_subtotal' => 'integer',
        'final_subtotal' => 'integer',
        'shipping_fee' => 'integer',
        'discount_amount' => 'integer',
        'estimated_total' => 'integer',
        'final_total' => 'integer',
        'estimated_weight' => 'float',
        'actual_weight' => 'float',
        'pickup_date' => 'date',
        'delivery_date' => 'date',
        'rating' => 'integer',
    ];

    /** Human-friendly status labels (Bahasa Indonesia). */
    public const STATUS_LABELS = [
        'pending_payment' => 'Menunggu Pembayaran',
        'placed' => 'Pesanan Diterima',
        'assigned_pickup' => 'Kurir Menjemput',
        'picked_up' => 'Cucian Dijemput',
        'at_outlet' => 'Tiba di Outlet',
        'weighed' => 'Sudah Ditimbang',
        'awaiting_price_confirm' => 'Menunggu Konfirmasi Harga',
        'processing' => 'Sedang Diproses',
        'ready' => 'Siap Diantar',
        'assigned_delivery' => 'Kurir Mengantar',
        'delivering' => 'Sedang Diantar',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
    ];

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (empty($order->code)) {
                $order->code = static::generateCode();
            }
            if (empty($order->client_uuid)) {
                $order->client_uuid = (string) Str::uuid();
            }
        });
    }

    public static function generateCode(): string
    {
        $date = now()->format('Ymd');
        $seq = static::withTrashed()
            ->whereDate('created_at', today())
            ->count() + 1;

        do {
            $code = sprintf('SLY-%s-%04d', $date, $seq);
            $seq++;
        } while (static::withTrashed()->where('code', $code)->exists());

        return $code;
    }

    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function isActive(): bool
    {
        return ! in_array($this->status, ['completed', 'cancelled'], true);
    }

    public function getRouteKeyName(): string
    {
        return 'code';
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(OrderStatusLog::class)->orderBy('created_at');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(CourierAssignment::class);
    }

    public function pickupSlot(): BelongsTo
    {
        return $this->belongsTo(TimeSlot::class, 'pickup_slot_id');
    }

    public function deliverySlot(): BelongsTo
    {
        return $this->belongsTo(TimeSlot::class, 'delivery_slot_id');
    }

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class);
    }
}
