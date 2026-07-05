<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    public function test_guest_pages_load(): void
    {
        $this->get('/')->assertOk();
        $this->get('/login')->assertOk();
        $this->get('/register')->assertOk();
    }

    public function test_customer_pages_load(): void
    {
        $user = User::where('email', 'rina@selly.test')->firstOrFail();
        $service = Service::first();

        $this->actingAs($user);
        $this->get('/home')->assertOk();
        $this->get('/layanan')->assertOk();
        $this->get('/layanan/' . $service->id)->assertOk();
        $this->get('/keranjang')->assertOk();
        $this->get('/pesanan')->assertOk();
        $this->get('/promo')->assertOk();
        $this->get('/akun')->assertOk();
    }

    public function test_staff_pages_load(): void
    {
        $this->actingAs(User::where('email', 'operator@selly.test')->firstOrFail());
        $this->get('/operator')->assertOk();

        $this->actingAs(User::where('email', 'kurir@selly.test')->firstOrFail());
        $this->get('/kurir')->assertOk();

        $this->actingAs(User::where('email', 'owner@selly.test')->firstOrFail());
        $this->get('/owner')->assertOk();
        $this->get('/owner/kategori')->assertOk();
        $this->get('/owner/layanan')->assertOk();
        $this->get('/owner/banner')->assertOk();
        $this->get('/owner/cabang')->assertOk();
        $this->get('/owner/faq')->assertOk();
        $this->get('/owner/pegawai')->assertOk();
    }

    public function test_landing_shows_map_and_faq(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Cabang terdekat')
            ->assertSee('Layanan & Harga')
            ->assertSee('openstreetmap.org', false)
            ->assertSee('Cara Kerja');
    }

    public function test_role_redirects_protect_customer_area(): void
    {
        $this->actingAs(User::where('email', 'kurir@selly.test')->firstOrFail());
        $this->get('/home')->assertRedirect('/kurir');
    }
}
