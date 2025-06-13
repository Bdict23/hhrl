<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\order;
use App\Models\order_detail;
use App\Models\Order as orderModel; // Renaming to avoid conflict with the Livewire component name

class Orders extends Component
{
    public $orders;
    public $markedAsCompleted = '1';

    protected $listeners = ['orderAdded' => 'refreshOrders'];

    public function mount()
    {
        $this->fetchData();
    }
    public function fetchData()
    {
        $this->orders = orderModel::where('order_status', '!=', 'FOR ALLOCATION')->where('branch_id', auth()->user()->branch->id)->whereDate('created_at', '>=', now())->get();
    }

    public function markItem($orderId, $markedAs)
    {
        if ($markedAs) {
            $this->markedAsCompleted = '1';
        } else {
            $this->markedAsCompleted = '0';
        }

        $order_details = order_detail::find($orderId);

        if ($order_details) {
            $order_details->marked = $this->markedAsCompleted;
            $order_details->save();
        } else {
            // Handle the case where the order is not found
            session()->flash('error', 'Order not found.');
        }
    }

    public function startOrder($orderId)
    {
        $order = order::find($orderId);
        if ($order) {
            $order->update([
                'order_status' => 'SERVING',
            ]);
        } else {
            // Handle the case where the order is not found
            session()->flash('error', 'Order not found.');
        }
    }

    public function serveOrder($orderId)
    {
        $order = order::find($orderId);
        if ($order) {
            $order->update([
                'order_status' => 'SERVED',
            ]);
        } else {
            // Handle the case where the order is not found
            session()->flash('error', 'Order not found.');
        }
    }

    public function refreshOrders()
    {
        $this->fetchData();
    }

    public function render()
    {
        return view('livewire.orders', ['orders' => $this->orders]);
    }
}
