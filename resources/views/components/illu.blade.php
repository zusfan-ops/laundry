@props(['name', 'class' => 'w-28 h-28'])

{{-- Colorful flat SVG illustrations for cards & empty states. --}}
@php
    $illus = [
        // Washing machine
        'washer' => '<rect x="20" y="14" width="60" height="76" rx="12" fill="#0891B2"/><rect x="20" y="14" width="60" height="18" rx="9" fill="#0E7490"/><circle cx="32" cy="23" r="3" fill="#fff"/><circle cx="42" cy="23" r="3" fill="#fff"/><circle cx="50" cy="60" r="22" fill="#ECFEFF"/><circle cx="50" cy="60" r="14" fill="#FBBF24"/><path d="M44 58a6 6 0 0 1 12 0 6 6 0 0 0 12 0" stroke="#fff" stroke-width="3" fill="none" transform="translate(-12 0) scale(0.7) translate(20 18)"/>',
        // Laundry basket
        'basket' => '<path d="M24 40h52l-6 48a6 6 0 0 1-6 5H36a6 6 0 0 1-6-5z" fill="#FBBF24"/><rect x="18" y="32" width="64" height="12" rx="6" fill="#0E7490"/><circle cx="42" cy="30" r="10" fill="#ECFEFF"/><circle cx="58" cy="26" r="13" fill="#FB7185"/><path d="M40 56v22M50 56v22M60 56v22" stroke="#fff" stroke-width="3" opacity="0.6"/>',
        // Hanger / shirt
        'hanger' => '<path d="M50 22a8 8 0 0 1 8 8c0 4-4 6-8 8" stroke="#0E7490" stroke-width="4" fill="none" stroke-linecap="round"/><path d="M50 46 22 70h56z" fill="#0891B2"/><rect x="30" y="66" width="40" height="14" rx="4" fill="#0E7490"/>',
        // Sparkle clean
        'sparkle' => '<path d="M50 16l8 22 22 8-22 8-8 22-8-22-22-8 22-8z" fill="#FBBF24"/><path d="M78 56l4 11 11 4-11 4-4 11-4-11-11-4 11-4z" fill="#0891B2"/>',
        // Empty box
        'box' => '<path d="M50 24l28 14v24L50 76 22 62V38z" fill="#ECFEFF"/><path d="M50 24l28 14-28 14-28-14z" fill="#0891B2"/><path d="M50 52v24" stroke="#0E7490" stroke-width="3"/>',
        // Coin / loyalty
        'coin' => '<circle cx="50" cy="50" r="32" fill="#FBBF24"/><circle cx="50" cy="50" r="24" fill="#F59E0B"/><path d="M50 36v28M42 44h12a5 5 0 0 1 0 10H44a5 5 0 0 0 0 10h12" stroke="#fff" stroke-width="3" fill="none" stroke-linecap="round"/>',
        // Delivery scooter
        'delivery' => '<circle cx="32" cy="74" r="9" fill="#0E7490"/><circle cx="70" cy="74" r="9" fill="#0E7490"/><path d="M24 56h26l8-16h10" stroke="#0891B2" stroke-width="5" fill="none" stroke-linecap="round"/><rect x="30" y="40" width="22" height="18" rx="3" fill="#FB7185"/>',
        // Bubbles
        'bubbles' => '<circle cx="40" cy="56" r="22" fill="#ECFEFF"/><circle cx="66" cy="44" r="14" fill="#0891B2"/><circle cx="62" cy="70" r="10" fill="#FBBF24"/><circle cx="34" cy="52" r="5" fill="#fff"/>',
    ];
    $svg = $illus[$name] ?? $illus['bubbles'];
@endphp

<svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" {{ $attributes->merge(['class' => $class]) }} aria-hidden="true">
    {!! $svg !!}
</svg>
