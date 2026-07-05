<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Courier;
use App\Models\Outlet;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\ServiceModifier;
use App\Models\TimeSlot;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ---- Outlet ----
        $outlet = Outlet::create([
            'name' => 'Selly Laundry — Pusat',
            'phone' => '0274-555-1234',
            'address' => 'Jl. Kaliurang KM 5, Yogyakarta',
            'lat' => -7.7626,
            'lng' => 110.3795,
            'free_shipping_threshold' => 50000,
            'base_shipping_fee' => 5000,
            'fee_per_km' => 2000,
            'is_active' => true,
        ]);

        // ---- Time slots ----
        $slots = [['08:00', '10:00'], ['10:00', '12:00'], ['13:00', '15:00'], ['15:00', '17:00'], ['18:00', '20:00']];
        foreach ($slots as [$start, $end]) {
            TimeSlot::create([
                'outlet_id' => $outlet->id,
                'start_time' => $start,
                'end_time' => $end,
                'capacity' => 10,
                'is_active' => true,
            ]);
        }

        // ---- Users (one per role) ----
        User::create([
            'name' => 'Owner Selly', 'email' => 'owner@selly.test', 'phone' => '081200000001',
            'password' => Hash::make('password'), 'role' => 'owner', 'outlet_id' => $outlet->id,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Admin Outlet', 'email' => 'admin@selly.test', 'phone' => '081200000002',
            'password' => Hash::make('password'), 'role' => 'outlet_admin', 'outlet_id' => $outlet->id,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Siti Operator', 'email' => 'operator@selly.test', 'phone' => '081200000003',
            'password' => Hash::make('password'), 'role' => 'operator', 'outlet_id' => $outlet->id,
            'email_verified_at' => now(),
        ]);

        $courierUser = User::create([
            'name' => 'Eko Kurir', 'email' => 'kurir@selly.test', 'phone' => '081200000004',
            'password' => Hash::make('password'), 'role' => 'courier', 'outlet_id' => $outlet->id,
            'email_verified_at' => now(),
        ]);

        Courier::create([
            'user_id' => $courierUser->id, 'outlet_id' => $outlet->id,
            'vehicle' => 'Motor — AB 1234 XY', 'is_available' => true,
            'last_lat' => -7.7626, 'last_lng' => 110.3795,
        ]);

        $customer = User::create([
            'name' => 'Rina Pelanggan', 'email' => 'rina@selly.test', 'phone' => '081200000005',
            'password' => Hash::make('password'), 'role' => 'customer',
            'loyalty_balance' => 1250, 'email_verified_at' => now(),
        ]);

        Address::create([
            'user_id' => $customer->id, 'label' => 'Rumah', 'recipient' => 'Rina',
            'phone' => '081200000005', 'full_address' => 'Jl. Gejayan No. 12, Sleman, Yogyakarta',
            'notes' => 'Pagar hijau', 'lat' => -7.7700, 'lng' => 110.3900, 'is_default' => true,
        ]);

        // ---- Categories ----
        $categories = [
            ['name' => 'Cuci Kiloan', 'icon' => 'washing-machine', 'color' => '#0EA5A4', 'sort_order' => 1],
            ['name' => 'Cuci-Setrika', 'icon' => 'shirt', 'color' => '#FFB020', 'sort_order' => 2],
            ['name' => 'Setrika Saja', 'icon' => 'flame', 'color' => '#FF6B57', 'sort_order' => 3],
            ['name' => 'Express', 'icon' => 'zap', 'color' => '#16A34A', 'sort_order' => 4],
            ['name' => 'Satuan', 'icon' => 'package', 'color' => '#0B7E7D', 'sort_order' => 5],
            ['name' => 'Sepatu & Tas', 'icon' => 'footprints', 'color' => '#F59E0B', 'sort_order' => 6],
        ];
        $cat = [];
        foreach ($categories as $c) {
            $cat[$c['name']] = ServiceCategory::create($c + ['is_active' => true]);
        }

        // ---- Services ----
        $services = [
            ['Cuci Kering', 'Cuci Kiloan', 'weight', 5000, 'kg', 3, 24, 'Cuci + kering tanpa setrika.'],
            ['Cuci-Setrika Reguler', 'Cuci-Setrika', 'weight', 7000, 'kg', 3, 48, 'Cuci, kering, dan setrika rapi.'],
            ['Setrika Saja', 'Setrika Saja', 'weight', 4000, 'kg', 2, 24, 'Setrika untuk pakaian bersih.'],
            ['Cuci Express 1 Hari', 'Express', 'weight', 10000, 'kg', 3, 12, 'Selesai dalam 24 jam.'],
            ['Bed Cover', 'Satuan', 'unit', 25000, 'pcs', 1, 72, 'Bed cover ukuran king/queen.'],
            ['Selimut Tebal', 'Satuan', 'unit', 20000, 'pcs', 1, 72, 'Selimut tebal & bulu.'],
            ['Jas / Blazer', 'Satuan', 'unit', 30000, 'pcs', 1, 72, 'Dry clean jas dan blazer.'],
            ['Gaun / Dress', 'Satuan', 'unit', 28000, 'pcs', 1, 72, 'Dry clean gaun pesta.'],
            ['Sepatu', 'Sepatu & Tas', 'unit', 35000, 'pcs', 1, 96, 'Deep clean sepatu sneakers.'],
            ['Tas Kulit', 'Sepatu & Tas', 'unit', 45000, 'pcs', 1, 96, 'Perawatan tas kulit.'],
            ['Karpet', 'Satuan', 'unit', 15000, 'pcs', 1, 96, 'Cuci karpet per lembar.'],
            ['Boneka', 'Satuan', 'unit', 18000, 'pcs', 1, 72, 'Cuci boneka besar.'],
        ];
        foreach ($services as [$name, $catName, $type, $price, $label, $min, $dur, $desc]) {
            Service::create([
                'category_id' => $cat[$catName]->id,
                'name' => $name, 'description' => $desc, 'pricing_type' => $type,
                'unit_price' => $price, 'unit_label' => $label, 'min_qty' => $min,
                'est_duration_hours' => $dur, 'is_active' => true,
            ]);
        }

        // ---- Modifiers ----
        $modifiers = [
            ['speed', 'Reguler', 1.00, 0],
            ['speed', 'Express', 1.50, 0],
            ['speed', 'Kilat (1 Hari)', 2.00, 0],
            ['perfume', 'Tanpa Parfum', 1.00, 0],
            ['perfume', 'Lavender', 1.00, 2000],
            ['perfume', 'Sakura', 1.00, 2000],
            ['perfume', 'Ocean Fresh', 1.00, 3000],
        ];
        foreach ($modifiers as [$type, $name, $mult, $fee]) {
            ServiceModifier::create([
                'type' => $type, 'name' => $name, 'multiplier' => $mult,
                'flat_fee' => $fee, 'is_active' => true,
            ]);
        }

        // ---- Vouchers ----
        Voucher::create([
            'code' => 'SELLY10', 'type' => 'percent', 'value' => 10, 'min_order' => 30000,
            'max_discount' => 15000, 'quota' => 100, 'per_user_limit' => 2,
            'starts_at' => now()->subDay(), 'ends_at' => now()->addMonth(), 'is_active' => true,
        ]);
        Voucher::create([
            'code' => 'GRATISONGKIR', 'type' => 'free_shipping', 'value' => 0, 'min_order' => 25000,
            'quota' => null, 'per_user_limit' => 5,
            'starts_at' => now()->subDay(), 'ends_at' => now()->addMonth(), 'is_active' => true,
        ]);
        Voucher::create([
            'code' => 'HEMAT5K', 'type' => 'fixed', 'value' => 5000, 'min_order' => 20000,
            'quota' => 200, 'per_user_limit' => 3,
            'starts_at' => now()->subDay(), 'ends_at' => now()->addMonth(), 'is_active' => true,
        ]);
    }
}
