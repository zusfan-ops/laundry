@php
    $current = request()->route()?->getName();
    $is = fn ($names) => in_array($current, (array) $names, true);
    $items = [
        ['home', 'Beranda', 'home'],
        ['catalog', 'Pesan Laundry', 'plus'],
        ['orders.index', 'Pesanan Saya', 'list-check'],
        ['promo', 'Promo & Voucher', 'ticket'],
        ['account', 'Akun', 'user'],
    ];
@endphp

{{-- Desktop sidebar (hidden on mobile; bottom-nav takes over there) --}}
<aside class="hidden lg:flex lg:flex-col lg:w-64 lg:shrink-0 lg:sticky lg:top-6 lg:self-start">
    <div class="bg-white rounded-3xl shadow-soft p-5">
        <a href="{{ route('home') }}" class="flex items-center gap-2.5 mb-6">
            <x-logo class="w-11 h-11" />
            <div>
                <p class="font-extrabold leading-none text-lg">Selly</p>
                <p class="text-[11px] text-selly-muted">Laundry</p>
            </div>
        </a>

        <nav class="space-y-1">
            @foreach($items as [$route, $label, $icon])
                @php $active = $is($route) || ($route==='orders.index' && $is('orders.show')); @endphp
                <a href="{{ route($route) }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition
                          {{ $active ? 'grad-primary text-white shadow-soft' : 'text-selly-muted hover:bg-selly-primary-soft hover:text-selly-primary-dark' }}">
                    <x-icon :name="$icon" class="w-5 h-5" />
                    {{ $label }}
                </a>
            @endforeach
        </nav>
    </div>

    <div class="bg-white rounded-3xl shadow-soft p-4 mt-4">
        <div class="flex items-center gap-3">
            <span class="w-10 h-10 rounded-full grad-violet text-white flex items-center justify-center font-bold">
                {{ \Illuminate\Support\Str::of(auth()->user()->name)->substr(0,1)->upper() }}
            </span>
            <div class="min-w-0">
                <p class="text-sm font-semibold truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs text-selly-muted truncate">{{ auth()->user()->phone }}</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="mt-3">
            @csrf
            <button class="w-full flex items-center justify-center gap-2 text-sm font-semibold text-selly-danger bg-selly-secondary-soft rounded-xl py-2">
                <x-icon name="log-out" class="w-4 h-4" /> Keluar
            </button>
        </form>
    </div>
</aside>
