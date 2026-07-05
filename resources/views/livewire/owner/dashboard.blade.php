<div>
    <x-manage-nav active="dashboard" />

    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-xl font-bold">Dashboard Owner</h1>
            <p class="text-sm text-selly-muted">Ringkasan operasional Selly Laundry.</p>
        </div>
        <a href="{{ route('operator.board') }}" class="text-sm text-selly-primary font-semibold">Papan Antrian →</a>
    </div>

    {{-- KPI cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-5">
        @foreach([
            ['trending-up','GMV (Lunas)', rupiah($gmv), 'grad-primary'],
            ['receipt','Total Order', $totalOrders, 'grad-ocean'],
            ['wallet','Rata-rata Order', rupiah($avgOrderValue), 'grad-violet'],
            ['check-circle','Completion Rate', $completionRate.'%', 'grad-mint'],
        ] as [$ic,$label,$val,$g])
            <div class="relative overflow-hidden rounded-2xl p-4 shadow-card {{ $g }} text-white">
                <x-icon :name="$ic" class="w-16 h-16 absolute -right-3 -bottom-3 opacity-20" />
                <div class="relative z-10">
                    <div class="flex items-center gap-1.5 text-white/85 text-xs font-medium"><x-icon :name="$ic" class="w-4 h-4" /> {{ $label }}</div>
                    <p class="text-2xl font-extrabold mt-1">{{ $val }}</p>
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        {{-- Status breakdown --}}
        <div class="bg-white rounded-2xl p-4 shadow-soft">
            <h2 class="font-semibold mb-3">Order per Status</h2>
            <div class="space-y-2">
                @forelse($statusBreakdown as $status => $count)
                    <div class="flex items-center justify-between">
                        <x-status-badge :status="$status" />
                        <span class="text-sm font-semibold">{{ $count }}</span>
                    </div>
                @empty
                    <p class="text-sm text-selly-muted">Belum ada data.</p>
                @endforelse
            </div>
            <div class="grid grid-cols-3 gap-2 mt-4 text-center">
                <div class="bg-selly-bg rounded-xl py-2"><p class="text-lg font-bold">{{ $activeOrders }}</p><p class="text-[11px] text-selly-muted">Aktif</p></div>
                <div class="bg-selly-bg rounded-xl py-2"><p class="text-lg font-bold text-selly-success">{{ $completed }}</p><p class="text-[11px] text-selly-muted">Selesai</p></div>
                <div class="bg-selly-bg rounded-xl py-2"><p class="text-lg font-bold text-selly-danger">{{ $cancelled }}</p><p class="text-[11px] text-selly-muted">Batal</p></div>
            </div>
        </div>

        {{-- Recent orders --}}
        <div class="bg-white rounded-2xl p-4 shadow-soft">
            <h2 class="font-semibold mb-3">Order Terbaru</h2>
            <div class="divide-y divide-gray-50">
                @forelse($recent as $order)
                    <div class="flex items-center justify-between py-2.5">
                        <div>
                            <p class="text-sm font-semibold">{{ $order->code }}</p>
                            <p class="text-xs text-selly-muted">{{ $order->user->name }} · {{ $order->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-selly-primary">{{ rupiah($order->final_total ?: $order->estimated_total) }}</p>
                            <x-status-badge :status="$order->status" />
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-selly-muted py-4">Belum ada order.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="mt-4 bg-white rounded-2xl p-4 shadow-soft flex items-center justify-between">
        <span class="text-sm text-selly-muted">Total pelanggan terdaftar</span>
        <span class="font-bold text-lg">{{ $customers }}</span>
    </div>
</div>
