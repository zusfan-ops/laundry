<?php

namespace App\Livewire\Customer;

use App\Models\Order;
use App\Models\Payment;
use App\Services\NotificationService;
use App\Services\OrderStateMachine;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.app')]
class OrderShow extends Component
{
    public Order $order;

    #[Validate('required|integer|min:1|max:5')]
    public int $rating = 5;

    #[Validate('nullable|string|max:500')]
    public string $review = '';

    public function mount(Order $order): void
    {
        abort_unless($order->user_id === auth()->id(), 403);
        $this->order = $order;
        $this->rating = $order->rating ?? 5;
        $this->review = $order->review ?? '';
    }

    public function confirmPrice(OrderStateMachine $sm): void
    {
        $sm->transition($this->order, 'processing', auth()->user(), ['note' => 'Pelanggan menyetujui harga final']);
        $this->order->refresh();
        $this->dispatch('toast', message: 'Harga disetujui. Cucian akan diproses.', type: 'success');
    }

    public function rejectPrice(OrderStateMachine $sm, NotificationService $notifications): void
    {
        DB::transaction(function () use ($sm, $notifications) {
            $sm->transition($this->order, 'cancelled', auth()->user(), ['note' => 'Pelanggan menolak harga final']);
            if ($this->order->payment_status === 'paid') {
                Payment::create([
                    'order_id' => $this->order->id, 'gateway' => 'cash', 'amount' => $this->order->final_total ?: $this->order->estimated_total,
                    'type' => 'refund', 'status' => 'refunded', 'paid_at' => now(),
                ]);
                $this->order->update(['payment_status' => 'refunded']);
            }
        });
        $this->order->refresh();
        $this->dispatch('toast', message: 'Pesanan dibatalkan. Refund diproses bila prabayar.', type: 'info');
    }

    public function payNow(NotificationService $notifications, OrderStateMachine $sm): void
    {
        $amount = $this->order->final_total ?: $this->order->estimated_total;

        DB::transaction(function () use ($amount, $sm) {
            Payment::create([
                'order_id' => $this->order->id, 'gateway' => 'cash', 'external_id' => 'SIM-' . uniqid(),
                'amount' => $amount, 'type' => 'charge', 'status' => 'paid', 'method' => 'qris', 'paid_at' => now(),
            ]);
            $this->order->update(['payment_status' => 'paid']);

            // Prepaid flow: pending_payment -> placed once paid.
            if ($this->order->status === 'pending_payment') {
                $sm->transition($this->order, 'placed', auth()->user(), ['note' => 'Pembayaran prabayar diterima']);
            }
        });

        $notifications->notify(auth()->user(), 'Pembayaran sukses', "Pembayaran {$this->order->code} berhasil.", $this->order, 'email');
        $this->order->refresh();
        $this->dispatch('toast', message: 'Pembayaran berhasil (simulasi).', type: 'success');
    }

    public function submitRating(): void
    {
        $this->validate();
        abort_unless($this->order->status === 'completed', 403);
        $this->order->update(['rating' => $this->rating, 'review' => $this->review ?: null]);
        $this->dispatch('toast', message: 'Terima kasih atas rating Anda!', type: 'success');
    }

    public function render()
    {
        $this->order->load(['items', 'statusLogs', 'address', 'pickupSlot', 'deliverySlot', 'payments']);

        return view('livewire.customer.order-show');
    }
}
