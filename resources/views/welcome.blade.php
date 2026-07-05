<x-layouts.guest title="Selly Laundry — Laundry Pickup & Delivery">
    {{-- Top navbar --}}
    <header class="sticky top-0 z-40 bg-white/80 backdrop-blur border-b border-slate-100">
        <div class="max-w-6xl mx-auto px-5 h-16 flex items-center justify-between">
            <a href="{{ route('welcome') }}" class="flex items-center gap-2.5">
                <x-logo class="w-10 h-10" />
                <div class="leading-none">
                    <p class="font-extrabold text-lg">Selly<span class="text-selly-primary">Laundry</span></p>
                </div>
            </a>
            <nav class="hidden md:flex items-center gap-7 text-sm font-medium text-selly-muted">
                <a href="#cara-kerja" class="hover:text-selly-primary transition">Cara Kerja</a>
                <a href="#cabang" class="hover:text-selly-primary transition">Cabang</a>
                <a href="#faq" class="hover:text-selly-primary transition">FAQ</a>
            </nav>
            <div class="flex items-center gap-2">
                <a href="{{ route('login') }}" class="hidden sm:inline text-sm font-semibold text-selly-primary px-4 py-2 rounded-xl hover:bg-selly-primary-soft transition">Masuk</a>
                <a href="{{ route('register') }}" class="text-sm font-bold text-white grad-primary px-4 py-2 rounded-xl shadow-soft hover:opacity-95 transition">Daftar</a>
            </div>
        </div>
    </header>

    {{-- Hero --}}
    <section class="relative overflow-hidden">
        <div class="max-w-6xl mx-auto px-5 pt-10 pb-14 lg:pt-16 lg:pb-20 grid lg:grid-cols-2 gap-10 items-center">
            <div class="text-center lg:text-left">
                <span class="inline-flex items-center gap-1.5 bg-selly-accent-soft text-selly-warning text-xs font-bold px-3 py-1.5 rounded-full">
                    <x-icon name="sparkles" class="w-3.5 h-3.5" /> #1 Laundry Antar-Jemput
                </span>
                <h1 class="mt-4 text-4xl lg:text-5xl font-extrabold leading-tight">
                    Cuci Tinggal <span class="text-grad">Pesan</span>,<br class="hidden lg:block"> Kami Jemput &amp; Antar.
                </h1>
                <p class="mt-4 text-selly-muted text-base lg:text-lg max-w-lg mx-auto lg:mx-0">
                    Layanan laundry profesional dengan harga transparan, penjemputan terjadwal, dan pelacakan pesanan real-time — langsung dari genggaman.
                </p>
                <div class="mt-7 flex flex-col sm:flex-row gap-3 justify-center lg:justify-start">
                    <a href="{{ route('register') }}" class="grad-primary text-white font-bold px-6 py-3.5 rounded-2xl shadow-card hover:opacity-95 transition text-center">
                        Mulai Sekarang
                    </a>
                    <a href="#cara-kerja" class="bg-white text-selly-text font-bold px-6 py-3.5 rounded-2xl shadow-soft hover:shadow-card transition text-center">
                        Lihat Cara Kerja
                    </a>
                </div>

                {{-- Stats --}}
                <div class="mt-9 grid grid-cols-3 gap-4 max-w-md mx-auto lg:mx-0">
                    @foreach([[$stats['services'].'+','Layanan'],[$stats['outlets'],'Cabang'],[$stats['customers'].'+','Pelanggan']] as [$num,$lbl])
                        <div>
                            <p class="text-2xl lg:text-3xl font-extrabold text-grad">{{ $num }}</p>
                            <p class="text-xs text-selly-muted">{{ $lbl }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Hero visual --}}
            <div class="relative">
                <div class="grad-ocean rounded-[32px] p-8 shadow-card relative overflow-hidden hero-header">
                    <x-illu name="bubbles" class="absolute -right-6 -top-6 w-40 h-40 opacity-30" />
                    <div class="relative z-10 grid grid-cols-2 gap-4">
                        <div class="bg-white/95 rounded-2xl p-4 col-span-2 flex items-center gap-3">
                            <x-illu name="washer" class="w-14 h-14" />
                            <div>
                                <p class="font-bold">Cuci Kiloan & Satuan</p>
                                <p class="text-xs text-selly-muted">Reguler, Express, hingga Kilat 1 hari.</p>
                            </div>
                        </div>
                        <div class="bg-white/95 rounded-2xl p-4 flex flex-col items-center text-center gap-1.5">
                            <x-illu name="delivery" class="w-12 h-12" />
                            <p class="text-xs font-semibold">Pickup & Antar</p>
                        </div>
                        <div class="bg-white/95 rounded-2xl p-4 flex flex-col items-center text-center gap-1.5">
                            <x-illu name="sparkle" class="w-12 h-12" />
                            <p class="text-xs font-semibold">Bersih & Wangi</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Promo banners --}}
    @if($banners->isNotEmpty())
        <section class="max-w-6xl mx-auto px-5 pb-4">
            <div class="grid md:grid-cols-3 gap-4">
                @foreach($banners as $banner)
                    <x-promo-banner :banner="$banner" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- How it works --}}
    <section id="cara-kerja" class="max-w-6xl mx-auto px-5 py-14">
        <div class="text-center mb-10">
            <h2 class="text-3xl font-extrabold">Cara Kerja</h2>
            <p class="text-selly-muted mt-2">Empat langkah mudah, cucian beres tanpa repot.</p>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach([
                ['shopping-bag','1. Pesan','Pilih layanan & jadwalkan pickup.','grad-ocean'],
                ['truck','2. Dijemput','Kurir menjemput cucian ke alamatmu.','grad-violet'],
                ['washing-machine','3. Ditimbang & Dicuci','Harga final transparan setelah ditimbang.','grad-mint'],
                ['sparkles','4. Diantar','Cucian bersih wangi diantar kembali.','grad-sunset'],
            ] as [$ic,$title,$desc,$g])
                <div class="bg-white rounded-3xl p-6 shadow-soft card-hover relative overflow-hidden">
                    <span class="inline-flex w-14 h-14 rounded-2xl {{ $g }} text-white items-center justify-center mb-4">
                        <x-icon :name="$ic" class="w-7 h-7" />
                    </span>
                    <p class="font-bold">{{ $title }}</p>
                    <p class="text-sm text-selly-muted mt-1">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Branches --}}
    @if($outlets->isNotEmpty())
        <section id="cabang" class="bg-white/60 py-14">
            <div class="max-w-6xl mx-auto px-5">
                <div class="text-center mb-10">
                    <h2 class="text-3xl font-extrabold flex items-center justify-center gap-2"><x-icon name="map-pin" class="w-7 h-7 text-selly-primary" /> Lokasi Cabang</h2>
                    <p class="text-selly-muted mt-2">Temukan outlet Selly Laundry terdekat.</p>
                </div>
                <div class="grid md:grid-cols-2 gap-6">
                    @foreach($outlets as $outlet)
                        <div class="bg-white rounded-3xl shadow-soft overflow-hidden card-hover">
                            @if($outlet->mapEmbedUrl())
                                <iframe class="w-full h-52 border-0" loading="lazy" title="Peta {{ $outlet->name }}" src="{{ $outlet->mapEmbedUrl() }}"></iframe>
                            @endif
                            <div class="p-5">
                                <p class="font-bold text-lg">{{ $outlet->name }}</p>
                                @if($outlet->address)<p class="text-sm text-selly-muted mt-1 flex items-start gap-1.5"><x-icon name="map-pin" class="w-4 h-4 shrink-0 mt-0.5"/> {{ $outlet->address }}</p>@endif
                                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-2 text-xs text-selly-muted">
                                    @if($outlet->phone)<span class="flex items-center gap-1"><x-icon name="phone" class="w-3.5 h-3.5"/> {{ $outlet->phone }}</span>@endif
                                    @if($outlet->opening_hours)<span class="flex items-center gap-1"><x-icon name="clock" class="w-3.5 h-3.5"/> {{ $outlet->opening_hours }}</span>@endif
                                </div>
                                @if($outlet->directionsUrl())
                                    <a href="{{ $outlet->directionsUrl() }}" target="_blank"
                                       class="inline-flex items-center gap-1.5 mt-4 grad-primary text-white text-sm font-bold px-4 py-2.5 rounded-xl">
                                        <x-icon name="navigation" class="w-4 h-4" /> Buka Rute
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- FAQ --}}
    @if($faqs->isNotEmpty())
        <section id="faq" class="max-w-4xl mx-auto px-5 py-14">
            <div class="text-center mb-10">
                <h2 class="text-3xl font-extrabold">Pertanyaan Umum</h2>
                <p class="text-selly-muted mt-2">Hal yang sering ditanyakan pelanggan.</p>
            </div>
            <div class="space-y-3" x-data="{ open: 0 }">
                @foreach($faqs as $i => $faq)
                    <div class="bg-white rounded-2xl shadow-soft overflow-hidden">
                        <button type="button" @click="open === {{ $i }} ? open = null : open = {{ $i }}"
                                class="w-full flex items-center justify-between gap-3 px-5 py-4 text-left">
                            <span class="font-bold">{{ $faq->question }}</span>
                            <span class="w-7 h-7 rounded-full bg-selly-primary-soft text-selly-primary flex items-center justify-center shrink-0 transition-transform" x-bind:class="open === {{ $i }} ? 'rotate-90' : ''">
                                <x-icon name="chevron-right" class="w-4 h-4" />
                            </span>
                        </button>
                        <div x-show="open === {{ $i }}" x-collapse class="px-5 pb-5 -mt-1">
                            <p class="text-sm text-selly-muted">{{ $faq->answer }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- CTA band --}}
    <section class="max-w-6xl mx-auto px-5 pb-14">
        <div class="grad-violet rounded-[32px] px-6 py-12 text-center text-white shadow-card relative overflow-hidden hero-header">
            <x-illu name="sparkle" class="absolute left-6 top-6 w-16 h-16 opacity-30" />
            <x-illu name="bubbles" class="absolute right-4 bottom-2 w-28 h-28 opacity-25" />
            <div class="relative z-10">
                <h2 class="text-2xl lg:text-3xl font-extrabold">Siap cucian beres tanpa repot?</h2>
                <p class="mt-2 text-white/90">Daftar gratis dan jadwalkan pickup pertamamu hari ini.</p>
                <a href="{{ route('register') }}" class="inline-block mt-6 bg-white text-selly-primary-dark font-bold px-8 py-3.5 rounded-2xl shadow-card hover:opacity-95 transition">
                    Mulai Sekarang
                </a>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="bg-slate-900 text-slate-300">
        <div class="max-w-6xl mx-auto px-5 py-10 grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="sm:col-span-2 lg:col-span-1">
                <div class="flex items-center gap-2.5 mb-3">
                    <x-logo class="w-10 h-10" />
                    <p class="font-extrabold text-white text-lg">Selly Laundry</p>
                </div>
                <p class="text-sm text-slate-400">Layanan laundry antar-jemput profesional dengan harga transparan.</p>
            </div>
            <div>
                <p class="font-bold text-white mb-3">Layanan</p>
                <ul class="space-y-2 text-sm text-slate-400">
                    <li>Cuci Kiloan</li><li>Cuci-Setrika</li><li>Express 1 Hari</li><li>Satuan & Sepatu</li>
                </ul>
            </div>
            <div>
                <p class="font-bold text-white mb-3">Perusahaan</p>
                <ul class="space-y-2 text-sm text-slate-400">
                    <li><a href="#cara-kerja" class="hover:text-white">Cara Kerja</a></li>
                    <li><a href="#cabang" class="hover:text-white">Cabang</a></li>
                    <li><a href="#faq" class="hover:text-white">FAQ</a></li>
                </ul>
            </div>
            <div>
                <p class="font-bold text-white mb-3">Mulai</p>
                <div class="space-y-2">
                    <a href="{{ route('register') }}" class="block text-center grad-primary text-white font-bold px-4 py-2.5 rounded-xl">Daftar</a>
                    <a href="{{ route('login') }}" class="block text-center bg-slate-800 text-white font-semibold px-4 py-2.5 rounded-xl">Masuk</a>
                </div>
            </div>
        </div>
        <div class="border-t border-slate-800 py-5 text-center text-xs text-slate-500">
            © {{ date('Y') }} Selly Laundry. Semua hak dilindungi.
        </div>
    </footer>
</x-layouts.guest>
