<div class="min-h-dvh flex flex-col bg-selly-surface w-full max-w-md mx-auto lg:my-10 lg:min-h-0 lg:rounded-3xl lg:overflow-hidden lg:shadow-card">
    <div class="grad-primary hero-header text-white px-6 pt-10 pb-8 rounded-b-3xl lg:rounded-b-3xl">
        <a href="{{ route('login') }}" class="relative z-10 inline-flex items-center gap-1 text-white/90 text-sm mb-4">
            <x-icon name="arrow-left" class="w-4 h-4" /> Kembali
        </a>
        <h1 class="relative z-10 text-2xl font-extrabold">Daftar Akun</h1>
        <p class="relative z-10 text-white/85 text-sm mt-1">Buat akun untuk mulai memesan laundry.</p>
    </div>

    <div class="px-6 pt-6 pb-10">
        <form wire:submit="register" class="space-y-4">
            <div>
                <label class="text-sm font-medium">Nama Lengkap</label>
                <input type="text" wire:model="name"
                       class="mt-1 w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-selly-primary focus:ring-1 focus:ring-selly-primary outline-none">
                @error('name') <p class="text-selly-danger text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-sm font-medium">No. HP</label>
                <input type="tel" wire:model="phone" placeholder="0812xxxxxxxx"
                       class="mt-1 w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-selly-primary focus:ring-1 focus:ring-selly-primary outline-none">
                @error('phone') <p class="text-selly-danger text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-sm font-medium">Email <span class="text-selly-muted font-normal">(opsional)</span></label>
                <input type="email" wire:model="email"
                       class="mt-1 w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-selly-primary focus:ring-1 focus:ring-selly-primary outline-none">
                @error('email') <p class="text-selly-danger text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-sm font-medium">Kata Sandi</label>
                <input type="password" wire:model="password"
                       class="mt-1 w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-selly-primary focus:ring-1 focus:ring-selly-primary outline-none">
                @error('password') <p class="text-selly-danger text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-sm font-medium">Ulangi Kata Sandi</label>
                <input type="password" wire:model="password_confirmation"
                       class="mt-1 w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-selly-primary focus:ring-1 focus:ring-selly-primary outline-none">
            </div>

            <button type="submit"
                    class="w-full bg-selly-primary text-white font-semibold py-3 rounded-xl active:scale-[0.99] transition shadow-soft"
                    wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="register">Daftar</span>
                <span wire:loading wire:target="register">Memproses…</span>
            </button>
        </form>

        <p class="text-center text-sm text-selly-muted mt-5">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="text-selly-primary font-semibold">Masuk</a>
        </p>
    </div>
</div>
