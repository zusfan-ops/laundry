<?php

namespace App\Livewire\Owner;

use App\Models\Courier;
use App\Models\Salary;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.staff')]
class Staff extends Component
{
    public string $view = 'staff';     // staff | payroll

    // Staff form
    public ?int $editingId = null;
    public bool $showForm = false;
    public string $name = '';
    public string $phone = '';
    public string $email = '';
    public string $password = '';
    public string $role = 'operator';
    public string $position = '';
    public int $base_salary = 0;
    public string $hired_at = '';

    // Payroll
    public string $period = '';
    public array $bonus = [];
    public array $deduction = [];

    public function mount(): void
    {
        $this->period = now()->format('Y-m');
        $this->hired_at = now()->toDateString();
    }

    private function staffRoles(): array
    {
        return ['operator', 'courier', 'outlet_admin', 'owner'];
    }

    public function createStaff(): void
    {
        $this->reset('editingId', 'name', 'phone', 'email', 'password', 'position', 'base_salary');
        $this->role = 'operator';
        $this->hired_at = now()->toDateString();
        $this->showForm = true;
    }

    public function editStaff(int $id): void
    {
        $u = User::findOrFail($id);
        $this->editingId = $u->id;
        $this->name = $u->name;
        $this->phone = $u->phone ?? '';
        $this->email = $u->email ?? '';
        $this->password = '';
        $this->role = $u->role;
        $this->position = $u->position ?? '';
        $this->base_salary = (int) $u->base_salary;
        $this->hired_at = optional($u->hired_at)->toDateString() ?? now()->toDateString();
        $this->showForm = true;
    }

    public function saveStaff(): void
    {
        $rules = [
            'name' => 'required|string|max:120',
            'phone' => 'required|string|max:20|unique:users,phone,' . ($this->editingId ?? 'NULL'),
            'email' => 'nullable|email|max:150|unique:users,email,' . ($this->editingId ?? 'NULL'),
            'role' => 'required|in:operator,courier,outlet_admin,owner',
            'position' => 'nullable|string|max:60',
            'base_salary' => 'integer|min:0',
            'hired_at' => 'nullable|date',
            'password' => ($this->editingId ? 'nullable' : 'required') . '|string|min:6',
        ];
        $data = $this->validate($rules);

        $outletId = auth()->user()->outlet_id ?? \App\Models\Outlet::value('id');

        $payload = [
            'name' => $data['name'], 'phone' => $data['phone'], 'email' => $data['email'] ?: null,
            'role' => $data['role'], 'position' => $data['position'] ?: null,
            'base_salary' => $data['base_salary'], 'hired_at' => $data['hired_at'] ?: null,
            'outlet_id' => $outletId,
        ];
        if (! empty($data['password'])) {
            $payload['password'] = Hash::make($data['password']);
        }

        $user = User::updateOrCreate(['id' => $this->editingId], $payload);

        // Ensure courier profile exists for courier role.
        if ($user->role === 'courier') {
            Courier::firstOrCreate(['user_id' => $user->id], ['outlet_id' => $outletId, 'is_available' => true]);
        }

        $this->showForm = false;
        $this->reset('editingId');
        $this->dispatch('toast', message: 'Data pegawai tersimpan.', type: 'success');
    }

    public function toggleStaff(int $id): void
    {
        $u = User::findOrFail($id);
        if ($u->id === auth()->id()) {
            $this->dispatch('toast', message: 'Tidak bisa menonaktifkan akun sendiri.', type: 'error');
            return;
        }
        $u->update(['is_active' => ! $u->is_active]);
        $this->dispatch('toast', message: $u->is_active ? 'Pegawai diaktifkan.' : 'Pegawai dinonaktifkan.', type: 'info');
    }

    // ---- Payroll ----
    public function generatePayroll(): void
    {
        $staff = User::whereIn('role', $this->staffRoles())->where('is_active', true)->get();

        foreach ($staff as $u) {
            $salary = Salary::firstOrNew(['user_id' => $u->id, 'period' => $this->period]);
            if (! $salary->exists) {
                $salary->base_amount = (int) $u->base_salary;
                $salary->bonus = 0;
                $salary->deduction = 0;
                $salary->status = 'draft';
                $salary->recalc();
                $salary->save();
            }
        }

        $this->dispatch('toast', message: 'Slip gaji periode ' . $this->period . ' dibuat.', type: 'success');
    }

    public function saveSalary(int $id): void
    {
        $salary = Salary::findOrFail($id);
        if ($salary->status === 'paid') {
            return;
        }
        $salary->bonus = max(0, (int) ($this->bonus[$id] ?? $salary->bonus));
        $salary->deduction = max(0, (int) ($this->deduction[$id] ?? $salary->deduction));
        $salary->recalc();
        $salary->save();
        $this->dispatch('toast', message: 'Slip gaji diperbarui.', type: 'success');
    }

    public function markPaid(int $id): void
    {
        $salary = Salary::findOrFail($id);
        $salary->update(['status' => 'paid', 'paid_at' => now()]);
        $this->dispatch('toast', message: 'Gaji ditandai lunas.', type: 'success');
    }

    public function render()
    {
        $staff = User::whereIn('role', $this->staffRoles())
            ->orderBy('role')->orderBy('name')->get();

        $salaries = Salary::with('user')->where('period', $this->period)
            ->get()
            ->each(function ($s) {
                $this->bonus[$s->id] ??= $s->bonus;
                $this->deduction[$s->id] ??= $s->deduction;
            });

        $payrollTotal = $salaries->sum('net_amount');

        return view('livewire.owner.staff', compact('staff', 'salaries', 'payrollTotal'));
    }
}
