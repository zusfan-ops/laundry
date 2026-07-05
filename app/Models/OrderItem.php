<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'service_id', 'service_name', 'pricing_type', 'unit_price',
        'speed_multiplier', 'speed_name', 'perfume_fee', 'perfume_name',
        'estimated_qty', 'actual_qty', 'line_total',
    ];

    protected $casts = [
        'unit_price' => 'integer',
        'speed_multiplier' => 'float',
        'perfume_fee' => 'integer',
        'estimated_qty' => 'float',
        'actual_qty' => 'float',
        'line_total' => 'integer',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /** Compute line total from a given quantity. line = ceil(qty * unit_price * mult) + perfume. */
    public function computeTotal(float $qty): int
    {
        return (int) ceil($qty * $this->unit_price * $this->speed_multiplier) + (int) $this->perfume_fee;
    }
}
