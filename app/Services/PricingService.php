<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Outlet;

class PricingService
{
    /** Weight tolerance before price re-confirmation is required (kg). */
    public const WEIGHT_TOLERANCE_KG = 0.5;

    /** Or 20% relative difference. */
    public const WEIGHT_TOLERANCE_PCT = 0.20;

    /** Haversine distance in kilometers between two lat/lng points. */
    public function distanceKm(?float $lat1, ?float $lng1, ?float $lat2, ?float $lng2): float
    {
        if ($lat1 === null || $lng1 === null || $lat2 === null || $lng2 === null) {
            return 0.0;
        }
        $earth = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
        return $earth * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    /**
     * Shipping fee from outlet to a destination, given the order subtotal (for free-shipping threshold).
     * Covers both pickup and delivery legs.
     */
    public function shippingFee(Outlet $outlet, ?float $lat, ?float $lng, int $subtotal): int
    {
        if ($outlet->free_shipping_threshold > 0 && $subtotal >= $outlet->free_shipping_threshold) {
            return 0;
        }

        $km = $this->distanceKm($outlet->lat, $outlet->lng, $lat, $lng);
        // base fee + per-km, counted for both pickup and delivery (round trip).
        $oneWay = $outlet->base_shipping_fee + (int) ceil($km * $outlet->fee_per_km);

        return $oneWay * 2;
    }

    /** Whether the actual weight differs enough from estimate to require customer confirmation. */
    public function needsPriceConfirm(float $estimated, float $actual): bool
    {
        $diff = abs($actual - $estimated);
        if ($diff <= self::WEIGHT_TOLERANCE_KG) {
            return false;
        }
        if ($estimated > 0 && ($diff / $estimated) <= self::WEIGHT_TOLERANCE_PCT) {
            return false;
        }

        return true;
    }

    /**
     * Recompute an order's final totals from its items' actual_qty.
     * line_total = ceil(actual_qty * unit_price * speed_multiplier) + perfume_fee.
     */
    public function recalcFinal(Order $order): void
    {
        $subtotal = 0;
        foreach ($order->items as $item) {
            $qty = $item->actual_qty ?? $item->estimated_qty ?? 0;
            $item->line_total = $item->computeTotal((float) $qty);
            $item->save();
            $subtotal += $item->line_total;
        }

        $discount = $order->discount_amount;
        // Re-evaluate free-shipping-style vouchers against the new subtotal if attached.
        if ($order->voucher) {
            $discount = $order->voucher->discountFor($subtotal, $order->shipping_fee);
        }

        $order->final_subtotal = $subtotal;
        $order->discount_amount = $discount;
        $order->final_total = max(0, $subtotal + $order->shipping_fee - $discount);
        $order->save();
    }
}
