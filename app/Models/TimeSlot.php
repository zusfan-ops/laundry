<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeSlot extends Model
{
    protected $fillable = ['outlet_id', 'start_time', 'end_time', 'capacity', 'is_active'];

    protected $casts = [
        'capacity' => 'integer',
        'is_active' => 'boolean',
    ];

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function label(): string
    {
        return substr($this->start_time, 0, 5) . ' - ' . substr($this->end_time, 0, 5);
    }

    /** Remaining capacity for a given date considering existing orders on this slot. */
    public function remainingFor(string $date, string $kind = 'pickup'): int
    {
        $column = $kind === 'delivery' ? 'delivery_slot_id' : 'pickup_slot_id';
        $dateColumn = $kind === 'delivery' ? 'delivery_date' : 'pickup_date';

        $used = Order::where($column, $this->id)
            ->whereDate($dateColumn, $date)
            ->whereNotIn('status', ['cancelled'])
            ->count();

        return max(0, $this->capacity - $used);
    }
}
