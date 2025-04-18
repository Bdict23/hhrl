<?php

namespace App\Livewire\Inventory;
use Illuminate\Support\Facades\DB;

use Livewire\Component;

class BackOrderSummary extends Component
{
    public $backorderList = [];
    public $requisitionList = [];
    public $requisitionIds = [];

    public function render()
    {
        return view('livewire.inventory.back-order-summary');
    }


    public function mount(){
        $this->fetchData();
    }

    public function fetchData()
    {
        $this->requisitionList = DB::table('backorders as b')
        ->join('requisition_infos as r', 'b.requisition_id', '=', 'r.id')
        ->select('b.requisition_id', 'r.requisition_number', 'r.merchandise_po_number', 'r.requisition_status', 'r.category','r.created_at')
        ->groupBy('b.requisition_id', 'r.requisition_number', 'r.merchandise_po_number', 'r.requisition_status','r.category','r.created_at')
        ->where('b.branch_id',auth()->user()->branch_id)->get();

    }

    public function showBackorder($requisitionNo){
      return redirect()->to('/show-backorder?requisition-number=' . $requisitionNo );
    }
}
