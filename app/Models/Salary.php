<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Salary extends Model
{
    protected $fillable = [
        'user_id', 'period', 'base_amount', 'bonus', 'deduction',
        'net_amount', 'status', 'paid_at', 'note',
    ];

    protected $casts = [
        'base_amount' => 'integer',
        'bonus' => 'integer',
        'deduction' => 'integer',
        'net_amount' => 'integer',
        'paid_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function recalc(): void
    {
        $this->net_amount = max(0, $this->base_amount + $this->bonus - $this->deduction);
    }
}
