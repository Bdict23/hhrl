<?php

namespace App\Livewire\Transactions;

use Livewire\Component;
use App\Models\Cashflow;

class CashFlowSummary extends Component
{

// data
public $cashFlows = [];
public $toDate;
public $fromDate;
public $statusCheckValue = 'ALL';
    public function render()
    {
        return view('livewire.transactions.cash-flow-summary');
    }
    public  function mount(){
        $this->fetchData();
    }
    public function fetchData(){
        $this->cashFlows = Cashflow::where('branch_id', auth()->user()->branch_id)->get();
    }
    public function search()
    {
        // Implement your search logic here based on the selected status and date range
        // You can emit an event or update a property to trigger the search results to be displayed
        $qry = Cashflow::where('branch_id', auth()->user()->branch_id);
        if($this->statusCheckValue != 'ALL'){
            $qry->where('status', $this->statusCheckValue);
        }
        if($this->fromDate){
            $qry->whereDate('created_at', '>=', $this->fromDate);
        }
        if($this->toDate){
            $qry->whereDate('created_at', '<=', $this->toDate);
        }
        $this->cashFlows = $qry->get();
    }
}
