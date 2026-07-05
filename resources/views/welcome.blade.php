<x-layouts.guest title="Selly Laundry — Cuci tinggal pesan">
    <div class="min-h-dvh bg-selly-bg">
        {{-- Hero --}}
        <div class="bg-selly-primary text-white px-7 pt-10 pb-7 rounded-b-3xl text-center relative overflow-hidden">
            <x-illu name="bubbles" class="absolute -left-6 top-6 w-28 h-28 opacity-20" />
            <x-illu name="sparkle" class="absolute right-2 top-2 w-16 h-16 opacity-25" />
            <div class="relative z-10 flex flex-col items-center">
                <x-logo class="w-20 h-20 mb-4 drop-shadow" />
                <h1 class="text-3xl font-bold">Selly Laundry</h1>
                <p class="text-white/85 mt-2 max-w-xs">Cuci, jemput, dan antar dalam satu aplikasi. Harga transparan, tracking real-time.</p>
            </div>
        </div>

        <div class="px-5 py-6 space-y-8">
            {{-- Stats strip --}}
            <div class="grid grid-cols-3 gap-3 -mt-12 relative z-10">
                <div class="bg-white rounded-2xl py-3 shadow-soft text-center">
                    <p class="text-xl font-bold text-selly-primary">{{ $stats['services'] }}+</p>
                    <p class="text-[11px] text-selly-muted">Layanan</p>
                </div>
                <div class="bg-white rounded-2xl py-3 shadow-soft text-center">
                    <p class="text-xl font-bold text-selly-primary">{{ $stats['outlets'] }}</p>
                    <p class="text-[11px] text-selly-muted">Cabang</p>
                </div>
                <div class="bg-white rounded-2xl py-3 shadow-soft text-center">
                    <p class="text-xl font-bold text-selly-primary">{{ $stats['customers'] }}+</p>
                    <p class="text-[11px] text-selly-muted">Pelanggan</p>
                </div>
            </div>

            {{-- Promo banners (owner-managed) --}}
            @if($banners->isNotEmpty())
                <div class="flex gap-3 overflow-x-auto no-scrollbar -mx-1 px-1">
                    @foreach($banners as $banner)
                        <div class="min-w-[85%]"><x-promo-banner :banner="$banner" /></div>
                    @endforeach
                </div>
            @endif

            {{-- How it works --}}
            <section>
                <h2 class="font-bold text-lg mb-3">Cara Kerja</h2>
                <div class="grid grid-cols-2 gap-3">
                    @foreach([
                        ['box', '1. Pesan', 'Pilih layanan & jadwalkan pickup.'],
                        ['delivery', '2. Dijemput', 'Kurir menjemput cucian ke alamatmu.'],
                        ['washer', '3. Ditimbang & Dicuci', 'Harga final transparan setelah ditimbang.'],
                        ['sparkle', '4. Diantar', 'Cucian bersih wangi diantar kembali.'],
                    ] as [$art, $title, $desc])
                        <div class="bg-white rounded-2xl p-4 shadow-soft relative overflow-hidden">
                            <x-illu :name="$art" class="absolute -right-3 -bottom-3 w-16 h-16 opacity-10" />
                            <div class="relative z-10">
                                <x-illu :name="$art" class="w-9 h-9 mb-2" />
                                <p class="font-semibold text-sm">{{ $title }}</p>
                                <p class="text-xs text-selly-muted mt-0.5">{{ $desc }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- Branch locations + map --}}
            @if($outlets->isNotEmpty())
                <section>
                    <h2 class="font-bold text-lg mb-3 flex items-center gap-1.5"><x-icon name="map-pin" class="w-5 h-5 text-selly-primary" /> Lokasi Cabang</h2>
                    <div class="space-y-4">
                        @foreach($outlets as $outlet)
                            <div class="bg-white rounded-2xl shadow-soft overflow-hidden">
                                @if($outlet->mapEmbedUrl())
                                    <iframe class="w-full h-48 border-0" loading="lazy" title="Peta {{ $outlet->name }}" src="{{ $outlet->mapEmbedUrl() }}"></iframe>
                                @endif
                                <div class="p-4">
                                    <p class="font-semibold">{{ $outlet->name }}</p>
                                    @if($outlet->address)<p class="text-sm text-selly-muted mt-0.5 flex items-start gap-1.5"><x-icon name="map-pin" class="w-4 h-4 shrink-0 mt-0.5"/> {{ $outlet->address }}</p>@endif
                                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-2 text-xs text-selly-muted">
                                        @if($outlet->phone)<span class="flex items-center gap-1"><x-icon name="phone" class="w-3.5 h-3.5"/> {{ $outlet->phone }}</span>@endif
                                        @if($outlet->opening_hours)<span class="flex items-center gap-1"><x-icon name="clock" class="w-3.5 h-3.5"/> {{ $outlet->opening_hours }}</span>@endif
                                    </div>
                                    @if($outlet->directionsUrl())
                                        <a href="{{ $outlet->directionsUrl() }}" target="_blank"
                                           class="inline-flex items-center gap-1.5 mt-3 bg-selly-primary-soft text-selly-primary-dark text-sm font-semibold px-3.5 py-2 rounded-xl">
                                            <x-icon name="navigation" class="w-4 h-4" /> Buka Rute
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- FAQ --}}
            @if($faqs->isNotEmpty())
                <section>
                    <h2 class="font-bold text-lg mb-3">Pertanyaan Umum</h2>
                    <div class="space-y-2" x-data="{ open: null }">
                        @foreach($faqs as $i => $faq)
                            <div class="bg-white rounded-2xl shadow-soft overflow-hidden">
                                <button type="button" @click="open === {{ $i }} ? open = null : open = {{ $i }}"
                                        class="w-full flex items-center justify-between gap-3 px-4 py-3.5 text-left">
                                    <span class="font-semibold text-sm">{{ $faq->question }}</span>
                                    <x-icon name="chevron-right" class="w-4 h-4 text-selly-muted transition-transform shrink-0" x-bind:class="open === {{ $i }} ? 'rotate-90' : ''" />
                                </button>
                                <div x-show="open === {{ $i }}" x-collapse class="px-4 pb-4 -mt-1">
                                    <p class="text-sm text-selly-muted">{{ $faq->answer }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>

        {{-- CTA --}}
        <div class="sticky bottom-0 bg-white/95 backdrop-blur border-t border-gray-100 px-6 py-4">
            <a href="{{ route('register') }}" class="block text-center w-full bg-selly-primary text-white font-semibold py-3.5 rounded-xl shadow-soft active:scale-[0.99] transition">
                Mulai Sekarang
            </a>
            <a href="{{ route('login') }}" class="block text-center w-full mt-2 text-selly-primary font-semibold text-sm">
                Sudah punya akun? Masuk
            </a>
        </div>
    </div>
</x-layouts.guest>
