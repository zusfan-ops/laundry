<div>
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-xl font-bold">Papan Antrian</h1>
            <p class="text-sm text-selly-muted">Kelola order yang masuk ke outlet.</p>
        </div>
        <button wire:click="$refresh" class="text-sm text-selly-primary font-semibold">Segarkan</button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        @foreach($columns as [$title, $orders])
            <div class="bg-white rounded-2xl p-3 shadow-soft">
                <div class="flex items-center justify-between mb-3 px-1">
                    <h2 class="font-semibold text-sm">{{ $title }}</h2>
                    <span class="text-xs bg-selly-muted/15 text-selly-muted px-2 py-0.5 rounded-full">{{ $orders->count() }}</span>
                </div>
                <div class="space-y-3 min-h-[40px]">
                    @forelse($orders as $order)
                        <div class="rounded-xl border border-gray-100 p-3">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-xs">{{ $order->code }}</span>
                                <x-status-badge :status="$order->status" />
                            </div>
                            <p class="text-xs text-selly-muted mt-1">{{ $order->user->name }} · {{ $order->items->count() }} item</p>
                            <p class="text-sm font-bold text-selly-primary mt-1">{{ rupiah($order->final_total ?: $order->estimated_total) }}</p>

                            <div class="mt-2 flex flex-wrap gap-1.5">
                                @if($order->status === 'placed')
                                    <button wire:click="assignPickup({{ $order->id }})" class="text-xs bg-selly-primary text-white px-2.5 py-1 rounded-lg font-medium">Tugaskan Pickup</button>
                                @elseif($order->status === 'picked_up')
                                    <button wire:click="receiveAtOutlet({{ $order->id }})" class="text-xs bg-selly-primary text-white px-2.5 py-1 rounded-lg font-medium">Terima di Outlet</button>
                                @elseif($order->status === 'at_outlet')
                                    <button wire:click="openWeigh({{ $order->id }})" class="text-xs bg-selly-primary text-white px-2.5 py-1 rounded-lg font-medium flex items-center gap-1"><x-icon name="scale" class="w-3.5 h-3.5"/> Timbang</button>
                                @elseif($order->status === 'awaiting_price_confirm')
                                    <span class="text-xs text-selly-accent font-medium">Menunggu konfirmasi pelanggan</span>
                                @elseif($order->status === 'processing')
                                    <button wire:click="markReady({{ $order->id }})" class="text-xs bg-selly-primary text-white px-2.5 py-1 rounded-lg font-medium">Selesai + QC</button>
                                @elseif($order->status === 'ready')
                                    <button wire:click="assignDelivery({{ $order->id }})" class="text-xs bg-selly-primary text-white px-2.5 py-1 rounded-lg font-medium">Tugaskan Antar</button>
                                @elseif(in_array($order->status, ['assigned_pickup','assigned_delivery','delivering']))
                                    <span class="text-xs text-selly-muted">Menunggu kurir…</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-selly-muted text-center py-4">Kosong</p>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>

    {{-- Weigh modal --}}
    @if($weighOrder)
        <div class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4" wire:key="weigh-modal">
            <div class="bg-white rounded-2xl w-full max-w-md p-5 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="font-bold">Timbang — {{ $weighOrder->code }}</h2>
                    <button wire:click="cancelWeigh"><x-icon name="x" class="w-5 h-5 text-selly-muted" /></button>
                </div>

                <p class="text-xs text-selly-muted mb-3">Estimasi berat: {{ rtrim(rtrim(number_format($weighOrder->estimated_weight,1),'0'),'.') }} kg. Masukkan kuantitas aktual.</p>

                <div class="space-y-3">
                    @foreach($weighOrder->items as $item)
                        <div class="flex items-center justify-between gap-3">
                            <div class="text-sm">
                                <p class="font-medium">{{ $item->service_name }}</p>
                                <p class="text-xs text-selly-muted">{{ rupiah($item->unit_price) }} × {{ rtrim(rtrim(number_format($item->speed_multiplier,2),'0'),'.') }}{{ $item->pricing_type==='weight' ? '/kg' : '/pcs' }}</p>
                            </div>
                            <div class="flex items-center gap-1">
                                <input type="number" step="0.1" min="0" wire:model="weights.{{ $item->id }}"
                                       class="w-20 rounded-lg border border-gray-200 px-2 py-1.5 text-sm text-right">
                                <span class="text-xs text-selly-muted w-7">{{ $item->pricing_type==='weight' ? 'kg' : 'pcs' }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4">
                    <label class="text-sm font-medium flex items-center gap-1.5 mb-1"><x-icon name="camera" class="w-4 h-4" /> Foto timbangan (bukti)</label>
                    <input type="file" wire:model="weighPhoto" accept="image/*" class="text-xs w-full">
                    <div wire:loading wire:target="weighPhoto" class="text-xs text-selly-muted mt-1">Mengunggah…</div>
                </div>

                <button wire:click="saveWeigh" wire:loading.attr="disabled" wire:target="saveWeigh,weighPhoto"
                        class="w-full bg-selly-primary text-white font-semibold py-2.5 rounded-xl mt-4 text-sm">
                    Simpan & Hitung Harga Final
                </button>
            </div>
        </div>
    @endif
</div>
