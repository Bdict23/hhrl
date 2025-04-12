<?php

namespace App\Livewire\Inventory;

use Livewire\Component;
use App\Models\Backorder as BackorderModel;
use App\Models\Requisition;
use App\Models\Item;
use App\Models\Branch;
use App\Models\Company;

class BackOrder extends Component
{
    public $backorderList = [];
    public $requisitionList = [];

    public function render()
    {
        return view('livewire.inventory.back-order');
    }


    public function mount()
    {
        $this->fetchData();
    }

    public function fetchData()
    {
        // Fetch data from the database
        $this->backorderList = BackorderModel::with(['requisition', 'item', 'branch', 'company'])->get();

    }

}
