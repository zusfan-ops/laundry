<div>
    <x-manage-nav active="outlets" />

    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-xl font-bold">Cabang / Outlet</h1>
            <p class="text-sm text-selly-muted">Atur lokasi, jam buka, dan ongkir tiap cabang.</p>
        </div>
        <button wire:click="create" class="bg-selly-primary text-white text-sm font-semibold px-4 py-2 rounded-xl flex items-center gap-1.5">
            <x-icon name="plus" class="w-4 h-4" /> Tambah Cabang
        </button>
    </div>

    @if($showForm)
        <div class="bg-white rounded-2xl p-4 shadow-soft mb-4 grid grid-cols-1 lg:grid-cols-2 gap-5">
            <div class="space-y-3">
                <h2 class="font-semibold">{{ $editingId ? 'Ubah' : 'Tambah' }} Cabang</h2>
                <div>
                    <label class="text-sm font-medium">Nama Cabang</label>
                    <input type="text" wire:model="name" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                    @error('name') <p class="text-selly-danger text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-sm font-medium">No. Telepon</label>
                        <input type="text" wire:model="phone" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-sm font-medium">Jam Buka</label>
                        <input type="text" wire:model="opening_hours" placeholder="07.00 - 21.00" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                    </div>
                </div>
                <div>
                    <label class="text-sm font-medium">Alamat</label>
                    <textarea wire:model="address" rows="2" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-sm font-medium">Latitude</label>
                        <input type="number" step="0.0000001" wire:model.live.debounce.500ms="lat" placeholder="-7.7626" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                        @error('lat') <p class="text-selly-danger text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium">Longitude</label>
                        <input type="number" step="0.0000001" wire:model.live.debounce.500ms="lng" placeholder="110.3795" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                        @error('lng') <p class="text-selly-danger text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <label class="text-sm font-medium">Link Google Maps (opsional)</label>
                    <input type="text" wire:model="maps_url" placeholder="https://maps.app.goo.gl/..." class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                    <p class="text-[11px] text-selly-muted mt-1">Klik kanan titik di Google Maps → salin koordinat untuk lat/lng.</p>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <label class="text-[11px] text-selly-muted">Gratis ongkir ≥</label>
                        <input type="number" wire:model="free_shipping_threshold" class="mt-1 w-full rounded-lg border border-gray-200 px-2 py-1.5 text-sm">
                    </div>
                    <div>
                        <label class="text-[11px] text-selly-muted">Ongkir dasar</label>
                        <input type="number" wire:model="base_shipping_fee" class="mt-1 w-full rounded-lg border border-gray-200 px-2 py-1.5 text-sm">
                    </div>
                    <div>
                        <label class="text-[11px] text-selly-muted">Per km</label>
                        <input type="number" wire:model="fee_per_km" class="mt-1 w-full rounded-lg border border-gray-200 px-2 py-1.5 text-sm">
                    </div>
                </div>
                <div class="flex gap-2 pt-1">
                    <button wire:click="save" class="bg-selly-primary text-white text-sm font-semibold px-5 py-2 rounded-xl">Simpan</button>
                    <button wire:click="$set('showForm', false)" class="text-selly-muted text-sm px-3">Batal</button>
                </div>
            </div>
            <div>
                <label class="text-sm font-medium block mb-1">Pratinjau Peta</label>
                @if($this->preview)
                    <iframe class="w-full h-64 rounded-xl border border-gray-200" loading="lazy" src="{{ $this->preview->mapEmbedUrl() }}"></iframe>
                @else
                    <div class="w-full h-64 rounded-xl border border-dashed border-gray-300 flex items-center justify-center text-sm text-selly-muted text-center px-4">
                        Isi Latitude & Longitude untuk melihat peta.
                    </div>
                @endif
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($outlets as $o)
            <div class="bg-white rounded-2xl shadow-soft overflow-hidden {{ $o->is_active ? '' : 'opacity-60' }}">
                @if($o->mapEmbedUrl())
                    <iframe class="w-full h-40 border-0" loading="lazy" src="{{ $o->mapEmbedUrl() }}"></iframe>
                @endif
                <div class="p-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="font-semibold">{{ $o->name }}</p>
                            <p class="text-xs text-selly-muted mt-0.5">{{ $o->address }}</p>
                        </div>
                        <button wire:click="edit({{ $o->id }})" class="text-selly-primary"><x-icon name="edit" class="w-4 h-4" /></button>
                    </div>
                    <div class="flex items-center gap-3 mt-2 text-xs text-selly-muted">
                        @if($o->phone)<span class="flex items-center gap-1"><x-icon name="phone" class="w-3.5 h-3.5"/> {{ $o->phone }}</span>@endif
                        @if($o->opening_hours)<span class="flex items-center gap-1"><x-icon name="clock" class="w-3.5 h-3.5"/> {{ $o->opening_hours }}</span>@endif
                    </div>
                    <div class="flex items-center justify-between mt-3">
                        <button wire:click="toggle({{ $o->id }})"
                                class="text-[11px] font-semibold px-2 py-0.5 rounded-full {{ $o->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $o->is_active ? 'Aktif' : 'Nonaktif' }}
                        </button>
                        @if($o->directionsUrl())
                            <a href="{{ $o->directionsUrl() }}" target="_blank" class="text-xs text-selly-primary font-semibold flex items-center gap-1"><x-icon name="navigation" class="w-3.5 h-3.5"/> Rute</a>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
