<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id', 'outlet_id', 'name', 'description', 'pricing_type',
        'unit_price', 'unit_label', 'min_qty', 'est_duration_hours', 'icon', 'is_active',
    ];

    protected $casts = [
        'unit_price' => 'integer',
        'min_qty' => 'float',
        'est_duration_hours' => 'integer',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function isWeight(): bool
    {
        return $this->pricing_type === 'weight';
    }
}
