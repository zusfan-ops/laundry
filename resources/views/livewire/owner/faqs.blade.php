<div>
    <x-manage-nav active="faqs" />

    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-xl font-bold">FAQ / Q&amp;A</h1>
            <p class="text-sm text-selly-muted">Pertanyaan umum yang tampil di landing.</p>
        </div>
        <button wire:click="create" class="bg-selly-primary text-white text-sm font-semibold px-4 py-2 rounded-xl flex items-center gap-1.5">
            <x-icon name="plus" class="w-4 h-4" /> Tambah
        </button>
    </div>

    @if($showForm)
        <div class="bg-white rounded-2xl p-4 shadow-soft mb-4">
            <h2 class="font-semibold mb-3">{{ $editingId ? 'Ubah' : 'Tambah' }} FAQ</h2>
            <div class="space-y-3">
                <div>
                    <label class="text-sm font-medium">Pertanyaan</label>
                    <input type="text" wire:model="question" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                    @error('question') <p class="text-selly-danger text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium">Jawaban</label>
                    <textarea wire:model="answer" rows="3" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm"></textarea>
                    @error('answer') <p class="text-selly-danger text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="w-32">
                    <label class="text-sm font-medium">Urutan</label>
                    <input type="number" wire:model="sort_order" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                </div>
            </div>
            <div class="flex gap-2 mt-4">
                <button wire:click="save" class="bg-selly-primary text-white text-sm font-semibold px-5 py-2 rounded-xl">Simpan</button>
                <button wire:click="$set('showForm', false)" class="text-selly-muted text-sm px-3">Batal</button>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-soft divide-y divide-gray-50">
        @forelse($faqs as $f)
            <div class="p-4 {{ $f->is_active ? '' : 'opacity-60' }}">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex-1">
                        <p class="font-semibold text-sm">{{ $f->question }}</p>
                        <p class="text-xs text-selly-muted mt-1">{{ $f->answer }}</p>
                    </div>
                    <div class="flex flex-col items-end gap-1.5 shrink-0">
                        <div class="flex gap-2">
                            <button wire:click="edit({{ $f->id }})" class="text-selly-primary"><x-icon name="edit" class="w-4 h-4" /></button>
                            <button wire:click="delete({{ $f->id }})" wire:confirm="Hapus FAQ ini?" class="text-selly-danger"><x-icon name="x" class="w-4 h-4" /></button>
                        </div>
                        <button wire:click="toggle({{ $f->id }})"
                                class="text-[11px] font-semibold px-2 py-0.5 rounded-full {{ $f->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $f->is_active ? 'Tampil' : 'Disembunyikan' }}
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <p class="p-6 text-sm text-selly-muted text-center">Belum ada FAQ.</p>
        @endforelse
    </div>
</div>
