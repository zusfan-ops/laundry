<?php

namespace App\Livewire\Customer;

use App\Models\Order;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.app')]
class OrderHistory extends Component
{
    #[Url]
    public string $tab = 'active';

    public function render()
    {
        $query = Order::where('user_id', auth()->id())->latest();

        $query->when($this->tab === 'active', fn ($q) => $q->whereNotIn('status', ['completed', 'cancelled']))
            ->when($this->tab === 'done', fn ($q) => $q->where('status', 'completed'))
            ->when($this->tab === 'cancelled', fn ($q) => $q->where('status', 'cancelled'));

        $orders = $query->get();

        return view('livewire.customer.order-history', compact('orders'));
    }
}
