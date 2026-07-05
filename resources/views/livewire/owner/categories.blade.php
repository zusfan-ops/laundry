<div>
    <x-manage-nav active="categories" />

    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-xl font-bold">Kategori Layanan</h1>
            <p class="text-sm text-selly-muted">Tambah, ubah, atau nonaktifkan kategori.</p>
        </div>
        <button wire:click="create" class="bg-selly-primary text-white text-sm font-semibold px-4 py-2 rounded-xl flex items-center gap-1.5">
            <x-icon name="plus" class="w-4 h-4" /> Tambah
        </button>
    </div>

    {{-- Form --}}
    @if($showForm)
        <div class="bg-white rounded-2xl p-4 shadow-soft mb-4">
            <h2 class="font-semibold mb-3">{{ $editingId ? 'Ubah' : 'Tambah' }} Kategori</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="text-sm font-medium">Nama</label>
                    <input type="text" wire:model="name" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                    @error('name') <p class="text-selly-danger text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium">Urutan</label>
                    <input type="number" wire:model="sort_order" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="text-sm font-medium">Warna</label>
                    <div class="flex items-center gap-2 mt-1">
                        <input type="color" wire:model.live="color" class="w-10 h-9 rounded border border-gray-200">
                        <input type="text" wire:model="color" class="flex-1 rounded-lg border border-gray-200 px-3 py-2 text-sm">
                    </div>
                </div>
                <div>
                    <label class="text-sm font-medium">Ikon</label>
                    <div class="flex flex-wrap gap-2 mt-1">
                        @foreach($iconOptions as $opt)
                            <button type="button" wire:click="$set('icon', '{{ $opt }}')"
                                    class="w-10 h-10 rounded-xl flex items-center justify-center border {{ $icon === $opt ? 'border-selly-primary bg-selly-primary-soft text-selly-primary' : 'border-gray-200 text-selly-muted' }}">
                                <x-icon :name="$opt" class="w-5 h-5" />
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="flex gap-2 mt-4">
                <button wire:click="save" class="bg-selly-primary text-white text-sm font-semibold px-5 py-2 rounded-xl">Simpan</button>
                <button wire:click="$set('showForm', false)" class="text-selly-muted text-sm px-3">Batal</button>
            </div>
        </div>
    @endif

    {{-- List --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
        @foreach($categories as $cat)
            <div class="bg-white rounded-2xl p-4 shadow-soft flex items-center gap-3 {{ $cat->is_active ? '' : 'opacity-60' }}">
                <span class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0"
                      style="background-color: {{ $cat->color }}1A; color: {{ $cat->color }}">
                    <x-icon :name="$cat->icon ?? 'box'" class="w-6 h-6" />
                </span>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-sm">{{ $cat->name }}</p>
                    <p class="text-xs text-selly-muted">{{ $cat->services_count }} layanan · urutan {{ $cat->sort_order }}</p>
                </div>
                <div class="flex flex-col items-end gap-1.5">
                    <button wire:click="edit({{ $cat->id }})" class="text-selly-primary"><x-icon name="edit" class="w-4 h-4" /></button>
                    <button wire:click="toggle({{ $cat->id }})"
                            class="text-[11px] font-semibold px-2 py-0.5 rounded-full {{ $cat->is_active ? 'bg-selly-success/15 text-selly-success' : 'bg-selly-muted/15 text-selly-muted' }}">
                        {{ $cat->is_active ? 'Aktif' : 'Nonaktif' }}
                    </button>
                </div>
            </div>
        @endforeach
    </div>
</div>
