<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Courier extends Model
{
    protected $fillable = [
        'user_id', 'outlet_id', 'vehicle', 'is_available', 'last_lat', 'last_lng',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'last_lat' => 'float',
        'last_lng' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(CourierAssignment::class);
    }
}
