<?php

namespace App\Livewire\Owner;

use App\Models\Service;
use App\Models\ServiceCategory;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.staff')]
class Services extends Component
{
    public ?int $editingId = null;
    public bool $showForm = false;

    #[Validate('required|integer|exists:service_categories,id')]
    public ?int $category_id = null;

    #[Validate('required|string|max:120')]
    public string $name = '';

    #[Validate('nullable|string|max:255')]
    public string $description = '';

    #[Validate('required|in:weight,unit')]
    public string $pricing_type = 'weight';

    #[Validate('required|integer|min:0')]
    public int $unit_price = 0;

    #[Validate('required|string|max:20')]
    public string $unit_label = 'kg';

    #[Validate('numeric|min:0')]
    public float $min_qty = 0;

    #[Validate('integer|min:1')]
    public int $est_duration_hours = 48;

    public function updatedPricingType(string $value): void
    {
        $this->unit_label = $value === 'weight' ? 'kg' : 'pcs';
    }

    public function create(): void
    {
        $this->reset('editingId', 'name', 'description', 'unit_price', 'min_qty');
        $this->pricing_type = 'weight';
        $this->unit_label = 'kg';
        $this->est_duration_hours = 48;
        $this->category_id = ServiceCategory::where('is_active', true)->value('id');
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $s = Service::findOrFail($id);
        $this->editingId = $s->id;
        $this->category_id = $s->category_id;
        $this->name = $s->name;
        $this->description = $s->description ?? '';
        $this->pricing_type = $s->pricing_type;
        $this->unit_price = $s->unit_price;
        $this->unit_label = $s->unit_label;
        $this->min_qty = (float) $s->min_qty;
        $this->est_duration_hours = $s->est_duration_hours;
        $this->showForm = true;
    }

    public function save(): void
    {
        $data = $this->validate();

        Service::updateOrCreate(
            ['id' => $this->editingId],
            $data + ['is_active' => true]
        );

        $this->showForm = false;
        $this->reset('editingId');
        $this->dispatch('toast', message: 'Layanan tersimpan.', type: 'success');
    }

    public function toggle(int $id): void
    {
        $s = Service::findOrFail($id);
        $s->update(['is_active' => ! $s->is_active]);
        $this->dispatch('toast', message: $s->is_active ? 'Layanan diaktifkan.' : 'Layanan dinonaktifkan.', type: 'info');
    }

    public function render()
    {
        $services = Service::with('category')->orderBy('category_id')->get();
        $categories = ServiceCategory::orderBy('sort_order')->get();

        return view('livewire.owner.services', compact('services', 'categories'));
    }
}
