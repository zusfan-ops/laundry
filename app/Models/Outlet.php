<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Outlet extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'phone', 'address', 'opening_hours', 'lat', 'lng', 'maps_url',
        'free_shipping_threshold', 'base_shipping_fee', 'fee_per_km', 'is_active',
    ];

    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
        'free_shipping_threshold' => 'integer',
        'base_shipping_fee' => 'integer',
        'fee_per_km' => 'integer',
        'is_active' => 'boolean',
    ];

    public function timeSlots(): HasMany
    {
        return $this->hasMany(TimeSlot::class);
    }

    public function couriers(): HasMany
    {
        return $this->hasMany(Courier::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /** Keyless OpenStreetMap embed URL centred on the outlet. */
    public function mapEmbedUrl(float $pad = 0.01): ?string
    {
        if ($this->lat === null || $this->lng === null) {
            return null;
        }
        $left = $this->lng - $pad;
        $right = $this->lng + $pad;
        $top = $this->lat + $pad;
        $bottom = $this->lat - $pad;

        return 'https://www.openstreetmap.org/export/embed.html?bbox='
            . "{$left},{$bottom},{$right},{$top}&layer=mapnik&marker={$this->lat},{$this->lng}";
    }

    /** Directions link — uses custom maps_url if set, else Google Maps to coords. */
    public function directionsUrl(): ?string
    {
        if ($this->maps_url) {
            return $this->maps_url;
        }
        if ($this->lat === null || $this->lng === null) {
            return null;
        }
        return "https://www.google.com/maps/search/?api=1&query={$this->lat},{$this->lng}";
    }
}
