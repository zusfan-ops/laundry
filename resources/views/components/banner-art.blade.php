@props(['art' => 'bubbles'])

{{-- Decorative translucent SVG layer that fills a promo banner card. --}}
@php
    $arts = [
        'bubbles' => '<circle cx="300" cy="40" r="60"/><circle cx="360" cy="120" r="34"/><circle cx="250" cy="150" r="20"/><circle cx="330" cy="-10" r="24"/>',
        'waves' => '<path d="M0 120 Q60 90 120 120 T240 120 T360 120 T480 120 V200 H0 Z" opacity="0.5"/><path d="M0 150 Q60 120 120 150 T240 150 T360 150 T480 150 V200 H0 Z" opacity="0.35"/>',
        'dots' => '<g>'.implode('', array_map(fn($i)=>'<circle cx="'.(30+($i%6)*65).'" cy="'.(25+intdiv($i,6)*45).'" r="7"/>', range(0,17))).'</g>',
        'sparkles' => '<path d="M320 20 l8 22 22 8 -22 8 -8 22 -8-22 -22-8 22-8z"/><path d="M250 90 l5 14 14 5 -14 5 -5 14 -5-14 -14-5 14-5z"/><path d="M360 110 l4 11 11 4 -11 4 -4 11 -4-11 -11-4 11-4z"/>',
        'leaves' => '<path d="M300 30 C340 30 360 70 320 110 C280 70 300 30 300 30Z"/><path d="M350 80 C380 80 392 110 360 140 C330 110 350 80 350 80Z" opacity="0.6"/>',
        'clothes' => '<path d="M300 30 l30 18 -12 14 -6-4 v44 h-24 v-44 l-6 4 -12-14z"/><circle cx="360" cy="60" r="26" opacity="0.5"/><circle cx="360" cy="60" r="14" opacity="0.7"/>',
    ];
    $shape = $arts[$art] ?? $arts['bubbles'];
@endphp

<svg class="absolute inset-0 w-full h-full pointer-events-none" viewBox="0 0 420 170" preserveAspectRatio="xMaxYMid slice" fill="#FFFFFF" fill-opacity="0.16" aria-hidden="true">
    {!! $shape !!}
</svg>
