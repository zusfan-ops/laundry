<?php

namespace App\Livewire\Customer;

use App\Services\CartService;
use Livewire\Attributes\On;
use Livewire\Component;

class CartBar extends Component
{
    public int $count = 0;
    public int $subtotal = 0;

    public function mount(CartService $cart): void
    {
        $this->refreshCart($cart);
    }

    #[On('cart-updated')]
    public function refreshCart(CartService $cart): void
    {
        $this->count = $cart->count();
        $this->subtotal = $cart->subtotal();
    }

    public function render()
    {
        return view('livewire.customer.cart-bar');
    }
}
