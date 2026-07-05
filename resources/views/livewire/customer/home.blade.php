<div>
    @php $grads = ['grad-primary','grad-ocean','grad-violet','grad-sunset','grad-mint','grad-primary']; @endphp

    {{-- Header --}}
    <header class="hero-header grad-primary text-white px-5 pt-6 pb-10 rounded-b-[28px] lg:rounded-b-3xl">
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <p class="text-white/80 text-sm">Halo,</p>
                <h1 class="text-xl lg:text-2xl font-extrabold">{{ auth()->user()->name }} 👋</h1>
                <p class="text-white/80 text-xs mt-1 flex items-center gap-1">
                    <x-icon name="map-pin" class="w-3.5 h-3.5" /> Selly Laundry — Pusat
                </p>
            </div>
            <a href="{{ route('account') }}" class="relative w-11 h-11 rounded-full bg-white/15 backdrop-blur flex items-center justify-center hover:bg-white/25 transition">
                <x-icon name="bell" class="w-5 h-5" />
                <span class="absolute top-2 right-2.5 w-2 h-2 rounded-full bg-selly-accent ring-2 ring-selly-primary"></span>
            </a>
        </div>

        {{-- Search --}}
        <a href="{{ route('catalog') }}" class="relative z-10 mt-5 flex items-center gap-2 bg-white rounded-2xl px-4 py-3 text-sm text-selly-muted shadow-soft">
            <x-icon name="search" class="w-5 h-5 text-selly-primary" />
            Cari layanan laundry…
        </a>
    </header>

    <div class="px-5 -mt-5 space-y-6 lg:px-7 lg:pb-8">
        {{-- Promo banners (owner-managed) --}}
        @if($banners->isNotEmpty())
            <div class="flex gap-3 overflow-x-auto no-scrollbar -mx-1 px-1 lg:grid lg:grid-cols-2 lg:overflow-visible lg:mx-0 lg:px-0">
                @foreach($banners as $banner)
                    <div class="min-w-[88%] snap-center lg:min-w-0">
                        <x-promo-banner :banner="$banner" />
                    </div>
                @endforeach
            </div>
        @else
            <div class="relative overflow-hidden rounded-2xl grad-sunset text-white p-5 shadow-card min-h-[120px] flex flex-col justify-center">
                <x-banner-art art="sparkles" />
                <div class="relative z-10">
                    <p class="text-xs font-semibold uppercase tracking-wide opacity-90">Promo Spesial</p>
                    <h3 class="text-lg font-bold mt-1">Gratis Ongkir & Diskon 10%</h3>
                    <p class="text-sm opacity-90">Pakai kode SELLY10 untuk order pertamamu.</p>
                </div>
            </div>
        @endif

        {{-- Quick highlights --}}
        <div class="grid grid-cols-3 gap-3">
            @foreach([['truck','Pickup & Antar','grad-ocean'],['scale','Harga Transparan','grad-mint'],['zap','Express 1 Hari','grad-violet']] as [$ic,$t,$g])
                <div class="rounded-2xl bg-white p-3 shadow-soft flex flex-col items-center text-center gap-1.5">
                    <span class="w-10 h-10 rounded-xl {{ $g }} text-white flex items-center justify-center"><x-icon :name="$ic" class="w-5 h-5" /></span>
                    <span class="text-[11px] font-medium leading-tight">{{ $t }}</span>
                </div>
            @endforeach
        </div>

        {{-- Categories --}}
        <div>
            <div class="flex items-center justify-between mb-3">
                <h2 class="font-bold text-lg">Kategori Layanan</h2>
                <a href="{{ route('catalog') }}" class="text-selly-primary text-sm font-semibold">Lihat semua</a>
            </div>
            <div class="grid grid-cols-4 sm:grid-cols-6 gap-3">
                @foreach($categories as $category)
                    <a href="{{ route('catalog', ['category' => $category->id]) }}" class="flex flex-col items-center gap-1.5 group">
                        <span class="w-14 h-14 lg:w-16 lg:h-16 rounded-2xl {{ $grads[$loop->index % count($grads)] }} text-white flex items-center justify-center shadow-soft group-hover:scale-105 transition">
                            <x-icon :name="$category->icon" class="w-7 h-7" />
                        </span>
                        <span class="text-[11px] text-center leading-tight text-selly-text font-medium">{{ $category->name }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- CTA --}}
        <a href="{{ route('catalog') }}"
           class="flex items-center justify-center gap-2 w-full grad-primary text-white font-bold py-3.5 rounded-2xl shadow-card active:scale-[0.99] transition">
            <x-icon name="plus" class="w-5 h-5" /> Jadwalkan Pickup Sekarang
        </a>

        {{-- Active order --}}
        @if($activeOrder)
            <a href="{{ route('orders.show', $activeOrder) }}" class="relative overflow-hidden block rounded-2xl bg-white p-4 shadow-soft border-2 border-selly-primary-soft card-hover">
                <x-illu name="delivery" class="absolute -right-3 -bottom-3 w-24 h-24 opacity-15" />
                <div class="relative z-10">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-bold">{{ $activeOrder->code }}</span>
                        <x-status-badge :status="$activeOrder->status" />
                    </div>
                    <p class="text-xs text-selly-muted mt-1">{{ $activeOrder->statusLabel() }}</p>
                    <div class="mt-3 flex items-center justify-between">
                        <span class="text-base font-extrabold text-selly-primary">{{ rupiah($activeOrder->final_total ?: $activeOrder->estimated_total) }}</span>
                        <span class="text-selly-primary text-sm font-semibold flex items-center gap-1">Lacak <x-icon name="chevron-right" class="w-4 h-4" /></span>
                    </div>
                </div>
            </a>
        @endif

        {{-- Promo vouchers --}}
        @if($vouchers->isNotEmpty())
            <div>
                <h2 class="font-bold text-lg mb-3">Promo Hari Ini</h2>
                <div class="flex gap-3 overflow-x-auto no-scrollbar pb-1 lg:grid lg:grid-cols-3 lg:overflow-visible">
                    @foreach($vouchers as $v)
                        <div class="min-w-[230px] lg:min-w-0 rounded-2xl bg-white p-4 shadow-soft border border-dashed border-selly-accent card-hover">
                            <div class="flex items-center gap-2">
                                <span class="w-8 h-8 rounded-lg grad-sunset text-white flex items-center justify-center"><x-icon name="ticket" class="w-4 h-4" /></span>
                                <span class="font-extrabold text-sm">{{ $v->code }}</span>
                            </div>
                            <p class="text-xs text-selly-muted mt-2">
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
            <h2 class="font-bold text-lg mb-3">Layanan Populer</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                @foreach($popular as $service)
                    <a href="{{ route('service.show', $service) }}" class="relative overflow-hidden rounded-2xl bg-white p-3.5 shadow-soft card-hover">
                        <x-illu :name="$service->pricing_type === 'weight' ? 'washer' : 'hanger'" class="absolute -right-2 -bottom-2 w-16 h-16 opacity-10" />
                        <div class="relative z-10">
                            <span class="w-11 h-11 rounded-xl {{ $grads[$loop->index % count($grads)] }} text-white flex items-center justify-center mb-2">
                                <x-icon :name="$service->category->icon" class="w-5 h-5" />
                            </span>
                            <p class="text-sm font-bold leading-tight">{{ $service->name }}</p>
                            <p class="text-xs text-selly-primary font-extrabold mt-1">{{ rupiah($service->unit_price) }}/{{ $service->unit_label }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
