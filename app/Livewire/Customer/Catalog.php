<?php

namespace App\Livewire\Customer;

use App\Models\Service;
use App\Models\ServiceCategory;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Catalog extends Component
{
    #[Url]
    public ?int $category = null;

    #[Url]
    public string $q = '';

    public function selectCategory(?int $id): void
    {
        $this->category = $id;
    }

    public function render()
    {
        $categories = ServiceCategory::where('is_active', true)->orderBy('sort_order')->get();

        $services = Service::with('category')
            ->where('is_active', true)
            ->when($this->category, fn ($query) => $query->where('category_id', $this->category))
            ->when($this->q, fn ($query) => $query->where('name', 'like', "%{$this->q}%"))
            ->orderBy('category_id')
            ->get();

        return view('livewire.customer.catalog', compact('categories', 'services'));
    }
}
