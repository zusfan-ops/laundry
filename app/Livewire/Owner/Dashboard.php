<?php

namespace App\Livewire\Owner;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.staff')]
class Dashboard extends Component
{
    public function render()
    {
        $gmv = Payment::where('status', 'paid')->where('type', 'charge')->sum('amount');
        $totalOrders = Order::count();
        $completed = Order::where('status', 'completed')->count();
        $cancelled = Order::where('status', 'cancelled')->count();
        $activeOrders = Order::whereNotIn('status', ['completed', 'cancelled'])->count();
        $customers = User::where('role', 'customer')->count();

        $avgOrderValue = $completed > 0
            ? (int) Order::where('status', 'completed')->avg('final_total')
            : 0;

        $statusBreakdown = Order::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')->pluck('total', 'status')->toArray();

        $recent = Order::with('user')->latest()->take(8)->get();

        $completionRate = $totalOrders > 0 ? round($completed / $totalOrders * 100) : 0;

        return view('livewire.owner.dashboard', compact(
            'gmv', 'totalOrders', 'completed', 'cancelled', 'activeOrders',
            'customers', 'avgOrderValue', 'statusBreakdown', 'recent', 'completionRate'
        ));
    }
}
