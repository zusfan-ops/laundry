<?php

namespace App\Livewire\Customer;

use App\Services\CartService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Cart extends Component
{
    public array $items = [];
    public int $subtotal = 0;
    public float $estimatedWeight = 0;

    public function mount(CartService $cart): void
    {
        $this->sync($cart);
    }

    private function sync(CartService $cart): void
    {
        $this->items = array_values($cart->items());
        $this->subtotal = $cart->subtotal();
        $this->estimatedWeight = $cart->estimatedWeight();
    }

    public function remove(string $rowId, CartService $cart): void
    {
        $cart->remove($rowId);
        $this->sync($cart);
        $this->dispatch('cart-updated');
        $this->dispatch('toast', message: 'Item dihapus', type: 'info');
    }

    public function render()
    {
        return view('livewire.customer.cart');
    }
}
