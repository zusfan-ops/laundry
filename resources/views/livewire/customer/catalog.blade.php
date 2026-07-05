<div>
    <header class="bg-selly-primary text-white px-5 pt-6 pb-5 rounded-b-3xl sticky top-0 z-20">
        <div class="flex items-center gap-3">
            <a href="{{ route('home') }}"><x-icon name="arrow-left" class="w-6 h-6" /></a>
            <h1 class="text-lg font-bold">Pilih Layanan</h1>
        </div>
        <div class="mt-4 flex items-center gap-2 bg-white rounded-xl px-4 py-2.5">
            <x-icon name="search" class="w-5 h-5 text-selly-muted" />
            <input type="text" wire:model.live.debounce.300ms="q" placeholder="Cari layanan…"
                   class="flex-1 text-sm text-selly-text outline-none bg-transparent">
        </div>
    </header>

    {{-- Category chips --}}
    <div class="px-5 mt-4">
        <div class="flex gap-2 overflow-x-auto no-scrollbar pb-1">
            <button wire:click="selectCategory(null)"
                    class="px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap {{ $category === null ? 'bg-selly-primary text-white' : 'bg-white text-selly-muted' }}">
                Semua
            </button>
            @foreach($categories as $c)
                <button wire:click="selectCategory({{ $c->id }})"
                        class="px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap {{ $category === $c->id ? 'bg-selly-primary text-white' : 'bg-white text-selly-muted' }}">
                    {{ $c->name }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- Service list --}}
    <div class="px-5 mt-4 space-y-3">
        @forelse($services as $service)
            <a href="{{ route('service.show', $service) }}" class="flex items-center gap-3 rounded-2xl bg-white p-3.5 shadow-soft">
                <span class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0"
                      style="background-color: {{ $service->category->color }}1A; color: {{ $service->category->color }}">
                    <x-icon :name="$service->category->icon" class="w-6 h-6" />
                </span>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-sm">{{ $service->name }}</p>
                    <p class="text-xs text-selly-muted truncate">{{ $service->description }}</p>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-sm font-bold text-selly-primary">{{ rupiah($service->unit_price) }}/{{ $service->unit_label }}</span>
                        @if($service->pricing_type === 'weight' && $service->min_qty > 0)
                            <span class="text-[10px] bg-selly-primary-soft text-selly-primary-dark px-1.5 py-0.5 rounded-full">Min {{ rtrim(rtrim(number_format($service->min_qty,1),'0'),'.') }} {{ $service->unit_label }}</span>
                        @endif
                    </div>
                </div>
                <x-icon name="chevron-right" class="w-5 h-5 text-selly-muted shrink-0" />
            </a>
        @empty
            <div class="text-center py-16 text-selly-muted">
                <x-icon name="search" class="w-12 h-12 mx-auto mb-3 opacity-40" />
                <p class="text-sm">Layanan tidak ditemukan.</p>
            </div>
        @endforelse
    </div>

    <livewire:customer.cart-bar />
</div>
