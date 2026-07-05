<div class="max-w-xl mx-auto">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-xl font-bold">Tugas Hari Ini</h1>
            <p class="text-sm text-selly-muted">{{ $assignments->count() }} tugas aktif · {{ $doneToday }} selesai hari ini</p>
        </div>
        <button wire:click="$refresh" class="text-sm text-selly-primary font-semibold">Segarkan</button>
    </div>

    <div class="space-y-3">
        @forelse($assignments as $a)
            <div class="bg-white rounded-2xl p-4 shadow-soft">
                <div class="flex items-center justify-between">
                    <span class="inline-flex items-center gap-1.5 text-sm font-bold {{ $a->type === 'pickup' ? 'text-selly-primary' : 'text-selly-accent' }}">
                        <x-icon :name="$a->type === 'pickup' ? 'box' : 'truck'" class="w-5 h-5" />
                        {{ $a->type === 'pickup' ? 'JEMPUT' : 'ANTAR' }}
                    </span>
                    <span class="text-xs font-semibold text-selly-muted">{{ $a->order->code }}</span>
                </div>

                <p class="font-semibold mt-2">{{ $a->order->user->name }}</p>
                <p class="text-sm text-selly-muted flex items-start gap-1.5 mt-1">
                    <x-icon name="map-pin" class="w-4 h-4 shrink-0 mt-0.5" /> {{ $a->order->address->full_address }}
                </p>

                <div class="flex items-center gap-2 mt-3">
                    <a href="https://www.google.com/maps/dir/?api=1&destination={{ $a->order->address->lat }},{{ $a->order->address->lng }}" target="_blank"
                       class="flex items-center gap-1.5 text-sm bg-selly-primary-soft text-selly-primary-dark font-semibold px-3 py-2 rounded-xl">
                        <x-icon name="navigation" class="w-4 h-4" /> Navigasi
                    </a>

                    @if($a->status === 'assigned' && $a->type === 'pickup')
                        <button wire:click="depart({{ $a->id }})" class="text-sm bg-selly-primary text-white font-semibold px-3 py-2 rounded-xl">Berangkat</button>
                    @elseif($a->status === 'assigned' && $a->type === 'delivery')
                        <button wire:click="startDelivery({{ $a->id }})" class="text-sm bg-selly-primary text-white font-semibold px-3 py-2 rounded-xl">Berangkat</button>
                    @elseif($a->status === 'on_the_way')
                        <button wire:click="arrive({{ $a->id }})" class="text-sm bg-selly-primary text-white font-semibold px-3 py-2 rounded-xl">Tiba</button>
                    @elseif($a->status === 'arrived')
                        <button wire:click="openProof({{ $a->id }})" class="text-sm bg-selly-success text-white font-semibold px-3 py-2 rounded-xl flex items-center gap-1.5"><x-icon name="camera" class="w-4 h-4"/> Selesai + Foto</button>
                    @endif
                    <span class="ml-auto text-xs text-selly-muted capitalize">{{ str_replace('_',' ',$a->status) }}</span>
                </div>
            </div>
        @empty
            <div class="text-center py-20 text-selly-muted bg-white rounded-2xl shadow-soft">
                <x-icon name="check-circle" class="w-14 h-14 mx-auto mb-3 opacity-40" />
                <p class="text-sm">Tidak ada tugas aktif. Mantap! 🎉</p>
            </div>
        @endforelse
    </div>

    {{-- Proof modal --}}
    @if($proofAssignmentId)
        <div class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl w-full max-w-sm p-5">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="font-bold">Bukti Serah Terima</h2>
                    <button wire:click="cancelProof"><x-icon name="x" class="w-5 h-5 text-selly-muted" /></button>
                </div>
                <input type="file" wire:model="proofPhoto" accept="image/*" capture="environment" class="text-sm w-full mb-2">
                <div wire:loading wire:target="proofPhoto" class="text-xs text-selly-muted mb-2">Mengunggah…</div>
                <button wire:click="complete" wire:loading.attr="disabled" wire:target="complete,proofPhoto"
                        class="w-full bg-selly-success text-white font-semibold py-2.5 rounded-xl text-sm">
                    Konfirmasi Selesai
                </button>
            </div>
        </div>
    @endif
</div>
