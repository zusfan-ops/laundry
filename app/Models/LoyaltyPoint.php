<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyPoint extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'order_id', 'type', 'points', 'balance_after', 'note', 'created_at'];

    protected $casts = [
        'points' => 'integer',
        'balance_after' => 'integer',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
