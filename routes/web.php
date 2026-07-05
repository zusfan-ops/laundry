<?php

use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Courier\Tasks as CourierTasks;
use App\Livewire\Customer\Account;
use App\Livewire\Customer\Cart;
use App\Livewire\Customer\Catalog;
use App\Livewire\Customer\Checkout;
use App\Livewire\Customer\Home;
use App\Livewire\Customer\OrderHistory;
use App\Livewire\Customer\OrderShow;
use App\Livewire\Customer\Promo;
use App\Livewire\Customer\ServiceDetail;
use App\Livewire\Operator\Board as OperatorBoard;
use App\Livewire\Owner\Banners as OwnerBanners;
use App\Livewire\Owner\Categories as OwnerCategories;
use App\Livewire\Owner\Dashboard as OwnerDashboard;
use App\Livewire\Owner\Faqs as OwnerFaqs;
use App\Livewire\Owner\Outlets as OwnerOutlets;
use App\Livewire\Owner\Services as OwnerServices;
use App\Livewire\Owner\Staff as OwnerStaff;
use App\Support\RoleRouter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Landing / splash
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route(RoleRouter::homeRouteFor(Auth::user()));
    }
    return view('welcome', [
        'banners' => \App\Models\PromoBanner::live()->take(5)->get(),
        'outlets' => \App\Models\Outlet::where('is_active', true)->orderBy('name')->get(),
        'faqs' => \App\Models\Faq::where('is_active', true)->orderBy('sort_order')->get(),
        'stats' => [
            'services' => \App\Models\Service::where('is_active', true)->count(),
            'outlets' => \App\Models\Outlet::where('is_active', true)->count(),
            'customers' => \App\Models\User::where('role', 'customer')->count(),
        ],
    ]);
})->name('welcome');

// Guest auth
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');
});

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

// Customer area
Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/home', Home::class)->name('home');
    Route::get('/layanan', Catalog::class)->name('catalog');
    Route::get('/layanan/{service}', ServiceDetail::class)->name('service.show');
    Route::get('/keranjang', Cart::class)->name('cart');
    Route::get('/checkout', Checkout::class)->name('checkout');
    Route::get('/pesanan', OrderHistory::class)->name('orders.index');
    Route::get('/pesanan/{order}', OrderShow::class)->name('orders.show');
    Route::get('/promo', Promo::class)->name('promo');
    Route::get('/akun', Account::class)->name('account');
});

// Staff menu dispatcher
Route::middleware('auth')->get('/staf', function () {
    return redirect()->route(RoleRouter::homeRouteFor(Auth::user()));
})->name('staff.home');

// Operator / outlet admin
Route::middleware(['auth', 'role:operator,outlet_admin,owner'])
    ->get('/operator', OperatorBoard::class)->name('operator.board');

// Courier
Route::middleware(['auth', 'role:courier'])
    ->get('/kurir', CourierTasks::class)->name('courier.tasks');

// Owner / management
Route::middleware(['auth', 'role:owner,outlet_admin'])->group(function () {
    Route::get('/owner', OwnerDashboard::class)->name('owner.dashboard');
    Route::get('/owner/kategori', OwnerCategories::class)->name('owner.categories');
    Route::get('/owner/layanan', OwnerServices::class)->name('owner.services');
    Route::get('/owner/banner', OwnerBanners::class)->name('owner.banners');
    Route::get('/owner/cabang', OwnerOutlets::class)->name('owner.outlets');
    Route::get('/owner/faq', OwnerFaqs::class)->name('owner.faqs');
    Route::get('/owner/pegawai', OwnerStaff::class)->name('owner.staff');
});
