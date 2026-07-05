<?php

namespace App\Livewire\Owner;

use App\Models\ServiceCategory;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.staff')]
class Categories extends Component
{
    public ?int $editingId = null;

    #[Validate('required|string|max:80')]
    public string $name = '';

    #[Validate('required|string|max:80')]
    public string $icon = 'package';

    #[Validate('required|string|max:20')]
    public string $color = '#0EA5A4';

    #[Validate('integer|min:0')]
    public int $sort_order = 0;

    public bool $showForm = false;

    public array $iconOptions = [
        'washing-machine', 'shirt', 'flame', 'zap', 'package',
        'footprints', 'sparkles', 'box', 'truck', 'gift', 'wallet',
    ];

    public function create(): void
    {
        $this->reset('editingId', 'name', 'icon', 'color', 'sort_order');
        $this->icon = 'package';
        $this->color = '#0EA5A4';
        $this->sort_order = ServiceCategory::max('sort_order') + 1;
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $cat = ServiceCategory::findOrFail($id);
        $this->editingId = $cat->id;
        $this->name = $cat->name;
        $this->icon = $cat->icon ?? 'package';
        $this->color = $cat->color ?? '#0EA5A4';
        $this->sort_order = $cat->sort_order;
        $this->showForm = true;
    }

    public function save(): void
    {
        $data = $this->validate();

        ServiceCategory::updateOrCreate(
            ['id' => $this->editingId],
            $data + ['is_active' => true]
        );

        $this->showForm = false;
        $this->reset('editingId', 'name', 'icon', 'color', 'sort_order');
        $this->dispatch('toast', message: 'Kategori tersimpan.', type: 'success');
    }

    public function toggle(int $id): void
    {
        $cat = ServiceCategory::findOrFail($id);
        $cat->update(['is_active' => ! $cat->is_active]);
        $this->dispatch('toast', message: $cat->is_active ? 'Kategori diaktifkan.' : 'Kategori dinonaktifkan.', type: 'info');
    }

    public function render()
    {
        $categories = ServiceCategory::withCount('services')->orderBy('sort_order')->get();

        return view('livewire.owner.categories', compact('categories'));
    }
}
