<div>
    <x-manage-nav active="services" />

    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-xl font-bold">Layanan</h1>
            <p class="text-sm text-selly-muted">Kelola layanan, harga, dan durasi.</p>
        </div>
        <button wire:click="create" class="bg-selly-primary text-white text-sm font-semibold px-4 py-2 rounded-xl flex items-center gap-1.5">
            <x-icon name="plus" class="w-4 h-4" /> Tambah
        </button>
    </div>

    @if($showForm)
        <div class="bg-white rounded-2xl p-4 shadow-soft mb-4">
            <h2 class="font-semibold mb-3">{{ $editingId ? 'Ubah' : 'Tambah' }} Layanan</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="text-sm font-medium">Nama</label>
                    <input type="text" wire:model="name" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                    @error('name') <p class="text-selly-danger text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium">Kategori</label>
                    <select wire:model="category_id" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                        @foreach($categories as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                    </select>
                    @error('category_id') <p class="text-selly-danger text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm font-medium">Deskripsi</label>
                    <input type="text" wire:model="description" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="text-sm font-medium">Tipe Harga</label>
                    <select wire:model.live="pricing_type" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                        <option value="weight">Kiloan (per kg)</option>
                        <option value="unit">Satuan (per pcs)</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium">Harga ({{ $pricing_type === 'weight' ? 'per kg' : 'per pcs' }})</label>
                    <input type="number" wire:model="unit_price" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                    @error('unit_price') <p class="text-selly-danger text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium">Label Satuan</label>
                    <input type="text" wire:model="unit_label" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="text-sm font-medium">Min. {{ $pricing_type === 'weight' ? 'Berat' : 'Qty' }}</label>
                    <input type="number" step="0.5" wire:model="min_qty" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="text-sm font-medium">Estimasi Durasi (jam)</label>
                    <input type="number" wire:model="est_duration_hours" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                </div>
            </div>
            <div class="flex gap-2 mt-4">
                <button wire:click="save" class="bg-selly-primary text-white text-sm font-semibold px-5 py-2 rounded-xl">Simpan</button>
                <button wire:click="$set('showForm', false)" class="text-selly-muted text-sm px-3">Batal</button>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-soft divide-y divide-gray-50">
        @foreach($services as $s)
            <div class="flex items-center gap-3 p-3.5 {{ $s->is_active ? '' : 'opacity-60' }}">
                <span class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0"
                      style="background-color: {{ $s->category->color ?? '#0891B2' }}1A; color: {{ $s->category->color ?? '#0891B2' }}">
                    <x-icon :name="$s->category->icon ?? 'box'" class="w-6 h-6" />
                </span>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-sm">{{ $s->name }}</p>
                    <p class="text-xs text-selly-muted">{{ $s->category->name }} · {{ rupiah($s->unit_price) }}/{{ $s->unit_label }} · {{ ucfirst($s->pricing_type) }}</p>
                </div>
                <button wire:click="edit({{ $s->id }})" class="text-selly-primary"><x-icon name="edit" class="w-4 h-4" /></button>
                <button wire:click="toggle({{ $s->id }})"
                        class="text-[11px] font-semibold px-2 py-0.5 rounded-full {{ $s->is_active ? 'bg-selly-success/15 text-selly-success' : 'bg-selly-muted/15 text-selly-muted' }}">
                    {{ $s->is_active ? 'Aktif' : 'Nonaktif' }}
                </button>
            </div>
        @endforeach
    </div>
</div>
