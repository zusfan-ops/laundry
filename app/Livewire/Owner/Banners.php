<?php

namespace App\Livewire\Owner;

use App\Models\PromoBanner;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.staff')]
class Banners extends Component
{
    public ?int $editingId = null;
    public bool $showForm = false;

    #[Validate('required|string|max:120')]
    public string $title = '';

    #[Validate('nullable|string|max:180')]
    public string $subtitle = '';

    #[Validate('nullable|string|max:40')]
    public string $cta_label = '';

    #[Validate('nullable|string|max:255')]
    public string $cta_url = '';

    #[Validate('required|string|max:40')]
    public string $art = 'bubbles';

    #[Validate('required|string|max:20')]
    public string $color_from = '#0EA5A4';

    #[Validate('required|string|max:20')]
    public string $color_to = '#0B7E7D';

    #[Validate('integer|min:0')]
    public int $sort_order = 0;

    public array $artOptions = ['bubbles', 'waves', 'dots', 'sparkles', 'leaves', 'clothes'];

    public array $palettes = [
        ['#0EA5A4', '#0B7E7D'],
        ['#FFB020', '#FF6B57'],
        ['#16A34A', '#0B7E7D'],
        ['#6366F1', '#0EA5A4'],
        ['#FF6B57', '#DC2626'],
    ];

    public function create(): void
    {
        $this->reset('editingId', 'title', 'subtitle', 'cta_label', 'cta_url');
        $this->art = 'bubbles';
        $this->color_from = '#0EA5A4';
        $this->color_to = '#0B7E7D';
        $this->sort_order = PromoBanner::max('sort_order') + 1;
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $b = PromoBanner::findOrFail($id);
        $this->editingId = $b->id;
        $this->title = $b->title;
        $this->subtitle = $b->subtitle ?? '';
        $this->cta_label = $b->cta_label ?? '';
        $this->cta_url = $b->cta_url ?? '';
        $this->art = $b->art;
        $this->color_from = $b->color_from;
        $this->color_to = $b->color_to;
        $this->sort_order = $b->sort_order;
        $this->showForm = true;
    }

    public function usePalette(int $i): void
    {
        [$this->color_from, $this->color_to] = $this->palettes[$i];
    }

    public function save(): void
    {
        $data = $this->validate();

        PromoBanner::updateOrCreate(
            ['id' => $this->editingId],
            $data + ['is_active' => true]
        );

        $this->showForm = false;
        $this->reset('editingId');
        $this->dispatch('toast', message: 'Banner tersimpan.', type: 'success');
    }

    public function toggle(int $id): void
    {
        $b = PromoBanner::findOrFail($id);
        $b->update(['is_active' => ! $b->is_active]);
        $this->dispatch('toast', message: $b->is_active ? 'Banner ditayangkan.' : 'Banner disembunyikan.', type: 'info');
    }

    public function delete(int $id): void
    {
        PromoBanner::findOrFail($id)->delete();
        $this->dispatch('toast', message: 'Banner dihapus.', type: 'info');
    }

    public function getPreviewProperty(): PromoBanner
    {
        return new PromoBanner([
            'title' => $this->title ?: 'Judul Promo',
            'subtitle' => $this->subtitle ?: 'Subjudul promo tampil di sini',
            'cta_label' => $this->cta_label ?: null,
            'cta_url' => $this->cta_url ?: null,
            'art' => $this->art,
            'color_from' => $this->color_from,
            'color_to' => $this->color_to,
        ]);
    }

    public function render()
    {
        $banners = PromoBanner::orderBy('sort_order')->get();

        return view('livewire.owner.banners', compact('banners'));
    }
}
