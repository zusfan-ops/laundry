<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceModifier extends Model
{
    protected $fillable = ['type', 'name', 'multiplier', 'flat_fee', 'is_active'];

    protected $casts = [
        'multiplier' => 'float',
        'flat_fee' => 'integer',
        'is_active' => 'boolean',
    ];
}
