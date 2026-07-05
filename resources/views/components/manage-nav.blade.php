@props(['active' => 'dashboard'])

@php
    $tabs = [
        'dashboard' => ['Dashboard', 'owner.dashboard', 'trending-up'],
        'categories' => ['Kategori', 'owner.categories', 'package'],
        'services' => ['Layanan', 'owner.services', 'washing-machine'],
        'banners' => ['Banner', 'owner.banners', 'sparkles'],
        'outlets' => ['Cabang', 'owner.outlets', 'map-pin'],
        'faqs' => ['FAQ', 'owner.faqs', 'inbox'],
        'staff' => ['Pegawai & Gaji', 'owner.staff', 'user'],
    ];
@endphp

<div class="flex gap-2 overflow-x-auto no-scrollbar mb-5 -mx-1 px-1">
    @foreach($tabs as $key => [$label, $route, $icon])
        <a href="{{ route($route) }}"
           class="flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap {{ $active === $key ? 'bg-selly-primary text-white shadow-soft' : 'bg-white text-selly-muted' }}">
            <x-icon :name="$icon" class="w-4 h-4" /> {{ $label }}
        </a>
    @endforeach
</div>
