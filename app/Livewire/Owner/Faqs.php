<?php

namespace App\Livewire\Owner;

use App\Models\Faq;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.staff')]
class Faqs extends Component
{
    public ?int $editingId = null;
    public bool $showForm = false;

    #[Validate('required|string|max:200')]
    public string $question = '';

    #[Validate('required|string|max:2000')]
    public string $answer = '';

    #[Validate('integer|min:0')]
    public int $sort_order = 0;

    public function create(): void
    {
        $this->reset('editingId', 'question', 'answer');
        $this->sort_order = Faq::max('sort_order') + 1;
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $f = Faq::findOrFail($id);
        $this->editingId = $f->id;
        $this->question = $f->question;
        $this->answer = $f->answer;
        $this->sort_order = $f->sort_order;
        $this->showForm = true;
    }

    public function save(): void
    {
        $data = $this->validate();
        Faq::updateOrCreate(['id' => $this->editingId], $data + ['is_active' => true]);
        $this->showForm = false;
        $this->reset('editingId');
        $this->dispatch('toast', message: 'FAQ tersimpan.', type: 'success');
    }

    public function toggle(int $id): void
    {
        $f = Faq::findOrFail($id);
        $f->update(['is_active' => ! $f->is_active]);
        $this->dispatch('toast', message: $f->is_active ? 'FAQ ditampilkan.' : 'FAQ disembunyikan.', type: 'info');
    }

    public function delete(int $id): void
    {
        Faq::findOrFail($id)->delete();
        $this->dispatch('toast', message: 'FAQ dihapus.', type: 'info');
    }

    public function render()
    {
        $faqs = Faq::orderBy('sort_order')->get();

        return view('livewire.owner.faqs', compact('faqs'));
    }
}
