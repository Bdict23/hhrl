<?php

namespace App\Livewire\Restaurant;

use Livewire\Component;
use App\Models\ProductionOrder;

class ProductionOrderLists extends Component
{
    public $productionOrders;
    public function render()
    {
        return view('livewire.restaurant.production-order-lists');
    }

    public function mount()
    {
       $this->fetchdata();
    }

    public function fetchdata()
    {
        $this->productionOrders = ProductionOrder::where('branch_id', auth()->user()->branch_id)->latest()->get();
    }
}
