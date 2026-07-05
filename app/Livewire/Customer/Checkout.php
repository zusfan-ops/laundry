<?php

namespace App\Livewire\Customer;

use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusLog;
use App\Models\Outlet;
use App\Models\TimeSlot;
use App\Models\Voucher;
use App\Models\VoucherUsage;
use App\Services\CartService;
use App\Services\NotificationService;
use App\Services\PricingService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Checkout extends Component
{
    public ?int $addressId = null;

    public string $pickupDate = '';
    public ?int $pickupSlotId = null;
    public string $deliveryDate = '';
    public ?int $deliverySlotId = null;

    public string $paymentMode = 'pay_after_weigh';
    public string $voucherCode = '';
    public ?int $appliedVoucherId = null;
    public int $discount = 0;
    public string $notes = '';

    // New address form
    public bool $showAddressForm = false;
    public array $newAddress = ['label' => '', 'recipient' => '', 'phone' => '', 'full_address' => '', 'notes' => ''];

    public function mount(CartService $cart): void
    {
        if ($cart->isEmpty()) {
            $this->redirectRoute('cart', navigate: false);
            return;
        }

        $this->addressId = auth()->user()->defaultAddress?->id
            ?? auth()->user()->addresses()->value('id');

        $this->pickupDate = today()->toDateString();
        $this->deliveryDate = today()->addDays(2)->toDateString();
    }

    public function getOutletProperty(): Outlet
    {
        return Outlet::where('is_active', true)->firstOrFail();
    }

    public function getAddressesProperty()
    {
        return auth()->user()->addresses()->orderByDesc('is_default')->get();
    }

    public function getSlotsProperty()
    {
        return TimeSlot::where('outlet_id', $this->outlet->id)->where('is_active', true)->orderBy('start_time')->get();
    }

    public function getSelectedAddressProperty(): ?Address
    {
        return $this->addressId ? Address::find($this->addressId) : null;
    }

    public function getSubtotalProperty(): int
    {
        return app(CartService::class)->subtotal();
    }

    public function getShippingFeeProperty(): int
    {
        $addr = $this->selectedAddress;
        return app(PricingService::class)->shippingFee(
            $this->outlet, $addr?->lat, $addr?->lng, $this->subtotal
        );
    }

    public function getTotalProperty(): int
    {
        return max(0, $this->subtotal + $this->shippingFee - $this->discount);
    }

    public function saveAddress(): void
    {
        $data = $this->validate([
            'newAddress.recipient' => 'required|string|max:120',
            'newAddress.phone' => 'required|string|max:20',
            'newAddress.full_address' => 'required|string',
        ])['newAddress'];

        $addr = auth()->user()->addresses()->create([
            'label' => $this->newAddress['label'] ?: 'Alamat',
            'recipient' => $data['recipient'],
            'phone' => $data['phone'],
            'full_address' => $data['full_address'],
            'notes' => $this->newAddress['notes'] ?: null,
            'lat' => $this->outlet->lat,  // demo: near outlet
            'lng' => $this->outlet->lng,
            'is_default' => $this->addresses->isEmpty(),
        ]);

        $this->addressId = $addr->id;
        $this->showAddressForm = false;
        $this->reset('newAddress');
        $this->dispatch('toast', message: 'Alamat ditambahkan', type: 'success');
    }

    public function applyVoucher(): void
    {
        $voucher = Voucher::where('code', strtoupper(trim($this->voucherCode)))->first();

        if (! $voucher || ! $voucher->isCurrentlyValid()) {
            $this->addError('voucherCode', 'Voucher tidak valid atau sudah berakhir.');
            return;
        }

        if ($this->subtotal < $voucher->min_order) {
            $this->addError('voucherCode', 'Minimum order ' . rupiah($voucher->min_order) . '.');
            return;
        }

        $used = VoucherUsage::where('voucher_id', $voucher->id)->where('user_id', auth()->id())->count();
        if ($used >= $voucher->per_user_limit) {
            $this->addError('voucherCode', 'Batas pemakaian voucher tercapai.');
            return;
        }

        $this->appliedVoucherId = $voucher->id;
        $this->discount = $voucher->discountFor($this->subtotal, $this->shippingFee);
        $this->dispatch('toast', message: 'Voucher diterapkan', type: 'success');
    }

    public function removeVoucher(): void
    {
        $this->appliedVoucherId = null;
        $this->discount = 0;
        $this->voucherCode = '';
    }

    public function placeOrder(CartService $cart, NotificationService $notifications)
    {
        $this->validate([
            'addressId' => 'required|exists:addresses,id',
            'pickupDate' => 'required|date',
            'pickupSlotId' => 'required|exists:time_slots,id',
            'deliveryDate' => 'required|date',
            'deliverySlotId' => 'required|exists:time_slots,id',
        ], [], [
            'addressId' => 'alamat',
            'pickupSlotId' => 'slot pickup',
            'deliverySlotId' => 'slot delivery',
        ]);

        if ($cart->isEmpty()) {
            $this->redirectRoute('cart', navigate: false);
            return;
        }

        $outlet = $this->outlet;
        $subtotal = $cart->subtotal();
        $shipping = $this->shippingFee;
        $discount = $this->discount;
        $status = $this->paymentMode === 'prepaid_estimate' ? 'pending_payment' : 'placed';

        $order = DB::transaction(function () use ($cart, $outlet, $subtotal, $shipping, $discount, $status, $notifications) {
            // Lock pickup slot capacity to avoid overbooking.
            $pickupSlot = TimeSlot::lockForUpdate()->find($this->pickupSlotId);
            if ($pickupSlot->remainingFor($this->pickupDate, 'pickup') <= 0) {
                throw new \RuntimeException('Slot pickup penuh, silakan pilih slot lain.');
            }

            $order = Order::create([
                'user_id' => auth()->id(),
                'outlet_id' => $outlet->id,
                'address_id' => $this->addressId,
                'status' => $status,
                'estimated_subtotal' => $subtotal,
                'shipping_fee' => $shipping,
                'discount_amount' => $discount,
                'estimated_total' => max(0, $subtotal + $shipping - $discount),
                'estimated_weight' => $cart->estimatedWeight(),
                'pickup_slot_id' => $this->pickupSlotId,
                'delivery_slot_id' => $this->deliverySlotId,
                'pickup_date' => $this->pickupDate,
                'delivery_date' => $this->deliveryDate,
                'payment_mode' => $this->paymentMode,
                'payment_status' => 'unpaid',
                'voucher_id' => $this->appliedVoucherId,
                'notes' => $this->notes ?: null,
                'created_by' => auth()->id(),
            ]);

            foreach ($cart->items() as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'service_id' => $item['service_id'],
                    'service_name' => $item['service_name'],
                    'pricing_type' => $item['pricing_type'],
                    'unit_price' => $item['unit_price'],
                    'speed_multiplier' => $item['speed_multiplier'],
                    'speed_name' => $item['speed_name'],
                    'perfume_fee' => $item['perfume_fee'],
                    'perfume_name' => $item['perfume_name'],
                    'estimated_qty' => $item['qty'],
                    'line_total' => $item['line_total'],
                ]);
            }

            if ($this->appliedVoucherId) {
                VoucherUsage::create([
                    'voucher_id' => $this->appliedVoucherId,
                    'user_id' => auth()->id(),
                    'order_id' => $order->id,
                    'discount' => $discount,
                ]);
            }

            OrderStatusLog::create([
                'order_id' => $order->id,
                'from_status' => null,
                'to_status' => $status,
                'actor_id' => auth()->id(),
                'actor_role' => 'customer',
                'note' => 'Order dibuat',
                'created_at' => now(),
            ]);

            $notifications->notify(auth()->user(), 'Pesanan diterima', "Pesanan {$order->code} sudah kami terima.", $order);
            $notifications->notifyOutletStaff($outlet->id, ['operator', 'outlet_admin'], 'Order baru', "Order {$order->code} masuk.", $order);

            return $order;
        });

        $cart->clear();
        $this->dispatch('cart-updated');

        return $this->redirectRoute('orders.show', ['order' => $order->code], navigate: false);
    }

    public function render()
    {
        return view('livewire.customer.checkout');
    }
}
