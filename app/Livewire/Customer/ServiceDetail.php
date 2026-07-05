<?php

namespace App\Livewire\Customer;

use App\Models\Service;
use App\Models\ServiceModifier;
use App\Services\CartService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class ServiceDetail extends Component
{
    public Service $service;

    public ?int $speedId = null;
    public ?int $perfumeId = null;
    public float $qty = 1;

    public function mount(Service $service): void
    {
        $this->service = $service->load('category');
        $this->qty = max(1, (float) $service->min_qty);

        // Default to "Reguler" speed and "Tanpa Parfum".
        $this->speedId = ServiceModifier::where('type', 'speed')->where('multiplier', 1.00)->value('id');
        $this->perfumeId = ServiceModifier::where('type', 'perfume')->where('flat_fee', 0)->value('id');
    }

    public function incQty(): void
    {
        $this->qty += $this->service->isWeight() ? 0.5 : 1;
    }

    public function decQty(): void
    {
        $step = $this->service->isWeight() ? 0.5 : 1;
        $min = max($step, (float) $this->service->min_qty);
        $this->qty = max($min, $this->qty - $step);
    }

    public function getEstimateProperty(): int
    {
        $speed = $this->speedId ? ServiceModifier::find($this->speedId) : null;
        $perfume = $this->perfumeId ? ServiceModifier::find($this->perfumeId) : null;
        $mult = $speed?->multiplier ?? 1.0;
        $fee = $perfume?->flat_fee ?? 0;

        return (int) ceil($this->qty * $this->service->unit_price * $mult) + (int) $fee;
    }

    public function addToCart(CartService $cart)
    {
        if ($this->service->isWeight() && $this->qty < $this->service->min_qty) {
            $this->qty = (float) $this->service->min_qty;
        }

        $speed = $this->speedId ? ServiceModifier::find($this->speedId) : null;
        $perfume = $this->perfumeId ? ServiceModifier::find($this->perfumeId) : null;

        $cart->add($this->service, $this->qty, $speed, $perfume);

        $this->dispatch('cart-updated');
        $this->dispatch('toast', message: 'Ditambahkan ke keranjang', type: 'success');

        return $this->redirectRoute('catalog', navigate: false);
    }

    public function render()
    {
        $speeds = ServiceModifier::where('type', 'speed')->where('is_active', true)->get();
        $perfumes = ServiceModifier::where('type', 'perfume')->where('is_active', true)->get();

        return view('livewire.customer.service-detail', compact('speeds', 'perfumes'));
    }
}
