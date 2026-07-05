<div>
    <header class="bg-selly-primary text-white px-5 pt-6 pb-4 rounded-b-3xl">
        <h1 class="text-lg font-bold">Pesanan Saya</h1>
        <div class="mt-4 flex gap-2">
            @foreach(['active' => 'Berjalan', 'done' => 'Selesai', 'cancelled' => 'Dibatalkan'] as $key => $label)
                <button wire:click="$set('tab', '{{ $key }}')"
                        class="px-4 py-1.5 rounded-full text-sm font-medium {{ $tab === $key ? 'bg-white text-selly-primary' : 'bg-white/15 text-white' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </header>

    <div class="px-5 mt-4 space-y-3">
        @forelse($orders as $order)
            <a href="{{ route('orders.show', $order) }}" class="block rounded-2xl bg-white p-4 shadow-soft">
                <div class="flex items-center justify-between">
                    <span class="font-semibold text-sm">{{ $order->code }}</span>
                    <x-status-badge :status="$order->status" />
                </div>
                <p class="text-xs text-selly-muted mt-1">{{ $order->created_at->format('d M Y, H:i') }} · {{ $order->items->count() }} item</p>
                <div class="flex items-center justify-between mt-2">
                    <span class="font-bold text-selly-primary">{{ rupiah($order->final_total ?: $order->estimated_total) }}</span>
                    <span class="text-selly-primary text-sm font-semibold flex items-center gap-1">Detail <x-icon name="chevron-right" class="w-4 h-4" /></span>
                </div>
            </a>
        @empty
            <div class="text-center py-16 text-selly-muted">
                <x-illu name="box" class="w-28 h-28 mx-auto mb-3" />
                <p class="text-sm">Belum ada pesanan di sini.</p>
                <a href="{{ route('catalog') }}" class="inline-block mt-4 bg-selly-primary text-white px-5 py-2.5 rounded-xl text-sm font-semibold">Pesan Sekarang</a>
            </div>
        @endforelse
    </div>
</div>
