@props(['status'])

@php
    $map = [
        'pending_payment' => ['Menunggu Bayar', 'bg-amber-100 text-amber-700'],
        'placed' => ['Diterima', 'bg-selly-primary-soft text-selly-primary-dark'],
        'assigned_pickup' => ['Kurir Menjemput', 'bg-blue-100 text-blue-700'],
        'picked_up' => ['Dijemput', 'bg-blue-100 text-blue-700'],
        'at_outlet' => ['Di Outlet', 'bg-indigo-100 text-indigo-700'],
        'weighed' => ['Ditimbang', 'bg-indigo-100 text-indigo-700'],
        'awaiting_price_confirm' => ['Konfirmasi Harga', 'bg-orange-100 text-orange-700'],
        'processing' => ['Diproses', 'bg-purple-100 text-purple-700'],
        'ready' => ['Siap Diantar', 'bg-teal-100 text-teal-700'],
        'assigned_delivery' => ['Kurir Mengantar', 'bg-blue-100 text-blue-700'],
        'delivering' => ['Diantar', 'bg-blue-100 text-blue-700'],
        'completed' => ['Selesai', 'bg-green-100 text-green-700'],
        'cancelled' => ['Dibatalkan', 'bg-red-100 text-red-700'],
    ];
    [$label, $classes] = $map[$status] ?? [$status, 'bg-gray-100 text-gray-600'];
@endphp

<span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $classes }}">{{ $label }}</span>
