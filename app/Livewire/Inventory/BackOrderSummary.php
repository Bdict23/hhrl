<?php

namespace App\Livewire\Inventory;
use Illuminate\Support\Facades\DB;

use Livewire\Component;

class BackOrderSummary extends Component
{
    public $backorderList = [];
    public $requisitionList = [];
    public $requisitionIds = [];


    public $fromDate;
    public $toDate;
    public $statusPO = 'All';

    public function render()
    {
        return view('livewire.inventory.back-order-summary');
    }


    public function mount(){
        if(auth()->user()->employee->getModulePermission('Back Orders') != 2 ){
            $this->fetchData();
        }else{
            return redirect()->to('dashboard');
        }
        
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

    public function search(){
        
        $query = DB::table('backorders as b')
            ->join('requisition_infos as r', 'b.requisition_id', '=', 'r.id')
            ->select('b.requisition_id', 'r.requisition_number', 'r.merchandise_po_number', 'r.requisition_status', 'r.category','r.created_at')
            ->groupBy('b.requisition_id', 'r.requisition_number', 'r.merchandise_po_number', 'r.requisition_status','r.category','r.created_at')
            ->where('b.branch_id',auth()->user()->branch_id);

        if ($this->statusPO !== "All") {
            $query->where('status', $this->statusPO);
        }

        if ($this->fromDate && $this->toDate) {
            $query->whereDate('created_at', '>=', $this->fromDate)
                  ->whereDate('created_at', '<=', $this->toDate);
        }

        $this->requisitionList = $query->get();
    }
}
