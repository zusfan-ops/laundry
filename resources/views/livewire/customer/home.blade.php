<div>
    {{-- Header --}}
    <header class="bg-selly-primary text-white px-5 pt-6 pb-8 rounded-b-3xl">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-white/80 text-sm">Halo,</p>
                <h1 class="text-xl font-bold">{{ auth()->user()->name }} 👋</h1>
                <p class="text-white/80 text-xs mt-1 flex items-center gap-1">
                    <x-icon name="map-pin" class="w-3.5 h-3.5" /> Selly Laundry — Pusat
                </p>
            </div>
            <a href="{{ route('account') }}" class="relative w-10 h-10 rounded-full bg-white/15 flex items-center justify-center">
                <x-icon name="bell" class="w-5 h-5" />
            </a>
        </div>

        {{-- Search --}}
        <a href="{{ route('catalog') }}" class="mt-4 flex items-center gap-2 bg-white rounded-xl px-4 py-3 text-sm text-selly-muted">
            <x-icon name="search" class="w-5 h-5 text-selly-muted" />
            Cari layanan laundry…
        </a>
    </header>

    <div class="px-5 -mt-4 space-y-6">
        {{-- Promo banners (owner-managed) --}}
        @if($banners->isNotEmpty())
            <div class="flex gap-3 overflow-x-auto no-scrollbar -mx-1 px-1">
                @foreach($banners as $banner)
                    <div class="min-w-[88%] snap-center">
                        <x-promo-banner :banner="$banner" />
                    </div>
                @endforeach
            </div>
        @else
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-selly-accent to-selly-coral text-white p-5 shadow-soft min-h-[120px] flex flex-col justify-center">
                <x-banner-art art="sparkles" />
                <div class="relative z-10">
                    <p class="text-xs font-semibold uppercase tracking-wide opacity-90">Promo Spesial</p>
                    <h3 class="text-lg font-bold mt-1">Gratis Ongkir & Diskon 10%</h3>
                    <p class="text-sm opacity-90">Pakai kode SELLY10 untuk order pertamamu.</p>
                </div>
            </div>
        @endif

        {{-- Categories --}}
        <div>
            <div class="flex items-center justify-between mb-3">
                <h2 class="font-semibold">Kategori Layanan</h2>
                <a href="{{ route('catalog') }}" class="text-selly-primary text-sm">Lihat semua</a>
            </div>
            <div class="grid grid-cols-4 gap-3">
                @foreach($categories as $category)
                    <a href="{{ route('catalog', ['category' => $category->id]) }}" class="flex flex-col items-center gap-1.5">
                        <span class="w-14 h-14 rounded-2xl flex items-center justify-center"
                              style="background-color: {{ $category->color }}1A; color: {{ $category->color }}">
                            <x-icon :name="$category->icon" class="w-7 h-7" />
                        </span>
                        <span class="text-[11px] text-center leading-tight text-selly-text">{{ $category->name }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- CTA --}}
        <a href="{{ route('catalog') }}"
           class="flex items-center justify-center gap-2 w-full bg-selly-primary text-white font-semibold py-3.5 rounded-xl shadow-soft active:scale-[0.99] transition">
            <x-icon name="plus" class="w-5 h-5" /> Jadwalkan Pickup
        </a>

        {{-- Active order --}}
        @if($activeOrder)
            <a href="{{ route('orders.show', $activeOrder) }}" class="relative overflow-hidden block rounded-2xl bg-white p-4 shadow-soft border border-selly-primary-soft">
                <x-illu name="delivery" class="absolute -right-3 -bottom-3 w-24 h-24 opacity-15" />
                <div class="relative z-10">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-semibold">{{ $activeOrder->code }}</span>
                        <x-status-badge :status="$activeOrder->status" />
                    </div>
                    <p class="text-xs text-selly-muted mt-1">{{ $activeOrder->statusLabel() }}</p>
                    <div class="mt-3 flex items-center justify-between">
                        <span class="text-sm font-bold text-selly-primary">{{ rupiah($activeOrder->final_total ?: $activeOrder->estimated_total) }}</span>
                        <span class="text-selly-primary text-sm font-semibold flex items-center gap-1">Lacak <x-icon name="chevron-right" class="w-4 h-4" /></span>
                    </div>
                </div>
            </a>
        @endif

        {{-- Promo vouchers --}}
        @if($vouchers->isNotEmpty())
            <div>
                <h2 class="font-semibold mb-3">Promo Hari Ini</h2>
                <div class="flex gap-3 overflow-x-auto no-scrollbar pb-1">
                    @foreach($vouchers as $v)
                        <div class="min-w-[220px] rounded-2xl bg-white p-4 shadow-soft border border-dashed border-selly-accent">
                            <div class="flex items-center gap-2">
                                <x-icon name="ticket" class="w-5 h-5 text-selly-accent" />
                                <span class="font-bold text-sm">{{ $v->code }}</span>
                            </div>
                            <p class="text-xs text-selly-muted mt-1">
                                @if($v->type==='percent') Diskon {{ $v->value }}%
                                @elseif($v->type==='fixed') Potongan {{ rupiah($v->value) }}
                                @else Gratis ongkir @endif
                                · min. {{ rupiah($v->min_order) }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Popular services --}}
        <div>
            <h2 class="font-semibold mb-3">Layanan Populer</h2>
            <div class="grid grid-cols-2 gap-3">
                @foreach($popular as $service)
                    <a href="{{ route('service.show', $service) }}" class="relative overflow-hidden rounded-2xl bg-white p-3 shadow-soft">
                        <x-illu :name="$service->pricing_type === 'weight' ? 'washer' : 'hanger'" class="absolute -right-2 -bottom-2 w-16 h-16 opacity-10" />
                        <div class="relative z-10">
                            <span class="w-10 h-10 rounded-xl flex items-center justify-center mb-2"
                                  style="background-color: {{ $service->category->color }}1A; color: {{ $service->category->color }}">
                                <x-icon :name="$service->category->icon" class="w-5 h-5" />
                            </span>
                            <p class="text-sm font-semibold leading-tight">{{ $service->name }}</p>
                            <p class="text-xs text-selly-primary font-semibold mt-1">{{ rupiah($service->unit_price) }}/{{ $service->unit_label }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
