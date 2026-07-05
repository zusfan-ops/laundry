<div class="pb-44">
    <header class="bg-selly-primary text-white px-5 pt-6 pb-5 rounded-b-3xl">
        <div class="flex items-center gap-3">
            <a href="{{ route('cart') }}"><x-icon name="arrow-left" class="w-6 h-6" /></a>
            <h1 class="text-lg font-bold">Atur Jadwal & Bayar</h1>
        </div>
    </header>

    <div class="px-5 mt-4 space-y-5">
        {{-- Address --}}
        <section class="rounded-2xl bg-white p-4 shadow-soft">
            <div class="flex items-center justify-between mb-2">
                <h2 class="font-semibold text-sm flex items-center gap-1.5"><x-icon name="map-pin" class="w-4 h-4 text-selly-primary" /> Alamat Pickup & Delivery</h2>
                <button wire:click="$toggle('showAddressForm')" class="text-selly-primary text-xs font-semibold">+ Tambah</button>
            </div>

            @if($showAddressForm)
                <div class="space-y-2 mb-3 bg-selly-bg rounded-xl p-3">
                    <input type="text" wire:model="newAddress.label" placeholder="Label (Rumah/Kantor)" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                    <input type="text" wire:model="newAddress.recipient" placeholder="Nama penerima" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                    @error('newAddress.recipient') <p class="text-selly-danger text-xs">{{ $message }}</p> @enderror
                    <input type="text" wire:model="newAddress.phone" placeholder="No. HP" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                    @error('newAddress.phone') <p class="text-selly-danger text-xs">{{ $message }}</p> @enderror
                    <textarea wire:model="newAddress.full_address" placeholder="Alamat lengkap" rows="2" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm"></textarea>
                    @error('newAddress.full_address') <p class="text-selly-danger text-xs">{{ $message }}</p> @enderror
                    <button wire:click="saveAddress" class="w-full bg-selly-primary text-white text-sm font-semibold py-2 rounded-lg">Simpan Alamat</button>
                </div>
            @endif

            <div class="space-y-2">
                @forelse($this->addresses as $addr)
                    <label class="flex gap-3 items-start p-3 rounded-xl border cursor-pointer {{ $addressId === $addr->id ? 'border-selly-primary bg-selly-primary-soft' : 'border-gray-200' }}">
                        <input type="radio" wire:model.live="addressId" value="{{ $addr->id }}" class="mt-1 text-selly-primary focus:ring-selly-primary">
                        <div class="text-sm">
                            <p class="font-semibold">{{ $addr->label }} · {{ $addr->recipient }}</p>
                            <p class="text-selly-muted text-xs">{{ $addr->full_address }}</p>
                        </div>
                    </label>
                @empty
                    <p class="text-sm text-selly-muted">Belum ada alamat. Tambahkan dulu ya.</p>
                @endforelse
            </div>
            @error('addressId') <p class="text-selly-danger text-xs mt-1">{{ $message }}</p> @enderror
        </section>

        {{-- Pickup schedule --}}
        <section class="rounded-2xl bg-white p-4 shadow-soft">
            <h2 class="font-semibold text-sm mb-2 flex items-center gap-1.5"><x-icon name="clock" class="w-4 h-4 text-selly-primary" /> Jadwal Pickup</h2>
            <input type="date" wire:model.live="pickupDate" min="{{ today()->toDateString() }}" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm mb-3">
            <div class="grid grid-cols-2 gap-2">
                @foreach($this->slots as $slot)
                    @php $remain = $slot->remainingFor($pickupDate, 'pickup'); @endphp
                    <button type="button" @disabled($remain <= 0)
                            wire:click="$set('pickupSlotId', {{ $slot->id }})"
                            class="rounded-xl border p-2.5 text-sm {{ $pickupSlotId === $slot->id ? 'border-selly-primary bg-selly-primary-soft' : 'border-gray-200' }} {{ $remain <= 0 ? 'opacity-40 cursor-not-allowed' : '' }}">
                        <span class="font-semibold block">{{ $slot->label() }}</span>
                        <span class="text-[11px] {{ $remain <= 0 ? 'text-selly-danger' : 'text-selly-muted' }}">{{ $remain <= 0 ? 'Penuh' : "Sisa $remain" }}</span>
                    </button>
                @endforeach
            </div>
            @error('pickupSlotId') <p class="text-selly-danger text-xs mt-1">{{ $message }}</p> @enderror
        </section>

        {{-- Delivery schedule --}}
        <section class="rounded-2xl bg-white p-4 shadow-soft">
            <h2 class="font-semibold text-sm mb-2 flex items-center gap-1.5"><x-icon name="truck" class="w-4 h-4 text-selly-primary" /> Jadwal Delivery</h2>
            <input type="date" wire:model.live="deliveryDate" min="{{ $pickupDate }}" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm mb-3">
            <div class="grid grid-cols-2 gap-2">
                @foreach($this->slots as $slot)
                    @php $remain = $slot->remainingFor($deliveryDate, 'delivery'); @endphp
                    <button type="button" @disabled($remain <= 0)
                            wire:click="$set('deliverySlotId', {{ $slot->id }})"
                            class="rounded-xl border p-2.5 text-sm {{ $deliverySlotId === $slot->id ? 'border-selly-primary bg-selly-primary-soft' : 'border-gray-200' }} {{ $remain <= 0 ? 'opacity-40 cursor-not-allowed' : '' }}">
                        <span class="font-semibold block">{{ $slot->label() }}</span>
                        <span class="text-[11px] {{ $remain <= 0 ? 'text-selly-danger' : 'text-selly-muted' }}">{{ $remain <= 0 ? 'Penuh' : "Sisa $remain" }}</span>
                    </button>
                @endforeach
            </div>
            @error('deliverySlotId') <p class="text-selly-danger text-xs mt-1">{{ $message }}</p> @enderror
        </section>

        {{-- Voucher --}}
        <section class="rounded-2xl bg-white p-4 shadow-soft">
            <h2 class="font-semibold text-sm mb-2 flex items-center gap-1.5"><x-icon name="ticket" class="w-4 h-4 text-selly-primary" /> Voucher</h2>
            @if($appliedVoucherId)
                <div class="flex items-center justify-between bg-selly-primary-soft rounded-lg px-3 py-2 text-sm">
                    <span class="font-semibold text-selly-primary-dark">{{ strtoupper($voucherCode) }} diterapkan (−{{ rupiah($discount) }})</span>
                    <button wire:click="removeVoucher" class="text-selly-danger text-xs">Hapus</button>
                </div>
            @else
                <div class="flex gap-2">
                    <input type="text" wire:model="voucherCode" placeholder="Masukkan kode" class="flex-1 rounded-lg border border-gray-200 px-3 py-2 text-sm uppercase">
                    <button wire:click="applyVoucher" class="bg-selly-accent text-white px-4 rounded-lg text-sm font-semibold">Pakai</button>
                </div>
                @error('voucherCode') <p class="text-selly-danger text-xs mt-1">{{ $message }}</p> @enderror
            @endif
        </section>

        {{-- Payment mode --}}
        <section class="rounded-2xl bg-white p-4 shadow-soft">
            <h2 class="font-semibold text-sm mb-2 flex items-center gap-1.5"><x-icon name="wallet" class="w-4 h-4 text-selly-primary" /> Metode Pembayaran</h2>
            <label class="flex gap-3 items-start p-3 rounded-xl border cursor-pointer mb-2 {{ $paymentMode==='pay_after_weigh' ? 'border-selly-primary bg-selly-primary-soft' : 'border-gray-200' }}">
                <input type="radio" wire:model.live="paymentMode" value="pay_after_weigh" class="mt-1 text-selly-primary focus:ring-selly-primary">
                <div class="text-sm"><p class="font-semibold">Bayar setelah ditimbang</p><p class="text-selly-muted text-xs">Disarankan untuk layanan kiloan.</p></div>
            </label>
            <label class="flex gap-3 items-start p-3 rounded-xl border cursor-pointer {{ $paymentMode==='prepaid_estimate' ? 'border-selly-primary bg-selly-primary-soft' : 'border-gray-200' }}">
                <input type="radio" wire:model.live="paymentMode" value="prepaid_estimate" class="mt-1 text-selly-primary focus:ring-selly-primary">
                <div class="text-sm"><p class="font-semibold">Prabayar estimasi</p><p class="text-selly-muted text-xs">Bayar di muka, selisih disesuaikan setelah penimbangan.</p></div>
            </label>
        </section>

        {{-- Notes --}}
        <textarea wire:model="notes" rows="2" placeholder="Catatan untuk outlet (opsional)" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm"></textarea>
    </div>

    {{-- Sticky summary --}}
    <div class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-[480px] bg-white p-4 border-t border-gray-100 z-30">
        <div class="space-y-1 text-sm mb-2">
            <div class="flex justify-between"><span class="text-selly-muted">Subtotal estimasi</span><span>{{ rupiah($this->subtotal) }}</span></div>
            <div class="flex justify-between"><span class="text-selly-muted">Ongkir (PP)</span><span>{{ $this->shippingFee === 0 ? 'Gratis' : rupiah($this->shippingFee) }}</span></div>
            @if($discount > 0)<div class="flex justify-between text-selly-success"><span>Diskon</span><span>−{{ rupiah($discount) }}</span></div>@endif
            <div class="flex justify-between font-bold text-base pt-1"><span>Total estimasi</span><span class="text-selly-primary">{{ rupiah($this->total) }}</span></div>
        </div>
        <button wire:click="placeOrder" wire:loading.attr="disabled"
                class="w-full bg-selly-primary text-white font-semibold py-3 rounded-xl active:scale-[0.99] transition">
            <span wire:loading.remove wire:target="placeOrder">Buat Pesanan</span>
            <span wire:loading wire:target="placeOrder">Memproses…</span>
        </button>
    </div>
</div>
