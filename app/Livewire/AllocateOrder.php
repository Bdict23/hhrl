<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Models\Table;

class AllocateOrder extends Component
{
    public $orders;
    public $tables;
    public $selectedOrder = null;
    public $table;

    protected $listeners = ['orderAdded' => 'refreshOrders'];

    public function mount()
    {
        $this->orders = Order::all();
        $this->tables = Table::all();
    }

    public function refreshOrders()
    {
        $this->orders = Order::all();
    }

    public function selectOrder($orderId)
    {
        $this->selectedOrder = Order::find($orderId);
        $this->orders = Order::all();
        $this->tables = Table::all();
    }

    public function allocateOrder($orderId)
    {
        $order = Order::find($orderId);
        if ($order) {
            $order->update([
                'order_status' => 'PENDING',
                'table_id' => $this->table
            ]);
        } else {
            // Handle the case where the order is not found
            session()->flash('error', 'Order not found.');
        }
    }

    public function render()
    {
        return view('livewire.allocate-order', [
            'orders' => $this->orders,
            'tables' => $this->tables,
            'selectedOrder' => $this->selectedOrder,
        ]);
    }
}
