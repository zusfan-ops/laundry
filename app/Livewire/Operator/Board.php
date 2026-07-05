<?php

namespace App\Livewire\Operator;

use App\Models\Courier;
use App\Models\CourierAssignment;
use App\Models\Order;
use App\Services\NotificationService;
use App\Services\OrderStateMachine;
use App\Services\PricingService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.staff')]
class Board extends Component
{
    use WithFileUploads;

    public ?int $weighOrderId = null;
    public array $weights = [];          // item_id => actual qty
    public $weighPhoto = null;

    public function getOutletId(): int
    {
        return auth()->user()->outlet_id;
    }

    public function assignPickup(int $orderId, OrderStateMachine $sm, NotificationService $notifications): void
    {
        $order = $this->findOrder($orderId);
        $courier = Courier::where('outlet_id', $this->getOutletId())->where('is_available', true)->first();
        if (! $courier) {
            $this->dispatch('toast', message: 'Tidak ada kurir tersedia.', type: 'error');
            return;
        }

        DB::transaction(function () use ($order, $courier, $sm, $notifications) {
            CourierAssignment::create([
                'order_id' => $order->id, 'courier_id' => $courier->id, 'type' => 'pickup',
                'status' => 'assigned', 'assigned_at' => now(),
            ]);
            $sm->transition($order, 'assigned_pickup', auth()->user(), ['note' => 'Kurir ditugaskan menjemput']);
            $notifications->notify($courier->user, 'Tugas baru', "Pickup order {$order->code}.", $order);
        });

        $this->dispatch('toast', message: 'Kurir pickup ditugaskan.', type: 'success');
    }

    public function receiveAtOutlet(int $orderId, OrderStateMachine $sm): void
    {
        $sm->transition($this->findOrder($orderId), 'at_outlet', auth()->user(), ['note' => 'Cucian tiba di outlet']);
        $this->dispatch('toast', message: 'Order diterima di outlet.', type: 'success');
    }

    public function openWeigh(int $orderId): void
    {
        $this->weighOrderId = $orderId;
        $order = $this->findOrder($orderId);
        $this->weights = [];
        foreach ($order->items as $item) {
            $this->weights[$item->id] = $item->pricing_type === 'weight'
                ? (float) ($item->estimated_qty ?? 0)
                : (float) ($item->estimated_qty ?? 1);
        }
    }

    public function cancelWeigh(): void
    {
        $this->reset('weighOrderId', 'weights', 'weighPhoto');
    }

    public function saveWeigh(OrderStateMachine $sm, PricingService $pricing, NotificationService $notifications): void
    {
        $order = $this->findOrder($this->weighOrderId);

        $photoPath = null;
        if ($this->weighPhoto) {
            $photoPath = $this->weighPhoto->store('weigh-photos', 'public');
        }

        DB::transaction(function () use ($order, $sm, $pricing, $notifications, $photoPath) {
            $actualWeight = 0;
            foreach ($order->items as $item) {
                $qty = (float) ($this->weights[$item->id] ?? $item->estimated_qty ?? 0);
                $item->actual_qty = $qty;
                $item->save();
                if ($item->pricing_type === 'weight') {
                    $actualWeight += $qty;
                }
            }
            $order->actual_weight = $actualWeight;
            $order->save();

            $pricing->recalcFinal($order);

            // weighed first (audit), then branch on tolerance.
            $sm->transition($order, 'weighed', auth()->user(), [
                'note' => "Berat aktual {$actualWeight} kg",
                'photo_path' => $photoPath,
            ]);

            $estimated = (float) ($order->estimated_weight ?? 0);
            if ($estimated > 0 && $pricing->needsPriceConfirm($estimated, $actualWeight)) {
                $sm->transition($order, 'awaiting_price_confirm', auth()->user(), ['note' => 'Selisih berat melewati ambang']);
                $notifications->notify($order->user, 'Konfirmasi harga', "Berat order {$order->code} berubah. Mohon konfirmasi.", $order, 'whatsapp');
            } else {
                $sm->transition($order, 'processing', auth()->user(), ['note' => 'Selisih dalam batas, langsung diproses']);
            }
        });

        $this->cancelWeigh();
        $this->dispatch('toast', message: 'Berat tersimpan & harga final dihitung.', type: 'success');
    }

    public function markReady(int $orderId, OrderStateMachine $sm): void
    {
        $sm->transition($this->findOrder($orderId), 'ready', auth()->user(), ['note' => 'Selesai diproses + QC']);
        $this->dispatch('toast', message: 'Order siap diantar.', type: 'success');
    }

    public function assignDelivery(int $orderId, OrderStateMachine $sm, NotificationService $notifications): void
    {
        $order = $this->findOrder($orderId);
        $courier = Courier::where('outlet_id', $this->getOutletId())->where('is_available', true)->first();
        if (! $courier) {
            $this->dispatch('toast', message: 'Tidak ada kurir tersedia.', type: 'error');
            return;
        }

        DB::transaction(function () use ($order, $courier, $sm, $notifications) {
            CourierAssignment::create([
                'order_id' => $order->id, 'courier_id' => $courier->id, 'type' => 'delivery',
                'status' => 'assigned', 'assigned_at' => now(),
            ]);
            $sm->transition($order, 'assigned_delivery', auth()->user(), ['note' => 'Kurir ditugaskan mengantar']);
            $notifications->notify($courier->user, 'Tugas baru', "Delivery order {$order->code}.", $order);
        });

        $this->dispatch('toast', message: 'Kurir delivery ditugaskan.', type: 'success');
    }

    private function findOrder(int $id): Order
    {
        return Order::with('items')->where('outlet_id', $this->getOutletId())->findOrFail($id);
    }

    public function render()
    {
        $outletId = $this->getOutletId();
        $base = fn (array $statuses) => Order::with(['items', 'user'])
            ->where('outlet_id', $outletId)
            ->whereIn('status', $statuses)
            ->latest()->get();

        $columns = [
            ['Masuk', $base(['placed', 'assigned_pickup', 'picked_up'])],
            ['Di Outlet', $base(['at_outlet'])],
            ['Ditimbang', $base(['weighed', 'awaiting_price_confirm'])],
            ['Proses', $base(['processing'])],
            ['Siap Antar', $base(['ready', 'assigned_delivery', 'delivering'])],
        ];

        $weighOrder = $this->weighOrderId ? $this->findOrder($this->weighOrderId) : null;

        return view('livewire.operator.board', compact('columns', 'weighOrder'));
    }
}
