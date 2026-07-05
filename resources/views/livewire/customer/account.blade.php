<div>
    <header class="bg-selly-primary text-white px-5 pt-6 pb-8 rounded-b-3xl">
        <div class="flex items-center gap-3">
            <span class="w-16 h-16 rounded-full bg-white/15 flex items-center justify-center text-2xl font-bold">
                {{ Str::of($user->name)->substr(0,1)->upper() }}
            </span>
            <div>
                <h1 class="text-xl font-bold">{{ $user->name }}</h1>
                <p class="text-white/80 text-sm">{{ $user->phone }}</p>
            </div>
        </div>
    </header>

    <div class="px-5 -mt-5 space-y-4">
        {{-- Loyalty card --}}
        <div class="rounded-2xl bg-gradient-to-r from-selly-primary-dark to-selly-primary text-white p-4 shadow-soft">
            <div class="flex items-center justify-between">
                <span class="text-sm flex items-center gap-1.5"><x-icon name="sparkles" class="w-4 h-4" /> Poin Loyalty</span>
                <span class="text-xs bg-white/20 px-2 py-0.5 rounded-full">Member</span>
            </div>
            <p class="text-3xl font-bold mt-2">{{ number_format($user->loyalty_balance, 0, ',', '.') }}</p>
            <p class="text-white/80 text-xs mt-1">Kumpulkan poin tiap transaksi & tukar jadi diskon.</p>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 gap-3">
            <div class="rounded-2xl bg-white p-4 shadow-soft text-center">
                <p class="text-2xl font-bold text-selly-primary">{{ $orderCount }}</p>
                <p class="text-xs text-selly-muted">Total Pesanan</p>
            </div>
            <div class="rounded-2xl bg-white p-4 shadow-soft text-center">
                <p class="text-2xl font-bold text-selly-primary">{{ $user->addresses->count() }}</p>
                <p class="text-xs text-selly-muted">Alamat Tersimpan</p>
            </div>
        </div>

        {{-- Addresses --}}
        <div class="rounded-2xl bg-white p-4 shadow-soft">
            <h2 class="font-semibold text-sm mb-3 flex items-center gap-1.5"><x-icon name="map-pin" class="w-4 h-4 text-selly-primary" /> Alamat Tersimpan</h2>
            @forelse($user->addresses as $addr)
                <div class="text-sm py-2 border-b border-gray-50 last:border-0">
                    <p class="font-medium">{{ $addr->label }} · {{ $addr->recipient }} @if($addr->is_default)<span class="text-[10px] bg-selly-primary-soft text-selly-primary-dark px-1.5 py-0.5 rounded-full ml-1">Utama</span>@endif</p>
                    <p class="text-selly-muted text-xs">{{ $addr->full_address }}</p>
                </div>
            @empty
                <p class="text-sm text-selly-muted">Belum ada alamat tersimpan.</p>
            @endforelse
        </div>

        {{-- Menu --}}
        <div class="rounded-2xl bg-white shadow-soft divide-y divide-gray-50">
            <a href="{{ route('orders.index') }}" class="flex items-center justify-between px-4 py-3.5 text-sm">
                <span class="flex items-center gap-2"><x-icon name="list-check" class="w-5 h-5 text-selly-muted" /> Riwayat Pesanan</span>
                <x-icon name="chevron-right" class="w-4 h-4 text-selly-muted" />
            </a>
            <a href="{{ route('promo') }}" class="flex items-center justify-between px-4 py-3.5 text-sm">
                <span class="flex items-center gap-2"><x-icon name="ticket" class="w-5 h-5 text-selly-muted" /> Promo & Voucher</span>
                <x-icon name="chevron-right" class="w-4 h-4 text-selly-muted" />
            </a>
            <button onclick="window.sellyInstall && window.sellyInstall()" class="w-full flex items-center justify-between px-4 py-3.5 text-sm">
                <span class="flex items-center gap-2"><x-icon name="plus" class="w-5 h-5 text-selly-muted" /> Tambah ke Layar Utama</span>
                <x-icon name="chevron-right" class="w-4 h-4 text-selly-muted" />
            </button>
        </div>

        {{-- Logout --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center gap-2 bg-white text-selly-danger font-semibold py-3 rounded-xl shadow-soft">
                <x-icon name="log-out" class="w-5 h-5" /> Keluar
            </button>
        </form>
    </div>
</div>
