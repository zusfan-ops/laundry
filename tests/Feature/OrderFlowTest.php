<?php

namespace Tests\Feature;

use App\Livewire\Courier\Tasks as CourierTasks;
use App\Livewire\Customer\Checkout;
use App\Livewire\Customer\OrderShow;
use App\Livewire\Customer\ServiceDetail;
use App\Livewire\Operator\Board;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;

class OrderFlowTest extends TestCase
{
    public function test_full_kiloan_order_lifecycle_with_price_confirmation(): void
    {
        $customer = User::where('email', 'rina@selly.test')->firstOrFail();
        $operator = User::where('email', 'operator@selly.test')->firstOrFail();
        $courier = User::where('email', 'kurir@selly.test')->firstOrFail();
        $service = Service::where('pricing_type', 'weight')->firstOrFail();

        // 1) Customer adds a weight service to the cart.
        $this->actingAs($customer);
        Livewire::test(ServiceDetail::class, ['service' => $service])
            ->set('qty', 3)
            ->call('addToCart')
            ->assertHasNoErrors();

        // 2) Customer places the order via checkout.
        $component = Livewire::test(Checkout::class)
            ->set('pickupSlotId', \App\Models\TimeSlot::first()->id)
            ->set('deliverySlotId', \App\Models\TimeSlot::first()->id)
            ->call('placeOrder')
            ->assertHasNoErrors();

        $order = Order::where('user_id', $customer->id)->latest()->firstOrFail();
        $this->assertSame('placed', $order->status);
        $this->assertGreaterThan(0, $order->estimated_total);

        // 3) Operator assigns pickup.
        $this->actingAs($operator);
        Livewire::test(Board::class)->call('assignPickup', $order->id);
        $order->refresh();
        $this->assertSame('assigned_pickup', $order->status);

        // 4) Courier completes pickup.
        $this->actingAs($courier);
        $pickup = $order->assignments()->where('type', 'pickup')->firstOrFail();
        $tasks = Livewire::test(CourierTasks::class);
        $tasks->call('depart', $pickup->id)->call('arrive', $pickup->id)
            ->call('openProof', $pickup->id)->call('complete');
        $order->refresh();
        $this->assertSame('picked_up', $order->status);

        // 5) Operator receives + weighs heavier than estimate -> price confirm.
        $this->actingAs($operator);
        Livewire::test(Board::class)->call('receiveAtOutlet', $order->id);
        $order->refresh();
        $this->assertSame('at_outlet', $order->status);

        $item = $order->items()->first();
        Livewire::test(Board::class)
            ->call('openWeigh', $order->id)
            ->set("weights.{$item->id}", 6) // double the 3kg estimate -> needs confirm
            ->call('saveWeigh');
        $order->refresh();
        $this->assertSame('awaiting_price_confirm', $order->status);
        $this->assertEquals(6.0, $order->actual_weight);
        $this->assertGreaterThan($order->estimated_total, $order->final_total);

        // 6) Customer confirms price.
        $this->actingAs($customer);
        Livewire::test(OrderShow::class, ['order' => $order])->call('confirmPrice');
        $order->refresh();
        $this->assertSame('processing', $order->status);

        // 7) Operator marks ready and assigns delivery.
        $this->actingAs($operator);
        Livewire::test(Board::class)->call('markReady', $order->id);
        Livewire::test(Board::class)->call('assignDelivery', $order->id);
        $order->refresh();
        $this->assertSame('assigned_delivery', $order->status);

        // 8) Courier delivers.
        $this->actingAs($courier);
        $delivery = $order->assignments()->where('type', 'delivery')->firstOrFail();
        $t = Livewire::test(CourierTasks::class);
        $t->call('startDelivery', $delivery->id)->call('arrive', $delivery->id)
            ->call('openProof', $delivery->id)->call('complete');
        $order->refresh();
        $this->assertSame('completed', $order->status);

        // Audit log should contain the full chain.
        $this->assertTrue($order->statusLogs()->where('to_status', 'completed')->exists());

        // Clean up the test order so the dev DB stays tidy.
        $order->statusLogs()->delete();
        $order->items()->delete();
        $order->assignments()->delete();
        $order->payments()->delete();
        $order->forceDelete();
    }
}
