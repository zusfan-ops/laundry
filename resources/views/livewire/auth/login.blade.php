<div class="min-h-dvh flex flex-col bg-selly-primary">
    {{-- Brand header --}}
    <div class="flex-1 flex flex-col items-center justify-center text-white px-6 py-10">
        <x-logo class="w-20 h-20 mb-4 drop-shadow" />

        <h1 class="text-2xl font-bold">Selly Laundry</h1>
        <p class="text-white/80 text-sm mt-1">Cuci tinggal pesan, kami jemput & antar.</p>
    </div>

    {{-- Form card --}}
    <div class="bg-selly-bg rounded-t-3xl px-6 pt-7 pb-10">
        <h2 class="text-lg font-semibold mb-1">Masuk</h2>
        <p class="text-sm text-selly-muted mb-5">Gunakan nomor HP atau email Anda.</p>

        <form wire:submit="authenticate" class="space-y-4">
            <div>
                <label class="text-sm font-medium">No. HP / Email</label>
                <input type="text" wire:model="login" autofocus
                       class="mt-1 w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-selly-primary focus:ring-1 focus:ring-selly-primary outline-none"
                       placeholder="081200000005 atau email">
                @error('login') <p class="text-selly-danger text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="text-sm font-medium">Kata Sandi</label>
                <input type="password" wire:model="password"
                       class="mt-1 w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-selly-primary focus:ring-1 focus:ring-selly-primary outline-none"
                       placeholder="••••••••">
                @error('password') <p class="text-selly-danger text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <label class="flex items-center gap-2 text-sm text-selly-muted">
                <input type="checkbox" wire:model="remember" class="rounded text-selly-primary focus:ring-selly-primary">
                Ingat saya
            </label>

            <button type="submit"
                    class="w-full bg-selly-primary text-white font-semibold py-3 rounded-xl active:scale-[0.99] transition shadow-soft"
                    wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="authenticate">Masuk</span>
                <span wire:loading wire:target="authenticate">Memproses…</span>
            </button>
        </form>

        <p class="text-center text-sm text-selly-muted mt-5">
            Belum punya akun?
            <a href="{{ route('register') }}" class="text-selly-primary font-semibold">Daftar</a>
        </p>

        <div class="mt-6 rounded-xl bg-selly-primary-soft p-3 text-xs text-selly-primary-dark">
            <p class="font-semibold mb-1">Akun demo (kata sandi: <code>password</code>)</p>
            <p>Pelanggan: rina@selly.test · Operator: operator@selly.test</p>
            <p>Kurir: kurir@selly.test · Owner: owner@selly.test</p>
        </div>
    </div>
</div>
