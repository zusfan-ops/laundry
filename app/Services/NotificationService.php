<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Order;
use App\Models\User;

class NotificationService
{
    /** Persist an in-app (push) notification. WhatsApp/email channels are logged for now. */
    public function notify(User $user, string $title, string $body, ?Order $order = null, string $channel = 'push'): Notification
    {
        return Notification::create([
            'user_id' => $user->id,
            'order_id' => $order?->id,
            'channel' => $channel,
            'title' => $title,
            'body' => $body,
            'is_read' => false,
            'sent_at' => now(),
            'created_at' => now(),
        ]);
    }

    /** Notify all staff of an outlet with a given role. */
    public function notifyOutletStaff(int $outletId, array $roles, string $title, string $body, ?Order $order = null): void
    {
        User::where('outlet_id', $outletId)
            ->whereIn('role', $roles)
            ->each(fn (User $u) => $this->notify($u, $title, $body, $order));
    }
}
