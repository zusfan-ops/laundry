<div>
    <x-manage-nav active="banners" />

    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-xl font-bold">Banner Promo</h1>
            <p class="text-sm text-selly-muted">Banner tampil di landing & beranda pelanggan.</p>
        </div>
        <button wire:click="create" class="bg-selly-primary text-white text-sm font-semibold px-4 py-2 rounded-xl flex items-center gap-1.5">
            <x-icon name="plus" class="w-4 h-4" /> Tambah
        </button>
    </div>

    @if($showForm)
        <div class="bg-white rounded-2xl p-4 shadow-soft mb-4 grid grid-cols-1 lg:grid-cols-2 gap-5">
            <div class="space-y-3">
                <h2 class="font-semibold">{{ $editingId ? 'Ubah' : 'Tambah' }} Banner</h2>
                <div>
                    <label class="text-sm font-medium">Judul</label>
                    <input type="text" wire:model.live="title" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                    @error('title') <p class="text-selly-danger text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium">Subjudul</label>
                    <input type="text" wire:model.live="subtitle" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-sm font-medium">Label Tombol</label>
                        <input type="text" wire:model.live="cta_label" placeholder="cth: Pakai Kode" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-sm font-medium">URL Tombol</label>
                        <input type="text" wire:model.live="cta_url" placeholder="/promo" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                    </div>
                </div>
                <div>
                    <label class="text-sm font-medium">Motif SVG</label>
                    <div class="flex flex-wrap gap-2 mt-1">
                        @foreach($artOptions as $opt)
                            <button type="button" wire:click="$set('art', '{{ $opt }}')"
                                    class="px-3 py-1.5 rounded-full text-xs font-medium border {{ $art === $opt ? 'border-selly-primary bg-selly-primary-soft text-selly-primary' : 'border-gray-200 text-selly-muted' }}">
                                {{ ucfirst($opt) }}
                            </button>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label class="text-sm font-medium">Palet Warna</label>
                    <div class="flex flex-wrap gap-2 mt-1">
                        @foreach($palettes as $i => $p)
                            <button type="button" wire:click="usePalette({{ $i }})"
                                    class="w-10 h-8 rounded-lg border border-gray-200" style="background: linear-gradient(135deg, {{ $p[0] }}, {{ $p[1] }});"></button>
                        @endforeach
                    </div>
                    <div class="flex gap-2 mt-2">
                        <input type="color" wire:model.live="color_from" class="w-10 h-9 rounded border border-gray-200">
                        <input type="color" wire:model.live="color_to" class="w-10 h-9 rounded border border-gray-200">
                        <input type="number" wire:model="sort_order" class="flex-1 rounded-lg border border-gray-200 px-3 py-2 text-sm" placeholder="Urutan">
                    </div>
                </div>
                <div class="flex gap-2 pt-1">
                    <button wire:click="save" class="bg-selly-primary text-white text-sm font-semibold px-5 py-2 rounded-xl">Simpan</button>
                    <button wire:click="$set('showForm', false)" class="text-selly-muted text-sm px-3">Batal</button>
                </div>
            </div>
            <div>
                <label class="text-sm font-medium block mb-1">Pratinjau</label>
                <x-promo-banner :banner="$this->preview" />
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse($banners as $b)
            <div class="relative {{ $b->is_active ? '' : 'opacity-60' }}">
                <x-promo-banner :banner="$b" />
                <div class="flex items-center gap-2 mt-2">
                    <button wire:click="edit({{ $b->id }})" class="text-xs bg-white border border-gray-200 px-3 py-1.5 rounded-lg font-medium flex items-center gap-1"><x-icon name="edit" class="w-3.5 h-3.5"/> Ubah</button>
                    <button wire:click="toggle({{ $b->id }})" class="text-xs font-semibold px-3 py-1.5 rounded-lg {{ $b->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">{{ $b->is_active ? 'Tayang' : 'Disembunyikan' }}</button>
                    <button wire:click="delete({{ $b->id }})" wire:confirm="Hapus banner ini?" class="text-xs text-selly-danger px-2 ml-auto">Hapus</button>
                </div>
            </div>
        @empty
            <p class="text-sm text-selly-muted">Belum ada banner. Tambahkan satu untuk tampil di landing.</p>
        @endforelse
    </div>
</div>
