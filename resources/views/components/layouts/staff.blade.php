<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0E7490">
    <title>{{ $title ?? 'Selly Laundry — Staf' }}</title>
    <meta name="app-base" content="{{ rtrim(parse_url(config('app.url'), PHP_URL_PATH) ?? '', '/') }}">
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/apple-touch-icon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased text-selly-text mesh-bg min-h-dvh">
    @auth
        <header class="grad-primary hero-header text-white sticky top-0 z-30 shadow-card">
            <div class="relative z-10 max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
                <a href="{{ route('staff.home') }}" class="flex items-center gap-2.5">
                    <x-logo class="w-9 h-9" />
                    <span class="font-extrabold text-lg">Selly Laundry</span>
                    <span class="text-xs bg-white/20 px-2 py-0.5 rounded-full capitalize">{{ str_replace('_', ' ', auth()->user()->role) }}</span>
                </a>
                <div class="flex items-center gap-3 text-sm">
                    <span class="hidden sm:inline font-medium">{{ auth()->user()->name }}</span>
                    <a href="{{ route('staff.home') }}" class="opacity-80 hover:opacity-100">Menu</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="opacity-80 hover:opacity-100">Keluar</button>
                    </form>
                </div>
            </div>
        </header>
    @endauth

    <main class="max-w-6xl mx-auto px-4 py-5">
        {{ $slot }}
    </main>

    <x-toast />
    @livewireScripts
</body>
</html>
