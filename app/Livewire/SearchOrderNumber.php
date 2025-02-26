<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Order;

class SearchOrderNumber extends Component
{
    public $search = '';

    public function render()
    {
        return view('livewire.orders', [
            'orders' => Order::where('order_number', 'like', '%' . $this->search . '%')->get(),
        ]);
    }
}
