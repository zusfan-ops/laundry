<div>
    @if($count > 0)
        <a href="{{ route('cart') }}"
           class="fixed bottom-24 left-1/2 -translate-x-1/2 w-[90%] max-w-[432px] z-30 flex items-center justify-between bg-selly-primary text-white px-4 py-3 rounded-xl shadow-lg active:scale-[0.99] transition">
            <span class="flex items-center gap-2 text-sm font-medium">
                <span class="bg-white/25 w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold">{{ $count }}</span>
                Lihat Keranjang
            </span>
            <span class="font-bold">{{ rupiah($subtotal) }}</span>
        </a>
    @endif
</div>
