<?php

namespace App\Livewire\Courier;

use App\Models\CourierAssignment;
use App\Services\NotificationService;
use App\Services\OrderStateMachine;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.staff')]
class Tasks extends Component
{
    use WithFileUploads;

    public ?int $proofAssignmentId = null;
    public $proofPhoto = null;

    private function courier()
    {
        return auth()->user()->courierProfile;
    }

    public function depart(int $assignmentId): void
    {
        $a = $this->findAssignment($assignmentId);
        $a->update(['status' => 'on_the_way']);
        $this->dispatch('toast', message: 'Status: dalam perjalanan.', type: 'info');
    }

    public function arrive(int $assignmentId): void
    {
        $a = $this->findAssignment($assignmentId);
        $a->update(['status' => 'arrived']);
        $this->dispatch('toast', message: 'Status: tiba di lokasi.', type: 'info');
    }

    public function openProof(int $assignmentId): void
    {
        $this->proofAssignmentId = $assignmentId;
        $this->proofPhoto = null;
    }

    public function cancelProof(): void
    {
        $this->reset('proofAssignmentId', 'proofPhoto');
    }

    /** Complete a pickup/delivery assignment idempotently and advance the order. */
    public function complete(OrderStateMachine $sm, NotificationService $notifications): void
    {
        $assignment = $this->findAssignment($this->proofAssignmentId);
        $order = $assignment->order;

        $photoPath = $this->proofPhoto ? $this->proofPhoto->store('proof-photos', 'public') : null;
        $clientUuid = (string) Str::uuid();   // generated client-side in a real offline app

        DB::transaction(function () use ($assignment, $order, $sm, $notifications, $photoPath, $clientUuid) {
            $assignment->update(['status' => 'done', 'done_at' => now(), 'proof_photo' => $photoPath]);

            if ($assignment->type === 'pickup' && $order->status === 'assigned_pickup') {
                $sm->transition($order, 'picked_up', auth()->user(), [
                    'note' => 'Cucian dijemput', 'photo_path' => $photoPath, 'client_uuid' => $clientUuid,
                ]);
            } elseif ($assignment->type === 'delivery' && $order->status === 'delivering') {
                $sm->transition($order, 'completed', auth()->user(), [
                    'note' => 'Cucian diserahkan ke pelanggan', 'photo_path' => $photoPath, 'client_uuid' => $clientUuid,
                ]);
            }
        });

        $this->cancelProof();
        $this->dispatch('toast', message: 'Tugas selesai.', type: 'success');
    }

    /** For delivery: start moving the order into "delivering". */
    public function startDelivery(int $assignmentId, OrderStateMachine $sm): void
    {
        $assignment = $this->findAssignment($assignmentId);
        $order = $assignment->order;
        $assignment->update(['status' => 'on_the_way']);
        if ($order->status === 'assigned_delivery') {
            $sm->transition($order, 'delivering', auth()->user(), ['note' => 'Kurir berangkat mengantar']);
        }
        $this->dispatch('toast', message: 'Mulai mengantar.', type: 'info');
    }

    private function findAssignment(int $id): CourierAssignment
    {
        return CourierAssignment::with('order.address')
            ->where('courier_id', $this->courier()->id)
            ->findOrFail($id);
    }

    public function render()
    {
        $courier = $this->courier();
        $assignments = CourierAssignment::with('order.address', 'order.user')
            ->where('courier_id', $courier?->id)
            ->whereNotIn('status', ['done', 'failed'])
            ->orderBy('type')
            ->get();

        $doneToday = CourierAssignment::where('courier_id', $courier?->id)
            ->where('status', 'done')->whereDate('done_at', today())->count();

        return view('livewire.courier.tasks', compact('assignments', 'doneToday'));
    }
}
