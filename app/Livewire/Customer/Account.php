<?php

namespace App\Livewire\Customer;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Account extends Component
{
    public function render()
    {
        $user = auth()->user()->load('addresses');
        $orderCount = $user->orders()->count();

        return view('livewire.customer.account', compact('user', 'orderCount'));
    }
}
