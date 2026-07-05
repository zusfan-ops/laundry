<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourierAssignment extends Model
{
    protected $fillable = [
        'order_id', 'courier_id', 'type', 'status', 'proof_photo', 'assigned_at', 'done_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'done_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function courier(): BelongsTo
    {
        return $this->belongsTo(Courier::class);
    }
}
