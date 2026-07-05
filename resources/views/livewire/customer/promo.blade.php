<div>
    <header class="bg-selly-primary text-white px-5 pt-6 pb-5 rounded-b-3xl">
        <h1 class="text-lg font-bold">Promo & Voucher</h1>
        <p class="text-white/80 text-sm">Hemat lebih banyak dengan voucher Selly.</p>
    </header>

    <div class="px-5 mt-4 space-y-3">
        @forelse($vouchers as $v)
            <div class="rounded-2xl bg-white shadow-soft overflow-hidden flex">
                <div class="w-20 bg-gradient-to-b from-selly-accent to-selly-coral text-white flex flex-col items-center justify-center shrink-0">
                    <x-icon name="ticket" class="w-7 h-7" />
                </div>
                <div class="p-4 flex-1">
                    <p class="font-bold">{{ $v->code }}</p>
                    <p class="text-sm text-selly-text">
                        @if($v->type==='percent') Diskon {{ $v->value }}%@if($v->max_discount) (maks {{ rupiah($v->max_discount) }})@endif
                        @elseif($v->type==='fixed') Potongan {{ rupiah($v->value) }}
                        @else Gratis ongkir @endif
                    </p>
                    <p class="text-xs text-selly-muted mt-1">Min. order {{ rupiah($v->min_order) }} · s/d {{ $v->ends_at?->format('d M Y') ?? 'tak terbatas' }}</p>
                </div>
            </div>
        @empty
            <div class="text-center py-16 text-selly-muted">
                <x-illu name="coin" class="w-28 h-28 mx-auto mb-3" />
                <p class="text-sm">Belum ada promo aktif.</p>
            </div>
        @endforelse
    </div>
</div>
