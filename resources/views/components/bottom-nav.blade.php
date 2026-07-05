@php
    $current = request()->route()?->getName();
    $is = fn ($names) => in_array($current, (array) $names, true);
@endphp

<nav class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-[480px] bg-white z-40"
     style="box-shadow: var(--shadow-nav);">
    <div class="grid grid-cols-5 items-end px-2 pt-2 pb-[max(0.5rem,env(safe-area-inset-bottom))] relative">
        <a href="{{ route('home') }}" class="flex flex-col items-center gap-1 py-1 {{ $is('home') ? 'text-selly-primary' : 'text-selly-muted' }}">
            <x-icon name="home" class="w-6 h-6" />
            <span class="text-[11px] font-medium">Beranda</span>
        </a>
        <a href="{{ route('orders.index') }}" class="flex flex-col items-center gap-1 py-1 {{ $is(['orders.index','orders.show']) ? 'text-selly-primary' : 'text-selly-muted' }}">
            <x-icon name="list-check" class="w-6 h-6" />
            <span class="text-[11px] font-medium">Pesanan</span>
        </a>

        {{-- Center FAB --}}
        <div class="flex justify-center">
            <a href="{{ route('catalog') }}"
               class="absolute -top-5 flex items-center justify-center w-14 h-14 rounded-full bg-selly-primary text-white shadow-lg ring-4 ring-white active:scale-95 transition">
                <x-icon name="plus" class="w-7 h-7" />
            </a>
            <span class="text-[11px] font-medium text-selly-muted mt-9">Pesan</span>
        </div>

        <a href="{{ route('promo') }}" class="flex flex-col items-center gap-1 py-1 {{ $is('promo') ? 'text-selly-primary' : 'text-selly-muted' }}">
            <x-icon name="ticket" class="w-6 h-6" />
            <span class="text-[11px] font-medium">Promo</span>
        </a>
        <a href="{{ route('account') }}" class="flex flex-col items-center gap-1 py-1 {{ $is('account') ? 'text-selly-primary' : 'text-selly-muted' }}">
            <x-icon name="user" class="w-6 h-6" />
            <span class="text-[11px] font-medium">Akun</span>
        </a>
    </div>
</nav>
