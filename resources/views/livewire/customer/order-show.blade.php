<div class="pb-10">
    @php
        $rankMap = ['pending_payment'=>0,'placed'=>1,'assigned_pickup'=>1,'picked_up'=>2,'at_outlet'=>2,
            'weighed'=>3,'awaiting_price_confirm'=>3,'processing'=>4,'ready'=>5,'assigned_delivery'=>5,
            'delivering'=>6,'completed'=>7,'cancelled'=>-1];
        $rank = $rankMap[$order->status] ?? 0;
        $milestones = [
            ['Pesanan dibuat', 1, 'receipt'],
            ['Cucian dijemput', 2, 'truck'],
            ['Ditimbang', 3, 'scale'],
            ['Diproses', 4, 'washing-machine'],
            ['Diantar', 6, 'navigation'],
            ['Selesai', 7, 'check-circle'],
        ];
    @endphp

    <header class="bg-selly-primary text-white px-5 pt-6 pb-6 rounded-b-3xl">
        <div class="flex items-center gap-3">
            <a href="{{ route('orders.index') }}"><x-icon name="arrow-left" class="w-6 h-6" /></a>
            <div>
                <h1 class="text-lg font-bold">{{ $order->code }}</h1>
                <p class="text-white/80 text-xs">{{ $order->created_at->format('d M Y, H:i') }}</p>
            </div>
        </div>
        <div class="mt-3"><x-status-badge :status="$order->status" /></div>
    </header>

    <div class="px-5 mt-4 space-y-4">

        {{-- Price confirmation banner --}}
        @if($order->status === 'awaiting_price_confirm')
            <div class="rounded-2xl bg-orange-50 border border-orange-200 p-4">
                <p class="font-semibold text-orange-700 flex items-center gap-1.5"><x-icon name="scale" class="w-5 h-5" /> Konfirmasi Harga</p>
                <p class="text-sm text-orange-700/90 mt-1">Berat aktual <b>{{ rtrim(rtrim(number_format($order->actual_weight,1),'0'),'.') }} kg</b> (estimasi {{ rtrim(rtrim(number_format($order->estimated_weight,1),'0'),'.') }} kg). Harga final menjadi <b>{{ rupiah($order->final_total) }}</b>.</p>
                @php $photo = $order->statusLogs->whereNotNull('photo_path')->last(); @endphp
                @if($photo)
                    <img src="{{ Storage::url($photo->photo_path) }}" class="mt-3 rounded-xl w-full max-h-48 object-cover" alt="Foto timbangan">
                @endif
                <div class="flex gap-2 mt-3">
                    <button wire:click="confirmPrice" class="flex-1 bg-selly-success text-white font-semibold py-2.5 rounded-xl text-sm">Setuju</button>
                    <button wire:click="rejectPrice" wire:confirm="Batalkan pesanan dan tolak harga?" class="flex-1 bg-white border border-selly-danger text-selly-danger font-semibold py-2.5 rounded-xl text-sm">Tolak</button>
                </div>
            </div>
        @endif

        {{-- Payment banner --}}
        @if($order->payment_status !== 'paid' && in_array($order->status, ['pending_payment','weighed','processing','ready']) && $order->status !== 'cancelled')
            <div class="rounded-2xl bg-white p-4 shadow-soft flex items-center justify-between">
                <div>
                    <p class="text-sm text-selly-muted">Tagihan {{ $order->final_total ? 'final' : 'estimasi' }}</p>
                    <p class="text-lg font-bold text-selly-primary">{{ rupiah($order->final_total ?: $order->estimated_total) }}</p>
                </div>
                <button wire:click="payNow" class="bg-selly-primary text-white font-semibold px-5 py-2.5 rounded-xl text-sm">Bayar Sekarang</button>
            </div>
        @endif

        {{-- Timeline --}}
        @if($order->status !== 'cancelled')
            <div class="rounded-2xl bg-white p-4 shadow-soft">
                <h2 class="font-semibold text-sm mb-3">Status Pesanan</h2>
                <div class="space-y-0">
                    @foreach($milestones as $i => [$label, $threshold, $icon])
                        @php $done = $rank >= $threshold; $active = ($rank === $threshold) || ($i>0 && $rank >= $milestones[$i-1][1] && $rank < $threshold); @endphp
                        <div class="flex gap-3">
                            <div class="flex flex-col items-center">
                                <span class="w-9 h-9 rounded-full flex items-center justify-center {{ $done ? 'bg-selly-primary text-white' : 'bg-gray-100 text-gray-400' }}">
                                    <x-icon :name="$done ? 'check' : $icon" class="w-4 h-4" />
                                </span>
                                @if(!$loop->last)<span class="w-0.5 flex-1 min-h-6 {{ $rank > $threshold ? 'bg-selly-primary' : 'bg-gray-200' }}"></span>@endif
                            </div>
                            <div class="pb-5 pt-1.5">
                                <p class="text-sm font-medium {{ $done ? 'text-selly-text' : 'text-selly-muted' }}">{{ $label }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Items --}}
        <div class="rounded-2xl bg-white p-4 shadow-soft">
            <h2 class="font-semibold text-sm mb-3">Rincian Pesanan</h2>
            <div class="space-y-2.5">
                @foreach($order->items as $item)
                    <div class="flex justify-between text-sm">
                        <div>
                            <p class="font-medium">{{ $item->service_name }}</p>
                            <p class="text-xs text-selly-muted">
                                @if($item->actual_qty)Aktual {{ rtrim(rtrim(number_format($item->actual_qty,1),'0'),'.') }}@else Est. {{ rtrim(rtrim(number_format($item->estimated_qty,1),'0'),'.') }}@endif
                                · {{ $item->speed_name }}
                            </p>
                        </div>
                        <span class="font-semibold">{{ rupiah($item->line_total) }}</span>
                    </div>
                @endforeach
            </div>
            <div class="border-t border-gray-100 mt-3 pt-3 space-y-1 text-sm">
                <div class="flex justify-between text-selly-muted"><span>Subtotal {{ $order->final_subtotal ? '(final)' : '(estimasi)' }}</span><span>{{ rupiah($order->final_subtotal ?: $order->estimated_subtotal) }}</span></div>
                <div class="flex justify-between text-selly-muted"><span>Ongkir</span><span>{{ $order->shipping_fee === 0 ? 'Gratis' : rupiah($order->shipping_fee) }}</span></div>
                @if($order->discount_amount > 0)<div class="flex justify-between text-selly-success"><span>Diskon</span><span>−{{ rupiah($order->discount_amount) }}</span></div>@endif
                <div class="flex justify-between font-bold pt-1"><span>Total {{ $order->final_total ? 'final' : 'estimasi' }}</span><span class="text-selly-primary">{{ rupiah($order->final_total ?: $order->estimated_total) }}</span></div>
            </div>
        </div>

        {{-- Address & schedule --}}
        <div class="rounded-2xl bg-white p-4 shadow-soft text-sm space-y-2">
            <div class="flex gap-2"><x-icon name="map-pin" class="w-4 h-4 text-selly-primary shrink-0 mt-0.5" /><span>{{ $order->address->full_address }}</span></div>
            <div class="flex gap-2"><x-icon name="clock" class="w-4 h-4 text-selly-primary shrink-0 mt-0.5" /><span>Pickup {{ $order->pickup_date?->format('d M') }} {{ $order->pickupSlot?->label() }} · Antar {{ $order->delivery_date?->format('d M') }} {{ $order->deliverySlot?->label() }}</span></div>
        </div>

        {{-- Rating --}}
        @if($order->status === 'completed')
            <div class="rounded-2xl bg-white p-4 shadow-soft">
                <h2 class="font-semibold text-sm mb-2">Beri Rating</h2>
                <div class="flex gap-1 mb-3" x-data>
                    @for($i=1; $i<=5; $i++)
                        <button wire:click="$set('rating', {{ $i }})">
                            <x-icon name="star" class="w-8 h-8 {{ $rating >= $i ? 'text-selly-accent fill-selly-accent' : 'text-gray-300' }}" style="{{ $rating >= $i ? 'fill: currentColor' : '' }}" />
                        </button>
                    @endfor
                </div>
                <textarea wire:model="review" rows="2" placeholder="Tulis ulasan (opsional)" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm mb-2"></textarea>
                <button wire:click="submitRating" class="w-full bg-selly-primary text-white font-semibold py-2.5 rounded-xl text-sm">Kirim Rating</button>
            </div>
        @endif
    </div>
</div>
