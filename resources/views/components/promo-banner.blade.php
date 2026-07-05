@props(['banner'])

<div class="relative overflow-hidden rounded-2xl p-5 text-white shadow-soft min-h-[120px] flex flex-col justify-center"
     style="background: linear-gradient(135deg, {{ $banner->color_from }}, {{ $banner->color_to }});">
    <x-banner-art :art="$banner->art" />
    <div class="relative z-10">
        <p class="text-[11px] font-semibold uppercase tracking-wide opacity-90">Promo</p>
        <h3 class="text-lg font-bold mt-0.5 leading-tight">{{ $banner->title }}</h3>
        @if($banner->subtitle)
            <p class="text-sm opacity-90 mt-1">{{ $banner->subtitle }}</p>
        @endif
        @if($banner->cta_label)
            @php
                $cta = $banner->cta_url ?: null;
                $href = $cta
                    ? (\Illuminate\Support\Str::startsWith($cta, ['http://', 'https://']) ? $cta : url($cta))
                    : '#';
            @endphp
            <a href="{{ $href }}" class="inline-block mt-3 bg-white/95 text-selly-primary-dark text-xs font-bold px-3.5 py-1.5 rounded-full">
                {{ $banner->cta_label }}
            </a>
        @endif
    </div>
</div>
