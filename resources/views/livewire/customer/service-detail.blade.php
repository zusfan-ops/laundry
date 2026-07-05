<div class="pb-32">
    <header class="bg-selly-primary text-white px-5 pt-6 pb-8 rounded-b-3xl">
        <a href="{{ route('catalog') }}" class="inline-flex"><x-icon name="arrow-left" class="w-6 h-6" /></a>
        <div class="flex items-center gap-3 mt-4">
            <span class="w-14 h-14 rounded-2xl bg-white/15 flex items-center justify-center">
                <x-icon :name="$service->category->icon" class="w-8 h-8" />
            </span>
            <div>
                <h1 class="text-xl font-bold">{{ $service->name }}</h1>
                <p class="text-white/80 text-sm">{{ rupiah($service->unit_price) }}/{{ $service->unit_label }} · ~{{ $service->est_duration_hours }} jam</p>
            </div>
        </div>
    </header>

    <div class="px-5 mt-5 space-y-5">
        <p class="text-sm text-selly-muted">{{ $service->description }}</p>

        {{-- Quantity / weight --}}
        <div class="rounded-2xl bg-white p-4 shadow-soft">
            <p class="font-semibold text-sm mb-1">
                {{ $service->isWeight() ? 'Perkiraan Berat' : 'Jumlah Item' }}
            </p>
            @if($service->isWeight())
                <p class="text-xs text-selly-muted mb-3">Harga final dihitung setelah cucian ditimbang di outlet.</p>
            @endif
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button wire:click="decQty" class="w-10 h-10 rounded-full bg-selly-primary-soft text-selly-primary flex items-center justify-center">
                        <x-icon name="minus" class="w-5 h-5" />
                    </button>
                    <span class="text-lg font-bold w-20 text-center">
                        {{ rtrim(rtrim(number_format($qty, 1), '0'), '.') }} {{ $service->unit_label }}
                    </span>
                    <button wire:click="incQty" class="w-10 h-10 rounded-full bg-selly-primary text-white flex items-center justify-center">
                        <x-icon name="plus" class="w-5 h-5" />
                    </button>
                </div>
            </div>
        </div>

        {{-- Speed tier --}}
        <div>
            <p class="font-semibold text-sm mb-2">Kecepatan</p>
            <div class="grid grid-cols-3 gap-2">
                @foreach($speeds as $s)
                    <button wire:click="$set('speedId', {{ $s->id }})"
                            class="rounded-xl border p-3 text-center text-sm {{ $speedId === $s->id ? 'border-selly-primary bg-selly-primary-soft' : 'border-gray-200 bg-white' }}">
                        <span class="block font-semibold">{{ $s->name }}</span>
                        <span class="text-xs text-selly-muted">x{{ rtrim(rtrim(number_format($s->multiplier,2),'0'),'.') }}</span>
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Perfume --}}
        <div>
            <p class="font-semibold text-sm mb-2">Parfum</p>
            <div class="grid grid-cols-2 gap-2">
                @foreach($perfumes as $p)
                    <button wire:click="$set('perfumeId', {{ $p->id }})"
                            class="rounded-xl border p-3 text-left text-sm {{ $perfumeId === $p->id ? 'border-selly-primary bg-selly-primary-soft' : 'border-gray-200 bg-white' }}">
                        <span class="block font-semibold">{{ $p->name }}</span>
                        <span class="text-xs text-selly-muted">{{ $p->flat_fee > 0 ? '+'.rupiah($p->flat_fee) : 'Gratis' }}</span>
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Sticky add bar --}}
    <div class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-[480px] bg-white p-4 border-t border-gray-100 z-30">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm text-selly-muted">Estimasi</span>
            <span class="text-lg font-bold text-selly-primary">{{ rupiah($this->estimate) }}</span>
        </div>
        <button wire:click="addToCart"
                class="w-full bg-selly-primary text-white font-semibold py-3 rounded-xl active:scale-[0.99] transition">
            Tambah ke Pesanan
        </button>
    </div>
</div>
