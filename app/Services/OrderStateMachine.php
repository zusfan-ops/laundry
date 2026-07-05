<?php

namespace App\Services;

use App\Exceptions\InvalidTransitionException;
use App\Models\Order;
use App\Models\OrderStatusLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrderStateMachine
{
    /** Allowed transitions per SELLY_LAUNDRY_WORKFLOW.md state diagram. */
    public const TRANSITIONS = [
        'pending_payment' => ['placed', 'cancelled'],
        'placed' => ['assigned_pickup', 'cancelled'],
        'assigned_pickup' => ['picked_up', 'cancelled'],
        'picked_up' => ['at_outlet'],
        'at_outlet' => ['weighed'],
        'weighed' => ['awaiting_price_confirm', 'processing'],
        'awaiting_price_confirm' => ['processing', 'cancelled'],
        'processing' => ['ready', 'cancelled'],
        'ready' => ['assigned_delivery'],
        'assigned_delivery' => ['delivering'],
        'delivering' => ['completed'],
        'completed' => [],
        'cancelled' => [],
    ];

    public function __construct(private NotificationService $notifications)
    {
    }

    public function canTransition(Order $order, string $to): bool
    {
        return in_array($to, self::TRANSITIONS[$order->status] ?? [], true);
    }

    /**
     * Transition an order to a new status, writing an audit log within the same transaction.
     *
     * @param  array{note?:string,photo_path?:string,lat?:float,lng?:float,client_uuid?:string}  $meta
     */
    public function transition(Order $order, string $to, ?User $actor = null, array $meta = []): Order
    {
        // Idempotency: if this client action was already recorded, return current state.
        if (! empty($meta['client_uuid'])) {
            $existing = OrderStatusLog::where('client_uuid', $meta['client_uuid'])->first();
            if ($existing) {
                return $order->refresh();
            }
        }

        if (! $this->canTransition($order, $to)) {
            throw InvalidTransitionException::between($order->status, $to);
        }

        return DB::transaction(function () use ($order, $to, $actor, $meta) {
            $from = $order->status;
            $order->status = $to;
            $order->save();

            OrderStatusLog::create([
                'order_id' => $order->id,
                'from_status' => $from,
                'to_status' => $to,
                'actor_id' => $actor?->id,
                'actor_role' => $actor?->role,
                'note' => $meta['note'] ?? null,
                'photo_path' => $meta['photo_path'] ?? null,
                'lat' => $meta['lat'] ?? null,
                'lng' => $meta['lng'] ?? null,
                'client_uuid' => $meta['client_uuid'] ?? null,
                'created_at' => now(),
            ]);

            $this->dispatchNotifications($order, $to);

            return $order;
        });
    }

    private function dispatchNotifications(Order $order, string $to): void
    {
        $customer = $order->user;
        $messages = [
            'placed' => ['Pesanan diterima', "Pesanan {$order->code} sudah kami terima."],
            'assigned_pickup' => ['Kurir menjemput', 'Kurir sedang dalam perjalanan menjemput cucian Anda.'],
            'picked_up' => ['Cucian dijemput', 'Cucian Anda sudah diambil kurir.'],
            'weighed' => ['Sudah ditimbang', 'Cucian Anda sudah ditimbang. Cek detail harga.'],
            'awaiting_price_confirm' => ['Konfirmasi harga', 'Berat aktual berbeda dari estimasi. Mohon konfirmasi harga final.'],
            'processing' => ['Sedang diproses', 'Cucian Anda sedang dicuci & disetrika.'],
            'ready' => ['Siap diantar', 'Cucian Anda sudah selesai dan siap diantar.'],
            'delivering' => ['Sedang diantar', 'Kurir sedang mengantar cucian Anda.'],
            'completed' => ['Selesai', 'Terima kasih! Jangan lupa beri rating ya.'],
            'cancelled' => ['Pesanan dibatalkan', "Pesanan {$order->code} telah dibatalkan."],
        ];

        if (isset($messages[$to]) && $customer) {
            [$title, $body] = $messages[$to];
            $this->notifications->notify($customer, $title, $body, $order);
        }
    }
}
