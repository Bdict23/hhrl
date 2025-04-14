<?php

namespace App\Livewire\Inventory;

use Livewire\Component;
use App\Models\Backorder as BackorderModel;
use App\Models\Requisition;
use App\Models\Item;
use App\Models\Branch;
use App\Models\Company;
use App\Models\RequisitionInfo;

class BackOrder extends Component
{
    // public $backordersGroup = [];
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
        $this->backorderList = RequisitionInfo::with('item', 'branch', 'company')
            ->whereIn('REQUISITION_status',  ['PARTIALLY FULFILLED', 'FOR PO'])
            ->get();

    }

}
