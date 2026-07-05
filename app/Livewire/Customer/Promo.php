<?php

namespace App\Livewire\Customer;

use App\Models\Voucher;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Promo extends Component
{
    public function render()
    {
        $vouchers = Voucher::where('is_active', true)
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>', now()))
            ->orderBy('min_order')
            ->get();

        return view('livewire.customer.promo', compact('vouchers'));
    }
}
