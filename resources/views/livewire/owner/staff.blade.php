<div>
    <x-manage-nav active="staff" />

    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-xl font-bold">Pegawai & Gaji</h1>
            <p class="text-sm text-selly-muted">Kelola pegawai outlet dan penggajian bulanan.</p>
        </div>
    </div>

    {{-- Sub tabs --}}
    <div class="flex gap-2 mb-4">
        <button wire:click="$set('view', 'staff')" class="px-4 py-2 rounded-full text-sm font-medium {{ $view === 'staff' ? 'bg-selly-primary text-white' : 'bg-white text-selly-muted' }}">Kepegawaian</button>
        <button wire:click="$set('view', 'payroll')" class="px-4 py-2 rounded-full text-sm font-medium {{ $view === 'payroll' ? 'bg-selly-primary text-white' : 'bg-white text-selly-muted' }}">Penggajian</button>
    </div>

    @if($view === 'staff')
        <div class="flex justify-end mb-3">
            <button wire:click="createStaff" class="bg-selly-primary text-white text-sm font-semibold px-4 py-2 rounded-xl flex items-center gap-1.5">
                <x-icon name="plus" class="w-4 h-4" /> Tambah Pegawai
            </button>
        </div>

        @if($showForm)
            <div class="bg-white rounded-2xl p-4 shadow-soft mb-4">
                <h2 class="font-semibold mb-3">{{ $editingId ? 'Ubah' : 'Tambah' }} Pegawai</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="text-sm font-medium">Nama</label>
                        <input type="text" wire:model="name" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                        @error('name') <p class="text-selly-danger text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium">Jabatan</label>
                        <input type="text" wire:model="position" placeholder="cth: Operator Cuci" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-sm font-medium">No. HP</label>
                        <input type="text" wire:model="phone" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                        @error('phone') <p class="text-selly-danger text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium">Email (opsional)</label>
                        <input type="email" wire:model="email" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                        @error('email') <p class="text-selly-danger text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium">Peran</label>
                        <select wire:model="role" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                            <option value="operator">Operator</option>
                            <option value="courier">Kurir</option>
                            <option value="outlet_admin">Admin Outlet</option>
                            <option value="owner">Owner</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-medium">Gaji Pokok / bulan</label>
                        <input type="number" wire:model="base_salary" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-sm font-medium">Tanggal Bergabung</label>
                        <input type="date" wire:model="hired_at" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="text-sm font-medium">{{ $editingId ? 'Kata Sandi Baru (opsional)' : 'Kata Sandi' }}</label>
                        <input type="password" wire:model="password" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">
                        @error('password') <p class="text-selly-danger text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="flex gap-2 mt-4">
                    <button wire:click="saveStaff" class="bg-selly-primary text-white text-sm font-semibold px-5 py-2 rounded-xl">Simpan</button>
                    <button wire:click="$set('showForm', false)" class="text-selly-muted text-sm px-3">Batal</button>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-soft divide-y divide-gray-50">
            @foreach($staff as $u)
                <div class="flex items-center gap-3 p-3.5 {{ $u->is_active ? '' : 'opacity-60' }}">
                    <span class="w-11 h-11 rounded-full bg-selly-primary-soft text-selly-primary-dark flex items-center justify-center font-bold shrink-0">
                        {{ \Illuminate\Support\Str::of($u->name)->substr(0,1)->upper() }}
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-sm">{{ $u->name }}</p>
                        <p class="text-xs text-selly-muted capitalize">{{ str_replace('_',' ',$u->role) }}@if($u->position) · {{ $u->position }}@endif · {{ rupiah($u->base_salary) }}/bln</p>
                    </div>
                    <button wire:click="editStaff({{ $u->id }})" class="text-selly-primary"><x-icon name="edit" class="w-4 h-4" /></button>
                    <button wire:click="toggleStaff({{ $u->id }})"
                            class="text-[11px] font-semibold px-2 py-0.5 rounded-full {{ $u->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $u->is_active ? 'Aktif' : 'Nonaktif' }}
                    </button>
                </div>
            @endforeach
        </div>
    @else
        {{-- Payroll --}}
        <div class="bg-white rounded-2xl p-4 shadow-soft mb-4 flex flex-wrap items-end gap-3">
            <div>
                <label class="text-sm font-medium">Periode</label>
                <input type="month" wire:model.live="period" class="mt-1 rounded-lg border border-gray-200 px-3 py-2 text-sm">
            </div>
            <button wire:click="generatePayroll" class="bg-selly-primary text-white text-sm font-semibold px-4 py-2 rounded-xl">Buat / Perbarui Slip</button>
            <div class="ml-auto text-right">
                <p class="text-xs text-selly-muted">Total Penggajian</p>
                <p class="text-lg font-bold text-selly-primary">{{ rupiah($payrollTotal) }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-soft overflow-hidden">
            @forelse($salaries as $s)
                <div class="p-3.5 border-b border-gray-50 last:border-0">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <p class="font-semibold text-sm">{{ $s->user->name }}</p>
                            <p class="text-xs text-selly-muted">Pokok {{ rupiah($s->base_amount) }}</p>
                        </div>
                        @if($s->status === 'paid')
                            <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-green-100 text-green-700">Lunas</span>
                        @else
                            <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-amber-100 text-amber-700">Draft</span>
                        @endif
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 items-end">
                        <div>
                            <label class="text-[11px] text-selly-muted">Bonus</label>
                            <input type="number" wire:model="bonus.{{ $s->id }}" @disabled($s->status==='paid') class="w-full rounded-lg border border-gray-200 px-2 py-1.5 text-sm">
                        </div>
                        <div>
                            <label class="text-[11px] text-selly-muted">Potongan</label>
                            <input type="number" wire:model="deduction.{{ $s->id }}" @disabled($s->status==='paid') class="w-full rounded-lg border border-gray-200 px-2 py-1.5 text-sm">
                        </div>
                        <div>
                            <label class="text-[11px] text-selly-muted">Diterima</label>
                            <p class="font-bold text-selly-primary text-sm py-1.5">{{ rupiah($s->net_amount) }}</p>
                        </div>
                        <div class="flex gap-1.5">
                            @if($s->status !== 'paid')
                                <button wire:click="saveSalary({{ $s->id }})" class="text-xs bg-selly-primary-soft text-selly-primary-dark font-semibold px-2.5 py-1.5 rounded-lg">Simpan</button>
                                <button wire:click="markPaid({{ $s->id }})" class="text-xs bg-selly-success text-white font-semibold px-2.5 py-1.5 rounded-lg">Bayar</button>
                            @else
                                <span class="text-xs text-selly-muted py-1.5">{{ $s->paid_at?->format('d M Y') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-selly-muted text-sm">
                    <x-illu name="coin" class="w-20 h-20 mx-auto mb-2" />
                    Belum ada slip gaji untuk periode ini. Klik "Buat / Perbarui Slip".
                </div>
            @endforelse
        </div>
    @endif
</div>
