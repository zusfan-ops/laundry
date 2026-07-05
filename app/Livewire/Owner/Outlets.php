<?php

namespace App\Livewire\Owner;

use App\Models\Outlet;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.staff')]
class Outlets extends Component
{
    public ?int $editingId = null;
    public bool $showForm = false;

    #[Validate('required|string|max:120')]
    public string $name = '';

    #[Validate('nullable|string|max:20')]
    public string $phone = '';

    #[Validate('nullable|string|max:255')]
    public string $address = '';

    #[Validate('nullable|string|max:120')]
    public string $opening_hours = '';

    #[Validate('nullable|numeric|between:-90,90')]
    public $lat = null;

    #[Validate('nullable|numeric|between:-180,180')]
    public $lng = null;

    #[Validate('nullable|string|max:255')]
    public string $maps_url = '';

    #[Validate('integer|min:0')]
    public int $free_shipping_threshold = 0;

    #[Validate('integer|min:0')]
    public int $base_shipping_fee = 0;

    #[Validate('integer|min:0')]
    public int $fee_per_km = 0;

    public function create(): void
    {
        $this->reset();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $o = Outlet::findOrFail($id);
        $this->editingId = $o->id;
        $this->name = $o->name;
        $this->phone = $o->phone ?? '';
        $this->address = $o->address ?? '';
        $this->opening_hours = $o->opening_hours ?? '';
        $this->lat = $o->lat;
        $this->lng = $o->lng;
        $this->maps_url = $o->maps_url ?? '';
        $this->free_shipping_threshold = (int) $o->free_shipping_threshold;
        $this->base_shipping_fee = (int) $o->base_shipping_fee;
        $this->fee_per_km = (int) $o->fee_per_km;
        $this->showForm = true;
    }

    public function save(): void
    {
        $data = $this->validate();

        Outlet::updateOrCreate(
            ['id' => $this->editingId],
            [
                'name' => $data['name'],
                'phone' => $data['phone'] ?: null,
                'address' => $data['address'] ?: null,
                'opening_hours' => $data['opening_hours'] ?: null,
                'lat' => $data['lat'] ?: null,
                'lng' => $data['lng'] ?: null,
                'maps_url' => $data['maps_url'] ?: null,
                'free_shipping_threshold' => $data['free_shipping_threshold'],
                'base_shipping_fee' => $data['base_shipping_fee'],
                'fee_per_km' => $data['fee_per_km'],
                'is_active' => true,
            ]
        );

        $this->showForm = false;
        $this->reset('editingId');
        $this->dispatch('toast', message: 'Cabang tersimpan.', type: 'success');
    }

    public function toggle(int $id): void
    {
        $o = Outlet::findOrFail($id);
        $o->update(['is_active' => ! $o->is_active]);
        $this->dispatch('toast', message: $o->is_active ? 'Cabang diaktifkan.' : 'Cabang dinonaktifkan.', type: 'info');
    }

    public function getPreviewProperty(): ?Outlet
    {
        if ($this->lat === null || $this->lat === '' || $this->lng === null || $this->lng === '') {
            return null;
        }
        return new Outlet(['lat' => (float) $this->lat, 'lng' => (float) $this->lng]);
    }

    public function render()
    {
        $outlets = Outlet::orderBy('name')->get();

        return view('livewire.owner.outlets', compact('outlets'));
    }
}
