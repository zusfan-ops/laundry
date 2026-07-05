@props(['class' => 'w-20 h-20'])

{{-- Inline SVG logo — never 404s, crisp at any size. --}}
<svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" {{ $attributes->merge(['class' => $class]) }} aria-label="Selly Laundry">
    <defs>
        <linearGradient id="sellyLogoBg" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0" stop-color="#06B6D4"/>
            <stop offset="1" stop-color="#0E7490"/>
        </linearGradient>
    </defs>
    <rect x="16" y="16" width="480" height="480" rx="112" fill="url(#sellyLogoBg)"/>
    <circle cx="256" cy="270" r="132" fill="#ECFEFF"/>
    <circle cx="256" cy="270" r="96" fill="#0E7490"/>
    <path d="M256 196c34 30 54 52 54 82a54 54 0 1 1-108 0c0-30 20-52 54-82z" fill="#FBBF24"/>
    <circle cx="238" cy="286" r="14" fill="#fff" opacity="0.85"/>
    <circle cx="180" cy="92" r="14" fill="#FBBF24"/>
    <circle cx="222" cy="92" r="14" fill="#FB7185"/>
</svg>
