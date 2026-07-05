<?php

namespace App\Services;

use App\Models\Service;
use App\Models\ServiceModifier;
use Illuminate\Support\Str;

class CartService
{
    private const KEY = 'cart';

    /** @return array<int, array> */
    public function items(): array
    {
        return session(self::KEY, []);
    }

    public function count(): int
    {
        return count($this->items());
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    public function subtotal(): int
    {
        return array_sum(array_map(fn ($i) => $i['line_total'], $this->items()));
    }

    public function estimatedWeight(): float
    {
        return (float) array_sum(array_map(
            fn ($i) => $i['pricing_type'] === 'weight' ? $i['qty'] : 0,
            $this->items()
        ));
    }

    public function add(Service $service, float $qty, ?ServiceModifier $speed, ?ServiceModifier $perfume): string
    {
        $items = $this->items();
        $rowId = (string) Str::uuid();

        $mult = $speed?->multiplier ?? 1.0;
        $perfumeFee = $perfume?->flat_fee ?? 0;
        $lineTotal = (int) ceil($qty * $service->unit_price * $mult) + (int) $perfumeFee;

        $items[$rowId] = [
            'row_id' => $rowId,
            'service_id' => $service->id,
            'service_name' => $service->name,
            'pricing_type' => $service->pricing_type,
            'unit_price' => (int) $service->unit_price,
            'unit_label' => $service->unit_label,
            'icon' => $service->category->icon ?? 'box',
            'qty' => $qty,
            'speed_id' => $speed?->id,
            'speed_name' => $speed?->name ?? 'Reguler',
            'speed_multiplier' => $mult,
            'perfume_id' => $perfume?->id,
            'perfume_name' => $perfume?->name,
            'perfume_fee' => (int) $perfumeFee,
            'line_total' => $lineTotal,
        ];

        session([self::KEY => $items]);

        return $rowId;
    }

    public function remove(string $rowId): void
    {
        $items = $this->items();
        unset($items[$rowId]);
        session([self::KEY => $items]);
    }

    public function clear(): void
    {
        session()->forget(self::KEY);
    }
}
