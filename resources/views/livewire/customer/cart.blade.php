<div class="pb-40">
    <header class="bg-selly-primary text-white px-5 pt-6 pb-5 rounded-b-3xl">
        <div class="flex items-center gap-3">
            <a href="{{ route('catalog') }}"><x-icon name="arrow-left" class="w-6 h-6" /></a>
            <h1 class="text-lg font-bold">Keranjang</h1>
        </div>
    </header>

    @if(count($items) === 0)
        <div class="text-center py-16 px-6 text-selly-muted">
            <x-illu name="basket" class="w-28 h-28 mx-auto mb-3" />
            <p class="font-semibold text-selly-text">Keranjang kosong</p>
            <p class="text-sm mt-1">Yuk pilih layanan laundry favoritmu.</p>
            <a href="{{ route('catalog') }}" class="inline-block mt-4 bg-selly-primary text-white px-5 py-2.5 rounded-xl text-sm font-semibold">Pilih Layanan</a>
        </div>
    @else
        <div class="px-5 mt-4 space-y-3">
            @foreach($items as $item)
                <div class="rounded-2xl bg-white p-3.5 shadow-soft flex gap-3">
                    <span class="w-11 h-11 rounded-xl bg-selly-primary-soft text-selly-primary flex items-center justify-center shrink-0">
                        <x-icon :name="$item['icon']" class="w-6 h-6" />
                    </span>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <p class="font-semibold text-sm">{{ $item['service_name'] }}</p>
                            <button wire:click="remove('{{ $item['row_id'] }}')" class="text-selly-muted">
                                <x-icon name="x" class="w-4 h-4" />
                            </button>
                        </div>
                        <p class="text-xs text-selly-muted">
                            {{ rtrim(rtrim(number_format($item['qty'],1),'0'),'.') }} {{ $item['unit_label'] }} ·
                            {{ $item['speed_name'] }}@if($item['perfume_name'] && $item['perfume_fee'] > 0) · {{ $item['perfume_name'] }} @endif
                        </p>
                        <p class="text-sm font-bold text-selly-primary mt-1">{{ rupiah($item['line_total']) }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="px-5 mt-5">
            <div class="rounded-2xl bg-selly-primary-soft p-4 text-sm text-selly-primary-dark flex items-start gap-2">
                <x-icon name="scale" class="w-5 h-5 shrink-0" />
                <span>Total final dihitung setelah cucian ditimbang di outlet. Estimasi berat: <b>{{ rtrim(rtrim(number_format($estimatedWeight,1),'0'),'.') }} kg</b>.</span>
            </div>
        </div>

        <div class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-[480px] bg-white p-4 border-t border-gray-100 z-30">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm text-selly-muted">Subtotal estimasi</span>
                <span class="text-lg font-bold text-selly-primary">{{ rupiah($subtotal) }}</span>
            </div>
            <a href="{{ route('checkout') }}"
               class="block text-center w-full bg-selly-primary text-white font-semibold py-3 rounded-xl active:scale-[0.99] transition">
                Lanjut Atur Jadwal
            </a>
        </div>
    @endif
</div>
