<x-layouts.guest title="Selly Laundry — Laundry Antar-Jemput">
    @php
        $kiloan = $services->where('pricing_type', 'weight')->take(4);
        $satuan = $services->where('pricing_type', 'unit')->take(4);
        $fallbackKiloan = [['Cuci Kering', 5000, 'kg'], ['Cuci-Setrika Reguler', 7000, 'kg'], ['Setrika Saja', 4000, 'kg'], ['Cuci Express 1 Hari', 10000, 'kg']];
        $fallbackSatuan  = [['Bed Cover', 25000, 'pcs'], ['Jas / Blazer', 30000, 'pcs'], ['Sepatu', 35000, 'pcs'], ['Karpet', 15000, 'pcs']];
    @endphp

    {{-- Navbar --}}
    <header class="sticky top-0 z-40 bg-[#F7FAFC]/85 backdrop-blur border-b border-slate-200/70">
        <div class="max-w-6xl mx-auto px-5 h-16 flex items-center justify-between">
            <a href="{{ route('welcome') }}" class="flex items-center gap-2.5">
                <x-logo class="w-9 h-9" />
                <p class="font-extrabold text-lg tracking-tight">Selly<span class="text-selly-primary">Laundry</span></p>
            </a>
            <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-600">
                <a href="#cara-kerja" class="hover:text-selly-primary transition">Cara Kerja</a>
                <a href="#harga" class="hover:text-selly-primary transition">Harga</a>
                <a href="#cabang" class="hover:text-selly-primary transition">Cabang</a>
                <a href="#faq" class="hover:text-selly-primary transition">FAQ</a>
            </nav>
            <div class="flex items-center gap-1.5">
                <a href="{{ route('login') }}" class="hidden sm:inline text-sm font-semibold text-slate-700 px-4 py-2 rounded-full hover:bg-slate-100 transition">Masuk</a>
                <a href="{{ route('register') }}" class="text-sm font-bold text-white bg-selly-primary px-5 py-2.5 rounded-full shadow-sm hover:bg-selly-primary-dark transition">Daftar</a>
            </div>
        </div>
    </header>

    {{-- Hero --}}
    <section class="relative overflow-hidden bg-gradient-to-b from-cyan-50/70 to-transparent">
        <div class="max-w-6xl mx-auto px-5 pt-12 pb-16 lg:pt-20 grid lg:grid-cols-[1.05fr_0.95fr] gap-12 lg:gap-8 items-center">
            {{-- Left --}}
            <div>
                <span class="inline-flex items-center gap-2 text-xs font-semibold text-selly-primary-dark bg-white ring-1 ring-cyan-100 px-3 py-1.5 rounded-full shadow-sm">
                    <span class="w-1.5 h-1.5 rounded-full bg-selly-accent"></span> Laundry antar-jemput #1 di kotamu
                </span>

                <h1 class="mt-5 text-[2.6rem] leading-[1.05] lg:text-6xl lg:leading-[1.02] font-extrabold tracking-tight text-slate-900">
                    Cuci tinggal <span class="mark-accent">pesan</span>,<br>
                    kami jemput &amp; antar.
                </h1>

                <p class="mt-5 text-slate-600 text-lg max-w-md">
                    Harga <b class="text-slate-800">transparan per kilo</b>, dijemput sesuai jadwal, dan bisa dilacak sampai kembali ke depan pintu.
                </p>

                <div class="mt-7 flex flex-wrap gap-3">
                    <a href="{{ route('register') }}" class="bg-selly-primary text-white font-bold px-6 py-3.5 rounded-full shadow-sm hover:bg-selly-primary-dark transition">
                        Jadwalkan Pickup
                    </a>
                    <a href="#cara-kerja" class="inline-flex items-center gap-2 text-slate-800 font-bold px-5 py-3.5 rounded-full ring-1 ring-slate-200 bg-white hover:ring-slate-300 transition">
                        <x-icon name="chevron-right" class="w-4 h-4 text-selly-primary" /> Cara kerjanya
                    </a>
                </div>

                {{-- Factual trust chips --}}
                <div class="mt-8 flex flex-wrap gap-x-5 gap-y-2 text-sm text-slate-600">
                    @foreach(['Bayar setelah ditimbang','Gratis ongkir min. 50rb','Express selesai 24 jam'] as $chip)
                        <span class="inline-flex items-center gap-1.5">
                            <x-icon name="check-circle" class="w-4 h-4 text-selly-success" /> {{ $chip }}
                        </span>
                    @endforeach
                </div>
            </div>

            {{-- Right: torn paper receipt (nota sobek, sedikit miring) --}}
            @php
                // Zigzag "sobekan" bawah nota — dibangun sekali, di-stretch via preserveAspectRatio.
                $tw = 13; $W = 320; $tornPath = "M0 0 H{$W} V5";
                for ($x = $W; $x > 0; $x -= $tw) {
                    $tornPath .= ' L' . round($x - $tw / 2, 1) . ' 15 L' . round(max(0, $x - $tw), 1) . ' 5';
                }
                $tornPath .= ' Z';
            @endphp
            <div class="relative py-6">
                <div class="absolute -right-10 -top-6 w-72 h-72 bg-cyan-200/40 blur-3xl rounded-full" aria-hidden="true"></div>
                <div class="absolute right-2 bottom-2 dot-grid text-cyan-200/70 w-36 h-36 -z-0" aria-hidden="true"></div>

                {{-- lembar nota di belakang (kesan tumpukan) --}}
                <div class="absolute inset-x-8 top-8 bottom-4 bg-white/70 rounded-2xl rotate-[5deg] ring-1 ring-slate-100" aria-hidden="true"></div>

                {{-- nota utama --}}
                <div class="relative max-w-sm mx-auto rotate-[-3deg]" style="filter: drop-shadow(0 22px 26px rgba(15,23,42,.16));">
                    <div class="bg-white rounded-t-2xl overflow-hidden">
                        <div class="bg-selly-primary text-white px-6 py-4 flex items-center justify-between">
                            <div>
                                <p class="text-[11px] text-white/80 uppercase tracking-widest">Pesanan</p>
                                <p class="font-bold tracking-tight tabular-nums">SLY-20260705-0042</p>
                            </div>
                            <span class="text-xs font-semibold bg-white/20 px-2.5 py-1 rounded-full">Sedang dicuci</span>
                        </div>

                        <div class="px-6 pt-6 pb-7">
                            <div class="space-y-3 text-sm">
                                @foreach([['washing-machine','Cuci-Setrika Reguler','3,0 kg',21000],['box','Bed Cover','1 pcs',25000],['sparkles','Parfum Lavender','',2000]] as [$ic,$nm,$q,$pr])
                                    <div class="flex items-center gap-3">
                                        <span class="w-9 h-9 rounded-lg bg-cyan-50 text-selly-primary flex items-center justify-center shrink-0"><x-icon :name="$ic" class="w-4 h-4" /></span>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-semibold text-slate-800 leading-tight">{{ $nm }}</p>
                                            @if($q)<p class="text-xs text-slate-400">{{ $q }}</p>@endif
                                        </div>
                                        <span class="font-semibold text-slate-700 tabular-nums">{{ rupiah($pr) }}</span>
                                    </div>
                                @endforeach
                            </div>

                            <div class="ticket-perf mt-5 pt-5">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-slate-500">Estimasi total</span>
                                    <span class="text-lg font-extrabold text-selly-primary tabular-nums">{{ rupiah(48000) }}</span>
                                </div>
                                <p class="text-xs text-slate-400 mt-1">Gratis ongkir · Selesai besok, 15.00</p>

                                <div class="mt-4">
                                    <div class="h-1.5 rounded-full bg-slate-100 overflow-hidden">
                                        <div class="h-full w-3/5 bg-selly-primary rounded-full"></div>
                                    </div>
                                    <div class="mt-2 flex justify-between text-[11px] font-medium">
                                        <span class="text-selly-success">✓ Dijemput</span>
                                        <span class="text-selly-primary">Dicuci</span>
                                        <span class="text-slate-400">Diantar</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- tepi bawah sobek --}}
                    <svg viewBox="0 0 {{ $W }} 16" preserveAspectRatio="none" class="block w-full h-4 -mt-px" aria-hidden="true">
                        <path d="{{ $tornPath }}" fill="#fff" />
                    </svg>
                </div>

                {{-- chip kurir (tegak, seperti stiker) --}}
                <div class="hidden sm:flex absolute left-0 lg:-left-4 bottom-6 items-center gap-2 bg-white rounded-2xl shadow-card ring-1 ring-slate-100 px-3.5 py-2.5 rotate-[-3deg]">
                    <span class="w-8 h-8 rounded-full bg-amber-50 text-selly-accent flex items-center justify-center"><x-icon name="truck" class="w-4 h-4" /></span>
                    <div class="text-xs leading-tight">
                        <p class="font-bold text-slate-800">Kurir 1,2 km lagi</p>
                        <p class="text-slate-400">Eko · AB 1234 XY</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Value strip --}}
    <section class="border-y border-slate-200/70 bg-white">
        <div class="max-w-6xl mx-auto px-5 py-6 grid grid-cols-2 md:grid-cols-4 gap-x-4 gap-y-5">
            @foreach([['scale','Harga transparan','Dihitung per kg'],['truck','Antar-jemput','Sesuai slot waktu'],['sparkles','Bersih & wangi','Parfum pilihan'],['navigation','Bisa dilacak','Real-time sampai antar']] as [$ic,$t,$d])
                <div class="flex items-center gap-3">
                    <span class="w-11 h-11 rounded-xl bg-cyan-50 text-selly-primary flex items-center justify-center shrink-0"><x-icon :name="$ic" class="w-5 h-5" /></span>
                    <div>
                        <p class="font-bold text-sm text-slate-800 leading-tight">{{ $t }}</p>
                        <p class="text-xs text-slate-500">{{ $d }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Promo banners (jika ada) --}}
    @if($banners->isNotEmpty())
        <section class="max-w-6xl mx-auto px-5 pt-12">
            <div class="grid md:grid-cols-3 gap-4">
                @foreach($banners as $banner)<x-promo-banner :banner="$banner" />@endforeach
            </div>
        </section>
    @endif

    {{-- Cara Kerja — route timeline --}}
    <section id="cara-kerja" class="max-w-6xl mx-auto px-5 py-16 lg:py-20">
        <div class="max-w-xl mb-12">
            <p class="text-sm font-bold text-selly-primary uppercase tracking-wide">Cara Kerja</p>
            <h2 class="mt-2 text-3xl lg:text-4xl font-extrabold tracking-tight text-slate-900">Empat langkah, cucian beres.</h2>
        </div>

        <div class="relative grid sm:grid-cols-2 lg:grid-cols-4 gap-x-6 gap-y-10">
            {{-- connector line (desktop) --}}
            <div class="hidden lg:block absolute top-7 left-[11%] right-[11%] border-t-2 border-dashed border-slate-200" aria-hidden="true"></div>
            @foreach([
                ['shopping-bag','Pesan','Pilih layanan & tentukan jadwal pickup.'],
                ['truck','Dijemput','Kurir menjemput cucian ke alamatmu.'],
                ['washing-machine','Ditimbang & dicuci','Harga final muncul setelah ditimbang.'],
                ['sparkles','Diantar','Cucian bersih wangi kembali ke pintumu.'],
            ] as $i => [$ic,$title,$desc])
                <div class="relative text-center lg:text-left">
                    <div class="relative z-10 w-14 h-14 rounded-2xl bg-white ring-1 ring-slate-100 shadow-soft flex items-center justify-center mx-auto lg:mx-0">
                        <x-icon :name="$ic" class="w-6 h-6 text-selly-primary" />
                        <span class="absolute -top-2 -right-2 w-6 h-6 rounded-full bg-selly-accent text-white text-xs font-extrabold flex items-center justify-center ring-2 ring-white">{{ $i + 1 }}</span>
                    </div>
                    <p class="mt-4 font-bold text-slate-900">{{ $title }}</p>
                    <p class="mt-1 text-sm text-slate-500">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Layanan & Harga --}}
    <section id="harga" class="bg-white border-y border-slate-200/70">
        <div class="max-w-6xl mx-auto px-5 py-16 lg:py-20">
            <div class="flex flex-wrap items-end justify-between gap-4 mb-10">
                <div class="max-w-xl">
                    <p class="text-sm font-bold text-selly-primary uppercase tracking-wide">Layanan &amp; Harga</p>
                    <h2 class="mt-2 text-3xl lg:text-4xl font-extrabold tracking-tight text-slate-900">Tarif jelas, tanpa kejutan.</h2>
                </div>
                <a href="{{ route('register') }}" class="text-sm font-bold text-selly-primary hover:text-selly-primary-dark">Lihat semua layanan →</a>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                {{-- Kiloan --}}
                <div class="rounded-3xl ring-1 ring-slate-100 p-6 lg:p-7 bg-gradient-to-b from-cyan-50/60 to-white">
                    <div class="flex items-center gap-2.5 mb-5">
                        <span class="w-10 h-10 rounded-xl bg-selly-primary text-white flex items-center justify-center"><x-icon name="washing-machine" class="w-5 h-5" /></span>
                        <h3 class="font-extrabold text-lg">Kiloan <span class="text-slate-400 font-medium text-sm">/ kg</span></h3>
                    </div>
                    <ul class="divide-y divide-slate-100">
                        @forelse($kiloan as $s)
                            <li class="flex items-center justify-between py-3">
                                <span class="text-slate-700">{{ $s->name }}</span>
                                <span class="font-bold text-slate-900">{{ rupiah($s->unit_price) }}<span class="text-slate-400 font-medium text-sm">/{{ $s->unit_label }}</span></span>
                            </li>
                        @empty
                            @foreach($fallbackKiloan as [$nm,$pr,$u])
                                <li class="flex items-center justify-between py-3">
                                    <span class="text-slate-700">{{ $nm }}</span>
                                    <span class="font-bold text-slate-900">{{ rupiah($pr) }}<span class="text-slate-400 font-medium text-sm">/{{ $u }}</span></span>
                                </li>
                            @endforeach
                        @endforelse
                    </ul>
                </div>

                {{-- Satuan --}}
                <div class="rounded-3xl ring-1 ring-slate-100 p-6 lg:p-7 bg-gradient-to-b from-amber-50/60 to-white">
                    <div class="flex items-center gap-2.5 mb-5">
                        <span class="w-10 h-10 rounded-xl bg-selly-accent text-white flex items-center justify-center"><x-icon name="box" class="w-5 h-5" /></span>
                        <h3 class="font-extrabold text-lg">Satuan <span class="text-slate-400 font-medium text-sm">/ item</span></h3>
                    </div>
                    <ul class="divide-y divide-slate-100">
                        @forelse($satuan as $s)
                            <li class="flex items-center justify-between py-3">
                                <span class="text-slate-700">{{ $s->name }}</span>
                                <span class="font-bold text-slate-900">{{ rupiah($s->unit_price) }}<span class="text-slate-400 font-medium text-sm">/{{ $s->unit_label }}</span></span>
                            </li>
                        @empty
                            @foreach($fallbackSatuan as [$nm,$pr,$u])
                                <li class="flex items-center justify-between py-3">
                                    <span class="text-slate-700">{{ $nm }}</span>
                                    <span class="font-bold text-slate-900">{{ rupiah($pr) }}<span class="text-slate-400 font-medium text-sm">/{{ $u }}</span></span>
                                </li>
                            @endforeach
                        @endforelse
                    </ul>
                </div>
            </div>
            <p class="text-xs text-slate-400 mt-4">*Harga kiloan bersifat estimasi; total final dihitung setelah cucian ditimbang di outlet.</p>
        </div>
    </section>

    {{-- Cabang --}}
    @if($outlets->isNotEmpty())
        <section id="cabang" class="max-w-6xl mx-auto px-5 py-16 lg:py-20">
            <div class="max-w-xl mb-10">
                <p class="text-sm font-bold text-selly-primary uppercase tracking-wide">Lokasi</p>
                <h2 class="mt-2 text-3xl lg:text-4xl font-extrabold tracking-tight text-slate-900">Cabang terdekat.</h2>
            </div>
            <div class="grid md:grid-cols-2 gap-6">
                @foreach($outlets as $outlet)
                    <div class="rounded-3xl overflow-hidden ring-1 ring-slate-100 bg-white">
                        @if($outlet->mapEmbedUrl())
                            <iframe class="w-full h-52 border-0" loading="lazy" title="Peta {{ $outlet->name }}" src="{{ $outlet->mapEmbedUrl() }}"></iframe>
                        @endif
                        <div class="p-5 flex items-start justify-between gap-4">
                            <div>
                                <p class="font-bold text-lg text-slate-900">{{ $outlet->name }}</p>
                                @if($outlet->address)<p class="text-sm text-slate-500 mt-1">{{ $outlet->address }}</p>@endif
                                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-2 text-xs text-slate-500">
                                    @if($outlet->phone)<span class="flex items-center gap-1"><x-icon name="phone" class="w-3.5 h-3.5"/> {{ $outlet->phone }}</span>@endif
                                    @if($outlet->opening_hours)<span class="flex items-center gap-1"><x-icon name="clock" class="w-3.5 h-3.5"/> {{ $outlet->opening_hours }}</span>@endif
                                </div>
                            </div>
                            @if($outlet->directionsUrl())
                                <a href="{{ $outlet->directionsUrl() }}" target="_blank" class="shrink-0 inline-flex items-center gap-1.5 text-sm font-bold text-selly-primary ring-1 ring-cyan-100 bg-cyan-50 px-3.5 py-2 rounded-full">
                                    <x-icon name="navigation" class="w-4 h-4" /> Rute
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
        <section id="faq" class="bg-white border-y border-slate-200/70">
            <div class="max-w-3xl mx-auto px-5 py-16 lg:py-20">
                <div class="text-center mb-10">
                    <p class="text-sm font-bold text-selly-primary uppercase tracking-wide">FAQ</p>
                    <h2 class="mt-2 text-3xl lg:text-4xl font-extrabold tracking-tight text-slate-900">Yang sering ditanyakan.</h2>
                </div>
                <div class="divide-y divide-slate-100" x-data="{ open: 0 }">
                    @foreach($faqs as $i => $faq)
                        <div>
                            <button type="button" @click="open === {{ $i }} ? open = null : open = {{ $i }}"
                                    class="w-full flex items-center justify-between gap-4 py-5 text-left">
                                <span class="font-bold text-slate-800">{{ $faq->question }}</span>
                                <x-icon name="plus" class="w-5 h-5 text-selly-primary shrink-0 transition-transform" x-bind:class="open === {{ $i }} ? 'rotate-45' : ''" />
                            </button>
                            <div x-show="open === {{ $i }}" x-collapse class="pb-5 -mt-1">
                                <p class="text-slate-600">{{ $faq->answer }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- CTA --}}
    <section class="max-w-6xl mx-auto px-5 py-16">
        <div class="relative overflow-hidden rounded-[28px] bg-selly-primary-dark text-white px-8 py-14 lg:px-14">
            <div class="absolute -right-16 -bottom-16 w-64 h-64 rounded-full bg-white/10"></div>
            <div class="absolute right-24 -top-10 w-32 h-32 rounded-full bg-selly-accent/20"></div>
            <div class="relative max-w-lg">
                <h2 class="text-3xl lg:text-4xl font-extrabold tracking-tight">Cucian menumpuk? Biar kami yang urus.</h2>
                <p class="mt-3 text-white/80">Daftar gratis, jadwalkan pickup pertamamu, dan pantau prosesnya langsung dari HP.</p>
                <div class="mt-7 flex flex-wrap gap-3">
                    <a href="{{ route('register') }}" class="bg-white text-selly-primary-dark font-bold px-6 py-3.5 rounded-full hover:bg-cyan-50 transition">Daftar Gratis</a>
                    <a href="{{ route('login') }}" class="font-bold px-6 py-3.5 rounded-full ring-1 ring-white/30 hover:bg-white/10 transition">Sudah punya akun</a>
                </div>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="bg-slate-900 text-slate-400">
        <div class="max-w-6xl mx-auto px-5 py-12 grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="sm:col-span-2 lg:col-span-1">
                <div class="flex items-center gap-2.5 mb-3">
                    <x-logo class="w-9 h-9" />
                    <p class="font-extrabold text-white text-lg">Selly Laundry</p>
                </div>
                <p class="text-sm">Layanan laundry antar-jemput profesional dengan harga transparan.</p>
            </div>
            <div>
                <p class="font-bold text-white mb-3 text-sm">Layanan</p>
                <ul class="space-y-2 text-sm"><li>Cuci Kiloan</li><li>Cuci-Setrika</li><li>Express 1 Hari</li><li>Satuan &amp; Sepatu</li></ul>
            </div>
            <div>
                <p class="font-bold text-white mb-3 text-sm">Perusahaan</p>
                <ul class="space-y-2 text-sm">
                    <li><a href="#cara-kerja" class="hover:text-white">Cara Kerja</a></li>
                    <li><a href="#harga" class="hover:text-white">Harga</a></li>
                    <li><a href="#cabang" class="hover:text-white">Cabang</a></li>
                    <li><a href="#faq" class="hover:text-white">FAQ</a></li>
                </ul>
            </div>
            <div>
                <p class="font-bold text-white mb-3 text-sm">Mulai</p>
                <div class="space-y-2">
                    <a href="{{ route('register') }}" class="block text-center bg-selly-primary text-white font-bold px-4 py-2.5 rounded-full">Daftar</a>
                    <a href="{{ route('login') }}" class="block text-center bg-slate-800 text-white font-semibold px-4 py-2.5 rounded-full">Masuk</a>
                </div>
            </div>
        </div>
        <div class="border-t border-slate-800 py-5 text-center text-xs text-slate-500">
            © {{ date('Y') }} Selly Laundry. Semua hak dilindungi.
        </div>
    </footer>
</x-layouts.guest>
