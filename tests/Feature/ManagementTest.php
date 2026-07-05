<?php

namespace Tests\Feature;

use App\Livewire\Owner\Banners;
use App\Livewire\Owner\Categories;
use App\Livewire\Owner\Staff;
use App\Models\PromoBanner;
use App\Models\Salary;
use App\Models\ServiceCategory;
use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;

class ManagementTest extends TestCase
{
    private function owner(): User
    {
        return User::where('email', 'owner@selly.test')->firstOrFail();
    }

    public function test_owner_can_create_and_toggle_category(): void
    {
        $this->actingAs($this->owner());

        Livewire::test(Categories::class)
            ->call('create')
            ->set('name', 'Kategori Uji')
            ->set('icon', 'gift')
            ->set('color', '#123456')
            ->call('save')
            ->assertHasNoErrors();

        $cat = ServiceCategory::where('name', 'Kategori Uji')->firstOrFail();
        $this->assertTrue($cat->is_active);

        Livewire::test(Categories::class)->call('toggle', $cat->id);
        $this->assertFalse($cat->refresh()->is_active);

        $cat->delete();
    }

    public function test_owner_can_create_promo_banner(): void
    {
        $this->actingAs($this->owner());

        Livewire::test(Banners::class)
            ->call('create')
            ->set('title', 'Banner Uji')
            ->set('subtitle', 'Subjudul uji')
            ->set('art', 'waves')
            ->call('save')
            ->assertHasNoErrors();

        $banner = PromoBanner::where('title', 'Banner Uji')->firstOrFail();
        $this->assertTrue($banner->is_active);

        // Edit path must handle null subtitle/cta without TypeError.
        $banner->update(['subtitle' => null, 'cta_label' => null, 'cta_url' => null]);
        Livewire::test(Banners::class)
            ->call('edit', $banner->id)
            ->assertSet('title', 'Banner Uji')
            ->set('title', 'Banner Uji Diubah')
            ->call('save')
            ->assertHasNoErrors();
        $this->assertSame('Banner Uji Diubah', $banner->refresh()->title);

        $banner->delete();
    }

    public function test_owner_can_add_staff_and_run_payroll(): void
    {
        $this->actingAs($this->owner());

        Livewire::test(Staff::class)
            ->call('createStaff')
            ->set('name', 'Pegawai Uji')
            ->set('phone', '081299990001')
            ->set('role', 'operator')
            ->set('base_salary', 4000000)
            ->set('password', 'secret123')
            ->call('saveStaff')
            ->assertHasNoErrors();

        $staff = User::where('phone', '081299990001')->firstOrFail();
        $this->assertSame(4000000, $staff->base_salary);

        // Generate payroll for current period and verify a draft slip exists.
        $period = now()->format('Y-m');
        Livewire::test(Staff::class)
            ->set('period', $period)
            ->call('generatePayroll');

        $salary = Salary::where('user_id', $staff->id)->where('period', $period)->firstOrFail();
        $this->assertSame(4000000, $salary->net_amount);
        $this->assertSame('draft', $salary->status);

        // Mark paid.
        Livewire::test(Staff::class)->set('period', $period)->call('markPaid', $salary->id);
        $this->assertSame('paid', $salary->refresh()->status);

        // Cleanup.
        Salary::where('user_id', $staff->id)->delete();
        $staff->forceDelete();
    }
}
