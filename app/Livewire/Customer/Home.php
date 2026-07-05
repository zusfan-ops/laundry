<?php

namespace App\Livewire\Customer;

use App\Models\Order;
use App\Models\PromoBanner;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Voucher;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Home extends Component
{
    public function render()
    {
        $user = auth()->user();

        $categories = ServiceCategory::where('is_active', true)->orderBy('sort_order')->get();

        $activeOrder = Order::where('user_id', $user->id)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->latest()
            ->first();

        $popular = Service::with('category')->where('is_active', true)->take(4)->get();

        $vouchers = Voucher::where('is_active', true)
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>', now()))
            ->take(5)->get();

        $banners = PromoBanner::live()->get();

        return view('livewire.customer.home', compact('categories', 'activeOrder', 'popular', 'vouchers', 'banners'));
    }
}
