<?php

namespace App\Livewire\Inventory;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\Backorder as BackorderModel;
use App\Models\Requisition;
use App\Models\Item;
use App\Models\Branch;
use App\Models\Company;
use App\Models\RequisitionInfo;
use App\Models\BackorderItemInfoDetail;

class BackOrder extends Component
{
    // public $backordersGroup = [];
    public $backorderList = [];
    public $requisitionList = [];
    public $requisitionIds = [];

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
        $this->requisitionList = DB::table('backorders as b')
        ->join('requisition_infos as r', 'b.requisition_id', '=', 'r.id')
        ->select('b.requisition_id', 'r.requisition_number', 'r.merchandise_po_number', 'r.requisition_status', 'r.category','r.created_at')
        ->groupBy('b.requisition_id', 'r.requisition_number', 'r.merchandise_po_number', 'r.requisition_status','r.category','r.created_at')
        ->get();

    }

}
