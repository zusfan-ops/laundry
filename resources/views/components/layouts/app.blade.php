<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover, maximum-scale=1">
    <meta name="theme-color" content="#0891B2">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Selly">
    <title>{{ $title ?? 'Selly Laundry' }}</title>
    <meta name="app-base" content="{{ rtrim(parse_url(config('app.url'), PHP_URL_PATH) ?? '', '/') }}">
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="mask-icon" href="{{ asset('favicon.svg') }}" color="#0891B2">
    <link rel="apple-touch-icon" href="{{ asset('icons/apple-touch-icon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased text-selly-text mesh-bg">
    @php $isCustomer = auth()->check() && auth()->user()->role === 'customer'; @endphp

    @if($isCustomer)
        <div class="lg:max-w-6xl lg:mx-auto lg:flex lg:gap-6 lg:px-6 lg:py-6">
            <x-customer-nav />
            <main class="app-shell pb-24 lg:pb-0 lg:flex-1 lg:min-w-0">
                {{ $slot }}
            </main>
        </div>
        <x-bottom-nav class="lg:hidden" />
    @else
        <div class="app-shell">
            {{ $slot }}
        </div>
    @endif

    <x-toast />
    @livewireScripts
</body>
</html>
